<?php

/**
 * Data rozpoczecia udostępniania
 *
 * @since       1.4.9
 * @param       int $post_id The ID of this download
 * @return      void
 */
function bpmj_eddpc_metabox_start_date( $post_id = 0 ) {

	$bpmj_eddpc_access_start_enabled = get_post_meta( $post_id, '_bpmj_eddpc_access_start_enabled', true ) ? true : false;
	$bpmj_eddpc_access_start		 = esc_attr( get_post_meta( $post_id, '_bpmj_eddpc_access_start', true ) );
	$bpmj_eddpc_access_start_display = $bpmj_eddpc_access_start_enabled ? '' : ' style="display:none;"';

	if ( !empty( $bpmj_eddpc_access_start ) && !strtotime( $bpmj_eddpc_access_start ) ) {
		$bpmj_eddpc_access_start = '';
	}
	?>

	<script type="text/javascript">jQuery( document ).ready( function ( $ ) {
			$( "#bpmj_eddpc_access_start_enabled" ).on( "click", function () {
				$( ".bpmj-eddpc-access-start-toggled-hide" ).toggle();
			} )
	        } );</script>

	<p>
		<input type="checkbox" name="_bpmj_eddpc_access_start_enabled" id="bpmj_eddpc_access_start_enabled" value="1" <?php echo checked( true, $bpmj_eddpc_access_start_enabled, false ); ?> />
		<label for="bpmj_eddpc_access_start_enabled"><?php esc_html_e( 'Check to activate the start date of content sharing', 'edd-paid-content' ); ?></label>
	</p>

	<div <?php echo $bpmj_eddpc_access_start_display; ?> class="bpmj-eddpc-access-start-toggled-hide">
		<input type="text" name="_bpmj_eddpc_access_start" class="medium-text" value="<?php echo $bpmj_eddpc_access_start; ?>" />
	</div>

	<?php
}

add_action( 'edd_meta_box_fields', 'bpmj_eddpc_metabox_start_date' );

/*
 *  ============================================================================
 *  SINGLE
 *  ============================================================================
 */

/**
 * Dodanie pola umożiwającego zdefiniowanie casu dostępu dla danego produktu (single)
 *
 * @access      public
 * @since       1.3
 * @return      void
 */
function bpmj_eddpc_single_price_access_time( $download_id ) {

	$access_time		 = bpmj_eddpc_get_access_time_single( $download_id );
	$access_time_unit	 = bpmj_eddpc_get_access_time_unit_single( $download_id );
	?>

	<div class="bpmj_eddpc_access_time">
		<label class="edd-label" for="_bpmj_eddpc_access_time"><?php _e( 'Access Time', 'edd-paid-content' ); ?>:</label>
		<input type="number" step="1" name="_bpmj_eddpc_access_time" id="bpmj_eddpc_access_time" size="4" style="width: 60px" value="<?php echo $access_time; ?>" />

		<label class="edd-label" for="_bpmj_eddpc_access_time_unit"><?php _e( 'measured in', 'edd-paid-content' ); ?>:</label>
		<select name="_bpmj_eddpc_access_time_unit" id="bpmj_eddpc_access_time_unit">
			<option value="minutes"<?php
			selected( 'minutes', $access_time_unit, true );
			echo '>';
			_e( 'minutes', 'edd-paid-content' );
			?></option>
			<option value="hours"<?php
			selected( 'hours', $access_time_unit, true );
			echo '>';
			_e( 'hours', 'edd-paid-content' );
			?></option>
			<option value="days"<?php
			selected( 'days', $access_time_unit, true );
			echo '>';
			_e( 'days', 'edd-paid-content' );
			?></option>
			<option value="months"<?php
			selected( 'months', $access_time_unit, true );
			echo '>';
			_e( 'months', 'edd-paid-content' );
			?></option>
			<option value="years"<?php
			selected( 'years', $access_time_unit, true );
			echo '>';
			_e( 'years', 'edd-paid-content' );
			?></option>
		</select>
	</div>
	<?php
}

add_action( 'edd_price_field', 'bpmj_eddpc_single_price_access_time', 20 );

/**
 * Zapis meta dla EDD (single)
 *
 * @access      public
 * @since       1.3
 * @return      array
 */
function bpmj_eddpc_save_single( $fields ) {
	$fields[]	 = '_bpmj_eddpc_access_time';
	$fields[]	 = '_bpmj_eddpc_access_time_unit';
	$fields[]	 = '_bpmj_eddpc_access_start_enabled';
	$fields[]	 = '_bpmj_eddpc_access_start';

	return $fields;
}

add_filter( 'edd_metabox_fields_save', 'bpmj_eddpc_save_single' );

/*
 *  ============================================================================
 *  VARIABLE
 *  ============================================================================
 */

/**
 * Dodanie pola umożiwającego zdefiniowanie czasu dostępu dla danego produktu (variable)
 *
 * @access      public
 * @since       1.3
 * @return      void
 */
function bpmj_eddpc_option_price_access_time( $download_id, $price_id, $args ) {

	if ( 'bundle' == edd_get_download_type( $download_id ) ) {
		return;
	}

	$access_time		 = bpmj_eddpc_get_access_time_variable( $download_id, $price_id );
	$access_time_unit	 = bpmj_eddpc_get_access_time_unit_variable( $download_id, $price_id );
	?>
	<td class="bpmj-eddpc-access-time">
		<input type="number" min="0" step="1" name="edd_variable_prices[<?php echo $price_id; ?>][access_time]" id="edd_variable_prices[<?php echo $price_id; ?>][access_time]" size="4" style="width: 70px" value="<?php echo absint( $access_time ); ?>" />
	</td>
	<td class="bpmj-eddpc-access-time-unit">
		<select name="edd_variable_prices[<?php echo $price_id; ?>][access_time_unit]" id="edd_variable_prices[<?php echo $price_id; ?>][access_time_unit]">
			<option value="minutes"<?php
			echo selected( 'minutes', $access_time_unit, true );
			echo '>';
			_e( 'minutes', 'edd-paid-content' );
			?></option>
			<option value="hours"<?php
			selected( 'hours', $access_time_unit, true );
			echo '>';
			_e( 'hours', 'edd-paid-content' );
			?></option>
			<option value="days"<?php
			selected( 'days', $access_time_unit, true );
			echo '>';
			_e( 'days', 'edd-paid-content' );
			?></option>
			<option value="months"<?php
			selected( 'months', $access_time_unit, true );
			echo '>';
			_e( 'months', 'edd-paid-content' );
			?></option>
			<option value="years"<?php
			selected( 'years', $access_time_unit, true );
			echo '>';
			_e( 'years', 'edd-paid-content' );
			?></option>
		</select>
	</td>
	<?php
}

add_action( 'edd_download_price_table_row', 'bpmj_eddpc_option_price_access_time', 800, 3 );

/**
 * Minuty - nagłówek tabeli (variable)
 *
 * @access      public
 * @since       1.3
 * @return      void
 */
function bpmj_eddpc_access_time_header( $download_id ) {

	if ( 'bundle' == edd_get_download_type( $download_id ) ) {
		return;
	}
	?>
	<th><?php _e( 'Access Time', 'edd-paid-content' ); ?></th>
	<th><?php _e( 'measured in', 'edd-paid-content' ); ?></th>
	<?php
}

add_action( 'edd_download_price_table_head', 'bpmj_eddpc_access_time_header', 800 );
?>
