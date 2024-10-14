<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BPMJ_Invoice_Factory_Woocommerce' ) ) {

	class BPMJ_Invoice_Factory_Woocommerce extends BPMJ_Invoice_Factory {

		/**
		 * @var WC_Order
		 */
		protected $order;

		/**
		 * @param WC_Order $order
		 * @param array $settings
		 * @param BPMJ_Base_Invoice $invoice_object
		 *
		 * @return BPMJ_Invoice_Factory_Woocommerce
		 */
		public static function factory( WC_Order $order, $settings, BPMJ_Base_Invoice $invoice_object ) {
			$factory           = new static( 'woocommerce', $settings, $invoice_object );
			$factory->order_id = static::get_wc_order_attribute( $order, 'id' );
			$factory->order    = $order;

			return $factory;
		}

		/**
		 * @param int $order_id
		 * @param mixed $default
		 *
		 * @return mixed|string
		 */
		private static function coalesce_wc_order_meta( $order_id, $default ) {
			$meta_value = '';
			foreach ( array_slice( func_get_args(), 2 ) as $meta_key ) {
				$meta_value = get_post_meta( $order_id, $meta_key, true );
				if ( $meta_value ) {
					break;
				}
			}

			return $meta_value;
		}

		/**
		 * @param WC_Order $order
		 * @param $attribute
		 *
		 * @return mixed
		 */
		private static function get_wc_order_attribute( WC_Order $order, $attribute ) {
			if ( version_compare( constant( 'WC_VERSION' ), '2.7', '<' ) ) {
				return $order->{$attribute};
			}

			switch ( $attribute ) {
				case 'id':
					return $order->get_id();
				case 'billing_country':
					return static::coalesce_wc_order_meta( $order->get_id(), 'PL', '_billing_country', '_billing_vat_country' );
				case 'billing_company':
					return static::coalesce_wc_order_meta( $order->get_id(), '', 'bpmj_woo_invoice_fields_company', '_billing_company', '_billing_vat_company' );
				case 'billing_nip':
					return static::coalesce_wc_order_meta( $order->get_id(), '', 'bpmj_woo_invoice_fields_nip', '_billing_nip', '_billing_vat_nip' );
				case 'billing_address':
					$address = get_post_meta( $order->get_id(), 'bpmj_woo_invoice_fields_street', true );
					if ( $address ) {
						return $address;
					}
					$address1 = static::coalesce_wc_order_meta( $order->get_id(), '', '_billing_address_1', '_billing_vat_address_1' );
					if ( $address1 ) {
						return $address1 . ' ' . static::coalesce_wc_order_meta( $order->get_id(), '', '_billing_address_2', '_billing_vat_address_2' );
					}

					return '';
				case 'billing_postcode':
					return static::coalesce_wc_order_meta( $order->get_id(), '', 'bpmj_woo_invoice_fields_postal', '_billing_postcode', '_billing_vat_postcode' );
				case 'billing_city':
					return static::coalesce_wc_order_meta( $order->get_id(), '', 'bpmj_woo_invoice_fields_city', '_billing_city', '_billing_vat_city' );
			}

			return get_post_meta( $order->get_id(), '_' . $attribute, true );
		}

		/**
		 * @return bool
		 */
		protected function should_issue_invoice() {
			return '1' === get_post_meta( $this->order_id, 'bpmj_woo_invoice_fields_check', true );
		}

		/**
		 * @return float
		 */
		public function get_paid_amount() {
			return $this->order->get_total();
		}

		/**
		 * @return string
		 */
		protected function get_currency_code() {
			return is_callable( array(
				$this->order,
				'get_currency'
			) ) ? $this->order->get_currency() : $this->order->get_order_currency();
		}

		/**
		 *
		 */
		protected function setup_contractor_info() {
			$receipt    = $this->invoice_object->is_receipt();
			$first_name = static::get_wc_order_attribute( $this->order, 'billing_first_name' );
			$last_name  = static::get_wc_order_attribute( $this->order, 'billing_last_name' );

			if ( empty ( $first_name ) || empty ( $last_name ) ) {
				$first_name = static::get_wc_order_attribute( $this->order, 'shipping_first_name' );
				$last_name  = static::get_wc_order_attribute( $this->order, 'shipping_last_name' );
			}
			$person_name = get_post_meta( $this->order_id, 'bpmj_woo_invoice_fields_name', true );
			if ( $person_name ) {
				$name_arr = explode( ' ', $person_name );

				if ( count( $name_arr ) > 1 ) {
					/*
					 * To poniższe ma sprawić, że 'Jan Andrzej Nowak' zostanie podzielone na:
					 * $first_name_b = 'Jan Andrzej'
					 * $last_name_b = 'Nowak'
					 */
					$first_name = implode( ' ', array_slice( $name_arr, 0, - 1 ) );
					$last_name  = implode( ' ', array_slice( $name_arr, - 1 ) );
				} else {
					$first_name = $person_name;
					$last_name  = '';
				}
			}
			$additional_data = array(
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'country'    => static::get_wc_order_attribute( $this->order, 'billing_vat_country' ),
			);
			$this->invoice_object->set_contractor_info(
				"{$first_name} {$last_name}",
				static::get_wc_order_attribute( $this->order, 'billing_company' ),
				static::get_wc_order_attribute( $this->order, 'billing_nip' ),
				static::get_wc_order_attribute( $this->order, 'billing_address' ),
				static::get_wc_order_attribute( $this->order, 'billing_postcode' ),
				static::get_wc_order_attribute( $this->order, 'billing_city' ),
				static::get_wc_order_attribute( $this->order, 'billing_email' ),
				$additional_data
			);
		}

		/**
		 *
		 */
		protected function setup_products() {
			$items         = $this->order->get_items();
			$calc_taxes    = get_option( 'woocommerce_calc_taxes' ) === 'yes';
			$tax_qualifier = BPMJ_Base_Invoice_Item::TAX_RATE;
			if ( ! $this->invoice_object->is_vat_payer() ) {
				$tax_qualifier = BPMJ_Base_Invoice_Item::TAX_EXEMPT;
			}
			foreach ( $items as $item ) {
				if ( $this->invoice_object->is_vat_payer() ) {
					$tax = $this->default_vat;
					if ( $calc_taxes ) {
						$tax_rates = WC_Tax::get_rates( $item[ 'tax_class' ] );
						$tax_info  = array_shift( $tax_rates );
						if ( isset( $tax_info[ 'rate' ] ) ) {
							$tax = (int) $tax_info[ 'rate' ];
						}
					}
				} else {
					$tax = null;
				}
				$additional_data = array(
					'woo_product_id' => $item[ 'variation_id' ] ? $item[ 'variation_id' ] : $item[ 'product_id' ],
				);
				$this->invoice_object->add_product(
					$item[ 'name' ],
					$this->order->get_line_total( $item, true, true ),
					BPMJ_Base_Invoice_Item::PRICE_GROSS_VALUE_AFTER_DISCOUNT,
					$item[ 'qty' ],
					$tax,
					$tax_qualifier,
					null,
					BPMJ_Base_Invoice_Item::DISCOUNT_NONE,
					$additional_data );
			}

			$shipping = is_callable( array(
				$this->order,
				'get_shipping_total'
			) ) ? $this->order->get_shipping_total() : $this->order->get_total_shipping();
			if ( $shipping > 0 ) {
				$shipping_tax_amount = $this->order->get_shipping_tax();
				if ( $this->invoice_object->is_vat_payer() ) {
					$tax = $this->default_vat;
					if ( $calc_taxes ) {
						$tax_rates = WC_Tax::get_shipping_tax_rates();
						$tax_info  = array_shift( $tax_rates );
						if ( isset( $tax_info[ 'rate' ] ) ) {
							$tax = (int) $tax_info[ 'rate' ];
						}
					}
				} else {
					$tax = null;
				}
				$this->invoice_object->add_product( $this->order->get_shipping_method(), bcadd( $shipping, $shipping_tax_amount, 2 ), BPMJ_Base_Invoice_Item::PRICE_GROSS_VALUE_AFTER_DISCOUNT, 1, $tax, $tax_qualifier );
			}
		}
	}
}
