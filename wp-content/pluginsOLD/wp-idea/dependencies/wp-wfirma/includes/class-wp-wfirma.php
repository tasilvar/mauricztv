<?php
// Zakoncz, jeżeli plik jest załadowany bezpośrednio
use bpmj\wpidea\admin\settings\core\configuration\Integrations_Settings_Group;
use bpmj\wpidea\integrations\Interface_External_Service_Integration;
use bpmj\wpidea\integrations\invoices\email\Interface_Sendable_By_Email;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP wFirma - API
 */
class BPMJ_WP_wFirma extends BPMJ_Base_Invoice
    implements Interface_External_Service_Integration, Interface_Sendable_By_Email
{

	/**
	 * @var string
	 */
	protected static $service_short_id = 'wpwf';

	/**
	 * @var string
	 */
	protected static $service_long_id = 'wfirma';

	/**
	 * @var string
	 */
	protected static $meta_key_invoice_src = 'wfirma_src';

	/**
	 * @var string
	 */
	protected static $meta_key_invoice_note = 'wfirma_note';

	/**
	 * @var string
	 */
	protected static $meta_key_invoice_status = 'wfirma_status';

	/**
	 * @var string
	 */
	protected static $error_note_css_class = 'bpmj_wpwf_error_note';

	/**
	 * @var string
	 */
	protected static $success_note_css_class = 'bpmj_wpwf_success_note';

	/**
	 * @var string
	 */
	protected static $invoice_service_name = 'WP wFirma';

	/**
	 * @var string
	 */
	protected static $invoice_post_type = 'bpmj_wp_wfirma';

	/**
	 * @var string
	 */
	protected static $api_endpoint_url_pattern = 'https://api2.wfirma.pl/%1$s?inputFormat=json';

	/**
	 * @var string
	 */
	protected static $api_endpoint_host = 'https://api2.wfirma.pl/';

	private $auth_type = null;

	/**
	 * Login z wfirma.pl
	 * @var string
	 */
	private $login = "";

	/**
	 * Hasło z wfirma.pl
	 * @var string
	 */
	private $password = "";

	private $oauth2_client_id = null;
	private $oauth2_client_secret = null;
	private $oauth2_authorization_code = null;
	private $oauth2_access_data = null;

	/**
	 * @var string
	 */
	private $wf_company_id = '';

	/**
	 * ID faktury utworzonej po stronie wFirma.pl
	 * @var int
	 */
	private $wfirma_invoice_id = '';

	/**
	 * @var bool
	 */
	private $out_of_service = false;

	/**
	 * Konstruktor obiektu
	 *
	 * @param string $wf_login
	 * @param string $wf_password
	 * @param string $wf_company_id
	 */
	public function __construct( $wf_login = '', $wf_password = '', $wf_company_id = null ) {
		global $bpmj_wpwf_settings;

		$this->auth_type = isset( $bpmj_wpwf_settings[ 'auth_type' ] ) ? $bpmj_wpwf_settings[ 'auth_type' ] : 'oauth2';
		$this->login         = $wf_login ? $wf_login : ( isset( $bpmj_wpwf_settings[ 'wf_login' ] ) ? $bpmj_wpwf_settings[ 'wf_login' ] : '' );
		$this->password      = $wf_password ? $wf_password : ( isset( $bpmj_wpwf_settings[ 'wf_pass' ] ) ? $bpmj_wpwf_settings[ 'wf_pass' ] : '' );
		$this->wf_company_id = $wf_company_id ? $wf_company_id : ( isset( $bpmj_wpwf_settings[ 'wf_company_id' ] ) ? $bpmj_wpwf_settings[ 'wf_company_id' ] : '' );
		$this->oauth2_client_id = isset( $bpmj_wpwf_settings[ 'wf_oauth2_client_id' ] ) ? trim($bpmj_wpwf_settings[ 'wf_oauth2_client_id' ]) : null;
		$this->oauth2_client_secret = isset( $bpmj_wpwf_settings[ 'wf_oauth2_client_secret' ] ) ? trim($bpmj_wpwf_settings[ 'wf_oauth2_client_secret' ]) : null;
		$this->oauth2_authorization_code = isset( $bpmj_wpwf_settings[ 'wf_oauth2_authorization_code' ] ) ? $bpmj_wpwf_settings[ 'wf_oauth2_authorization_code' ] : '';
		$this->oauth2_access_data = isset( $bpmj_wpwf_settings[ 'wf_oauth2_access_data' ] ) ? unserialize($bpmj_wpwf_settings[ 'wf_oauth2_access_data' ]) : null;
		if( 'oauth2' == $this->auth_type ) {
		    self::$api_endpoint_url_pattern .= '&oauth_version=2';
		}
	}

	private function get_access_token(): ?string {
	    if( !$this->oauth2_access_data ) {
	        $this->oauth2_access_data = $this->get_access_token_from_code($this->oauth2_authorization_code, 'authorization_code');
	    }
	    
	    if(time() > $this->oauth2_access_data['expires_at']) {
	        $this->oauth2_access_data = $this->get_access_token_from_code($this->oauth2_access_data['refresh_token'], 'refresh_token');
	    }
	    
	    return $this->oauth2_access_data['access_token'];
	}

	private function get_access_token_from_code($code, $grant_type) {
	    global $bpmj_wpwf_settings;

	    $data = array(
	        'grant_type' => $grant_type,
	        ('authorization_code' == $grant_type ? 'code' : 'refresh_token') => $code,
	        'redirect_uri' => get_site_url() . Integrations_Settings_Group::WFIRMA_OAUTH_RETURN_PATH,
	        'client_id' => $this->oauth2_client_id,
	        'client_secret' => $this->oauth2_client_secret
	    );
	    $url = $this->get_api_url( 'oauth2/token' );

	    $response = $this->make_api_call( $url, http_build_query($data), 'POST', true );
	    
	    if ( is_wp_error( $response ) ) {
	        do_action( 'wpi_debug_log', 'wFirma błąd pobierania access_token, wysłane dane: ' . print_r($data, true) );
	        throw new Exception($response->get_error_message());
	    }

	    $res_body = wp_remote_retrieve_body( $response );
	    $decode = json_decode( $res_body, true );
	    
	    if ( json_last_error() != JSON_ERROR_NONE || ! isset( $decode ) || empty( $decode ) ) {
	        do_action( 'wpi_debug_log', 'wFirma błąd pobietania access_token, wysłane dane: ' . print_r($data, true) );
	        throw new Exception('Invalid API response format');
	    } else if( isset($decode['error']) ) {
	        do_action( 'wpi_debug_log', 'wFirma błąd pobietania access_token, wysłane dane: ' . print_r($data, true) );
	        throw new Exception('API call failed: [' . $decode['error'] . '] ' . $decode['error_description']);
	    }
	    else {
	        $decode['expires_at'] = time() + (int) $decode['expires_in']; 
	        $bpmj_wpwf_settings[ 'wf_oauth2_access_data' ] = serialize($decode);
	        update_option( 'bpmj_wpwf_settings', $bpmj_wpwf_settings );

	        return $decode;
	    }
	}

	/**
	 * @return array
	 */
	protected function get_api_call_headers() {
	    if( $this->auth_type == 'oauth2' ) {
	        return array(
	            'Authorization' => 'Bearer ' . $this->get_access_token()
	        );
		}
		
		return array(
			'Authorization' => 'Basic ' . base64_encode( $this->login . ':' . $this->password )
		);
	}

	/**
	 * @param string $url
	 * @param string|array $data
	 * @param string $method
	 *
	 * @return array|WP_Error
	 */
	protected function make_api_call( $url, $data, $method = 'POST', $no_headers = false ) {
		if ( ! empty( $this->wf_company_id ) ) {
			$url .= '&company_id=' . $this->wf_company_id;
		}

		try {
		    $headers = $no_headers ? null : $this->get_api_call_headers();
		} catch(\Exception $e) {
		    do_action( 'wpi_debug_log', 'wFirma API: ' . $e->getMessage() );
		    return new WP_Error('wfirma_oauth2_error', $e->getMessage());
		}

		$args = array(
		    'timeout'     => 45,
		    'headers'     => $headers,
		    'redirection' => 5,
		    'body'        => $this->prepare_request_body( $data ),
		    'method'      => $method,
		);
		$response = wp_remote_post( $url, $args );

		if ( ! is_wp_error($response) && isset( $response[ 'body' ] ) && false !== strpos( $response[ 'body' ], '{"status":{"code":"OUT OF SERVICE"}}' ) ) {
			$this->out_of_service = true;
		}
		
		return $response;
	}

	/**
	 * @return string
	 */
	protected function get_invoices_api_url() {
		return $this->get_api_url( 'invoices/add' );
	}

	/**
	 * Przeanalizuj odpowiedź serwera
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
		$data = json_decode( $res_body, true );
		if ( json_last_error() != JSON_ERROR_NONE || ! isset( $data ) || empty( $data ) ) {
			return __( 'Nieznany błąd podczas wysyłki!', 'bpmj_wpwf' );
		} else if ( 'OK' !== $data[ 'status' ][ 'code' ] ) {
			$add = '';
			if ( 'ERROR' === $data[ 'status' ][ 'code' ] ) {
				$add = ' (' . $data[ 'invoices' ][ 0 ][ 'invoice' ][ 'errors' ][ 0 ][ 'error' ][ 'field' ] . ' - ' . $data[ 'invoices' ][ 0 ][ 'invoice' ][ 'errors' ][ 0 ][ 'error' ][ 'message' ] . ')';
			}

			$add .= $res_body;

			return __( 'Błąd:', 'bpmj_wpwf' ) . ' ' . $data[ 'status' ][ 'code' ] . $add;
		} else {
			$invoice                 = $data[ 'invoices' ][ 0 ][ 'invoice' ];
			$this->wfirma_invoice_id = $invoice[ 'id' ];
            $this->set_remote_invoice_id($this->wfirma_invoice_id);

			$link = 'https://wfirma.pl/invoices/index/all';
			$note = '<span class="' . static::$success_note_css_class . '">' . $note_date . __( 'Dokument utworzony!', 'bpmj_wpwf' ) . ' <a target="_blank" href="' . $link . '">' . __( 'Zobacz.', 'bpmj_wpwf' ) . '</a>' . '</span>';

			// Nadpisz tytuł
			$args = array(
				'ID'         => $post_id,
				'post_title' => $this->remote_invoice_number = $invoice[ 'fullnumber' ],
			);
			wp_update_post( $args );

			// Zapisz informacje o powodzeniu
			update_post_meta( $post_id, static::$meta_key_invoice_status, 'ok' );
			update_post_meta( $post_id, static::$meta_key_invoice_note, $logs . $note );

			// Wyślij automatycznie e-maila z dokumentem do klienta
			$this->send_auto_email( $src );
		}

		return '';
	}

	/**
	 * Szuka kontrachenta w systemie wfirma (po email i nip)
	 *
	 * @param string $email
	 * @param string $nip
	 *
	 * @return bool|int
	 */
	public function find_contractor( $email, $nip ) {
		$data     = array(
			'contractors' => array(
				'parameters' => array(
					'conditions' => array(
						array(
							'condition' => array(
								'field'    => 'email',
								'operator' => 'eq',
								'value'    => $email,
							),
						),
						array(
							'condition' => array(
								'field'    => 'nip',
								'operator' => 'eq',
								'value'    => str_replace( '-', '', $nip ),
							),
						),
					),
				),
			),
		);
		$url      = $this->get_api_url( 'contractors/find' );
		$response = $this->make_api_call( $url, $data );

		if ( is_wp_error( $response ) ) {
			return - 1;
		}

		$res_body = wp_remote_retrieve_body( $response );
		$decode   = json_decode( $res_body, true );
		if ( json_last_error() != JSON_ERROR_NONE || ! isset( $decode ) || empty( $decode ) ) {
			return - 1;
		} else if ( 'OK' == $decode[ 'status' ][ 'code' ] ) {
			if ( 0 == $decode[ 'contractors' ][ 'parameters' ][ 'total' ] ) {
				return false;
			}

			return $decode[ 'contractors' ][ 0 ][ 'contractor' ][ 'id' ];
		}

		return - 1;
	}

	/**
	 * Dodaje kontrachenta do systemu wfirma (wymagane, aby automatycznie wysłać dokument)
	 *
	 * @param array $contractor
	 *
	 * @return bool|int
	 */
	public function insert_or_update_contractor( $contractor ) {

		$contractor_id = $this->find_contractor( $contractor[ 'email' ], $contractor[ 'nip' ] );
		if ( - 1 === $contractor_id ) {
			return - 1;
		}

		$data = array(
			'contractors' => array(
				'0' => array(
					'contractor' => $contractor
				),
			),
		);

		if ( $contractor_id ) {
			$url = $this->get_api_url( 'contractors/edit/' . $contractor_id . '/' );
		} else {
			$url = $this->get_api_url( 'contractors/add' );
		}

		$response = $this->make_api_call( $url, $data );

		if ( is_wp_error( $response ) ) {
			return - 1;
		}

		$res_body = wp_remote_retrieve_body( $response );
		$decode   = json_decode( $res_body, true );
		if ( json_last_error() != JSON_ERROR_NONE || ! isset( $decode ) || empty( $decode ) ) {
			return - 1;
		} else if ( 'OK' === $decode[ 'status' ][ 'code' ] ) {
			if ( 0 == $decode[ 'contractors' ][ 'parameters' ][ 'total' ] ) {
				return false;
			}

			return $decode[ 'contractors' ][ 0 ][ 'contractor' ][ 'id' ];
		}

		return - 1;
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
		$contractor = array(
			'name'   => $this->contractor[ 'company_name' ] ? $this->contractor[ 'company_name' ] : $this->contractor[ 'person_name' ],
			'email'  => $this->contractor[ 'email' ],
			'street' => '(adres nieznany)',
			'zip'    => '00-000',
			'city'   => '(miasto nieznane)',
		);
		if ( ! $this->is_receipt() ) {
			$contractor    = array(
				'name'        => $contractor[ 'name' ],
				'street'      => $this->contractor[ 'street' ],
				'nip'         => $this->contractor[ 'nip' ],
				'zip'         => $this->contractor[ 'post_code' ],
				'city'        => $this->contractor[ 'city' ],
				'email'       => $this->contractor[ 'email' ],
				'tax_id_type' => $this->contractor[ 'company_name' ] ? 'nip' : 'none',
			);
			if ( ! empty( $src[ 'src' ] ) && 'edd' === $src[ 'src' ] ) {
				$contractor = apply_filters( 'bpmj_wpwf_contractor', $contractor, $src[ 'id' ] );
			}
			$contractor_id = $this->insert_or_update_contractor( $contractor );
			if ( - 1 === $contractor_id ) {
				$contractor = array_merge( array( 'contractor_id' => 'FAILED' ), $contractor );
			} else {
				$contractor = array(
					'id' => $contractor_id,
				);
			}
		}

		$document_type = $this->is_vat_payer() ? 'normal' : 'bill';
		if ( $this->is_receipt() ) {
			$document_type = 'receipt_' . $document_type;
		}
		$data = array(
			'contractor'          => $contractor,
			'type'                => $document_type,
			'paymentmethod'       => 'transfer',
			'date'                => $this->issue_date,
			'disposaldate'        => $this->issue_date,
			'paymentdate'         => $this->issue_date,
			'alreadypaid_initial' => number_format( $this->paid_amount, 2, '.', '' ),
			'currency'            => 'PLN',
			'price_type'          => 'brutto',
			'invoicecontents'     => array(),
		);
		if ( ! empty( $src[ 'id' ] ) ) {
			$order_number = $src[ 'id' ];
			if ( 'edd' === $src[ 'src' ] ) {
				$order_number = edd_get_payment_number( $src[ 'id' ] );
			}
			$data[ 'description' ] = str_replace( array(
					"http://",
					"https://"
				), "", site_url() ) . " #" . $order_number;
		}

		/** @var BPMJ_Base_Invoice_Item $item */
		foreach ( $this->items as $key => $item ) {
			$invoice_item = array(
				'invoicecontent' => array(
					'name'  => $item->get_name(),
					'vat'   => $this->is_vat_payer() ? $item->get_tax_rate() : 'zw',
					'price' => $item->get_gross_unit_price_after_discount(),
					'unit'  => 'szt.',
					'count' => $item->get_quantity(),
				),
			);

            $flat_rate_tax_symbol = $item->get_flat_rate_tax_symbol();
            if( apply_filters( 'wpi_invoice_flat_rate_enabled', true ) && ! empty($flat_rate_tax_symbol) ) {
                $invoice_item['invoicecontent']['lumpcode'] = $flat_rate_tax_symbol;
            }

			$data[ 'invoicecontents' ][] = $invoice_item;
		}

        if ( ! empty( $src[ 'src' ] ) && 'edd' === $src[ 'src' ] ) {
            $data = apply_filters( 'bpmj_wpwf_edd_invoice_data', $data, $src[ 'id' ] );
        }

		return array( 'invoices' => array( 'invoice' => $data ) );
	}

	/**
	 *
	 */
	public function send_invoice() {
		$contractor = $this->invoice_data[ 'invoices' ][ 'invoice' ][ 'contractor' ];
		if ( ! empty( $contractor[ 'contractor_id' ] ) && 'FAILED' === $contractor[ 'contractor_id' ] ) {
			// There was an error on finding the contractor - try again
			unset( $contractor[ 'contractor_id' ] );
			$contractor_id = $this->insert_or_update_contractor( $contractor );
			if ( - 1 === $contractor_id ) {
				// Creating or updating the contractor failed again - notify admin and quit
				$src_meta = get_post_meta( $this->invoice_post_id, static::$meta_key_invoice_src, true );
				$src      = maybe_unserialize( $src_meta );

				$this->notify_site_admin( $src, __( 'Nie udało się pobrać ani zaktualizować danych kontrahenta', 'bpmj_wpwf' ) );
				$note_date = '[' . date_i18n( "Y-m-d H:i:s" ) . '] ';
				$note = '<span class="' . static::$error_note_css_class . '">' . $note_date . 'Nie udało się pobrać ani zaktualizować danych kontrahenta' . '</span>';

				$logs = '<span class="' . static::$error_note_css_class . '">Logs: ' . var_export( $contractor, true ) . '</span>';

				// Save error information
				update_post_meta( $this->invoice_post_id, static::$meta_key_invoice_status, 'error' );
				update_post_meta( $this->invoice_post_id, static::$meta_key_invoice_note, $note . $logs );

                do_action('wpi_after_invoice_created_error', static::$invoice_post_type, $src, strip_tags( $note ) );

				return;
			}

			$this->invoice_data[ 'invoices' ][ 'invoice' ][ 'contractor' ] = array(
				'contractor_id' => $contractor_id,
			);

			$encoded_data = str_replace( '\\', '\\\\', $this->maybe_encode( $this->invoice_data ) );
			$post         = array(
				'ID'           => $this->invoice_post_id,
				'post_content' => sanitize_text_field( $encoded_data ),
			);
			wp_update_post( $post );
		}

		parent::send_invoice();
	}

	/**
	 * Wysyła automatycznie e-mail z fakturą do klienta
	 *
	 * @param array $src
	 */
	private function send_auto_email( $src ) {
		if ( $this->should_invoice_be_auto_sent_by_email($src['kind']) ) {
			$this->send_by_email($this->wfirma_invoice_id);
			$this->emit_after_invoice_auto_sent_event($src);
		}
	}

	/**
	 * @return array
	 */
	public function get_user_companies() {
		$url                 = $this->get_api_url( 'user_companies/find' );
		$parameters          = $this->maybe_encode( array(
			'user_companies' => array(
				'parameters' => array(
					'limit' => 100,
				),
			),
		) );
		$old_wf_company_id   = $this->wf_company_id;
		$this->wf_company_id = '';
		$response            = $this->make_api_call( $url, $parameters );
		$this->wf_company_id = $old_wf_company_id;
		$res_body            = wp_remote_retrieve_body( $response );
		$decode              = json_decode( $res_body, true );

		if ( empty( $decode[ 'user_companies' ] ) ) {
			return array();
		} else {
			$companies = array();
			foreach ( $decode[ 'user_companies' ] as $company ) {
				if ( empty( $company[ 'user_company' ] ) || empty( $company[ 'user_company' ][ 'company' ] ) ) {
					continue;
				}

				$companies[ $company[ 'user_company' ][ 'company' ][ 'id' ] ] = $company[ 'user_company' ][ 'company' ][ 'name' ];
			}

			return $companies;
		}
	}

	/**
	 * @return bool
	 */
	public function is_out_of_service() {
		return $this->out_of_service;
	}

	public function init_access_data(): void {
	    try {
	        $url = $this->get_api_url( 'invoices/find' );
	        $data = array(
	            'invoices' => array(
	                'parameters' => array(
	                    'page' => 1,
	                    'limit' => 1
	                ),
	            ),
	        );
	        $this->make_api_call( $url, $data );
	    } catch (\Exception $e) {
	    }
	}
	
	public function set_authorization_code(string $code): void {
	    global $bpmj_wpwf_settings;

	    $bpmj_wpwf_settings[ 'wf_oauth2_authorization_code' ] = $code;
	    unset($bpmj_wpwf_settings[ 'wf_oauth2_access_data' ]);
	    
	    do_action( 'wpi_debug_log', 'wFirma API: pobrano authorization_code "' . $code . '"');

	    update_option( 'bpmj_wpwf_settings', $bpmj_wpwf_settings );
	}

	public function check_connection(): bool
	{
	    $url = $this->get_api_url( 'invoices/find' );
	    $response = $this->make_api_call( $url, [] );
	    $body = json_decode( wp_remote_retrieve_body( $response ) );
	    return isset($body->invoices);
	}
	
    public function get_service_name()
    {
        return $this::$invoice_service_name;
    }

    public function send_by_email(int $invoice_id): void
    {
        $params = [
            'invoices' => [
                'parameters' => [
                    [
                        'parameter' => [
                            'name' => 'page',
                            'value' => 'invoice',
                        ],
                    ],
                    [
                        'parameter' => [
                            'name' => 'leaflet',
                            'value' => 0,
                        ],
                    ],
                    [
                        'parameter' => [
                            'name' => 'duplicate',
                            'value' => 0,
                        ],
                    ],
                    [
                        'parameter' => [
                            'name' => 'email',
                            'value' => $this->contractor['email'],
                        ],
                    ],
                ],
            ],
        ];
        $json = $this->maybe_encode($params);
        $url = $this->get_api_url('invoices/send/' . $invoice_id);
        $this->make_api_call($url, $json);
    }

    private function should_invoice_be_auto_sent_by_email(string $kind): bool
    {
        global $bpmj_wpwf_settings;

        $receipt = 'receipt' === $kind;
        $contractor_email = $this->contractor['email'];

        return !empty($contractor_email)
            && (!$receipt && !empty($bpmj_wpwf_settings['auto_sent'])
                || $receipt && !empty($bpmj_wpwf_settings['auto_sent_receipt']));
    }

    private function emit_after_invoice_auto_sent_event(array $src): void
    {
        do_action(
            'bpmj_' . static::$service_short_id . '_after_invoice_sent_to_customer',
            $src,
            $this->wfirma_invoice_id,
            $this->get_remote_invoice_number()
        );
    }
}
