<?php
// Zakończ, jeśli wczytano bezpośrednio
if ( !defined( 'ABSPATH' ) )
	exit;

function bpmj_eddpc_add_meta_box() {
	global $post;

	$post_types		 = get_post_types( array( 'show_ui' => true ) );
	$excluded_types	 = array( 'download', 'edd_payment', 'reply', 'acf', 'deprecated_log', 'courses' );

	$title = __( 'EDD Paid Content', 'edd-paid-content' );
	if ( defined( 'BPMJ_EDDCM_NAME_LOCALIZED' ) ) {
		$title = BPMJ_EDDCM_NAME_LOCALIZED;
	}
	
	//if ( !in_array( get_post_type( $post->ID ), apply_filters( 'bpmj_eddpc_excluded_post_types', $excluded_types ) ) ) {
		add_meta_box(
		'bpmj-eddpc-metabox', $title, 'bpmj_eddpc_render_meta_box', array( 'post', 'page' ), 'normal', 'default'
		);
	//}
}

add_action( 'add_meta_boxes', 'bpmj_eddpc_add_meta_box' );

function bpmj_eddpc_render_meta_box( $post_id ) {
	global $post;

	$downloads		 = get_posts( array( 'post_type' => 'download', 'posts_per_page' => -1 ) );
	$restricted_to	 = get_post_meta( $post->ID, '_bpmj_eddpc_restricted_to', true );
	$redirect_page	 = get_post_meta( $post->ID, '_bpmj_eddpc_redirect_page', true );
	$redirect_url	 = get_post_meta( $post->ID, '_bpmj_eddpc_redirect_url', true );
	$drip_value		 = get_post_meta( $post->ID, '_bpmj_eddpc_drip_value', true );
	$drip_unit		 = get_post_meta( $post->ID, '_bpmj_eddpc_drip_unit', true );

	if ( $downloads ) {
		?>
		<div id="bpmj-eddpc-options" class="edd_meta_table_wrap bpmj_eddpc_meta_table_wrap">
			<p><strong><?php echo __( 'Set access to this content only for buyers of Digital Products.', 'edd-paid-content' ); ?></strong></p>
			<table class="widefat edd_repeatable_table" width="100%" cellpadding="0" cellspacing="0">
				<thead>
				<th><?php echo edd_get_label_singular(); ?></th>
				<th><?php echo __( 'Option', 'edd-paid-content' ); ?></th>
				<?php do_action( 'bpmj_eddpc_table_head', $post_id ); ?>
				<th style="width: 2%"></th>
				</thead>
				<tbody>
					<?php
					if ( !empty( $restricted_to ) && is_array( $restricted_to ) ) {
						foreach ( $restricted_to as $key => $value ) {
							echo '<tr class="bpmj-eddpc-option-wrapper edd_repeatable_row" data-key="' . absint( $key ) . '">';
							do_action( 'bpmj_eddpc_render_option_row', $key, $post_id );
							echo '</tr>';
						}
					} else {
						echo '<tr class="bpmj-eddpc-option-wrapper edd_repeatable_row">';
						do_action( 'bpmj_eddpc_render_option_row', 0, $post_id );
						echo '</tr>';
					}
					?>
					<tr>
						<td class="submit" colspan="4" style="float: none; clear:both; background:#fff;">
							<a class="button-secondary edd_add_repeatable" style="margin: 6px 0;"><?php _e( 'Dodaj kolejny Produkt Cyfrowy', 'edd-paid-content' ); ?></a>
						</td>
					</tr>
				</tbody>
			</table>
			<table class="widefat edd_repeatable_table" width="100%" cellpadding="0" cellspacing="0">
				<thead><tr><th colspan="2"><?php _e( 'Additional settings', 'edd-paid-content' ); ?></th></tr></thead>
				<tr>
					<td>
						<?php _e( 'Time from purchase after which to make the content available', 'edd-paid-content' ); ?>
					</td>
					<td>
						<input name="bpmj_eddpc_drip_value" class="text" type="number" step="1" value="<?php echo $drip_value; ?>" />

						<label class="edd-label" for="bpmj_eddpc_drip_unit"><?php _e( 'measured in', 'edd-paid-content' ); ?>:</label>
						<select name="bpmj_eddpc_drip_unit" id="bpmj_eddpc_access_time_unit">
							<option value="minutes"<?php
							selected( 'minutes', $drip_unit, true );
							echo '>';
							_e( 'minutes', 'edd-paid-content' );
							?></option>
							<option value="hours"<?php
							selected( 'hours', $drip_unit, true );
							echo '>';
							_e( 'hours', 'edd-paid-content' );
							?></option>
							<option value="days"<?php
									selected( 'days', $drip_unit, true );
									echo '>';
									_e( 'days', 'edd-paid-content' );
									?></option>
							<option value="months"<?php
					selected( 'months', $drip_unit, true );
					echo '>';
					_e( 'months', 'edd-paid-content' );
					?></option>
							<option value="years"<?php
					selected( 'years', $drip_unit, true );
					echo '>';
					_e( 'years', 'edd-paid-content' );
					?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
							<?php _e( 'Redirect a user who does not have access to the site', 'edd-paid-content' ); ?>
					</td>
					<td>
						<select name="bpmj_eddpc_redirect_page" class="large-text">
							<option value="none" <?php selected( $redirect_page, 'none' ); ?>>-</option>
		<?php
		// Pobierz wszystkie strony
		$all_pages = get_posts( array( 'post_type' => 'page', 'posts_per_page' => -1 ) );

		$pages_c = count( $all_pages );
		for ( $i = 0; $i < $pages_c; $i++ ) {
			?>
								<option <?php echo $redirect_page == $all_pages[ $i ]->ID ? 'selected="selected"' : ''; ?> value="<?php echo $all_pages[ $i ]->ID; ?>"> <?php echo $all_pages[ $i ]->post_title; ?></option>
		<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
		<?php _e( 'or redirect it to the URL', 'edd-paid-content' ); ?>
					</td>
					<td>
						<input name="bpmj_eddpc_redirect_url" class="large-text" type="text" value="<?php echo $redirect_url; ?>" />
					</td>
				</tr>
			</table>
		</div>
		<?php
	}
}

function bpmj_eddpc_render_option_row( $key, $post ) {
    // plain downloads here (no bundles)
	$downloads		 = get_posts( array( 'post_type' => 'download', 'posts_per_page' => -1, 'meta_key' => '_edd_product_type', 'meta_value' => 'bundle', 'meta_compare' => 'NOT EXISTS' ) );
	$restricted_to	 = get_post_meta( $post->ID, '_bpmj_eddpc_restricted_to', true );
	$download_id	 = isset( $restricted_to[ $key ][ 'download' ] ) ? $restricted_to[ $key ][ 'download' ] : 0;
	?>
	<td>
		<select name="bpmj_eddpc_download[<?php echo $key; ?>][download]" id="bpmj_eddpc_download[<?php echo $key; ?>][download]" class="bpmj_eddpc_download" data-key="<?php echo esc_attr( $key ); ?>">
			<option value=""><?php echo __( 'Lack', 'edd-paid-content' ); ?></option>
			<option value="any"<?php selected( 'any', $download_id ); ?>><?php echo __( 'Customers who purchased any Digital Product', 'edd-paid-content' ); ?></option>
		<?php
		foreach ( $downloads as $download ) {
			echo '<option value="' . absint( $download->ID ) . '" ' . selected( $download_id, $download->ID, false ) . '>' . esc_html( get_the_title( $download->ID ) ) . '</option>';
		}
		?>
		</select>
	</td>
	<td>
		<?php
		if ( isset( $restricted_to[ $key ][ 'price_id' ] ) && edd_has_variable_prices( $restricted_to[ $key ][ 'download' ] ) ) {
			$prices = edd_get_variable_prices( $restricted_to[ $key ][ 'download' ] );
			echo '<select class="edd_price_options_select edd-select edd-select bpmj_eddpc_download" name="bpmj_eddpc_download[' . $key . '][price_id]">';
			echo '<option value="all" ' . selected( 'all', $restricted_to[ $key ][ 'price_id' ], false ) . '>' . __( 'All prices', 'edd-paid-content' ) . '</option>';
			foreach ( $prices as $id => $data ) {
				echo '<option value="' . absint( $id ) . '" ' . selected( $id, $restricted_to[ $key ][ 'price_id' ], false ) . '>' . esc_html( $data[ 'name' ] ) . '</option>';
			}
			echo '</select>';
			echo '<p class="bpmj_eddpc_variable_none" style="display: none;">' . __( 'Lack', 'edd-paid-content' ) . '</p>';
		} else {
			echo '<p class="bpmj_eddpc_variable_none">' . __( 'Lack', 'edd-paid-content' ) . '</p>';
		}
		?>
		<img src="<?php echo admin_url( '/images/wpspin_light.gif' ); ?>" class="waiting bpmj_eddpc_loading" style="display:none;"/>
	</td>
	<td>
		<a href="#" class="edd_remove_repeatable" data-type="price" style="background: url(<?php echo admin_url( '/images/xit.gif' ); ?>) no-repeat;">&times;</a>
	</td>
	<?php
	do_action( 'bpmj_eddpc_metabox', $post->ID, $restricted_to, null );
	echo wp_nonce_field( 'bpmj-eddpc-nonce', 'bpmj-eddpc-nonce' );
}

add_action( 'bpmj_eddpc_render_option_row', 'bpmj_eddpc_render_option_row', 10, 3 );

function bpmj_eddpc_save_meta_data( $post_id ) {

	if ( !isset( $_POST[ 'bpmj_eddpc_download' ] ) || !is_array( $_POST[ 'bpmj_eddpc_download' ] ) ) {
		return;
	}

	if ( !isset( $_POST[ 'bpmj-eddpc-nonce' ] ) || !wp_verify_nonce( $_POST[ 'bpmj-eddpc-nonce' ], 'bpmj-eddpc-nonce' ) ) {
		return;
	}

	if ( !empty( $_POST[ 'bpmj_eddpc_download' ] ) ) {
		// Grab the items this post was previously restricted to and remove related meta
		$previous_items = get_post_meta( $post_id, '_bpmj_eddpc_restricted_to', true );
		if ( $previous_items ) {
			foreach ( $previous_items as $item ) {
				if ( 'any' !== $item[ 'download' ] ) {
					delete_post_meta( $item[ 'download' ], '_bpmj_eddpc_protected_post', $post_id );

					// Remove them from product
					$products = get_post_meta( $item[ 'download' ], 'edd_download_files', true );
					foreach ( $products as $key => $product ) {
						if ( $key == $post_id ) {
							unset( $products[ $key ] );
						}
					}
					update_post_meta( $item[ 'download' ], 'edd_download_files', $products );
				}
			}
		}
		$has_items = false;
		foreach ( $_POST[ 'bpmj_eddpc_download' ] as $item ) {
			if ( 'any' !== $item[ 'download' ] && !empty( $item[ 'download' ] ) ) {
				$saved_ids = get_post_meta( $item[ 'download' ], '_bpmj_eddpc_protected_post' );
				if ( !in_array( $post_id, $saved_ids ) ) {
					add_post_meta( $item[ 'download' ], '_bpmj_eddpc_protected_post', $post_id );
				}
				$has_items = true;
			} else if ( 'any' == $item[ 'download' ] ) {
				$has_items = true;
			}

			if ( strpos( get_permalink( $post_id ), '-revision-' ) === false ) {
				$products = get_post_meta( $item[ 'download' ], 'edd_download_files', true );

				if ( !empty( $products ) ) {
					continue;
				}

				$condition = isset( $item[ 'price_id' ] ) ? $item[ 'price_id' ] : 'all';

				$products[ $post_id ] = array(
					'index'			 => count( $products ) + 1,
					'name'			 => get_the_title( $post_id ),
					'file'			 => get_permalink( $post_id ),
					'attachment_id'	 => 0,
					'condition'		 => $condition
				);

				update_post_meta( $item[ 'download' ], 'edd_download_files', $products );
			}
		}
		if ( $has_items ) {
			update_post_meta( $post_id, '_bpmj_eddpc_restricted_to', $_POST[ 'bpmj_eddpc_download' ] );
		} else {
			delete_post_meta( $post_id, '_bpmj_eddpc_restricted_to' );
		}
	} else {
		delete_post_meta( $post_id, '_bpmj_eddpc_restricted_to' );

		$products = get_post_meta( $item[ 'download' ], 'edd_download_files', true );
		unset( $products[ $post_id ] );
		update_post_meta( $item[ 'download' ], 'edd_download_files', $products );
	}


	update_post_meta( $post_id, '_bpmj_eddpc_redirect_page', $_POST[ 'bpmj_eddpc_redirect_page' ] );
	update_post_meta( $post_id, '_bpmj_eddpc_redirect_url', $_POST[ 'bpmj_eddpc_redirect_url' ] );
	update_post_meta( $post_id, '_bpmj_eddpc_drip_value', $_POST[ 'bpmj_eddpc_drip_value' ] );
	update_post_meta( $post_id, '_bpmj_eddpc_drip_unit', $_POST[ 'bpmj_eddpc_drip_unit' ] );

	do_action( 'bpmj_eddpc_save_meta_data', $post_id, $_POST );
}

add_action( 'save_post', 'bpmj_eddpc_save_meta_data' );

/**
 * Remove unnecessary fields for recurring payments setup
 */
function bpmj_eddpc_remove_recurring_fields() {
	remove_action( 'edd_meta_box_fields', 'edd_meta_box_recurring_payments_interval' );
	remove_action( 'edd_price_field', 'edd_meta_box_recurring_payments_interval' );
	remove_filter( 'edd_metabox_fields_save', 'edd_meta_box_recurring_payments_interval_fields' );
	remove_filter( 'edd_metabox_save__edd_recurring_payments_interval', 'edd_meta_box_recurring_payments_interval_save' );
	remove_action( 'edd_download_price_table_head', 'edd_meta_box_recurring_payments_interval_variable_head' );
	remove_filter( 'edd_download_price_table_row', 'edd_meta_box_recurring_payments_interval_variable_row', 10 );
	remove_filter( 'edd_metabox_save_edd_variable_prices', 'edd_meta_box_recurring_payments_interval_save_variable' );
}

add_action( 'init', 'bpmj_eddpc_remove_recurring_fields' );
