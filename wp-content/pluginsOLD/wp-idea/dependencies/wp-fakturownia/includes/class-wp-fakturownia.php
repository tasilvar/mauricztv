<?php

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
use bpmj\wpidea\integrations\Interface_External_Service_Integration;
use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\http\Http_Client;

if (!defined('ABSPATH')) {
    exit;
}

use bpmj\wpidea\sales\product\model\Gtu;
use bpmj\wpidea\integrations\invoices\email\Interface_Sendable_By_Email;

/**
 * WP Fakturownia - API
 */
class BPMJ_WP_Fakturownia
    extends BPMJ_Base_Invoice
    implements Interface_External_Service_Integration, Interface_Sendable_By_Email
{

    /**
     * @var string
     */
    protected static $service_short_id = 'wpfa';

    /**
     * @var string
     */
    protected static $service_long_id = 'fakturownia';

    /**
     * @var string
     */
    protected static $meta_key_invoice_src = 'fakturownia_src';

    /**
     * @var string
     */
    protected static $meta_key_invoice_note = 'fakturownia_note';

    /**
     * @var string
     */
    protected static $meta_key_invoice_status = 'fakturownia_status';

    /**
     * @var string
     */
    protected static $error_note_css_class = 'bpmj_wpfa_error_note';

    /**
     * @var string
     */
    protected static $success_note_css_class = 'bpmj_wpfa_success_note';

    /**
     * @var string
     */
    protected static $invoice_service_name = 'WP Fakturownia';

    /**
     * @var string
     */
    protected static $invoice_post_type = 'bpmj_wp_fakturownia';

    /**
     * @var string
     */
    protected static $api_endpoint_url_pattern = 'https://.fakturownia.pl/%1$s.json';

    /**
     * Token pobrany z fakturownia.pl
     * @var string
     */
    private $token = '';

    /**
     * URL na który przesyłane będą fakturu JSON
     * @var string
     */
    private $host = '';

    /**
     * Slug konta w fakturownia.pl
     * @var string
     */
    private $slug = '';

    /**
     * ID faktury utworzonej po stronie Fakturownia.pl
     * @var int
     */
    private $fakturownia_invoice_id = '';

    /**
     * Funkcja pobierająca id produktu z fakturowni na podstawie danych produktu
     * Sygnatura: int funkcja(BPMJ_Base_Invoice_Item $item, BPMJ_Base_Invoice $invoice_object)
     * @var callable
     */
    private $fakturownia_product_id_resolver;

    /**
     * Funkcja pobierająca dodatkowe informacje o produkcie
     * Sygnatura: int funkcja(BPMJ_Base_Invoice_Item $item, BPMJ_Base_Invoice $invoice_object)
     * @var callable
     */
    private $fakturownia_additional_info_resolver;

    /**
     * BPMJ_WP_Fakturownia constructor.
     * @param null $api_key
     */
    public function __construct($api_key = null)
    {
        global $bpmj_wpfa_settings;
        
        $this->vat_exemption = $bpmj_wpfa_settings['vat_exemption'] ?? $this->vat_exemption;

        if (!$api_key) {
            $api_key = $bpmj_wpfa_settings['apikey'];
        }
        $this->prepare_token($api_key);
    }

    /**
     * Definiuje zmienne host i slug - wciąga dane z tokena
     *
     * @param string $token
     */
    private function prepare_token($token)
    {
        $this->token = $token;

        // Sprawdzenie poprawności tokena
        if (false !== strrpos($this->token, '/')) {
            // Token podzielony na części
            $token_parts = explode('/', $this->token);

            // Pobranie slug klienta z fakturownia.pl
            if (isset($token_parts[1])) {
                $this->slug = $token_parts[1];
                $this->host = $this->slug . '.fakturownia.pl';
            }
        }
    }

    /**
     * @param string $action
     *
     * @return string
     */
    protected function get_api_url($action)
    {
        return 'https://' . $this->host . '/' . $action . '.json';
    }

    /**
     * @return string
     */
    protected function get_invoices_api_url()
    {
        return $this->get_api_url('invoices');
    }

    /**
     * @param int $post_id
     *
     * @return $this
     */
    public function set_from_invoice_post($post_id)
    {
        parent::set_from_invoice_post($post_id);
        if (!empty($this->invoice_data['api_token'])) {
            $this->prepare_token($this->invoice_data['api_token']);
        }

        return $this;
    }

    /**
     * @param string $res_header
     * @param string $res_body
     * @param string $note_date
     * @param int $post_id
     * @param string $logs
     * @param array $src
     *
     * @return string
     */
    protected function analyze_server_response($res_header, $res_body, $note_date, $post_id, $logs, $src)
    {
        /*
         * Przeanalizuj odpowiedź serwera
         */

        switch ($res_header) {
            case '201 Created': // Faktura została uwtorzona OK

                $decode = json_decode($res_body);
                $this->fakturownia_invoice_id = $decode->id;
                $this->set_remote_invoice_id($this->fakturownia_invoice_id);

                // Bezpośredni link do edycji faktury
                $link = 'https://' . $this->slug . '.fakturownia.pl/invoices/' . $this->fakturownia_invoice_id;

                $note = '<span class="' . static::$success_note_css_class . '">' . $note_date . __(
                        'Dokument utworzony!',
                        'bpmj_wpfa'
                    ) . ' <a target="_blank" href="' . $link . '">' . __('Zobacz.', 'bpmj_wpfa') . '</a>' . '</span>';

                // Nadpisz tytuł
                $args = [
                    'ID' => $post_id,
                    'post_title' => $this->remote_invoice_number = $decode->number,
                ];
                wp_update_post($args);

                // Zapisz informacje o powodzeniu
                update_post_meta($post_id, static::$meta_key_invoice_status, 'ok');
                update_post_meta($post_id, static::$meta_key_invoice_note, $logs . $note);

                // Wyślij automatycznie e-maila z faktura do klienta
                $this->send_auto_email($src);

                return ''; // No error
            case '422 Unprocessable Entity': // Błąd merytoryczny, np Nieprawidłowy NIP itp.
                $decode = json_decode($res_body, true);
                if (false !== $decode && !empty($decode['message'])) {
                    return $this->maybe_encode($decode['message']);
                }

                return __('Obiekt JSON jest niezgodny z API Fakturownia.pl: ', 'bpmj_wpfa');
        }

        return sprintf(__('Błąd podczas wysyłki faktury: %s', 'bpmj_wpfa'), $res_header);
    }

    /**
     * Wysyła automatycznie e-mail z fakturą do klienta
     *
     * @param array $src
     */
    private function send_auto_email($src = [])
    {
        if ($this->should_invoice_be_auto_sent_by_email($src['kind'] ?? '')) {
            $this->send_by_email($this->fakturownia_invoice_id);
            $this->emit_after_invoice_auto_sent_event($src);
        }
    }

    /**
     * @param array $invoice_data
     * @param array $src
     *
     * @return array
     */
    protected function prepare_invoice_data(array $invoice_data, array $src = [])
    {
        global $bpmj_wpfa_settings;

        $invoice_products = [];
        $has_vat_exemption = false;
        foreach ($this->items as $item) {
            $invoice_product = [];
            $invoice_product['name'] = $item->get_name();
            $invoice_product['quantity'] = $item->get_quantity();
            $invoice_product['quantity_unit'] = 'szt.';
            $invoice_product['tax'] = $this->is_vat_payer() ? $item->get_tax_rate() : 'zw';
            $invoice_product['total_price_gross'] = $item->get_gross_value_after_discount();

            if( 'zw' == $invoice_product['tax'] ) {
                $has_vat_exemption = true;
            }
            $gtu = $item->get_gtu();
            if ($gtu && Gtu::NO_GTU !== $gtu) {
                $invoice_product['gtu_code'] = $this->prepare_gtu_code($gtu);
            }

            $fakturownia_product_id = $this->get_fakturownia_product_id($item);
            if (!empty($fakturownia_product_id)) {
                $invoice_product['product_id'] = $fakturownia_product_id;
            }

            $add_info = $this->get_fakturownia_additional_info($item);
            if (!empty($add_info)) {
                $invoice_product['additional_info'] = $add_info;
            }

            $flat_rate_tax_symbol = $item->get_flat_rate_tax_symbol();
            if (apply_filters('wpi_invoice_flat_rate_enabled', true) && !empty($flat_rate_tax_symbol)) {
                $invoice_product['lump_sum_tax'] = str_replace('.', ',', $flat_rate_tax_symbol);
            }

            if ($item->get_gross_value_after_discount(
                ) > 0 && (!empty($invoice_product['product_id']) || !empty($invoice_product['name']))) {
                $invoice_products[] = $invoice_product;
            }
        }

        if ( $has_vat_exemption ) {
            $invoice_data['exempt_tax_kind'] = '' != $this->get_vat_exemption() ? $this->get_vat_exemption() : Translator_Static_Helper::translate('settings.sections.integrations.fakturownia.fakturownia_vat_exemption.default');
        }

        if (empty($invoice_data['payment_type'])) {
            $invoice_data['payment_type'] = 'transfer';
        }

        $kind = 'vat';
        if ($this->is_receipt()) {
            $kind = 'receipt';
        }
        $order_number = !empty($src['id']) ? $src['id'] : null;
        if (!empty($src['id']) && 'edd' === $src['src']) {
            $order_number = edd_get_payment_number($src['id']);
        }
        $invoice_data = array_merge([
                                        'kind' => $kind,
                                        'buyer_first_name' => isset($this->contractor['additional_data']['first_name']) ? $this->contractor['additional_data']['first_name'] : '',
                                        'buyer_last_name' => isset($this->contractor['additional_data']['last_name']) ? $this->contractor['additional_data']['last_name'] : '',
                                        'buyer_name' => $this->contractor['company_name'] ? $this->contractor['company_name'] : $this->contractor['person_name'],
                                        'buyer_tax_no' => $this->contractor['nip'],
                                        'buyer_post_code' => $this->contractor['post_code'],
                                        'buyer_city' => $this->contractor['city'],
                                        'buyer_street' => $this->contractor['street'],
                                        'buyer_email' => $this->contractor['email'],
                                        'buyer_company' => $this->contractor['company_name'] ? '1' : '0',
                                        'buyer_country' => isset($this->contractor['additional_data']['country']) ? $this->contractor['additional_data']['country'] : 'PL',
                                        'currency' => $this->currency,
                                        'oid' => $order_number,
                                        'positions' => $invoice_products,
                                    ], $invoice_data);

        if (!empty($src['src']) && 'edd' === $src['src']) {
            $invoice_data = apply_filters('bpmj_wpfa_edd_invoice_data', $invoice_data, $src['id']);
        }

        $result = array_merge([
                                  'api_token' => $this->token,
                              ], ['invoice' => $invoice_data]);
        $default_invoice_data = [
            'number' => null,
            'sell_date' => current_time('Y-m-d'),
            'issue_date' => current_time('Y-m-d'),
            'payment_to' => current_time('Y-m-d'),
            'payment_type' => 'transfer',
            'status' => 'paid',
            'currency' => 'PLN',
            'additional_info' => '0',
        ];

        // ID firmy utworzonej po stronie fakturownia.pl
        $department_id = isset($bpmj_wpfa_settings['departments_id']) && !empty($bpmj_wpfa_settings['departments_id']) ? $bpmj_wpfa_settings['departments_id'] : false;

        if ($department_id) {
            $default_invoice_data['department_id'] = $department_id;
        }

        $result['invoice'] = array_merge($default_invoice_data, !empty($result['invoice']) ? $result['invoice'] : []);

        if (empty($result['invoice']['buyer_first_name']) || empty($result['invoice']['buyer_last_name'])) {
            $result['invoice']['buyer_first_name'] = '............';
            $result['invoice']['buyer_last_name'] = '............';
        }

        return $result;
    }

    protected function prepare_gtu_code(string $gtu): string
    {
        return strtoupper($gtu);
    }

    /**
     * Zapisuje kod JSON odpowiedzialny za dodanie faktury do fakturowni
     *
     * @param array $data
     * @param array $src
     *
     * @return int post ID
     */
    public function store_invoice(array $data, array $src = [])
    {
        parent::store_invoice($data, $src);

        // Jeżeli utworzyło post
        if ($this->invoice_post_id) {
            // Jeżeli jest podany API TOKEN
            if (!empty($this->invoice_data['api_token'])) {
                $note = '<span class="bpmj_wpfa_pending_note">' . __('Oczekuje na wysyłkę', 'bpmj_wpfa') . '</span>';

                update_post_meta($this->invoice_post_id, static::$meta_key_invoice_status, 'pending');
                update_post_meta($this->invoice_post_id, static::$meta_key_invoice_note, $note);
            } else { // Jeżeli nie podano API TOKENA, wyświetl błąd
                $note = '<span class="' . static::$error_note_css_class . '">' . __(
                        'Błąd merytoryczny w JSON. Brak pola <b>api_token</b>',
                        'bpmj_wpfa'
                    ) . '</span>';

                // Zapisz informacje o błędzie
                update_post_meta($this->invoice_post_id, static::$meta_key_invoice_status, 'error');
                update_post_meta($this->invoice_post_id, static::$meta_key_invoice_note, $note);

                do_action('wpi_after_invoice_created_error', static::$invoice_post_type, [], strip_tags($note));
            }
        }

        return $this->invoice_post_id;
    }

    /**
     * Zapisuje dane produktu w serwisie Fakturownia.pl
     *
     * @param string $name
     * @param string $product_code
     * @param string $gross_unit_price
     * @param string $tax_rate
     * @param int|bool $product_id
     *
     * @return int|bool
     */
    public function create_modify_product($name, $product_code, $gross_unit_price, $tax_rate, $product_id = false)
    {
        if ($product_id) {
            // Sprawdzamy, czy podany $product_id jest prawidłowy
            $url = $this->get_api_url('products/' . $product_id) . '?api_token=' . $this->token;
            $response = wp_remote_get($url);
            if ('200 OK' !== wp_remote_retrieve_header($response, 'status')) {
                $product_id = false;
            }
        }
        if (!$product_id) {
            $name_prepared = bpmj_wpfa_normalize_string_for_comparison($name);
            $options = bpmj_wpfa_get_products_as_options();
            foreach ($options as $key => $value) {
                if (bpmj_wpfa_normalize_string_for_comparison($value) === $name_prepared) {
                    $product_id = $key;
                    break;
                }
            }
        }
        $data = [
            'api_token' => $this->token,
            'product' => [
                'name' => $name,
                'code' => $product_code,
                'price_gross' => $gross_unit_price,
                'tax' => $tax_rate,
            ]
        ];

        $url = $this->get_api_url('products' . ($product_id ? '/' . $product_id : ''));

        //Budowanie żądania HTTP POST do Fakturownia.pl
        $response = wp_remote_post($url, [
                                           'method' => $product_id ? 'PUT' : 'POST',
                                           'timeout' => 45,
                                           'headers' => [
                                               'content-type' => 'application/json'
                                           ],
                                           'redirection' => 5,
                                           'body' => json_encode($data)
                                       ]
        );

        if (in_array(wp_remote_retrieve_response_code($response), [200, 201])) {
            $result = json_decode(wp_remote_retrieve_body($response));

            return $result->id;
        }

        return false;
    }

    /**
     * Zwraca listę produktów zapisanych w serwisie Fakturownia.pl
     *
     * @return array|bool
     */
    public function get_products()
    {
        $url = $this->get_api_url('products') . '?api_token=' . $this->token;

        $response = wp_remote_get($url);
        $page = 1;
        $products = [];
        while (200 === wp_remote_retrieve_response_code($response)) {
            $data = json_decode(wp_remote_retrieve_body($response));
            if (is_array($data) && count($data) > 0) {
                $products = array_merge($products, $data);
                $response = wp_remote_get($url . '&page=' . (++$page));
            } else {
                break;
            }
        }

        return !empty($products) ? $products : false;
    }

    /**
     * @param callable $fakturownia_product_id_resolver
     */
    public function set_fakturownia_product_id_resolver($fakturownia_product_id_resolver)
    {
        if (is_callable($fakturownia_product_id_resolver)) {
            $this->fakturownia_product_id_resolver = $fakturownia_product_id_resolver;
        }
    }

    /**
     * @param BPMJ_Base_Invoice_Item $item
     *
     * @return int
     */
    public function get_fakturownia_product_id(BPMJ_Base_Invoice_Item $item)
    {
        if ($this->fakturownia_product_id_resolver) {
            return call_user_func($this->fakturownia_product_id_resolver, $item, $this);
        }

        return null;
    }

    /**
     * @param callable $fakturownia_additional_info_resolver
     */
    public function set_fakturownia_additional_info_resolver($fakturownia_additional_info_resolver)
    {
        if (is_callable($fakturownia_additional_info_resolver)) {
            $this->fakturownia_additional_info_resolver = $fakturownia_additional_info_resolver;
        }
    }

    /**
     * @param $item
     *
     * @return string|null
     */
    public function get_fakturownia_additional_info($item)
    {
        if ($this->fakturownia_additional_info_resolver) {
            return call_user_func($this->fakturownia_additional_info_resolver, $item, $this);
        }

        return null;
    }

    public function check_connection(): bool
    {
        $url = $this->get_api_url('products');

        $client = new Http_Client();
        $response = $client->create_request()
            ->set_url($url)
            ->add_param('api_token', $this->token)
            ->send();

        if ($response->is_error()) {
            return false;
        }

        $body = $response->get_decoded_body();

        if (!isset($body->code)) {
            return true;
        }

        if ($body->code != 'error') {
            return true;
        }

        return false;
    }

    public function get_service_name()
    {
        return $this::$invoice_service_name;
    }

    public function send_by_email(int $invoice_id): void
    {
        $url = $this->get_api_url(
                'invoices/' . $invoice_id . '/send_by_email'
            ) . '?api_token=' . $this->token;

        wp_remote_post($url);
    }

    private function should_invoice_be_auto_sent_by_email(string $kind): bool
    {
        global $bpmj_wpfa_settings;

        //Sprawdzanie, czy jest włączona opcja automatycznego wysyłania e-maili.
        return ('receipt' !== $kind && !empty($bpmj_wpfa_settings['auto_sent'])) || ('receipt' === $kind && !empty($bpmj_wpfa_settings['auto_sent_receipt']));
    }

    private function emit_after_invoice_auto_sent_event(array $src): void
    {
        do_action(
            'bpmj_' . static::$service_short_id . '_after_invoice_sent_to_customer',
            $src,
            $this->fakturownia_invoice_id,
            $this->get_remote_invoice_number()
        );
    }

}
