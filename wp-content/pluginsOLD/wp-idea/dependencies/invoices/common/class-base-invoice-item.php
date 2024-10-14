<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BPMJ_Base_Invoice_Item' ) ) {

	class BPMJ_Base_Invoice_Item {

		/**
		 * Unit price qualifier
		 */
		const PRICE_NET_UNIT_PRICE_BEFORE_DISCOUNT = 'net_unit_price_before_discount';

		/**
		 * Unit price qualifier
		 */
		const PRICE_NET_UNIT_PRICE_AFTER_DISCOUNT = 'net_unit_price_after_discount';

		/**
		 * Gross unit price qualifier
		 */
		const PRICE_GROSS_UNIT_PRICE_AFTER_DISCOUNT = 'gross_unit_price_after_discount';

		/**
		 * Net value before discount qualifier
		 */
		const PRICE_NET_VALUE_BEFORE_DISCOUNT = 'net_value_before_discount';

		/**
		 * Net value after discount qualifier
		 */
		const PRICE_NET_VALUE_AFTER_DISCOUNT = 'net_value_after_discount';

		/**
		 * Gross value after discount qualifier
		 */
		const PRICE_GROSS_VALUE_AFTER_DISCOUNT = 'gross_value_after_discount';

		/**
		 * Rate qualifier for $tax field
		 */
		const TAX_RATE = 'rate';

		/**
		 * Amount qualifier for $tax field
		 */
		const TAX_AMOUNT = 'amount';

		/**
		 * Exempt qualifier for $tax field
		 */
		const TAX_EXEMPT = 'exempt';

		/**
		 * No discount qualifier
		 */
		const DISCOUNT_NONE = 'none';

		/**
		 * Percent qualifier for $discount field
		 */
		const DISCOUNT_PERCENT = 'percent';

		/**
		 * Flat amount qualifier for $discount field
		 */
		const DISCOUNT_AMOUNT = 'amount';

		/**
		 * @var string
		 */
		protected $name = '';

		/**
		 * @var string
		 */
		protected $net_unit_price_before_discount = null;

		/**
		 * @var string
		 */
		protected $net_unit_price_after_discount = null;

		/**
		 * @var int
		 */
		protected $quantity = 1;

		/**
		 * @var string
		 */
		protected $net_value_before_discount = null;

		/**
		 * @var string
		 */
		protected $discount_qualifier = 'none';

		/**
		 * @var string
		 */
		protected $discount_percent = null;

		/**
		 * @var string
		 */
		protected $discount_amount = null;

		/**
		 * @var string
		 */
		protected $net_value_after_discount = null;

		/**
		 * @var string
		 */
		protected $tax_qualifier = 'rate';

		/**
		 * @var string
		 */
		protected $tax_rate = null;

		/**
		 * @var string
		 */
		protected $tax_amount = null;

		/**
		 * @var string
		 */
		protected $gross_unit_price_after_discount = null;

		/**
		 * @var string
		 */
		protected $gross_value_after_discount = null;

		/**
		 * @var string
		 */
		protected $gross_value_before_discount = null;

		/**
		 * @var array
		 */
		protected $additional_data = array();

        /**
         * @var string
         */
        protected $gtu;

        /**
         * @var string
         */
        protected $flat_rate_tax_symbol;

		public function __construct( $name, $price, $price_qualifier, $quantity, $tax, $tax_qualifier = 'rate', $discount = null, $discount_qualifier = 'none', $additional_data = array(), $gtu = null, $flat_rate_tax_symbol = null ) {
			$this->set_name( $name );
			$this->set_quantity( $quantity );
			$this->set_additional_data( $additional_data );
			$this->set_discount_qualifier( $discount_qualifier );
			$this->set_discount_amount( static::DISCOUNT_AMOUNT === $discount_qualifier ? $discount : '0.00' );
			$this->set_discount_percent( static::DISCOUNT_PERCENT === $discount_qualifier ? $discount : '0' );
			$this->set_tax_qualifier( $tax_qualifier );
			$this->set_tax_amount( static::TAX_AMOUNT === $tax_qualifier ? $tax : '0.00' );
			$this->set_tax_rate( static::TAX_RATE === $tax_qualifier ? $tax : '0' );
			switch ( $price_qualifier ) {
				case static::PRICE_NET_UNIT_PRICE_BEFORE_DISCOUNT:
					$this->set_net_unit_price_before_discount( $price );
					break;
				case static::PRICE_NET_UNIT_PRICE_AFTER_DISCOUNT:
					$this->set_net_unit_price_after_discount( $price );
					break;
				case static::PRICE_GROSS_UNIT_PRICE_AFTER_DISCOUNT:
					$this->set_gross_unit_price_after_discount( $price );
					break;
				case static::PRICE_NET_VALUE_BEFORE_DISCOUNT:
					$this->set_net_value_before_discount( $price );
					break;
				case static::PRICE_NET_VALUE_AFTER_DISCOUNT:
					$this->set_net_value_after_discount( $price );
					break;
				case static::PRICE_GROSS_VALUE_AFTER_DISCOUNT:
					$this->set_gross_value_after_discount( $price );
					break;
			}
			$this->set_gtu($gtu);
			$this->set_flat_rate_tax_symbol($flat_rate_tax_symbol);
		}

		/**
		 * @return string
		 */
		public function get_name() {
			return $this->name;
		}

		/**
		 * @param string $name
		 */
		public function set_name( $name ) {
			$this->name = $name;
		}

		/**
		 * @return string
		 */
		public function get_net_unit_price_before_discount() {
			return $this->net_unit_price_before_discount;
		}

		/**
		 * @param string $net_unit_price_before_discount
		 */
		public function set_net_unit_price_before_discount( $net_unit_price_before_discount ) {
			$this->net_unit_price_before_discount = $net_unit_price_before_discount;
			$this->set_net_value_before_discount( $this->bcmul( $net_unit_price_before_discount, $this->get_quantity(), 2 ) );
		}

		/**
		 * @return string
		 */
		public function get_net_unit_price_after_discount() {
			return $this->net_unit_price_after_discount;
		}

		/**
		 * @param string $net_unit_price_after_discount
		 */
		public function set_net_unit_price_after_discount( $net_unit_price_after_discount ) {
			$this->net_unit_price_after_discount = $net_unit_price_after_discount;
			$this->set_net_value_after_discount( $this->bcmul( $net_unit_price_after_discount, $this->get_quantity(), 2 ) );
		}

		/**
		 * @return int
		 */
		public function get_quantity() {
			return $this->quantity;
		}

		/**
		 * @param int $quantity
		 */
		public function set_quantity( $quantity ) {
			$this->quantity = $quantity;
		}

		/**
		 * @return string
		 */
		public function get_net_value_before_discount() {
			return $this->net_value_before_discount;
		}

		/**
		 * @param string $net_value_before_discount
		 */
		public function set_net_value_before_discount( $net_value_before_discount ) {
			$this->net_value_before_discount = $net_value_before_discount;
			if ( ! $this->net_unit_price_before_discount ) {
				$this->net_unit_price_before_discount = $this->calculate_net_unit_price( $net_value_before_discount, $this->get_quantity() );
			}
			$net_value_after_discount = $net_value_before_discount;
			switch ( $this->discount_qualifier ) {
				case static::DISCOUNT_AMOUNT:
					$net_value_after_discount = bcsub( $net_value_before_discount, $this->get_discount_amount(), 2 );
					$this->set_discount_percent( $this->bcdiv( $this->bcmul( $this->get_discount_amount(), 100, 2 ), $net_value_before_discount, 2 ) );
					break;
				case static::DISCOUNT_PERCENT:
					$net_value_after_discount = bcsub( $net_value_before_discount, $this->bcmul( $net_value_before_discount, $this->bcdiv( $this->get_discount_percent(), 100, 4 ), 2 ), 2 );
					$this->set_discount_amount( bcsub( $net_value_before_discount, $net_value_after_discount, 2 ) );
					break;
			}
			$this->set_net_value_after_discount( $net_value_after_discount );
		}

		/**
		 * @return string
		 */
		public function get_discount_percent() {
			return $this->discount_percent;
		}

		/**
		 * @param string $discount_percent
		 */
		public function set_discount_percent( $discount_percent ) {
			$this->discount_percent = $discount_percent;
		}

		/**
		 * @return string
		 */
		public function get_discount_amount() {
			return $this->discount_amount;
		}

		/**
		 * @param string $discount_amount
		 */
		public function set_discount_amount( $discount_amount ) {
			$this->discount_amount = $discount_amount;
		}

		/**
		 * @return string
		 */
		public function get_net_value_after_discount() {
			return $this->net_value_after_discount;
		}

		/**
		 * @param string $net_value_after_discount
		 */
		public function set_net_value_after_discount( $net_value_after_discount ) {
			$this->net_value_after_discount = $net_value_after_discount;
			if ( ! $this->net_value_before_discount ) {
				$this->calculate_net_value_before_discount( $net_value_after_discount );
				$this->net_unit_price_before_discount = $this->calculate_net_unit_price( $this->net_value_before_discount, $this->get_quantity() );
				$this->net_unit_price_after_discount  = $this->calculate_net_unit_price( $net_value_after_discount, $this->get_quantity() );
			}
			$gross_value_after_discount = $net_value_after_discount;
			switch ( $this->tax_qualifier ) {
				case static::TAX_AMOUNT:
					$gross_value_after_discount = bcadd( $net_value_after_discount, $this->tax_amount, 2 );
					$this->tax_rate             = $this->bcdiv( $this->bcmul( $this->tax_amount, 100, 2 ), $net_value_after_discount, 2 );
					break;
				case static::TAX_RATE:
					$gross_value_after_discount = bcadd( $net_value_after_discount, $this->bcmul( $net_value_after_discount, $this->bcdiv( $this->tax_rate, 100, 4 ), 2 ), 2 );
					$this->tax_amount           = bcsub( $gross_value_after_discount, $net_value_after_discount, 2 );
					break;
			}
			$this->set_gross_value_after_discount( $gross_value_after_discount );
		}

		/**
		 * @return string
		 */
		public function get_tax_rate() {
			return $this->tax_rate;
		}

		/**
		 * @param string $tax_rate
		 */
		public function set_tax_rate( $tax_rate ) {
			$this->tax_rate = $tax_rate;
		}

		/**
		 * @return string
		 */
		public function get_tax_amount() {
			return $this->tax_amount;
		}

		/**
		 * @param string $tax_amount
		 */
		public function set_tax_amount( $tax_amount ) {
			$this->tax_amount = $tax_amount;
		}

		/**
		 * @return string
		 */
		public function get_gross_unit_price_after_discount() {
			return $this->gross_unit_price_after_discount;
		}

		/**
		 * @param string $gross_unit_price_after_discount
		 */
		public function set_gross_unit_price_after_discount( $gross_unit_price_after_discount ) {
			$this->gross_unit_price_after_discount = $gross_unit_price_after_discount;
			$this->set_gross_value_after_discount( $this->bcmul( $gross_unit_price_after_discount, $this->get_quantity(), 2 ) );
		}

		/**
		 * @return string
		 */
		public function get_gross_value_after_discount() {
			return $this->gross_value_after_discount;
		}

		/**
		 * @param string $gross_value_after_discount
		 */
		public function set_gross_value_after_discount( $gross_value_after_discount ) {
			$this->gross_value_after_discount = $gross_value_after_discount;
			if ( ! $this->net_value_after_discount ) {
				switch ( $this->tax_qualifier ) {
					case static::TAX_EXEMPT:
						$this->net_value_after_discount = $gross_value_after_discount;
						break;
					case static::TAX_AMOUNT:
						$this->net_value_after_discount = bcsub( $gross_value_after_discount, $this->tax_amount, 2 );
						$this->tax_rate                 = $this->bcdiv( $this->bcmul( $this->tax_amount, 100, 0 ), $this->net_value_after_discount, 2 );
						break;
					case static::TAX_RATE:
                        $tax_rate = (int)$this->tax_rate; // $this->tax_rate moze miec wartosc "np" lub "23%"
						$this->net_value_after_discount = $this->bcmul( $this->bcdiv( $gross_value_after_discount, bcadd( 100, $tax_rate, 2 ), 4 ), 100, 2 );
						$this->tax_amount               = bcsub( $gross_value_after_discount, $this->net_value_after_discount, 2 );
						break;
				}
				$this->calculate_net_value_before_discount( $this->net_value_after_discount );
				$this->net_unit_price_before_discount  = $this->calculate_net_unit_price( $this->net_value_before_discount, $this->get_quantity() );
				$this->net_unit_price_after_discount   = $this->calculate_net_unit_price( $this->net_value_after_discount, $this->get_quantity() );
				$this->gross_unit_price_after_discount = $this->bcdiv( $gross_value_after_discount, $this->get_quantity(), 2 );
			}
			$this->gross_value_before_discount = bcadd( $this->net_value_before_discount, $this->bcmul( $this->net_value_before_discount, $this->bcdiv( $this->tax_rate, 100, 4 ), 2 ), 2 );
		}

		/**
		 * @return string
		 */
		public function get_gross_value_before_discount() {
			return $this->gross_value_before_discount;
		}

		/**
		 * @param string $gross_value_before_discount
		 */
		public function set_gross_value_before_discount( $gross_value_before_discount ) {
			$this->gross_value_before_discount = $gross_value_before_discount;
		}

		/**
		 * @param string $key
		 *
		 * @return mixed
		 */
		public function get_additional_data( $key = null ) {
			if ( $key ) {
				return isset( $this->additional_data[ $key ] ) ? $this->additional_data[ $key ] : null;
			}

			return $this->additional_data;
		}

		/**
		 * @param array $additional_data
		 */
		public function set_additional_data( $additional_data ) {
			$this->additional_data = $additional_data;
		}

		/**
		 * @param string $discount_qualifier
		 */
		public function set_discount_qualifier( $discount_qualifier ) {
			$this->discount_qualifier = $discount_qualifier;
		}

		/**
		 * @param string $tax_qualifier
		 */
		public function set_tax_qualifier( $tax_qualifier ) {
			$this->tax_qualifier = $tax_qualifier;
		}

		/**
		 * @param string $net_value
		 * @param string $quantity
		 *
		 * @return string
		 */
		private function calculate_net_unit_price( $net_value, $quantity ) {
			return $this->bcdiv( $net_value, $quantity, 2 );
		}

		/**
		 * @param string $net_value_after_discount
		 */
		private function calculate_net_value_before_discount( $net_value_after_discount ) {
			$this->net_value_before_discount = '0.00';
			switch ( $this->discount_qualifier ) {
				case static::DISCOUNT_NONE:
					$this->net_value_before_discount = $net_value_after_discount;
					break;
				case static::DISCOUNT_AMOUNT:
					$this->net_value_before_discount = bcadd( $net_value_after_discount, $this->discount_amount, 2 );
					$this->discount_percent          = $this->bcdiv( $this->bcmul( $this->discount_amount, 100, 2 ), $this->net_value_before_discount, 2 );
					break;
				case static::DISCOUNT_PERCENT:
					$this->net_value_before_discount = $this->bcmul( $this->bcdiv( $net_value_after_discount, bcsub( 100, $this->discount_percent, 2 ), 2 ), 100, 2 );
					$this->discount_amount           = bcsub( $this->net_value_before_discount, $net_value_after_discount, 2 );
					break;
			}
		}

		/**
		 * bcmul emulation with rounding
		 *
		 * @param string $left_operand
		 * @param string $right_operand
		 * @param int $scale
		 *
		 * @return string
		 */
		private function bcmul( $left_operand, $right_operand, $scale ) {
			if ( $scale === 0 ) {
				return bcmul( $left_operand, $right_operand, 0 );
			}
			$magnitude           = pow( 10, $scale );
			$precision_magnitude = pow( 10, $scale * 2 );
			$int_left_operand    = (int) bcmul( $precision_magnitude, $left_operand, 0 );
			$int_right_operand   = (int) bcmul( $precision_magnitude, $right_operand, 0 );
			$int_result          = (int) round( $int_left_operand * $int_right_operand / $precision_magnitude / $magnitude );
			$result              = bcdiv( $int_result, $magnitude, $scale );

			return $result;
		}

		/**
		 * bcdiv emulation with rounding
		 *
		 * @param string $left_operand
		 * @param string $right_operand
		 * @param int $scale
		 *
		 * @return string
		 */
		public function bcdiv( $left_operand, $right_operand, $scale ) {
		    $right_operand     = $this->sanitize_operand($right_operand);
		    $left_operand      = $this->sanitize_operand($left_operand);
			$magnitude         = pow( 10, $scale );
			$int_left_operand  = (int) bcmul( $magnitude, $left_operand, 0 );
			$int_right_operand = (int) bcmul( $magnitude, $right_operand, 0 );
			$int_result        = (int) round( $magnitude * $int_left_operand / $int_right_operand );
			$result            = bcdiv( $int_result, $magnitude, $scale );

			return $result;
		}

		private function sanitize_operand($operand): float
        {
            if (is_string($operand)) {
                $operand = str_replace(',','.',$operand);
            }
            return (float)$operand;
        }

		public function set_gtu(?string $gtu): void
        {
            $this->gtu = $gtu;
        }

        public function set_flat_rate_tax_symbol(?string $flat_rate_tax_symbol): void
        {
            $this->flat_rate_tax_symbol = $flat_rate_tax_symbol;
        }

        public function get_flat_rate_tax_symbol(): ?string
        {
            return $this->flat_rate_tax_symbol ?? null;
        }

        public function get_gtu(): ?string
        {
            return $this->gtu ?? null;
        }
	}
}
