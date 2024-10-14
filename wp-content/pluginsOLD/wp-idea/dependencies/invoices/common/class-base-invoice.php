<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once dirname(__FILE__) . '/class-base-invoice-item.php';

if ( ! class_exists( 'BPMJ_Base_Invoice' ) ) {

	abstract class BPMJ_Base_Invoice {

		/**
		 * @var string
		 */
		protected static $service_short_id = 'invc';

		/**
		 * @var string
		 */
		protected static $service_long_id = 'invoice';

		/**
		 * @var string
		 */
		protected static $meta_key_invoice_src = 'bpmj_invoice_src';

		/**
		 * @var string
		 */
		protected static $meta_key_invoice_note = 'bpmj_invoice_note';

		/**
		 * @var string
		 */
		protected static $meta_key_invoice_status = 'bpmj_invoice_status';

		/**
		 * @var string
		 */
		protected static $error_note_css_class = 'bpmj_invoice_error_note';

		/**
		 * @var string
		 */
		protected static $success_note_css_class = 'bpmj_invoice_success_note';

		/**
		 * @var string
		 */
		protected static $invoice_service_name = 'Base Invoice';

		/**
		 * @var string
		 */
		protected static $invoice_post_type = 'bpmj_invoice';

		/**
		 * @var string
		 */
		protected static $api_endpoint_url_pattern = 'https://example.com/%1$s/';

		/**
		 * Post ID
		 * @var int
		 */
		protected $invoice_post_id = 0;

        /**
         * Created invoice number
         * @var string
         */
        protected $remote_invoice_number = '';

        protected ?int $remote_invoice_id = null;

		/**
		 * @var bool
		 */
		protected $is_vat_payer = true;

		/**
		 * @var bool
		 */
		protected $is_receipt = false;

		/**
		 * Invoice data array
		 * @var array
		 */
		protected $invoice_data = array();

		/**
		 * Invoice post data
		 * @var string
		 */
		protected $post_date = '';

		/**
		 * @var string
		 */
		protected $issue_date;

		/**
		 * @var array
		 */
		protected $contractor = array();

		/**
		 * @var BPMJ_Base_Invoice_Item[]
		 */
		protected $items = array();

		/**
		 * @var string
		 */
		protected $paid_amount = '';

		/**
		 * @var string
		 */
		protected $currency = 'PLN';

		/**
		 * @var string
		 */
		protected $invoice_type;

        /**
         * @var string
         */
        protected $vat_exemption;

		/**
		 * @param int $post_id
		 *
		 * @return $this
		 */
		public function set_from_invoice_post( $post_id ) {
			$this->invoice_post_id = $post_id;
			$invoice_post          = get_post( $this->invoice_post_id );
			if ( $invoice_post instanceof WP_Post ) {
				$this->invoice_data = $this->restore_invoice_data_from_post( $invoice_post->post_content );
				if ( false === $this->invoice_data ) {
					$this->invoice_data = array();
				}
				$this->post_date  = $invoice_post->post_date;
				$this->contractor = get_post_meta( $this->invoice_post_id, static::get_invoice_contractor_meta_key(), true );
			} else {
				$this->invoice_data = array();
			}

			return $this;
		}

		/**
		 * @param bool $is_vat_payer
		 */
		public function set_is_vat_payer( $is_vat_payer = true ) {
			$this->is_vat_payer = $is_vat_payer;
		}

		/**
		 * @return bool
		 */
		public function is_vat_payer() {
			return $this->is_vat_payer;
		}

		/**
		 * @param bool $is_receipt
		 */
		public function set_is_receipt( $is_receipt = true ) {
			$this->is_receipt = $is_receipt;
		}

		/**
		 * @return bool
		 */
		public function is_receipt() {
			return $this->is_receipt;
		}

		/**
		 * Sends invoice to API endpoint
		 */
		public function send_invoice() {
			// Set invoice issue date to "today"
		    $this->set_invoice_issue_date( current_time( 'Y-m-d' ) );
			$url = $this->get_invoices_api_url();

			$response = $this->make_api_call( $url, $this->maybe_encode( $this->invoice_data ) );

			// Interpret API's response
			$this->process_invoice_response( $response );
		}

		/**
		 * Interprets API's response
		 *
		 * @param WP_Error|array $response - server's response
		 */
		private function process_invoice_response( $response ) {

			$post_id = $this->invoice_post_id;

			// Response status
			$res_header = wp_remote_retrieve_header( $response, 'status' );

			// Response body
			$res_body = wp_remote_retrieve_body( $response );

			// Previous logs
			$logs = get_post_meta( $post_id, static::$meta_key_invoice_note, true );

			// New entry date
			$note_date = '[' . date_i18n( "Y-m-d H:i:s" ) . '] ';

			// Connection error
			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();

				$note = '<span class="' . static::$error_note_css_class . '">' . $note_date . $error_message . '</span>';

				// Save error information
				update_post_meta( $post_id, static::$meta_key_invoice_status, 'error' );
				update_post_meta( $post_id, static::$meta_key_invoice_note, $logs . $note );

				return;
			}

			$src_meta = get_post_meta( $post_id, static::$meta_key_invoice_src, true );
			$src      = maybe_unserialize( $src_meta );


			/*
			 * Analyze server response
			 */
			$error = $this->analyze_server_response( $res_header, $res_body, $note_date, $post_id, $logs, $src );

			if ( ! empty( $error ) ) {
				$note = '<span class="' . static::$error_note_css_class . '">' . $note_date . $error . '</span>';

				// Save error information
				update_post_meta( $post_id, static::$meta_key_invoice_status, 'error' );
				update_post_meta( $post_id, static::$meta_key_invoice_note, $logs . $note );
				$this->notify_site_admin( $src, $error );
                do_action('wpi_after_invoice_created_error', static::$invoice_post_type, $src, $error);
			} else if ( $this->get_remote_invoice_number() ) {
                do_action(
                    'wpi_after_invoice_created_success',
                    static::$invoice_post_type,
                    $src,
                    $this->get_remote_invoice_number(),
                    $post_id,
                    $this->get_remote_invoice_id()
                );
				do_action( 'bpmj_' . static::$service_short_id . '_after_invoice_created', $src, $this->get_remote_invoice_number() );
			}
		}

		/**
		 * Saves object to database as post
		 *
		 * @param array $data
		 * @param array $src
		 *
		 * @return int post ID
		 */
		public function store_invoice( array $data, array $src = array() ) {
			$this->invoice_data = $this->prepare_invoice_data( $data, $src );
			$title = uniqid( 'Dokument-' );
			if ( ! empty( $src[ 'src' ] ) && ! empty( $src[ 'id' ] ) && ! empty( $src[ 'kind' ] ) ) {
				$title = $src[ 'src' ] . '-' . $src[ 'id' ] . '-' . $src[ 'kind' ];
			}

			$post = array(
				'post_content' => $this->prepare_data_for_saving( $this->invoice_data ),
				'post_title'   => $title,
				'post_status'  => 'publish',
				'post_type'    => static::$invoice_post_type,
			);

			$this->invoice_post_id = wp_insert_post( $post );

			// If the post got inserted
			if ( $this->invoice_post_id ) {
				update_post_meta( $this->invoice_post_id, static::$meta_key_invoice_src, $src );
				update_post_meta( $this->invoice_post_id, static::$meta_key_invoice_status, 'pending' );
				update_post_meta( $this->invoice_post_id, static::get_invoice_contractor_meta_key(), $this->contractor );
			}

            do_action('wpi_after_store_invoice', static::$invoice_post_type, $this->invoice_post_id, $title, $this->invoice_data, $src);

			return $this->invoice_post_id;
		}

		/**
		 * @param string $date
		 */
		public function set_invoice_issue_date( $date ) {
			$this->issue_date = $date;
		}

		/**
		 * @param string $person_name
		 * @param string $company_name
		 * @param string $nip
		 * @param string $street
		 * @param string $post_code
		 * @param string $city
		 * @param string $email
		 * @param array $additional_data
		 */
		public function set_contractor_info( $person_name, $company_name, $nip, $street, $post_code, $city, $email, $additional_data = array() ) {
			$pl_post_code = preg_replace( '/\D+/', '', $post_code );
			if ( 5 === strlen( $pl_post_code ) ) {
				$post_code = substr( $pl_post_code, 0, 2 ) . '-' . substr( $pl_post_code, 2 );
			}

			$this->contractor = array(
				'person_name'     => $person_name,
				'company_name'    => $company_name,
				'nip'             => $nip,
				'street'          => $street,
				'post_code'       => $post_code,
				'city'            => $city,
				'email'           => $email,
				'additional_data' => $additional_data,
			);
		}

		/**
		 * @param string $currency_code
		 */
		public function set_currency_code( $currency_code ) {
			$this->currency = $currency_code;
		}

		/**
		 * @param string $name
		 * @param string $price
		 * @param string $price_qualifier
		 * @param int $quantity
		 * @param string $tax
		 * @param string $tax_qualifier
		 * @param string $discount
		 * @param string $discount_qualifier
		 * @param array $additional_data
         * @param ?string $gtu
         * @param ?string $flat_rate_tax_symbol
		 */
		public function add_product( $name, $price, $price_qualifier, $quantity, $tax, $tax_qualifier = 'rate', $discount = null, $discount_qualifier = 'none', $additional_data = array(), $gtu = null, $flat_rate_tax_symbol = null ) {
			if ( '0.00' < number_format( $price, 2, '.', '' ) ) {
				// Allow only items with positive price
				$this->items[] = new BPMJ_Base_Invoice_Item( $name, $price, $price_qualifier, $quantity, $tax, $tax_qualifier, $discount, $discount_qualifier, $additional_data, $gtu, $flat_rate_tax_symbol );
			}
		}

		/**
		 * @param string $amount
		 */
		public function set_invoice_paid_amount( $amount ) {
			$this->paid_amount = $amount;
		}

		/**
		 * @return string
		 */
		abstract protected function get_invoices_api_url();

		/**
		 * @param string $url
		 * @param string|array $data
		 * @param string $method
		 *
		 * @return array|WP_Error
		 */
		protected function make_api_call( $url, $data, $method = 'POST' ) {
			$args = array(
				'timeout'     => 45,
				'headers'     => $this->get_api_call_headers(),
				'redirection' => 5,
				'body'        => $this->prepare_request_body( $data ),
				'method'      => $method,
			);

			return wp_remote_post( $url, $args );
		}

		/**
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
			return '';
		}

		/**
		 * @param array $data
		 * @param array $src
		 *
		 * @return array
		 */
		protected function prepare_invoice_data( array $data, array $src = array() ) {
			return $data;
		}

		/**
		 * @return string
		 */
		public function get_remote_invoice_number() {
			return $this->remote_invoice_number;
		}

        private function get_remote_invoice_id(): ?int
        {
            return $this->remote_invoice_id;
        }

        protected function set_remote_invoice_id(int $invoice_id): void
        {
            $this->remote_invoice_id = $invoice_id;
        }

        /**
		 * @param string $action
		 *
		 * @return string
		 */
		protected function get_api_url( $action ) {
			return sprintf( static::$api_endpoint_url_pattern, $action );
		}

        /**
		 * @return array
		 */
		protected function get_api_call_headers() {
			return array(
				'content-type' => 'application/json',
			);
		}

        /**
		 * @param array $src
		 * @param string $error
		 */
		protected function notify_site_admin( $src, $error ) {
			// Notify site admin
			$src_id = 'N/A';
			if ( isset( $src[ 'id' ] ) ) {
				$src_id = $src[ 'id' ];
			}

            $email   = apply_filters( 'wpi_admin_notices_email', get_option( 'admin_email' ) );
			$message = 'Dzień dobry,

Niestety wystąpił problem przy wystawianiu faktury przez oprogramowanie ' . static::$invoice_service_name . '.
Komunikat: ' . $error . '
Nr zamówienia: ' . $src_id . '
	
Więcej informacji zmajdziesz w panelu administracyjnym w zakładce ' . static::$invoice_service_name . '.

--;
Wiadomość wygenerowana automatycznie.';

			$message .= "\n\nZawartość wysłana do " . static::$invoice_service_name . ': ' . "\n" . var_export( $this->invoice_data, true );
			wp_mail( $email, static::$invoice_service_name . ' - błąd!', $message );
		}

        /**
		 * @param array|string $data
		 *
		 * @return string
		 */
		protected function maybe_encode( $data ) {
			return is_string( $data ) ? $data : $this->encode_data( $data );
		}

        /**
		 * @param string $data_encoded
		 *
		 * @return array
		 */
		protected function decode_data( $data_encoded ) {
			return $this->decode_json( $data_encoded );
		}

        /**
		 * @param array $data
		 *
		 * @return string
		 */
		protected function encode_data( array $data ) {
			return $this->encode_json( $data );
		}

        /**
		 * @param $json
		 *
		 * @return array
		 */
		protected function decode_json( $json ) {
			return json_decode( $json, true );
		}

        /**
		 * @param array $data
		 *
		 * @return string
		 */
		protected function encode_json( array $data ) {
			return json_encode( $data );
		}

        /**
		 * @param $xml_string
		 *
		 * @return array
		 */
		protected function decode_xml( $xml_string ) {
			$xml  = simplexml_load_string( $xml_string, "SimpleXMLElement", LIBXML_NOCDATA );
			$json = $this->encode_json( (array) $xml );

			return $this->decode_json( $json );
		}

        /**
		 * Author : Lalit Patel, Paweł Sypek (adaptation)
		 * Website: http://www.lalit.org/lab/convert-php-array-to-xml-with-attributes
		 * License: Apache License 2.0
		 *          http://www.apache.org/licenses/LICENSE-2.0
		 *
		 * @param string $node_name
		 * @param array $data
		 * @param string $xml_version
		 * @param string $xml_encoding
		 *
		 * @return string
		 */
		protected function encode_xml( $node_name, array $data, $xml_version = '1.0', $xml_encoding = 'UTF-8' ) {
			$bool_to_str           = function ( $value ) {
				//convert boolean to text value.
				$value = $value === true ? 'true' : $value;
				$value = $value === false ? 'false' : $value;

				return $value;
			};
			$is_valid_tag_name     = function ( $tag ) {
				$pattern = '/^[a-z_]+[a-z0-9\:\-\.\_]*[^:]*$/i';

				return preg_match( $pattern, $tag, $matches ) && $matches[ 0 ] === $tag;
			};
			$convert_array_to_node = function ( DOMDocument $xml, $node_name, $data ) use ( &$convert_array_to_node, $bool_to_str, $is_valid_tag_name ) {
				$node = $xml->createElement( $node_name );
				if ( is_array( $data ) ) {
					// get the attributes first.;
					if ( isset( $data[ '@attributes' ] ) ) {
						foreach ( $data[ '@attributes' ] as $key => $value ) {
							if ( ! $is_valid_tag_name( $key ) ) {
								throw new Exception( 'Illegal character in attribute name. attribute: ' . $key . ' in node: ' . $node_name );
							}
							$node->setAttribute( $key, $bool_to_str( $value ) );
						}
						unset( $data[ '@attributes' ] ); //remove the key from the array once done.
					}

					// check if it has a value stored in @value, if yes store the value and return
					// else check if its directly stored as string
					if ( isset( $data[ '@value' ] ) ) {
						$node->appendChild( $xml->createTextNode( $bool_to_str( $data[ '@value' ] ) ) );
						unset( $data[ '@value' ] );    //remove the key from the array once done.
						//return from recursion, as a note with value cannot have child nodes.
						return $node;
					} else if ( isset( $data[ '@cdata' ] ) ) {
						$node->appendChild( $xml->createCDATASection( $bool_to_str( $data[ '@cdata' ] ) ) );
						unset( $data[ '@cdata' ] );    //remove the key from the array once done.
						//return from recursion, as a note with cdata cannot have child nodes.
						return $node;
					}
				}

				//create subnodes using recursion
				if ( is_array( $data ) ) {
					// recurse to get the node for that key
					foreach ( $data as $key => $value ) {
						if ( ! $is_valid_tag_name( $key ) ) {
							throw new Exception( 'Illegal character in tag name. tag: ' . $key . ' in node: ' . $node_name );
						}
						if ( is_array( $value ) && is_numeric( key( $value ) ) ) {
							// MORE THAN ONE NODE OF ITS KIND;
							// if the new array is numeric index, means it is array of nodes of the same kind
							// it should follow the parent key name
							foreach ( $value as $k => $v ) {
								$node->appendChild( $convert_array_to_node( $xml, $key, $v ) );
							}
						} else {
							// ONLY ONE NODE OF ITS KIND
							$node->appendChild( $convert_array_to_node( $xml, $key, $value ) );
						}
						unset( $data[ $key ] ); //remove the key from the array once done.
					}
				}

				// after we are done with all the keys in the array (if it is one)
				// we check if it has any text value, if yes, append it.
				if ( ! is_array( $data ) ) {
					$node->appendChild( $xml->createTextNode( $bool_to_str( $data ) ) );
				}

				return $node;
			};

			$xml = new DOMDocument( $xml_version, $xml_encoding );
			try {
				$xml->appendChild( $convert_array_to_node( $xml, $node_name, $data ) );
			} catch ( Exception $e ) {
				$root  = $xml->createElement( $node_name );
				$error = $xml->createElement( 'error', htmlspecialchars( $e->getMessage(), ENT_QUOTES, $xml_encoding ) );
				$root->appendChild( $error );
				$xml->appendChild( $root );
			}

			return $xml->saveXML();

		}


        /**
		 * @return string
		 */
		public function test_output() {
			return $this->maybe_encode( $this->prepare_invoice_data( array(), array() ) );
		}

        /**
		 * @param string $url
		 * @param mixed $data
		 * @param string $method
		 *
		 * @return array|WP_Error
		 */
		public function test_api_call( $url = null, $data = null, $method = null ) {
			$url    = $url ? $url : $this->get_test_api_url();
			$data   = $data ? $data : $this->get_test_data();
			$method = $method ? $method : $this->get_test_method();

			if ( ! $url ) {
				_doing_it_wrong( __FUNCTION__, 'Please setup test URL in child class overriding parent::get_test_api_url() first', '1.0.0' );

				return array();
			}

			$response = $this->make_api_call( $url, $data, $method );

			// Response status
			$res_header = wp_remote_retrieve_header( $response, 'status' );

			// Response body
			$res_body = wp_remote_retrieve_body( $response );

			return array( 'status' => $res_header, 'data' => $this->decode_data( $res_body ) );
		}

        /**
		 * @return string
		 */
		protected function get_test_api_url() {
			return '';
		}

        /**
		 * @return string|array
		 */
		protected function get_test_data() {
			return '';
		}

        /**
		 * @return string
		 */
		protected function get_test_method() {
			return 'POST';
		}

        /**
		 * @param mixed $data
		 *
		 * @return string
		 */
		protected function prepare_request_body( $data ) {
			return $this->maybe_encode( $data );
		}

        /**
		 * @param mixed $invoice_data
		 *
		 * @return string
		 */
		protected function prepare_data_for_saving( $invoice_data ) {
			/*
			 * Add slashes to all '\' because otherwise WP will strip them completely in a moment.
			 * They're required for all unicode characters (\uXXXX) to work.
			 */
			$encoded_data = str_replace( '\\', '\\\\', $this->maybe_encode( $invoice_data ) );

			return sanitize_text_field( $encoded_data );
		}

        /**
		 * @param string $post_content
		 *
		 * @return array
		 */
		protected function restore_invoice_data_from_post( $post_content ) {
			return $this->decode_data( $post_content );
		}

        /**
		 * @return string
		 */
		public function get_service_long_id() {
			return static::$service_long_id;
		}

        /**
		 * @return string
		 */
		public function get_service_short_id() {
			return static::$service_short_id;
		}

        /**
		 * @return string
		 */
		protected static function get_invoice_contractor_meta_key() {
			return static::$service_long_id . '_contractor';
		}

        public function set_vat_exemption( $vat_exemption )
        {
            $this->vat_exemption = $vat_exemption;
        }

        public function get_vat_exemption()
        {
            return $this->vat_exemption;
        }
    }
}
