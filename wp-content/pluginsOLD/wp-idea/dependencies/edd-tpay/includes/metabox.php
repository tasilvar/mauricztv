<?php

if ( ! function_exists( 'edd_meta_box_recurring_payments_switch' ) ) {
	function edd_meta_box_recurring_payments_switch( $download_id = 0 ) {
		if ( edd_download_is_recurring( $download_id ) ) {
			$recurring_payments_enabled  = edd_recurring_payments_enabled_for_download( $download_id );
			$recurring_payments_possible = edd_recurring_payments_possible_for_download( $download_id );
			?>
			<p>
				<input type="hidden" name="_edd_recurring_payments_enabled" value=""/>
				<label>
					<input type="checkbox"
					       name="_edd_recurring_payments_enabled" <?php checked( $recurring_payments_enabled ); ?>
					       value="1"
						<?php disabled( ! $recurring_payments_possible ); ?>/>
					<?php _e( 'Check this box to enable recurring payments for this product', 'edd-tpay' ); ?>
				</label>
			</p>
			<?php
		}
	}

	add_action( 'edd_price_field', 'edd_meta_box_recurring_payments_switch' );
}

if ( ! function_exists( 'edd_meta_box_recurring_payments_switch_fields' ) ) {
	/**
	 * @param array $fields
	 *
	 * @return array
	 */
	function edd_meta_box_recurring_payments_switch_fields( $fields ) {
		$fields[] = '_edd_recurring_payments_enabled';

		return $fields;
	}

	add_filter( 'edd_metabox_fields_save', 'edd_meta_box_recurring_payments_switch_fields' );
}

if ( ! function_exists( 'edd_meta_box_recurring_payments_interval' ) ) {
	function edd_meta_box_recurring_payments_interval( $download_id = 0 ) {
		if ( edd_download_is_recurring( $download_id ) ) {
			$recurring_payments_interval = edd_recurring_get_interval( $download_id );
			$recurring_payments_possible = edd_recurring_payments_possible_for_download( $download_id );
			?>
			<input type="hidden" name="_edd_recurring_payments_interval" value="this value will be changed on save"/>
			<p>
				<label>
					<select
						name="_edd_recurring_payments_interval_number" <?php disabled( ! $recurring_payments_possible ); ?>>
						<?php foreach ( range( 1, 30 ) as $number ): ?>
							<option
								value="<?php echo $number; ?>" <?php selected( $number, false !== $recurring_payments_interval ? $recurring_payments_interval[ 'number' ] : false ); ?>>
								+<?php echo $number; ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<label>
					<select
						name="_edd_recurring_payments_interval_unit" <?php disabled( ! $recurring_payments_possible ); ?>>
						<?php foreach ( edd_recurring_get_interval_units() as $unit => $unit_name ): ?>
							<option
								value="<?php echo $unit; ?>" <?php selected( $unit, false !== $recurring_payments_interval ? $recurring_payments_interval[ 'unit' ] : 'months' ); ?>>
								<?php echo $unit_name; ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<?php _e( 'Set the interval between recurring payments for this item', 'edd-tpay' ); ?>
			</p>
			<?php
		}
	}

	add_action( 'edd_price_field', 'edd_meta_box_recurring_payments_interval' );
}

if ( ! function_exists( 'edd_meta_box_recurring_payments_switch_variable_head' ) ) {
	function edd_meta_box_recurring_payments_switch_variable_head( $download_id = 0 ) {
		if ( edd_download_is_recurring( $download_id ) ) {
			?>
			<th><?php _e( 'Recurring payments', 'edd-tpay' ); ?></th>
			<?php
		}
	}

	add_action( 'edd_download_price_table_head', 'edd_meta_box_recurring_payments_switch_variable_head' );
}

if ( ! function_exists( 'edd_meta_box_recurring_payments_switch_variable_row' ) ) {
	function edd_meta_box_recurring_payments_switch_variable_row( $download_id = 0, $price_id = null ) {
		if ( edd_download_is_recurring( $download_id ) ) {
			$recurring_payments_enabled  = edd_recurring_payments_enabled_for_download( $download_id, $price_id );
			$recurring_payments_possible = edd_recurring_payments_possible_for_download( $download_id, $price_id );
			?>
			<td>
				<input type="hidden" name="edd_variable_prices[<?php echo $price_id; ?>][recurring_payments_enabled]"
				       value=""/>
				<label>
					<input type="checkbox"
					       name="edd_variable_prices[<?php echo $price_id; ?>][recurring_payments_enabled]" <?php checked( $recurring_payments_enabled ); ?>
					       value="1"
						<?php disabled( ! $recurring_payments_possible ); ?>/>
					<?php _e( 'Enable', 'edd-tpay' ); ?>
				</label>
			</td>
			<?php
		}
	}

	add_action( 'edd_download_price_table_row', 'edd_meta_box_recurring_payments_switch_variable_row', 10, 2 );
}

if ( ! function_exists( 'edd_meta_box_recurring_payments_interval_variable_head' ) ) {
	function edd_meta_box_recurring_payments_interval_variable_head( $download_id = 0 ) {
		if ( edd_download_is_recurring( $download_id ) ) {
			?>
			<th><?php _e( 'Interval', 'edd-tpay' ); ?></th>
			<?php
		}
	}

	add_action( 'edd_download_price_table_head', 'edd_meta_box_recurring_payments_interval_variable_head' );
}

if ( ! function_exists( 'edd_meta_box_recurring_payments_interval_variable_row' ) ) {
	function edd_meta_box_recurring_payments_interval_variable_row( $download_id = 0, $price_id = null ) {
		if ( edd_download_is_recurring( $download_id ) ) {
			$recurring_payments_interval = edd_recurring_get_interval( $download_id, $price_id );
			$recurring_payments_possible = edd_recurring_payments_possible_for_download( $download_id, $price_id );
			?>
			<td>
				<label>
					<select
						name="edd_variable_prices[<?php echo $price_id; ?>][recurring_payments_interval_number]" <?php disabled( ! $recurring_payments_possible ); ?>>
						<?php foreach ( range( 1, 30 ) as $number ): ?>
							<option
								value="<?php echo $number; ?>" <?php selected( $number, false !== $recurring_payments_interval ? $recurring_payments_interval[ 'number' ] : false ); ?>>
								+<?php echo $number; ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<label>
					<select
						name="edd_variable_prices[<?php echo $price_id; ?>][recurring_payments_interval_unit]" <?php disabled( ! $recurring_payments_possible ); ?>>
						<?php foreach ( edd_recurring_get_interval_units() as $unit => $unit_name ): ?>
							<option
								value="<?php echo $unit; ?>" <?php selected( $unit, false !== $recurring_payments_interval ? $recurring_payments_interval[ 'unit' ] : 'months' ); ?>>
								<?php echo $unit_name; ?></option>
						<?php endforeach; ?>
					</select>
				</label>
			</td>
			<?php
		}
	}

	add_action( 'edd_download_price_table_row', 'edd_meta_box_recurring_payments_interval_variable_row', 10, 2 );
}

if ( ! function_exists( 'edd_meta_box_recurring_payments_interval_fields' ) ) {
	/**
	 * @param array $fields
	 *
	 * @return array
	 */
	function edd_meta_box_recurring_payments_interval_fields( $fields ) {
		$fields[] = '_edd_recurring_payments_interval';

		return $fields;
	}

	add_filter( 'edd_metabox_fields_save', 'edd_meta_box_recurring_payments_interval_fields' );
}

if ( ! function_exists( 'edd_meta_box_recurring_payments_interval_save' ) ) {
	/**
	 * @return string
	 */
	function edd_meta_box_recurring_payments_interval_save() {
		if ( ! empty( $_POST[ '_edd_recurring_payments_interval_number' ] ) && ! empty( $_POST[ '_edd_recurring_payments_interval_unit' ] ) ) {
			return $_POST[ '_edd_recurring_payments_interval_number' ] . ' ' . $_POST[ '_edd_recurring_payments_interval_unit' ];
		}

		return '';
	}

	add_filter( 'edd_metabox_save__edd_recurring_payments_interval', 'edd_meta_box_recurring_payments_interval_save' );
}

if ( ! function_exists( 'edd_meta_box_recurring_payments_interval_save_variable' ) ) {
	/**
	 * @param $variable_prices
	 *
	 * @return array
	 */
	function edd_meta_box_recurring_payments_interval_save_variable( $variable_prices ) {
		$new_variable_prices = array();
		if ( is_array( $variable_prices ) ) {
			foreach ( $variable_prices as $price_id => $variable_price ) {
				if ( ! empty( $variable_price[ 'recurring_payments_interval_number' ] ) && ! empty( $variable_price[ 'recurring_payments_interval_unit' ] ) ) {
					$variable_price[ 'recurring_payments_interval' ] = $variable_price[ 'recurring_payments_interval_number' ] . ' ' . $variable_price[ 'recurring_payments_interval_unit' ];
					unset( $variable_price[ 'recurring_payments_interval_number' ], $variable_price[ 'recurring_payments_interval_unit' ] );
				}				
				$new_variable_prices[ $price_id ] = $variable_price;
			}
		} else {
			$new_variable_prices = $variable_prices;
		}

		return $new_variable_prices;
	}

	add_filter( 'edd_metabox_save_edd_variable_prices', 'edd_meta_box_recurring_payments_interval_save_variable' );
}
