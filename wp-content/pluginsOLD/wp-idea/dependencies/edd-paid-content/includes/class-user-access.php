<?php

/**
 * Class BPMJ_EDDPC_User_Access
 */
class BPMJ_EDDPC_User_Access {
	/**
	 *
	 */
	const ACCESS_META_KEY = '_bpmj_eddpc_access';

	const USER_ACCESS_BASE_QUERY = /** @lang text */
		'
	    SELECT 
	            um.user_id,
	            p.ID product_id,
	            um_buy.meta_value buy_time_raw,
	            FROM_UNIXTIME(um_buy.meta_value) buy_time,
	            um_access.meta_value access_time_raw,
	            FROM_UNIXTIME(um_access.meta_value) access_time,
	            um_total.meta_value total_time_raw,
	            um_last.meta_value last_time_raw,
	            FROM_UNIXTIME(um_last.meta_value) last_time,
	            um_price.meta_value price_id,
	            op.option_value gmt_offset,
	            IF(um_access.meta_value IS NULL OR um_access.meta_value = \'\', 1, IF(UNIX_TIMESTAMP() < um_access.meta_value + IFNULL(op.option_value, 0) * 3600, 1, 0)) access_valid
	    FROM
	        {user_meta_table} um
	    JOIN {posts_table} p ON um.meta_key = \'_bpmj_eddpc_access_to_download\'
	        AND um.meta_value = p.ID
	    LEFT JOIN {user_meta_table} um_buy ON um_buy.user_id = um.user_id
	        AND um_buy.meta_key = CONCAT(\'_bpmj_eddpc_\', um.meta_value, \'_buy_time\')
	    LEFT JOIN {user_meta_table} um_access ON um_access.user_id = um.user_id
	        AND um_access.meta_key = CONCAT(\'_bpmj_eddpc_\', um.meta_value, \'_access_time\')
	    LEFT JOIN {user_meta_table} um_total ON um_total.user_id = um.user_id
	        AND um_total.meta_key = CONCAT(\'_bpmj_eddpc_\', um.meta_value, \'_total_time\')
	    LEFT JOIN {user_meta_table} um_last ON um_last.user_id = um.user_id
	        AND um_last.meta_key = CONCAT(\'_bpmj_eddpc_\', um.meta_value, \'_last_time\')
	    LEFT JOIN {user_meta_table} um_price ON um_price.user_id = um.user_id
	        AND um_price.meta_key = CONCAT(\'_bpmj_eddpc_\', um.meta_value, \'_price_id\')
	    LEFT JOIN {options_table} op ON op.option_name = \'gmt_offset\'';

	/**
	 * @var BPMJ_EDDPC_User_Access
	 */
	private static $instance;

	/**
	 * @var array
	 */
	protected $previous_meta_value = array();

	/**
	 * @var array
	 */
	protected $product_user_stats = array();

	/**
	 * BPMJ_EDDPC_User_Access constructor.
	 */
	protected function __construct() {
		add_action( 'update_user_meta', array( $this, 'hook_update_user_meta' ), 10, 3 );
		add_action( 'updated_user_meta', array( $this, 'hook_updated_user_meta' ), 10, 4 );
		add_action( 'added_user_meta', array( $this, 'hook_updated_user_meta' ), 10, 4 );
	}

	/**
	 * @return BPMJ_EDDPC_User_Access
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 * @param int $meta_id
	 * @param int $object_id
	 * @param string $meta_key
	 */
	public function hook_update_user_meta( $meta_id, $object_id, $meta_key ) {
		if ( static::ACCESS_META_KEY !== $meta_key ) {
			return;
		}
		$this->previous_meta_value = array();
		$previous_meta_value       = get_metadata_by_mid( 'user', $meta_id );
		if ( $previous_meta_value ) {
			$this->previous_meta_value = $previous_meta_value->meta_value;
		}
	}

	/**
	 * @param int $meta_id
	 * @param int $user_id
	 * @param string $meta_key
	 * @param array $meta_value
	 */
	public function hook_updated_user_meta( $meta_id, $user_id, $meta_key, $meta_value ) {
		if ( static::ACCESS_META_KEY !== $meta_key ) {
			return;
		}
		if ( empty( $this->previous_meta_value ) ) {
			$meta_difference = $meta_value;
		} else {
			$meta_difference = array_udiff_assoc( $meta_value, $this->previous_meta_value, function ( $array_a, $array_b ) {
				return strcmp( serialize( $array_a ), serialize( $array_b ) );
			} );
			foreach ( array_diff( array_keys( $this->previous_meta_value ), array_keys( $meta_value ) ) as $download_id_to_remove ) {
				$meta_difference[ $download_id_to_remove ] = 'remove';
			}
		}
		$this->update_individual_meta_values( $user_id, $meta_difference );
	}

	/**
	 * @param int $user_id
	 * @param array $meta_value
	 */
	public function update_individual_meta_values( $user_id, $meta_value ) {
		$user_meta_download_id_key = '_bpmj_eddpc_access_to_download';
		foreach ( $meta_value as $download_id => $access ) {
			$split_meta_array       = array(
				'buy_time'    => null,
				'access_time' => null,
				'total_time'  => null,
				'last_time'   => null,
				'price_id'    => array(),
			);
			$user_meta_download_ids = get_user_meta( $user_id, $user_meta_download_id_key );
			if ( 'remove' === $access ) {
				foreach ( array_keys( $split_meta_array ) as $meta_key_suffix ) {
					$split_meta_key = '_bpmj_eddpc_' . $download_id . '_' . $meta_key_suffix;
					delete_user_meta( $user_id, $split_meta_key );
				}
				if ( in_array( $download_id, $user_meta_download_ids ) ) {
					delete_user_meta( $user_id, $user_meta_download_id_key, $download_id );
				}
			} else {
				$split_meta_array = array_merge( $split_meta_array, array_intersect_key( $access, $split_meta_array ) );
				foreach ( $split_meta_array as $key => $value ) {
					$split_meta_key   = '_bpmj_eddpc_' . $download_id . '_' . $key;
					$split_meta_value = $value;
					update_user_meta( $user_id, $split_meta_key, $split_meta_value );
				}
				if ( ! in_array( $download_id, $user_meta_download_ids ) ) {
					add_user_meta( $user_id, $user_meta_download_id_key, $download_id );
				}
			}
		}
	}

	/**
	 * @return string
	 */
	public static function get_base_query() {
		global $wpdb;

		return str_replace( array(
			'{user_meta_table}',
			'{posts_table}',
			'{options_table}',
		), array(
			$wpdb->usermeta,
			$wpdb->posts,
			$wpdb->options,
		), static::USER_ACCESS_BASE_QUERY );
	}

	/**
	 * @param int $product_id
	 *
	 * @return array|null
	 */
	public function get_product_user_stats( $product_id ) {
		$this->load_product_user_stats_cache( array( $product_id ) );

		return isset( $this->product_user_stats[ $product_id ] ) ? $this->product_user_stats[ $product_id ] : null;
	}

	/**
	 * @param array $product_id_array
	 *
	 * @return array
	 */
	public function get_product_user_stats_multi( array $product_id_array ) {
		$this->load_product_user_stats_cache( $product_id_array );

		return array_intersect_key( $this->product_user_stats, array_flip( $product_id_array ) );
	}

	/**
	 * @param array $product_id_array
	 */
	protected function load_product_user_stats_cache( $product_id_array ) {
		global $wpdb;

		$products_to_load = array_diff( $product_id_array, array_keys( $this->product_user_stats ) );
		$products_to_load = array_filter( array_map( 'intval', $products_to_load ) );
		if ( empty( $products_to_load ) ) {
			return;
		}

		$wpdb->query('SET SQL_BIG_SELECTS=1');
		$query = '
			SELECT 
			    t.product_id,
			    SUM(1) all_users_count,
			    SUM(access_valid) access_valid_count
			FROM
			    (' . static::get_base_query() . ') t
		    WHERE
                t.product_id IN (' . implode( ',', $products_to_load ) . ')
			GROUP BY t.product_id';

		foreach ( $wpdb->get_results( $query, ARRAY_A ) as $row ) {
			$this->product_user_stats[ $row[ 'product_id' ] ] = $row;
		}
		foreach ( $products_to_load as $product_id ) {
			if ( ! isset( $this->product_user_stats[ $product_id ] ) ) {
				$this->product_user_stats[ $product_id ] = array(
					'product_id'         => (string) $product_id,
					'all_users_count'    => (string) 0,
					'access_valid_count' => (string) 0,
				);
			}
		}
	}

	/**
	 * @param int $product_id
	 * @param bool $only_valid
	 *
	 * @return array
	 */
	public function get_product_user_list( $product_id, $only_valid = false ) {
		global $wpdb;

		$wpdb->query('SET SQL_BIG_SELECTS=1');
		$query = '
			SELECT 
			    t.user_id
			FROM
			    (' . static::get_base_query() . ') t
			WHERE
			    t.product_id = ' . ( (int) $product_id );
		if ( $only_valid ) {
			$query .= ' AND t.access_valid = 1 ';
		}

		return array_map( 'intval', array_unique( $wpdb->get_col( $query ) ) );
	}

	/**
	 * @param int $user_id
	 * @param bool $only_valid
	 *
	 * @return array
	 */
	public function get_user_product_list( $user_id, $only_valid = false ) {
		global $wpdb;

		$wpdb->query('SET SQL_BIG_SELECTS=1');
		$query = '
			SELECT 
			    t.product_id
			FROM
			    (' . static::get_base_query() . ') t
			WHERE
			    t.user_id = ' . ( (int) $user_id );
		if ( $only_valid ) {
			$query .= ' AND t.access_valid = 1 ';
		}

		return array_map( 'intval', array_unique( $wpdb->get_col( $query ) ) );
	}
}