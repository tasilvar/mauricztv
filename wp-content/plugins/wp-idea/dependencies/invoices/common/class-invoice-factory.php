<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use bpmj\wpidea\sales\product\Invoice_Tax_Payer_Helper;

if ( ! class_exists( 'BPMJ_Invoice_Factory' ) ) {

	abstract class BPMJ_Invoice_Factory {

		/**
		 * @var string
		 */
		protected $service_short_id = '';

		/**
		 * @var string
		 */
		protected $service_long_id = '';

		/**
		 * @var array
		 */
		protected $settings = array();

		/**
		 * @var bool
		 */
		protected $allows_receipts = false;

		/**
		 * @var int
		 */
		protected $default_vat = 23;

        /**
         * @var string
         */
		protected $vat_exemption = 'Art. 113 ust. 1';

		/**
		 * @var bool
		 */
		protected $is_vat_payer = true;

		/**
		 * @var string
		 */
		protected $type = 'abstract';

		/**
		 * @var int
		 */
		protected $order_id = 0;

		/**
		 * @var string
		 */
		protected $invoice_type = 'vat';

		/**
		 * @var BPMJ_Base_Invoice
		 */
		protected $invoice_object = null;

		/**
		 * BPMJ_Invoice_Factory constructor.
		 *
		 * @param string $type
		 * @param array $settings
		 * @param BPMJ_Base_Invoice $invoice_object
		 */
		public function __construct( $type, $settings, BPMJ_Base_Invoice $invoice_object ) {
            $wpidea_settings = get_option('wp_idea');

			$this->type            = $type;
			$this->settings        = $settings;
			$this->allows_receipts = ! empty( $settings[ 'receipt' ] );
			$this->default_vat     = Invoice_Tax_Payer_Helper::get_default_vat_rate();
			$this->vat_exemption   = $settings['vat_exemption'] ?? $this->vat_exemption;
			$this->is_vat_payer    = Invoice_Tax_Payer_Helper::is_enabled();
			if ( ! $this->is_vat_payer ) {
				$this->invoice_type = 'bill';
			}
			$this->invoice_object = $invoice_object;
			$this->invoice_object->set_vat_exemption( $this->vat_exemption );
			$this->invoice_object->set_is_vat_payer( $this->is_vat_payer );
			$this->service_short_id = $invoice_object->get_service_short_id();
			$this->service_long_id  = $invoice_object->get_service_long_id();
		}

		/**
		 * @return bool
		 */
		public function create_invoice_object() {
			if ( ! $this->should_issue_invoice() ) {
				if ( $this->allows_receipts ) {
					$this->invoice_type = 'receipt';
					$this->invoice_object->set_is_receipt();
				} else {
					return false;
				}
			}
			$this->invoice_object->set_invoice_paid_amount( $this->get_paid_amount() );
			$this->invoice_object->set_invoice_issue_date( $this->get_invoice_date() );
			$this->invoice_object->set_currency_code( $this->get_currency_code() );

			$this->setup_contractor_info();
			$this->setup_products();

			return true;
		}

		/**
		 * @return bool
		 */
		protected function should_issue_invoice() {
			return true;
		}

		/**
		 * @return float
		 */
		protected function get_paid_amount() {
			return 0.0;
		}

		/**
		 * @return string
		 */
		protected function get_invoice_date() {
		    return current_time( 'Y-m-d' );
		}

		/**
		 * @return string
		 */
		protected function get_currency_code() {
			return 'PLN';
		}

		/**
		 * @param array $data
		 * @param array $src
		 */
		public function store_invoice( $data = array(), $src = array() ) {
			$src = array_merge( array(
				'src'  => $this->type,
				'id'   => $this->order_id,
				'kind' => $this->invoice_type,
			), $src );

			$this->invoice_object->store_invoice( $data, $src );
		}

		abstract protected function setup_contractor_info();

		abstract protected function setup_products();
	}
}
