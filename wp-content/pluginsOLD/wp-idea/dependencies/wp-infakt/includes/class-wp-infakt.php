<?php
// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use bpmj\wpidea\integrations\Interface_External_Service_Integration;
use bpmj\wpidea\sales\product\model\Gtu;
use bpmj\wpidea\integrations\invoices\email\Interface_Sendable_By_Email;

/**
 * WP Infakt - API
 */
class BPMJ_WP_Infakt extends BPMJ_Base_Invoice
    implements Interface_External_Service_Integration, Interface_Sendable_By_Email
{

	/**
	 * @var string
	 */
	protected static $service_short_id = 'wpinfakt';

	/**
	 * @var string
	 */
	protected static $service_long_id = 'infakt';

	/**
	 * @var string
	 */
	protected static $meta_key_invoice_src = 'infakt_src';

	/**
	 * @var string
	 */
	protected static $meta_key_invoice_note = 'infakt_note';

	/**
	 * @var string
	 */
	protected static $meta_key_invoice_status = 'infakt_status';

	/**
	 * @var string
	 */
	protected static $error_note_css_class = 'bpmj_wpinfakt_error_note';

	/**
	 * @var string
	 */
	protected static $success_note_css_class = 'bpmj_wpinfakt_success_note';

	/**
	 * @var string
	 */
	protected static $invoice_service_name = 'WP Infakt';

	/**
	 * @var string
	 */
	protected static $invoice_post_type = 'bpmj_wp_infakt';

	/**
	 * @var string
	 */
	protected static $api_endpoint_url_pattern = 'https://api.infakt.pl/v3/%1$s.json';

	/**
	 * @var string
	 */
	protected static $api_endpoint_host = 'https://api.infakt.pl/';

	/**
	 * Token dla API pobrany z infakt.pl
	 * @var string
	 */
	private $api_token = "";

	/**
	 * ID faktury utworzonej po stronie infakt.pl
	 * @var int
	 */
	private $infakt_invoice_id = '';

	/**
	 * Konstruktor obiektu
	 */
	public function __construct($api_token = null) {
		global $bpmj_wpinfakt_settings;

		$this->api_token = $api_token ?? $bpmj_wpinfakt_settings[ 'infakt_api_key' ];
	}

	/**
	 * @return array
	 */
	protected function get_api_call_headers() {
		return array(
			'Content-Type'    => 'application/json',
			'X-inFakt-ApiKey' => $this->api_token,
		);
	}

	/**
	 * @return string
	 */
	protected function get_invoices_api_url() {
		return $this->get_api_url( 'invoices' );
	}

	/**
	 * Interpretuje odpowiedź serwera po próbie wystawienia faktury
	 *
	 * @param string $res_header
	 * @param string $res_body
	 * @param string $note_date
	 * @param int $post_id
	 * @param string $logs
	 * @param array $src
	 *
	 * @return string
	 */
	protected function analyze_server_response( $res_header, $res_body, $note_date, $post_id, $logs, $src ) {
		/*
		 * Przeanalizuj odpowiedź serwera
		 */
		$decode = json_decode( $res_body, true );
		if ( json_last_error() != JSON_ERROR_NONE || ! isset( $decode ) || empty( $decode ) ) {
			return __( 'Nieznany błąd podczas wysyłki!', 'bpmj_wpinfakt' );
		} else if ( ! empty( $decode[ 'error' ] ) ) {
			return $decode[ 'error' ] . ( empty( $decode[ 'errors' ] ) ? '' : ' (' . json_encode( $decode[ 'errors' ] ) . ')' );
		} else {

			$this->infakt_invoice_id = $decode[ 'id' ];
            $this->set_remote_invoice_id($this->infakt_invoice_id);

			// Bezpośredni link do edycji faktury
			$link = 'https://www.infakt.pl/app/faktury/' . $this->infakt_invoice_id;

			$note = '<span class="' . static::$success_note_css_class . '">' . __( 'Dokument utworzony!', 'bpmj_wpinfakt' ) . '<a target="_blank" href="' . $link . '">' . __( 'Zobacz.', 'bpmj_wpinfakt' ) . '</a>' . '</span>';

			// Nadpisz tytuł
			$args = array(
				'ID'         => $post_id,
				'post_title' => $this->remote_invoice_number = $decode[ 'number' ],
			);
			wp_update_post( $args );

			// Zapisz informacje
			update_post_meta( $post_id, static::$meta_key_invoice_status, 'ok' );
			update_post_meta( $post_id, static::$meta_key_invoice_note, $note );

			// Oznacz fakturę jako zapłacona
			$this->mark_invoice_as_paid();

			// Wyślij automatycznie e-maila z faktura do klienta
			$this->send_auto_email( $src );

			return '';
		}
	}

	/**
	 * Wysyła automatycznie e-mail z fakturą do klienta
	 *
	 * @param array $src
	 */
	private function send_auto_email( $src ) {
		if ( $this->should_invoice_be_auto_sent_by_email() ) {
			$this->send_by_email($this->infakt_invoice_id);
			$this->emit_after_invoice_auto_sent_event($src);
		}
	}

	/**
	 * Oznacza fakturę jako zapłacona
	 */
	private function mark_invoice_as_paid() {
		$json = $this->maybe_encode( array(
			'paid_date' => $this->issue_date,
		) );
		$url  = $this->get_api_url( 'invoices/' . $this->infakt_invoice_id . '/paid' );
		$this->make_api_call( $url, $json );
	}

	/**
	 * @param string $number
	 *
	 * @return string
	 */
	protected function prepare_monetary_value( $number ) {
		return bcmul( $number, 100, 0 );
	}

	protected function prepare_invoice_data( array $data, array $src = array() ) {
		if ( empty( $this->issue_date ) ) {
		    $this->issue_date = current_time( 'Y-m-d' );
		}
		$invoice_date = $this->issue_date;
		$data         = array_merge( array(
			'currency'            => $this->currency,
			'paid_price'          => $this->prepare_monetary_value( $this->paid_amount ),
			'kind'                => 'vat',
            'payment_method'      => $this->get_infakt_payment_string_by_payment_gateway( get_post_meta( $src['id'], '_edd_payment_gateway', true ) ),
			'recipient_signature' => $this->contractor[ 'person_name' ],
			'invoice_date'        => $invoice_date,
			'sale_date'           => $invoice_date,
			'payment_date'        => $invoice_date,
			'gross_price'         => $this->prepare_monetary_value( $this->paid_amount ),
			'client_company_name' => $this->contractor[ 'company_name' ] ? $this->contractor[ 'company_name' ] : $this->contractor[ 'person_name' ],
			'client_street'       => $this->contractor[ 'street' ],
			'client_city'         => $this->contractor[ 'city' ],
			'client_post_code'    => $this->contractor[ 'post_code' ],
			'client_tax_code'     => $this->contractor[ 'nip' ],
			'sale_type'           => 'service',
			'invoice_date_kind'   => 'sale_date',
            'vat_exemption_reason' => $this->get_vat_exemption(),
			'services'            => array(),
		), $data );

		if ( ! empty( $src[ 'id' ] ) ) {
			$data[ 'notes' ] = str_replace( array(
					"http://",
					"https://"
				), "", site_url() ) . " #" . $src[ 'id' ];
		}

		foreach ( $this->items as $item ) {
			$service = array(
				'name'        => $item->get_name(),
				'tax_symbol'  => $item->get_tax_rate(),
				'unit'        => 'szt.',
				'quantity'    => $item->get_quantity(),
				'gross_price' => $this->prepare_monetary_value( $item->get_gross_value_after_discount() ),
			);

			if ( ! $this->is_vat_payer() ) {
				$service[ 'tax_symbol' ] = 'zw';
			}

            $gtu = $item->get_gtu();
            if($gtu && Gtu::NO_GTU !== $gtu) {
                $service[ 'gtu_id' ] = $this->prepare_gtu_code($gtu);
            }

            $flat_rate_tax_symbol = $item->get_flat_rate_tax_symbol();
            if( apply_filters( 'wpi_invoice_flat_rate_enabled', true ) && !empty( $flat_rate_tax_symbol ) ) {
                $service['flat_rate_tax_symbol'] =  $item->get_flat_rate_tax_symbol();
            }

			$data[ 'services' ][] = $service;
		}

		if ( ! $this->is_vat_payer() ) {
			$data[ 'vat_exemption_reason' ] = 1;
		}

        if ( ! empty( $src[ 'src' ] ) && 'edd' === $src[ 'src' ] ) {
            $data = apply_filters( 'bpmj_wpinfakt_edd_invoice_data', $data, $src[ 'id' ] );
        }

		return array( 'invoice' => $data );
	}


    public function check_connection(): bool
    {
        $url = $this->get_api_url( 'invoices' );
        $response = $this->make_api_call( $url, [] , 'GET');
        $body = json_decode( wp_remote_retrieve_body( $response ) );

       return isset($body->metainfo);
    }

    public function get_service_name()
    {
        return $this::$invoice_service_name;
    }


    protected function prepare_gtu_code(string $gtu): int
    {
        $gtu_exploded = explode('_', $gtu);
        return $gtu_exploded[1] ? (int)$gtu_exploded[1] : 0;
    }

    private function get_infakt_payment_string_by_payment_gateway( $payment_gateway )
    {
        switch ($payment_gateway) {
            case 'dotpay_gateway':
                return 'dotpay';
            case 'payu':
                return 'payu';
            case 'przelewy24_gateway':
                return 'przelewy24';
            case 'tpay_gateway':
                return 'tpay';
            case 'paypal':
                return 'paypal';
            default:
                return 'other';
        }
    }

    public function send_by_email(int $invoice_id): void
    {
        $json = $this->maybe_encode([
             'print_type' => 'original',
             'locale'     => 'pl',
             'recipient'  => $this->contractor[ 'email' ],
        ]);
        $url  = $this->get_api_url( 'invoices/' . $invoice_id . '/deliver_via_email' );

        $this->make_api_call( $url, $json );
    }

    private function should_invoice_be_auto_sent_by_email(): bool
    {
        global $bpmj_wpinfakt_settings;

        return isset( $bpmj_wpinfakt_settings[ 'auto_sent' ] ) && ! empty( $bpmj_wpinfakt_settings[ 'auto_sent' ] );
    }

    private function emit_after_invoice_auto_sent_event(array $src): void
    {
        do_action(
            'bpmj_' . static::$service_short_id . '_after_invoice_sent_to_customer',
            $src,
            $this->infakt_invoice_id,
            $this->get_remote_invoice_number()
        );
    }
}
