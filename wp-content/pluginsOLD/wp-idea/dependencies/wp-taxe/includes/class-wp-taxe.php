<?php
use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\integrations\Interface_External_Service_Integration;

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use bpmj\wpidea\sales\product\model\Gtu;
use bpmj\wpidea\integrations\invoices\email\Interface_Sendable_By_Email;

/**
 * WP Taxe - API
 */
class BPMJ_WP_Taxe extends BPMJ_Base_Invoice
    implements Interface_External_Service_Integration, Interface_Sendable_By_Email
{

	/**
	 * @var string
	 */
	protected static $service_short_id = 'wptaxe';

	/**
	 * @var string
	 */
	protected static $service_long_id = 'taxe';

	/**
	 * @var string
	 */
	protected static $meta_key_invoice_src = 'taxe_src';

	/**
	 * @var string
	 */
	protected static $meta_key_invoice_note = 'taxe_note';

	/**
	 * @var string
	 */
	protected static $meta_key_invoice_status = 'taxe_status';

	/**
	 * @var string
	 */
	protected static $error_note_css_class = 'bpmj_wptaxe_error_note';

	/**
	 * @var string
	 */
	protected static $success_note_css_class = 'bpmj_wptaxe_success_note';

	/**
	 * @var string
	 */
	protected static $invoice_service_name = 'WP Taxe';

	/**
	 * @var string
	 */
	protected static $invoice_post_type = 'bpmj_wp_taxe';

	/**
	 * @var string
	 */
	protected static $api_endpoint_url_pattern = 'https://panel.taxe.pl/api/%1$s';

	/**
	 * @var string
	 */
	protected static $api_endpoint_host = 'https://panel.taxe.pl/';

	/**
	 * Login do serwisu taxe.pl
	 * @var string
	 */
	private $login = "";

	/**
	 * Token dla API pobrany z taxe.pl
	 * @var string
	 */
	private $api_token = "";

	/**
	 * ID faktury utworzonej po stronie taxe.pl
	 * @var int
	 */
	private $taxe_invoice_id = '';

    /**
     * BPMJ_WP_Taxe constructor.
     * @param null $login
     * @param null $api_token
     */
	public function __construct($login = null, $api_token = null) {
		global $bpmj_wptaxe_settings;

		$this->login     = $login ?? $bpmj_wptaxe_settings[ 'taxe_login' ];
		$this->api_token = $api_token ?? $bpmj_wptaxe_settings[ 'taxe_api_key' ];
	}

    /**
	 * @param string $data_encoded
	 *
	 * @return array
	 */
	protected function decode_data( $data_encoded ) {
		return $this->decode_xml( $data_encoded );
	}

	/**
	 * @param array $data
	 *
	 * @return string
	 */
	protected function encode_data( array $data ) {
		return $this->encode_xml( 'xml', $data );
	}

	/**
	 * @return array
	 */
	protected function get_api_call_headers() {
		return array(
			'Content-Type'  => 'application/x-www-form-urlencoded',
			'Authorization' => 'Basic ' . base64_encode( "{$this->login}:{$this->api_token}" ),
		);
	}

	/**
	 * @return string
	 */
	protected function get_invoices_api_url() {
		return $this->get_api_url( 'invoices/create' );
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
		$decode = $this->decode_xml( $res_body );
		if ( empty( $decode ) || empty( $decode[ 'result' ] ) ) {
			return __( 'Nieznany błąd podczas wysyłki! Sprawdź klucze API.', 'bpmj_wptaxe' );
		}
		$result = $decode[ 'result' ];
		if ( ! is_array( $result ) ) {
			$result = array( 'error' => $result );
		} else {
			$object = reset( $result );
			if ( is_array( $object ) ) {
				$result = $object;
			}
		}
		if ( ! empty( $result[ 'error' ] ) ) {
			return $result[ 'status' ] . ' ' . $result[ 'error' ];
		} else {

			$this->taxe_invoice_id = $result[ 'id' ];
            $this->set_remote_invoice_id($this->taxe_invoice_id);

			// Bezpośredni link do edycji faktury
			$link = static::$api_endpoint_host . '/fakturowanie/pdf/' . $this->taxe_invoice_id;

			$note = '<span class="' . static::$success_note_css_class . '">' . __( 'Dokument utworzony!', 'bpmj_wptaxe' ) . '<a target="_blank" href="' . $link . '">' . __( 'Zobacz.', 'bpmj_wptaxe' ) . '</a>' . '</span>';

			// Nadpisz tytuł
			$args = array(
				'ID'         => $post_id,
				'post_title' => $this->remote_invoice_number = $result[ 'invoiceNumber' ],
			);
			wp_update_post( $args );

			// Zapisz informacje
			update_post_meta( $post_id, static::$meta_key_invoice_status, 'ok' );
			update_post_meta( $post_id, static::$meta_key_invoice_note, $note );

			// Wyślij automatycznie e-maila z faktura do klienta
			$this->send_auto_email( $src );
		}

		return '';
	}

	/**
	 * Wysyła automatycznie e-mail z fakturą do klienta
	 *
	 * @param array $src
	 */
	private function send_auto_email( $src ) {

		if ($this->should_invoice_be_auto_sent_by_email($src['kind'])) {
			$this->send_by_email($this->taxe_invoice_id);
			$this->emit_after_invoice_auto_sent_event($src);
		}
	}

	/**
	 * @param array $data
	 * @param array $src
	 *
	 * @return array
	 */
	protected function prepare_invoice_data( array $data, array $src = array() ) {
		if ( empty( $this->issue_date ) ) {
		    $this->issue_date = current_time( 'Y-m-d' );
		}
		$invoice_date = $this->issue_date;
		$invoice_type = 'FAK';
		if ( $this->is_receipt() ) {
			$invoice_type = 'PAR';
		} else if ( ! $this->is_vat_payer() ) {
			$invoice_type = 'RAC';
		}
		$data = array_merge( array(
			'invoiceType'    => $invoice_type,
			'dateOfIssue'    => $invoice_date,
			'dateOfSale'     => $invoice_date,
			'dateOfSaleType' => 'D',
			'currency'       => $this->currency,
			'buyer'          => array(
				'name'            => $this->contractor[ 'company_name' ] ? $this->contractor[ 'company_name' ] : $this->contractor[ 'person_name' ],
				'address'         => $this->contractor[ 'street' ],
				'postalCode'      => $this->contractor[ 'post_code' ],
				'city'            => $this->contractor[ 'city' ],
				'isNaturalPerson' => empty( $this->contractor[ 'company_name' ] ),
				'email'           => $this->contractor[ 'email' ],
			),
			'paid'           => $this->paid_amount,
			'paymentMethod'  => 'przelew',
			'items'          => array( 'item' => array() ),
		), $data );

		$has_vat_exemption = false;

		if ( ! empty( $src[ 'id' ] ) ) {
			$data[ 'orderNumber' ]     = $src[ 'id' ];
			$data[ 'additionalNotes' ] = str_replace( array(
					"http://",
					"https://"
				), "", site_url() ) . " #" . $src[ 'id' ];
		}

		if ( $this->contractor[ 'nip' ] ) {
			$data[ 'buyer' ][ 'taxID' ]     = $this->contractor[ 'nip' ];
			$data[ 'buyer' ][ 'taxIDType' ] = 'NIP';
		}

		$gtu_codes = '';

		foreach ( $this->items as $key => $item ) {
			$invoice_item = array(
				'@attributes' => array(
					'index' => $key,
				),
				'name'        => $item->get_name(),
				'unitPrice'   => $item->get_net_unit_price_after_discount(),
				'unit'        => 'szt.',
				'quantity'    => $item->get_quantity(),
			    'VATRate'     => $this->is_vat_payer() ? ('zw' != $item->get_tax_rate() ? ($item->get_tax_rate() . '%') : 'zw.') : 'zw.',
			);

            $flat_rate_tax_symbol = $item->get_flat_rate_tax_symbol();
            if( apply_filters( 'wpi_invoice_flat_rate_enabled', true ) && ! empty( $flat_rate_tax_symbol ) ) {
                $invoice_item['LumpRate'] = $flat_rate_tax_symbol . '%';
            }

            $gtu = $item->get_gtu();
            if($gtu && Gtu::NO_GTU !== $gtu) {
                if(!empty($gtu_codes)) {
                    $gtu_codes .= ',';
                }
                $gtu_codes .= $this->prepare_gtu_code($gtu);
            }

            if( 'zw.' == $invoice_item['VATRate'] ) {
                $has_vat_exemption = true;
            }

			$data[ 'items' ][ 'item' ][] = $invoice_item;
		}

		if( $has_vat_exemption ) {
		    $data[ 'podstawaZwolnienia' ] = '' != $this->get_vat_exemption() ? $this->get_vat_exemption() : Translator_Static_Helper::translate('settings.sections.integrations.taxe.taxe_vat_exemption.default');
		}

        if(!empty($gtu_codes)) {
            $data[ 'gtuList' ] = $gtu_codes;
        }

        if ( ! empty( $src[ 'src' ] ) && 'edd' === $src[ 'src' ] ) {
            $data = apply_filters( 'bpmj_wptaxe_edd_invoice_data', $data, $src[ 'id' ] );
        }

		return array( 'invoices' => array( 'invoice' => $data ) );
	}

    protected function prepare_gtu_code(string $gtu): string
    {
        return strtoupper($gtu);
    }

	/**
	 * @return string
	 */
	protected function get_test_api_url() {
		return $this->get_api_url( 'invoices/list' );
	}

	/**
	 * @return array|string
	 */
	protected function get_test_data() {
		return array(
			'params' => array(
				'display' => array(
					'beginwith' => 0,
					'limit'     => 1,
				),
			),
		);
	}

	/**
	 * @param mixed $data
	 *
	 * @return string
	 */
	protected function prepare_request_body( $data ) {
		return http_build_query( array( 'xml' => $this->maybe_encode( $data ) ) );
	}

	/**
	 * @param mixed $invoice_data
	 *
	 * @return string
	 */
	protected function prepare_data_for_saving( $invoice_data ) {
		return htmlentities( $this->maybe_encode( $invoice_data ), ENT_QUOTES | ENT_XML1 );
	}

	/**
	 * @param string $post_content
	 *
	 * @return array
	 */
	protected function restore_invoice_data_from_post( $post_content ) {
		return $this->decode_data( html_entity_decode( $post_content, ENT_QUOTES | ENT_XML1 ) );
	}


    public function check_connection(): bool
    {
        $response = $this->test_api_call( ) ;
        return isset($response['data']['list']);
    }

    public function get_service_name()
    {
        return $this::$invoice_service_name;
    }

    public function send_by_email(int $invoice_id): void
    {
        if (empty($this->invoice_data['invoices']['invoice']['buyer']['email'])) {
            return;
        }

        $xml = $this->maybe_encode([
           'contractor' => [
               'email' => $this->invoice_data['invoices']['invoice']['buyer']['email'],
           ],
       ]);
        $url = $this->get_api_url('invoices/send/' . $invoice_id);
        $this->make_api_call( $url, $xml );
    }

    private function should_invoice_be_auto_sent_by_email(string $kind): bool
    {
        global $bpmj_wptaxe_settings;

        return !empty($this->invoice_data['invoices']['invoice']['buyer']['email'])
            && (!('receipt' === $kind) && !empty($bpmj_wptaxe_settings['auto_sent'])
                || ('receipt' === $kind) && !empty($bpmj_wptaxe_settings['auto_sent_receipt']));
    }


    private function emit_after_invoice_auto_sent_event(array $src): void
    {
        do_action(
            'bpmj_' . static::$service_short_id . '_after_invoice_sent_to_customer',
            $src,
            $this->taxe_invoice_id,
            $this->get_remote_invoice_number()
        );
    }
}
