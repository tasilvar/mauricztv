<?php
// Zakoncz, jeżeli plik jest załadowany bezpośrednio
use bpmj\wpidea\integrations\Interface_External_Service_Integration;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use bpmj\wpidea\sales\product\model\Gtu;
use bpmj\wpidea\integrations\invoices\email\Interface_Sendable_By_Email;

/**
 * WP iFirma - API
 */
class BPMJ_WP_iFirma extends BPMJ_Base_Invoice
    implements Interface_External_Service_Integration, Interface_Sendable_By_Email
{

	/**
	 * @var string
	 */
	protected static $service_short_id = 'wpifirma';

	/**
	 * @var string
	 */
	protected static $service_long_id = 'ifirma';

	/**
	 * @var string
	 */
	protected static $meta_key_invoice_src = 'ifirma_src';

	/**
	 * @var string
	 */
	protected static $meta_key_invoice_note = 'ifirma_note';

	/**
	 * @var string
	 */
	protected static $meta_key_invoice_status = 'ifirma_status';

	/**
	 * @var string
	 */
	protected static $error_note_css_class = 'bpmj_wpifirma_error_note';

	/**
	 * @var string
	 */
	protected static $success_note_css_class = 'bpmj_wpifirma_success_note';

	/**
	 * @var string
	 */
	protected static $invoice_service_name = 'WP iFirma';

	/**
	 * @var string
	 */
	protected static $invoice_post_type = 'bpmj_wp_ifirma';

	/**
	 * @var string
	 */
	protected static $api_endpoint_url_pattern = 'https://www.ifirma.pl/iapi/%1$s.json';

	/**
	 * @var string
	 */
	protected static $api_endpoint_host = 'https://www.ifirma.pl/';

	/**
	 * Login do serwisu ifirma.pl
	 * @var string
	 */
	private $login = '';

	/**
	 * Token dla faktur pobrany z ifirma.pl
	 * @var string
	 */
	private $invoice_token = "";

	/**
	 * Token abonent pobrany z ifirma.pl
	 * @var string
	 */
	private $subscriber_token = "";

	/**
	 * ID faktury utworzonej po stronie iFirma.pl
	 * @var int
	 */
	private $ifirma_invoice_id = '';

	/**
	 * Hash używany do autoryzacji następnego zapytania
	 * @var string
	 */
	private $hash;

	/**
	 * @var string
	 */
	protected $numeration = '';

    /**
     * BPMJ_WP_iFirma constructor.
     * @param null $email
     * @param null $invoice_key
     * @param null $subscriber_key
     */
	public function __construct($email = null, $invoice_key = null, $subscriber_key = null) {
		global $bpmj_wpifirma_settings;

		$this->login            = $email ?? $bpmj_wpifirma_settings[ 'ifirma_email' ];
		$this->invoice_token    = $invoice_key ?? $bpmj_wpifirma_settings[ 'ifirma_invoice_key' ];
		$this->subscriber_token = $subscriber_key ?? $bpmj_wpifirma_settings[ 'ifirma_subscriber_key' ];
		if ( ! empty( $bpmj_wpifirma_settings[ 'numeration' ] ) ) {
			$this->set_invoice_numeration( $bpmj_wpifirma_settings[ 'numeration' ] );
		}
	}

	/**
	 * @param string $hex
	 *
	 * @return string
	 */
	function hex2str( $hex ) {
		$string = '';
		for ( $i = 0; $i < strlen( $hex ) - 1; $i += 2 ) {
			$string .= chr( hexdec( $hex[ $i ] . $hex[ $i + 1 ] ) );
		}

		return $string;
	}

	/**
	 * @param string $url
	 * @param string $data
	 * @param string $method
	 *
	 * @return array|null|WP_Error
	 */
	protected function make_api_call( $url, $data, $method = 'POST' ) {
		if ( ! $this->hash ) {
			_doing_it_wrong( __METHOD__, __( 'Before making an API call set the authentication hash using self::prepare_hash', 'bpmj_wpifirma' ), '1.3.4' );

			return null;
		}


		$response   = parent::make_api_call( $url, $data, $method );
		$this->hash = '';

		return $response;
	}

	protected function get_api_call_headers() {
		return array(
			'content-type'   => 'application/json',
			'Authentication' => 'IAPIS user=' . $this->login . ', hmac-sha1=' . $this->hash,
		);
	}

	/**
	 * Ustawia prawidłowy miesiąc księgowy
	 */
	private function set_ifi_date() {
		$json = '';

		$url = $this->get_api_url( 'abonent/miesiacksiegowy' );
		$this->prepare_hash( $url, 'abonent', $json );

		$response = $this->make_api_call( $url, $json, 'GET' );

		if ( is_wp_error( $response ) || ! $response ) {
			return false;
		}

		$res_body = wp_remote_retrieve_body( $response );
		$decode   = json_decode( $res_body );

        if ( empty( $decode ) || empty( $decode->response->MiesiacKsiegowy ) || empty( $decode->response->RokKsiegowy ) ) {
            $msg = !empty($decode) ? $decode->response->Kod . ' - ' . $decode->response->Informacja : 'n/a';
            do_action( 'wpi_after_invoice_created_error', static::$invoice_post_type, [],  $msg);

            return false;
        }

		$response_code = isset( $decode->response->Kod ) ? (int) $decode->response->Kod : 0;
		if ( 202 === $response_code || 400 <= $response_code && $response_code <= 599 ) {
            do_action( 'wpi_after_invoice_created_error', static::$invoice_post_type, [], $decode->response->Kod . ' - ' . $decode->response->Informacja );

			$this->send_admin_error_notice( __( 'Podczas zmiany miesiąca księgowego wystąpił błąd', 'bpmj_wpifirma' )
			                                . '. '
			                                . sprintf( __( 'Kod: %s: %s', 'bpmj_wpifirma' ), $response_code, $decode->response->Informacja ), $response_code );

			return false;
		}

		$ifirma_miesiac_ksiegowy = $decode->response->MiesiacKsiegowy;
		$ifirma_rok_ksiegowy     = $decode->response->RokKsiegowy;
        $invoice_date = $this->invoice_data['DataWystawienia'];
		$year  = date( 'Y', strtotime( $invoice_date ));
		$month = date( 'm', strtotime( $invoice_date ));
		$diff  = ( intval( $year * 1 ) - $ifirma_rok_ksiegowy * 1 ) * 12
		         + ( $month * 1 - $ifirma_miesiac_ksiegowy * 1 );
		if ( $diff > 0 ) {
			$json = '{"MiesiacKsiegowy":"NAST","PrzeniesDaneZPoprzedniegoRoku":true}';
		} else if ( $diff < 0 ) {
			$json = '{"MiesiacKsiegowy":"POPRZ","PrzeniesDaneZPoprzedniegoRoku":true}';
		} else {
			return true;
		}

		$diff = abs( $diff );
		while ( $diff > 0 ) {
			$this->prepare_hash( $url, 'abonent', $json );
			$this->make_api_call( $url, $json, 'PUT' );
			-- $diff;
		}

		return true;
	}

	/**
	 * @param string $numeration
	 */
	public function set_invoice_numeration( $numeration ) {
		$this->numeration = $numeration;
	}

	/**
	 * @return string
	 */
	protected function get_invoices_api_url() {
		return $this->get_api_url( 'fakturakraj' );
	}

	/**
	 *
	 */
	public function send_invoice() {
		// Ustawienie miesiąca księgowego
		if ( ! $this->set_ifi_date() ) {
			return;
		}
		$this->prepare_hash( $this->get_invoices_api_url(), 'faktura', $this->invoice_data );
		parent::send_invoice();
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
		$decode = json_decode( $res_body );
		if ( json_last_error() != JSON_ERROR_NONE || ! isset( $decode ) || empty( $decode ) ) {
			return __( 'Nieznany błąd podczas wysyłki! Sprawdź klucze API.', 'bpmj_wpifirma' );
		} else if ( $decode->response->Kod != 0 ) {
			$response_code = (int) $decode->response->Kod;
			$message       = sprintf( __( 'Kod: %s: %s', 'bpmj_wpifirma' ), $response_code, $decode->response->Informacja );
			if ( 202 === $response_code || 400 <= $response_code && $response_code <= 599 ) {
                do_action( 'wpi_after_invoice_created_error', static::$invoice_post_type, [], $decode->response->Kod . ' - ' . $decode->response->Informacja );
				$this->send_admin_error_notice( __( 'Podczas wystawiania faktury wystąpił błąd', 'bpmj_wpifirma' ) . '. ' . $message, $response_code );
			}

			return $message;
		} else {
			$this->ifirma_invoice_id = $decode->response->Identyfikator;
            $this->set_remote_invoice_id($this->ifirma_invoice_id);

			// Bezpośredni link do edycji faktury
			$link = static::$api_endpoint_host . 'iapi/fakturakraj/' . $this->ifirma_invoice_id . '.pdf';

			$note = '<span class="' . static::$success_note_css_class . '">' . __( 'Dokument utworzony!', 'bpmj_wpifirma' ) . '<!-- <a target="_blank" href="' . $link . '">' . __( 'Zobacz.', 'bpmj_wpifirma' ) . '</a>-->' . '</span>';

			// Nadpisz tytuł
			$args = array(
				'ID'         => $post_id,
				'post_title' => $this->remote_invoice_number = $decode->response->Identyfikator,
			);
			wp_update_post( $args );

			// Zapisz informacje
			update_post_meta( $post_id, static::$meta_key_invoice_status, 'ok' );
			update_post_meta( $post_id, static::$meta_key_invoice_note, $note );

			// WP Better Logger logs
			$args = array(
				'message'         => "Faktura nr {$this->remote_invoice_number} została wystawiona!",
				'type'            => 'success',
				'origin'          => 'bpmj_wp_ifirma',
				'additional_data' => array(
					'Identyfikator dokumentu' => $this->ifirma_invoice_id,
				)
			);
			do_action( 'wpbl_log_event', $args );

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
		if ($this->should_invoice_be_auto_sent_by_email()) {
			$this->send_by_email($this->ifirma_invoice_id);
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

		$data = array_merge( array(
			'Zaplacono'             => $this->paid_amount ? number_format( $this->paid_amount, 2, '.', '' ) : '0.00',
			'LiczOd'                => 'BRT',
			'DataWystawienia'       => $this->issue_date,
			'DataSprzedazy'         => $this->issue_date,
			'FormatDatySprzedazy'   => 'DZN',
			'SposobZaplaty'         => $this->get_ifirma_payment_string_by_payment_gateway( get_post_meta( $src['id'], '_edd_payment_gateway', true ) ),
			'RodzajPodpisuOdbiorcy' => 'BPO',
			'WidocznyNumerGios'     => false,
			'Numer'                 => null,
			'Pozycje'               => array(),
			'Kontrahent'            => array(
				'OsobaFizyczna' => ! $this->contractor[ 'company_name' ] || $this->is_receipt() ? true : false,
				'Nazwa'         => $this->contractor[ 'company_name' ] ? $this->contractor[ 'company_name' ] : $this->contractor[ 'person_name' ],
				'Identyfikator' => null,
				'PrefiksUE'     => null,
				'NIP'           => $this->contractor[ 'nip' ],
				'Ulica'         => $this->contractor[ 'street' ],
				'KodPocztowy'   => $this->contractor[ 'post_code' ],
				'Miejscowosc'   => $this->contractor[ 'city' ],
				'Email'         => $this->contractor[ 'email' ],
			),
		), $data );

		if ( $this->numeration ) {
			$data[ 'NazwaSeriiNumeracji' ] = $this->numeration;
		}

		if ( ! empty( $src[ 'id' ] ) ) {
			$order_number = $src[ 'id' ];
			if ( 'edd' === $src[ 'src' ] ) {
				$order_number = edd_get_payment_number( $src[ 'id' ] );
			}
			$data[ 'Uwagi' ] = str_replace( array(
					"http://",
					"https://"
				), "", site_url() ) . " #" . $order_number;
		}

		foreach ( $this->items as $key => $item ) {
		    $tax_rate_type = $this->is_vat_payer() ? ('zw' != $item->get_tax_rate() ? 'PRC' : 'ZW') : 'ZW';
			$invoice_item  = array(
				'NazwaPelna'      => $item->get_name(),
			    'StawkaVat'       => 'ZW' != $tax_rate_type ? $item->bcdiv( $item->get_tax_rate(), 100, 2 ) : null,
				'TypStawkiVat'    => $tax_rate_type,
				'CenaJednostkowa' => $item->get_gross_unit_price_after_discount(),
				'Jednostka'       => 'szt.',
				'Ilosc'           => $item->get_quantity(),
			);

            $flat_rate_tax_symbol = $item->get_flat_rate_tax_symbol();
            if( apply_filters( 'wpi_invoice_flat_rate_enabled', true ) && ! empty($flat_rate_tax_symbol) ) {
                $invoice_item['StawkaRyczaltu'] = number_format( (float) ($flat_rate_tax_symbol / 100), 3 );
            }

            if ( 'ZW' == $tax_rate_type ) {
                // W przypadku pozostawienia tego pola pustego, API zwraca błąd: Kod: 201: W polu CN / PKWiU należy podać artykuł ustawy o podatku VAT, na podstawie którego następuje zwolnienie
                // Także w przypadku gdy wystawiający nie jest płatnikiem vat, mimo że dokumentacja mówi inaczej
                $invoice_item[ 'PKWiU' ] = $this->get_vat_exemption();
			}

            $gtu = $item->get_gtu();
            if($gtu && Gtu::NO_GTU !== $gtu) {
                $invoice_item[ 'GTU' ] = $this->prepare_gtu_code($gtu);
            }

			$data[ 'Pozycje' ][] = $invoice_item;
		}

        if ( ! empty( $src[ 'src' ] ) && 'edd' === $src[ 'src' ] ) {
            $data = apply_filters( 'bpmj_wpifirma_edd_invoice_data', $data, $src[ 'id' ] );
        }

		return $data;
	}

    protected function prepare_gtu_code(string $gtu): string
    {
        $gtu_exploded = explode('_', $gtu);
        return $gtu_exploded[1] ?? 'BRAK';
    }

	private function get_ifirma_payment_string_by_payment_gateway( $payment_gateway )
    {
        switch ($payment_gateway) {
            case 'dotpay_gateway':
                return 'DOT';
            case 'payu':
                return 'ALG';
            case 'przelewy24_gateway':
                return 'P24';
            case 'tpay_gateway':
                return 'TPA';
            default:
                return 'PRZ';
        }
    }

	/**
	 * @param string $url
	 * @param string $type
	 * @param string|array $json
	 *
	 * @return false|string
	 */
	private function prepare_hash( $url, $type, $json ) {
		$token = '';
		switch ( $type ) {
			case 'abonent':
				$token = $this->subscriber_token;
				break;
			case 'faktura':
				$token = $this->invoice_token;
				break;
		}
		$this->hash = hash_hmac( 'sha1', implode( '', array(
			$url,
			$this->login,
			$type,
			$this->maybe_encode( $json ),
		) ), $this->hex2str( $token ) );

		return $this->hash;
	}

	/**
	 * @param string $url
	 * @param string $data
	 * @param string $method
	 *
	 * @return array|WP_Error
	 */
	public function test_api_call( $url = null, $data = null, $method = null ) {
		$this->prepare_hash( $url, 'faktura', $data );

		return parent::test_api_call( $url, $data, $method );
	}

	/**
	 * @param string $message
	 * @param $response_code
	 */
	public function send_admin_error_notice( $message, $response_code ) {
		$message = implode( '. ', array_filter( array(
			$message,
			in_array( $response_code, array(
				// Poniższe kody sugerują, że klucze API mogą być niepoprawne
				// http://api.ifirma.pl/faq/
				400,
				401,
				402,
				403
			) ) ? __( 'Sprawdź poprawność kluczy do API w ustawieniach', 'bpmj_wpifirma' ) : '',
			sprintf( __( 'Ostatnio ten problem wystąpił: %s', 'bpmj_wpifirma' ), current_time( 'mysql' ) ),
		) ) );
		update_option( 'bpmj_wpifirma_processing_disabled_until', date( 'Y-m-d H:i:s', strtotime( '+2 hours', current_time( 'timestamp' ) ) ), false );

		// Usuwamy ewentualne dwu- i wielokropki
		$message = preg_replace( '/\.+/', '.', $message );

        $to_email = apply_filters( 'wpi_admin_notices_email', get_option( 'admin_email' ) );

		$body  = 'Dzień dobry,

Niestety wystąpił problem przy wystawianiu faktury przez oprogramowanie ' . static::$invoice_service_name . '.
Komunikat: ' . $message . '
Wysyłanie kolejnych faktur zostało wstrzymane na 2 godziny lub do czasu aktualizacji konfiguracji.


--;
Wiadomość wygenerowana automatycznie.';

		wp_mail( $to_email, static::$invoice_service_name . ' - błąd konfiguracji, generowanie faktur zostało wstrzymane!', $body );
	}



    public function check_connection(): bool
    {
        global $bpmj_wpifirma_settings;

        $connection_invoice = $this->check_connection_invoice();

        $connection_subscriber = true;
        if($bpmj_wpifirma_settings[ 'ifirma_subscriber_key' ]){
            $connection_subscriber =  $this->check_connection_subscriber();
        }

        return ($connection_invoice && $connection_subscriber);
    }

    private function check_connection_subscriber()
    {
        $url = $this->get_api_url( 'abonent/limit' ) ;
        $this->prepare_hash( $url, 'abonent', '' );
        $response = $this->make_api_call( $url, '', 'GET' );

        if ( is_wp_error( $response ) || ! $response ) {
            return false;
        }
        if(wp_remote_retrieve_response_code( $response ) != 200){
            return false;
        }

        $body = json_decode( wp_remote_retrieve_body( $response ) );
        if(isset($body->response->Kod) && $body->response->Kod === 0){
            return true;
        }

        return false;
    }

    private function check_connection_invoice()
    {
        $url = $this->get_api_url( 'kontrahenci/test' ) ;
        $this->prepare_hash( $url, 'faktura', '' );
        $response = $this->make_api_call( $url, '', "GET" );

        if ( is_wp_error( $response ) || ! $response ) {
            return false;
        }
        if(wp_remote_retrieve_response_code( $response ) != 200){
            return false;
        }

        $body = json_decode( wp_remote_retrieve_body( $response ) );
        if(isset($body->response->Kod) && ($body->response->Kod === 208 || $body->response->Kod === 0)){
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
        $json = $this->maybe_encode([
            'Tekst' => '',
            'Przelew' => false,
            'Pobranie' => false,
            'MTransfer' => null,
        ]);
        $url = $this->get_api_url('fakturakraj/send/' . $invoice_id);
        $this->prepare_hash($url, 'faktura', $json);
        $this->make_api_call($url, $json );
    }

    private function should_invoice_be_auto_sent_by_email(): bool
    {
        global $bpmj_wpifirma_settings;

        return isset($bpmj_wpifirma_settings['auto_sent']) && !empty($bpmj_wpifirma_settings['auto_sent']);
    }

    private function emit_after_invoice_auto_sent_event(array $src): void
    {
        do_action(
            'bpmj_' . static::$service_short_id . '_after_invoice_sent_to_customer',
            $src,
            $this->ifirma_invoice_id,
            $this->get_remote_invoice_number()
        );
    }
}
