<?php

/**
 * Class is creating metabox in EDD product edit page
 */

if (!defined('ABSPATH'))
	exit;


class BPMJ_EDD_Sell_Discount_Product_Metabox
{

	/**
	 * Product ID
	 * @var $id
	 */
	protected $id;

	/**
	 * @var BPMJ_EDD_Sell_Discount_Product_Metabox
	 */
	protected static $instance;

	/**
	 * BPMJ_EDD_Sell_Discount_Product_Metabox constructor.
	 */
	protected function __construct() {
		if ( apply_filters( 'bpmj_edd_sell_discount_enabled', true ) ) {
			add_action( 'add_meta_boxes', array( $this, 'add' ) );
			add_action( 'save_post_download', array( $this, 'save' ), 10, 3 );
			add_action( 'save_post_courses', array( $this, 'save' ), 10, 3 );
		}
	}

	/**
	 * @return BPMJ_EDD_Sell_Discount_Product_Metabox
	 */
	public static function instance() {
		if ( ! isset( static::$instance ) ) {
			static::$instance = new static();
		}

		return static::$instance;
	}


	/**
	 * Add new custom metabox to product edit page
	 */
	public function add() {
		$this->id = get_the_id();
		add_meta_box( 'edd-sell-discount-metabox', __( 'Discount codes', BPMJ_EDDCM_DOMAIN ), array(
			$this,
			'html'
		), array( 'download', 'courses' ), 'normal', 'high', null );
	}


	/**
	 * Custom metabox html markup
	 */
	public function html() {
		if ( ! isset( $this->id ) ) {
			$this->id = get_the_ID();
		}
		require_once BPMJ_EDD_SELL_DISCOUNT_DIR . 'admin/html/html.product-metabox.php';
	}

	/**
	 * Custom metabox html markup displayed for post id, dedicated for Courses metabox
	 */
	public function html_id( $id ) {
		$this->id = $id;
		require_once BPMJ_EDD_SELL_DISCOUNT_DIR . 'admin/html/html.course-metabox.php';
	}


	/**
	 * Get saved Shoplo Product ID
	 * @return integer
	 */
	protected function get_selected_code() {
		return get_post_meta( $this->id, '_edd-sell-discount-code', true );
	}


	/**
	 * Get product discount time value
	 * @return integer
	 */
	protected function get_time() {
		return get_post_meta( $this->id, '_edd-sell-discount-time', true );
	}

	/**
	 * Get product discount time type value
	 * @return string
	 */
	protected function get_time_type() {
		return get_post_meta( $this->id, '_edd-sell-discount-time-type', true );
	}


	/**
	 * Generate select field with time types
	 */
	protected function discount_time_types() {
		$types = array(
			array(
				'value' => 'days',
				'html'  => __( 'Days', BPMJ_EDDCM_DOMAIN )
			),
			array(
				'value' => 'weeks',
				'html'  => __( 'Weeks', BPMJ_EDDCM_DOMAIN )
			),
			array(
				'value' => 'months',
				'html'  => __( 'Months', BPMJ_EDDCM_DOMAIN )
			)
		);

		echo '<select name="edd-sell-discount-time-type" id="edd-sell-discount-time-type">';
		echo '<option value="">' . __( 'The duration of', BPMJ_EDDCM_DOMAIN ) . '</option>';

		foreach ( $types as $type ) {
			$selected = $this->get_time_type() == $type[ "value" ] ? 'selected="selected"' : '';
			echo '<option value=' . $type[ "value" ] . ' ' . $selected . '>' . $type[ "html" ] . '</option>';
		}

		echo '</select>';
	}


	/**
	 * Generate select field with available discount codes
	 */
	protected function discount_codes() {

		// Get discounts
		$args = array(
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key'     => '_edd_sell_discount_code',
					'value'   => '1',
					'compare' => '!='
				),
				array(
					'key'     => '_edd_sell_discount_code',
					'compare' => 'NOT EXISTS'
				),
			),
		);

		$discount_codes = edd_get_discounts( apply_filters( 'bpmj_edd_sell_discount_discounts_query_args', $args ) );


		// Generate select field
		echo '<select name="edd-sell-discount-code" id="edd-sell-discount-code">';

		// If no available codes to use
		if ( empty( $discount_codes ) ) {
			echo '<option value="no-code" selected>' . __( 'No coupons to use', BPMJ_EDDCM_DOMAIN ) . '</option>';

		} else {
			echo '<option value="">' . __( 'Select', BPMJ_EDDCM_DOMAIN ) . '</option>';

			foreach ( $discount_codes as $code ) {

				$code_type  = get_post_meta( $code->ID, '_edd_discount_type', true ) == 'percent' ? '%' : edd_currency_symbol();
				$code_value = get_post_meta( $code->ID, '_edd_discount_amount', true );
				$selected   = $this->get_selected_code() == $code->ID ? 'selected="selected"' : '';

				echo '<option value="' . $code->ID . '" ' . $selected . '>' . $code->post_title . ' (' . $code_value . $code_type . ')</option>';
			}
		}
		echo '</select>';
	}


	/**
	 * Save all fields form custom metabox
	 *
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function save( $post_id ) {
		if ( ! isset( $_POST[ 'edd-sell-discount-nonce' ] ) || ! wp_verify_nonce( $_POST[ 'edd-sell-discount-nonce' ], basename( BPMJ_EDD_SELL_DISCOUNT_DIR ) ) ) {
			return $post_id;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}


		// Save coupon id
		$code_id = isset( $_POST[ 'edd-sell-discount-code' ] ) ? esc_html( $_POST[ 'edd-sell-discount-code' ] ) : '';
		update_post_meta( $post_id, '_edd-sell-discount-code', $code_id );

		// Coupon time
		$code_time = isset( $_POST[ 'edd-sell-discount-time' ] ) ? esc_html( $_POST[ 'edd-sell-discount-time' ] ) : '';
		update_post_meta( $post_id, '_edd-sell-discount-time', $code_time );

		// Coupon time type
		$code_time_type = isset( $_POST[ 'edd-sell-discount-time-type' ] ) ? esc_html( $_POST[ 'edd-sell-discount-time-type' ] ) : '';
		update_post_meta( $post_id, '_edd-sell-discount-time-type', $code_time_type );

		return $post_id;
	}


}

BPMJ_EDD_Sell_Discount_Product_Metabox::instance();