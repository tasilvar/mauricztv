<?php


/**
 * Class BPMJ_EDDPC_Upgrades
 */
class BPMJ_EDDPC_Upgrades {

	/**
	 *
	 */
	const OPTION_KEY = 'bpmj_eddpc_version';

	/**
	 *
	 */
	const OPTION_KEY_UPGRADES = 'bpmj_eddpc_upgrades';

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
	const IS_UPGRADING_TRANSIENT = 'bpmj_eddpc_is_upgrading';

	/**
	 * @var BPMJ_EDDPC_Upgrades
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
		if ( 1 === version_compare( BPMJ_EDD_PC_VERSION, $this->current_version ) ) {
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
	 * @return BPMJ_EDDPC_Upgrades
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
				update_option( self::OPTION_KEY, BPMJ_EDD_PC_VERSION );
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
			'version'   => BPMJ_EDD_PC_VERSION,
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
	 * Issue #203 in WP Idea
	 * Issue #190 in WP Idea
	 *
	 * @return string|bool
	 */
	public function u0001_insert_price_ids_into_usermeta_v2() {
		global $wpdb;
		$meta_key            = '_bpmj_eddpc_access';
		$iteration           = $this->get_upgrade_iteration( __FUNCTION__ );
		$rows_processed_once = 50;
		$limit               = $rows_processed_once;
		$offset              = $iteration * $rows_processed_once;
		$results             = $wpdb->get_results( "SELECT user_id, meta_key, meta_value FROM {$wpdb->usermeta} WHERE meta_key = '$meta_key' ORDER BY umeta_id LIMIT $limit OFFSET $offset", ARRAY_A );
		if ( empty( $results ) ) {
			// All done
			return true;
		} else {
			foreach ( $results as $row ) {
				$user_id    = $row[ 'user_id' ];
				$meta_value = maybe_unserialize( $row[ 'meta_value' ] );
				foreach ( $meta_value as $download_id => $access ) {
					$price_ids = isset( $access[ 'price_id' ] ) ? $access[ 'price_id' ] : array();
					if ( edd_has_variable_prices( $download_id ) ) {
						foreach ( array_keys( edd_get_variable_prices( $download_id ) ) as $price_id ) {
							if ( bpmj_eddpc_has_user_purchased_single_or_in_bundle( $user_id, $download_id, $price_id ) ) {
								$price_ids[] = (int) $price_id;
							}
						}
					}
					$meta_value[ $download_id ][ 'price_id' ] = array_unique( $price_ids );
				}

				// This also upgrades user access information
				$did_action = did_action( 'updated_user_meta' );
				update_user_meta( $user_id, $meta_key, $meta_value );
				if ( $did_action === did_action( 'updated_user_meta' ) ) {
					// Nothing changed in the meta - need to trigger user access update manually
					BPMJ_EDDPC_User_Access::instance()->update_individual_meta_values( $user_id, $meta_value );
				}
			}

			return static::UPGRADE_STATUS_PARTIAL;
		}
	}

	/**
	 * Issue #302 in WP Idea
	 *
	 * @return bool|string
	 */
	public function u0002_fix_removed_users_from_courses() {
		global $wpdb;
		$rows_processed_once = 50;
		$limit               = $rows_processed_once;
		$results             = $wpdb->get_results( "
			SELECT p.ID as download_id, um.user_id FROM {$wpdb->posts} p 
			  JOIN {$wpdb->usermeta} um ON p.post_type = 'download' 
			   AND um.meta_key = '_bpmj_eddpc_access_to_download' 
			   AND p.ID = um.meta_value
			   AND NOT EXISTS (SELECT 1 FROM {$wpdb->usermeta} um2 
			   					WHERE um2.user_id = um.user_id 
			   					  AND um2.meta_key = '_bpmj_eddpc_access' 
			   					  AND um2.meta_value LIKE CONCAT('%', 'i:', p.ID, ';a:%')) 
             LIMIT $limit", ARRAY_A );
		if ( empty( $results ) ) {
			return true;
		}

		$meta_key_suffixes = array(
			'buy_time',
			'access_time',
			'total_time',
			'last_time',
			'price_id',
		);

		foreach ( $results as $row ) {
			$user_id     = $row[ 'user_id' ];
			$download_id = $row[ 'download_id' ];
			foreach ( $meta_key_suffixes as $suffix ) {
				$split_meta_key = '_bpmj_eddpc_' . $download_id . '_' . $suffix;
				delete_user_meta( $user_id, $split_meta_key );
			}
			delete_user_meta( $user_id, '_bpmj_eddpc_access_to_download', $download_id );
		}

		return static::UPGRADE_STATUS_PARTIAL;
	}
}