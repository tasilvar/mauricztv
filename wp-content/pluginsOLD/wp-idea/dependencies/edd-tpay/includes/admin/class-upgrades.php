<?php

namespace bpmj\wp\eddtpay\admin;

use bpmj\wp\eddtpay\gateways\TpayRecurrence;

/**
 * Class Upgrades
 */
class Upgrades {

	/**
	 *
	 */
	const OPTION_KEY = 'bpmj_eddtpay_version';

	/**
	 *
	 */
	const OPTION_KEY_UPGRADES = 'bpmj_eddtpay_upgrades';

	/**
	 *
	 */
	const UPGRADE_METHOD_PATTERN = '/^u(\d{4})\_.+/';

	/**
	 *
	 */
	const UPGRADE_STATUS_PARTIAL = 'partial';

	/**
	 *
	 */
	const IS_UPGRADING_TRANSIENT = 'bpmj_eddtpay_is_upgrading';

	/**
	 * @var Upgrades
	 */
	protected static $instance;

	/**
	 * @var array
	 */
	protected $upgrades;

	/**
	 * @var string
	 */
	protected $current_version;

	/**
	 * @return bool
	 */
	public function is_upgrade_needed() {
		if ( 1 === version_compare( BPMJ_TRA_EDD_VERSION, $this->current_version ) ) {
			$is_upgrading = get_transient( self::IS_UPGRADING_TRANSIENT );
			if ( ! $is_upgrading ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * bpmj\wpidea\admin\BPMJ_EDDCM_Upgrades constructor
	 */
	private function __construct() {
		$this->current_version = get_option( self::OPTION_KEY, '0.0.0.0' );
		$this->upgrades        = get_option( self::OPTION_KEY_UPGRADES, array() );
	}

	/**
	 * @return Upgrades
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @return array
	 */
	public function get_pending_upgrades() {
		$methods          = get_class_methods( $this );
		$pending_upgrades = array();
		foreach ( $methods as $method ) {
			$method_entry = isset( $this->upgrades[ $method ] ) ? $this->upgrades[ $method ] : null;
			if ( 1 === preg_match( self::UPGRADE_METHOD_PATTERN, $method ) && ( ! $method_entry || static::UPGRADE_STATUS_PARTIAL === $method_entry[ 'result' ] ) ) {
				$pending_upgrades[] = $method;
			}
		}
		sort( $pending_upgrades );

		return $pending_upgrades;
	}

	/**
	 * @param bool $mark_only
	 */
	public function auto_upgrade( $mark_only = false ) {
		if ( $this->is_upgrade_needed() ) {
			set_transient( self::IS_UPGRADING_TRANSIENT, true, 60 );
			do_action( 'bpmj_eddpc_before_upgrade' );
			$upgrade_finished = $this->do_pending_upgrades( $mark_only );
			if ( $upgrade_finished ) {
				do_action( 'bpmj_eddpc_after_upgrade' );
				update_option( self::OPTION_KEY, BPMJ_TRA_EDD_VERSION );
			}
			delete_transient( self::IS_UPGRADING_TRANSIENT );
		}
	}

	/**
	 * @param bool $mark_only
	 *
	 * @return bool
	 */
	public function do_pending_upgrades( $mark_only = false ) {
		$anything_partial = false;
		foreach ( $this->get_pending_upgrades() as $upgrade_method ) {
			$result = null;
			if ( ! $mark_only ) {
				$result = $this->{$upgrade_method}();
				if ( static::UPGRADE_STATUS_PARTIAL === $result ) {
					$anything_partial = true;
				}
			}
			$this->mark_upgrade_as_complete( $upgrade_method, $result );
		}

		return ! $anything_partial;
	}

	/**
	 * @param string $upgrade_method
	 * @param bool|mixed $result
	 */
	private function mark_upgrade_as_complete( $upgrade_method, $result ) {
		$this->upgrades[ $upgrade_method ] = array(
			'date'      => date( 'Y-m-d H:i:s' ),
			'version'   => BPMJ_TRA_EDD_VERSION,
			'result'    => $result,
			'iteration' => $this->get_upgrade_iteration( $upgrade_method ) + 1,
		);
		update_option( self::OPTION_KEY_UPGRADES, $this->upgrades );
	}

	/**
	 * @param string $upgrade_method
	 *
	 * @return int
	 */
	private function get_upgrade_iteration( $upgrade_method ) {
		if ( ! isset( $this->upgrades[ $upgrade_method ] ) ) {
			return 0;
		}
		if ( ! isset( $this->upgrades[ $upgrade_method ][ 'iteration' ] ) ) {
			return 0;
		}

		return (int) $this->upgrades[ $upgrade_method ][ 'iteration' ];
	}

	/*******************************
	 * ADD UPGRADE FUNCTIONS BELOW *
	 *******************************/

	/**
	 * Find all pending payments and convert "_last_try" meta keys into "_next_try" ones
	 */
	public function u0001_convert_last_try_to_next_try_v2() {
		/** @var \WP_Post[] $payments */
		$payments = edd_get_payments( array(
			'status'     => 'pending',
			'meta_query' => array(
				'_tpay_payment_subtype'    => array(
					'key'   => '_tpay_payment_subtype',
					'value' => 'recurrent',
				),
				'_tpay_recurrent_last_try' => array(
					'key'     => '_tpay_recurrent_last_try',
					'compare' => 'EXISTS',
				),
				'_tpay_cli_auth'           => array(
					'key'     => '_tpay_cli_auth',
					'compare' => 'EXISTS',
				),
			),
			'number'     => 20,
		) );

		if ( empty( $payments ) ) {
			return true;
		}

		foreach ( $payments as $payment ) {
			$last_try = get_post_meta( $payment->ID, '_tpay_recurrent_last_try', true );
			delete_post_meta( $payment->ID, '_tpay_recurrent_last_try' );
			$next_try = date( 'Y-m-d H:i:s', strtotime( '+12 hours', strtotime( $last_try ) ) );

			update_post_meta( $payment->ID, '_tpay_recurrent_next_try', $next_try );
		}

		return static::UPGRADE_STATUS_PARTIAL;
	}
	
	public function u0002_recurrence_data_repair_v2(): void
	{
	    require_once BPMJ_TRA_EDD_DIR . '/includes/gateways/tpay-recurrence.php';
	    $correctable_payments = TpayRecurrence::hotfix_pb_457_correctable_list();
	    foreach ($correctable_payments as $payment_id) {
	        TpayRecurrence::hotfix_pb_457_do_correction($payment_id);
	    }
	}
	
}