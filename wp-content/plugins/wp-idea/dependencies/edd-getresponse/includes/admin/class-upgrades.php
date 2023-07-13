<?php

namespace bpmj\wp\eddres\admin;

use bpmj\wp\eddres\Plugin;

/**
 * Class Upgrades
 */
class Upgrades {

	/**
	 *
	 */
	const OPTION_KEY = 'bpmj_eddres_version';

	/**
	 *
	 */
	const OPTION_KEY_UPGRADES = 'bpmj_eddres_upgrades';

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
	const IS_UPGRADING_TRANSIENT = 'bpmj_eddres_is_upgrading';

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
		if ( 1 === version_compare( BPMJ_EDDRES_VERSION, $this->current_version ) ) {
			$is_upgrading = get_transient( self::IS_UPGRADING_TRANSIENT );
			if ( ! $is_upgrading ) {
				return true;
			}
		}

		return false;
	}

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
				update_option( self::OPTION_KEY, BPMJ_EDDRES_VERSION );
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
			'version'   => BPMJ_EDDRES_VERSION,
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

	public function u0001_upgrade_to_v2() {
		global $edd_options, $wpdb;

		$lists = Plugin::instance()->get_getresponse_handler()->get_lists();
		if ( ! empty( $edd_options[ 'bpmj_eddres_register_user_list' ] ) ) {
			if ( ! key_exists( $edd_options[ 'bpmj_eddres_register_user_list' ], $lists ) && false !== ( $campaign_id = array_search( $edd_options[ 'bpmj_eddres_register_user_list' ], $lists ) ) ) {
				$edd_options[ 'bpmj_eddres_list' ] = (string) $campaign_id;
			}
		}

		unset( $edd_options[ 'bpmj_eddres_register_user_list' ], $edd_options[ 'bpmj_eddres_general_list' ] );

		update_option( 'edd_settings', $edd_options );

		$results = $wpdb->get_results( "SELECT * FROM {$wpdb->postmeta} WHERE `meta_key` IN ('edd_getresponse','edd_getresponse_delete') LIMIT 100", ARRAY_A );
		if ( empty( $results ) ) {
			return true;
		}

		foreach ( $results as $row ) {
			$post_id        = $row[ 'post_id' ];
			$meta_key       = $row[ 'meta_key' ];
			$meta_value     = maybe_unserialize( $row[ 'meta_value' ] );
			$new_meta_value = array();

			if ( $meta_value ) {
				foreach ( $meta_value as $campaign_name ) {
					$campaign_id = array_search( $campaign_name, $lists );
					if ( false !== $campaign_id ) {
						$new_meta_value[] = $campaign_id;
					}
				}
				switch ( $meta_key ) {
					case 'edd_getresponse':
						update_post_meta( $post_id, '_edd_getresponse', $new_meta_value );
						break;
					case 'edd_getresponse_delete':
						update_post_meta( $post_id, '_edd_getresponse_unsubscribe', $new_meta_value );
						break;
				}
			}

			delete_post_meta( $post_id, $meta_key );
		}

		return static::UPGRADE_STATUS_PARTIAL;
	}
}