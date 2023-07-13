<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use bpmj\wpidea\sales\product\Meta_Helper as Product_Meta_Helper;
use bpmj\wpidea\sales\product\Invoice_Tax_Payer_Helper;
use bpmj\wpidea\modules\cart\api\Fees_API;

if ( ! class_exists( 'BPMJ_Invoice_Factory_Edd' ) ) {

	class BPMJ_Invoice_Factory_Edd extends BPMJ_Invoice_Factory {

		/**
		 * @var array
		 */
		protected $payment_meta = array(
			'bpmj_edd_invoice_type'         => 'person',
			'bpmj_edd_invoice_person_name'  => '',
			'bpmj_edd_invoice_company_name' => '',
			'bpmj_edd_invoice_nip'          => '',
			'bpmj_edd_invoice_street'       => '',
			'bpmj_edd_invoice_postcode'     => '',
			'bpmj_edd_invoice_city'         => '',
			'email'                         => '',
		);

		/**
		 * @param int $payment_id
		 * @param array $settings
		 * @param BPMJ_Base_Invoice $invoice_object
		 *
		 * @return BPMJ_Invoice_Factory_Edd
		 */
		public static function factory( $payment_id, $settings, BPMJ_Base_Invoice $invoice_object ) {
			$factory               = new static( 'edd', $settings, $invoice_object );
			$factory->order_id     = $payment_id;
			$factory->payment_meta = array_merge( $factory->payment_meta, edd_get_payment_meta( $payment_id ) );

			return $factory;
		}

		/**
		 * @return bool
		 */
		protected function should_issue_invoice() {
			return isset( $this->payment_meta[ 'bpmj_edd_invoice_check' ] ) && $this->payment_meta[ 'bpmj_edd_invoice_check' ] == '1';
		}

		/**
		 * @return float
		 */
		public function get_paid_amount() {
			return edd_get_payment_amount( $this->order_id );
		}

		/**
		 * @return string
		 */
		protected function get_currency_code() {
			return edd_get_payment_currency_code( $this->order_id );
		}

		/**
		 *
		 */
		protected function setup_contractor_info() {
			$is_company      = ( 'person' !== $this->payment_meta[ 'bpmj_edd_invoice_type' ] ) ? true : false;
			$additional_data = array();
			if ( ! $is_company && ( $user_info = edd_get_payment_meta_user_info( $this->order_id ) ) ) {
				$additional_data[ 'first_name' ] = $user_info[ 'first_name' ];
				$additional_data[ 'last_name' ]  = $user_info[ 'last_name' ];
				if ( empty( $this->payment_meta[ 'bpmj_edd_invoice_person_name' ] ) ) {
					$this->payment_meta[ 'bpmj_edd_invoice_person_name' ] = "{$user_info[ 'first_name' ]} {$user_info[ 'last_name' ]}";
				} else if ( empty( $user_info[ 'first_name' ] ) && empty( $user_info[ 'last_name' ] ) && false !== strpos( $this->payment_meta[ 'bpmj_edd_invoice_person_name' ], ' ' ) ) {
					list( $additional_data[ 'first_name' ], $additional_data[ 'last_name' ] ) = explode( ' ', $this->payment_meta[ 'bpmj_edd_invoice_person_name' ], 2 );
				}
			}
			$user_email = '';
			if ( ! empty( $this->payment_meta[ 'email' ] ) ) {
				$user_email = $this->payment_meta[ 'email' ];
			} else if ( ! empty( $this->payment_meta[ 'user_info' ] ) && ! empty( $this->payment_meta[ 'user_info' ][ 'email' ] ) ) {
				$user_email = $this->payment_meta[ 'user_info' ][ "email" ];
			}

            $invoice_apartment_number = '';

            if ( ! empty( $this->payment_meta[ 'bpmj_edd_invoice_apartment_number' ] ) ) {
                $invoice_apartment_number = ' / '.$this->payment_meta[ 'bpmj_edd_invoice_apartment_number' ];
            }

            $invoice_street = $this->payment_meta[ 'bpmj_edd_invoice_street' ] . ' '. $this->payment_meta[ 'bpmj_edd_invoice_building_number' ] . $invoice_apartment_number;

			$this->invoice_object->set_contractor_info(
				$is_company ? '' : $this->payment_meta[ 'bpmj_edd_invoice_person_name' ],
				$is_company ? $this->payment_meta[ 'bpmj_edd_invoice_company_name' ] : '',
				$is_company ? $this->payment_meta[ 'bpmj_edd_invoice_nip' ] : '',
                $invoice_street,
				$this->payment_meta[ 'bpmj_edd_invoice_postcode' ],
				$this->payment_meta[ 'bpmj_edd_invoice_city' ],
				$user_email,
				$additional_data
			);
		}

		/**
		 *
		 */
		protected function setup_products() {
			if ( is_array( $this->payment_meta[ 'cart_details' ] ) ) {
				$cart = $this->payment_meta[ 'cart_details' ];
			} else {
				$cart = unserialize( $this->payment_meta[ 'cart_details' ] );
			}

			foreach ( $cart as $item ) {
                $gtu = Product_Meta_Helper::get_gtu_as_string($item[ 'id' ]);
                $flat_rate_tax_symbol = Product_Meta_Helper::get_flat_rate_tax_symbol($item[ 'id' ]);

				// Sprawdź, czy ten produkt jest wariantem
				$item_name            = $item[ 'name' ];
				$item_additional_data = array(
					'edd_product_id' => $item[ 'id' ],
					'edd_price_id'   => 0,
				);
				if ( ! empty( $item[ 'item_number' ][ 'options' ][ 'price_id' ] ) && edd_has_variable_prices( $item[ 'id' ] ) ) {
					$price_id = $item[ 'item_number' ][ 'options' ][ 'price_id' ];
					// Nazwa wariantu
					$variant_name = edd_get_price_option_name( $item[ 'id' ], $price_id, $this->order_id );
					if ( ! empty( $variant_name ) ) {
						$item_name .= ' - ' . $variant_name;
					}
					$item_additional_data[ 'edd_price_id' ] = $price_id;

					$gtu = Product_Meta_Helper::get_gtu_as_string_for_variant($item[ 'id' ], $price_id);
                    $flat_rate_tax_symbol = Product_Meta_Helper::get_flat_rate_tax_symbol_for_variant($item[ 'id' ], $price_id);
				}

				$item_price = number_format( $item[ 'price' ], 2, '.', '' );

				// Ustal stawkę VAT w zalezności od wybranego rodzaju dokumentu sprzedaży
				// Sprawdzy, jaki typ dokumentu sprzedaży jest ustawiony
				$tax           = null;
				$tax_qualifier = BPMJ_Base_Invoice_Item::TAX_RATE;

				if ( $this->invoice_object->is_vat_payer() ) { //FAKTURA VAT
					$tax = Product_Meta_Helper::get_invoices_vat_rate( $item[ 'id' ] );

					// Pobiera stawkę VAT z metaboxa
					if ( empty( $tax ) ) {
						$tax = Invoice_Tax_Payer_Helper::get_default_vat_rate(); // Jeżeli nie podano w metaboxie, ustal domyślną
					}
				} else if ( ! $this->invoice_object->is_vat_payer() || strtolower( $tax ) == 'zw' ) { //RACHUNEK
					$tax           = null; // Przy rachunku podatek VAT = zw
					$tax_qualifier = BPMJ_Base_Invoice_Item::TAX_EXEMPT;
				}

				$discount_amount = null;
				$discount_type   = 'none';
				if ( $item[ 'discount' ] > 0 ) {
					$discount_amount = number_format( $item[ 'discount' ], 2, '.', '' );
					$discount_type   = BPMJ_Base_Invoice_Item::DISCOUNT_AMOUNT;
				}

				$tax = apply_filters( 'bpmj_' . $this->service_short_id . '_edd_tax_product', $tax, $this->order_id );
				$this->invoice_object->add_product( $item_name, $item_price, BPMJ_Base_Invoice_Item::PRICE_GROSS_VALUE_AFTER_DISCOUNT, $item[ 'quantity' ], $tax, $tax_qualifier, $discount_amount, $discount_type, $item_additional_data, $gtu, $flat_rate_tax_symbol );
			}

			$fee_tax           = $this->default_vat;
			$fee_tax_qualifier = BPMJ_Base_Invoice_Item::TAX_RATE;
			if ( ! $this->invoice_object->is_vat_payer() || strtolower( $fee_tax ) == 'zw' ) {
				$fee_tax           = null;
				$fee_tax_qualifier = BPMJ_Base_Invoice_Item::TAX_EXEMPT;
				$fee_vat           = null;
			}

			$fee_tax = apply_filters( 'bpmj_' . $this->service_short_id . '_edd_tax_fee', $fee_tax, $this->order_id );
			$fees    = edd_get_payment_fees( $this->order_id );
			if ( ! empty( $fees ) ) {
				foreach ( $fees as $fee ) {
                    $fee_custom_tax_rate = $fee[Fees_API::TAX_RATE_FEE_INDEX] ?? null;

                    if($fee_custom_tax_rate) {
                        $fee_tax = $fee_custom_tax_rate;
                    }

					$this->invoice_object->add_product( $fee[ 'label' ], number_format( $fee[ 'amount' ], 2, '.', '' ), BPMJ_Base_Invoice_Item::PRICE_GROSS_VALUE_AFTER_DISCOUNT, 1, $fee_tax, $fee_tax_qualifier );
				}
			}
		}
	}
}
