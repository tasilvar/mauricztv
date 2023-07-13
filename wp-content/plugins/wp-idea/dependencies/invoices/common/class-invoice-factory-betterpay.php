<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BPMJ_Invoice_Factory_Betterpay' ) ) {

	class BPMJ_Invoice_Factory_Betterpay extends BPMJ_Invoice_Factory {

		/**
		 * @var array
		 */
		protected $order_data = array();

		/**
		 * @var array
		 */
		protected $order_additional_data = array();

		/**
		 * @param array $order_data
		 * @param array $settings
		 * @param BPMJ_Base_Invoice $invoice_object
		 *
		 * @return static
		 */
		public static function factory( $order_data, $settings, BPMJ_Base_Invoice $invoice_object ) {
			$factory                        = new static( 'betterpay', $settings, $invoice_object );
			$factory->order_id              = $order_data[ 'id' ];
			$factory->order_data            = $order_data;
			$factory->order_additional_data = unserialize( $order_data[ 'add' ] );
			if ( $order_data[ 'tax' ] ) {
				$factory->default_vat = $order_data[ 'tax' ];
			}

			return $factory;
		}

		/**
		 * @return bool
		 */
		protected function should_issue_invoice() {
			return ! empty( $this->order_additional_data[ 'nip' ] );
		}

		/**
		 * @return float
		 */
		public function get_paid_amount() {
			return $this->order_additional_data[ 'amount' ];
		}

		/**
		 *
		 */
		protected function setup_contractor_info() {
			$this->invoice_object->set_contractor_info(
				'',
				$this->invoice_object->is_receipt() ? '' : $this->order_additional_data[ 'company' ],
				$this->invoice_object->is_receipt() ? '' : $this->order_additional_data[ 'nip' ],
				$this->invoice_object->is_receipt() ? '' : $this->order_additional_data[ 'address' ],
				$this->invoice_object->is_receipt() ? '' : $this->order_additional_data[ 'postal' ],
				$this->invoice_object->is_receipt() ? '' : $this->order_additional_data[ 'city' ],
				$this->order_data[ 'email' ]
			);
		}

		/**
		 *
		 */
		protected function setup_products() {
			$tax_qualifier = $this->is_vat_payer ? BPMJ_Base_Invoice_Item::TAX_RATE : BPMJ_Base_Invoice_Item::TAX_EXEMPT;
			$this->invoice_object->add_product( $this->order_data[ 'name' ], $this->order_data[ 'amount' ], BPMJ_Base_Invoice_Item::PRICE_GROSS_VALUE_AFTER_DISCOUNT, 1, $this->default_vat, $tax_qualifier );
		}
	}
}
