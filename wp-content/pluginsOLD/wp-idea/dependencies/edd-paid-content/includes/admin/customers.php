<?php

/**
 * Dodanie karty z datatmi wygaśnięcia dostępu
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) { exit; }


/**
 * Dodanie zakładki ważności dostępu do karty użytkownika
 */
function bpmj_eddpc_customer_tab( $tabs ) {

	$customer_id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : false;
	$customer    = new EDD_Customer( $customer_id );
	$access_time = get_user_meta( $customer->user_id, "_bpmj_eddpc_access", true );

	if ( is_array( $access_time ) ) {

		$tabs['bpmj_eddpc_renewals'] = array( 'dashicon' => 'dashicons-clock', 'title' => __( 'Validity of access', 'edd-paid-content' ) );

	}


	return $tabs;
}
add_filter( 'edd_customer_tabs', 'bpmj_eddpc_customer_tab', 10, 1 );


/**
 * Dodanie widoku karty ważności dostępu
 */
function bpmj_eddpc_customer_view( $views ) {

	$customer_id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : false;
	$customer    = new EDD_Customer( $customer_id );
	$access_time = get_user_meta( $customer->user_id, "_bpmj_eddpc_access", true );

	if ( is_array( $access_time ) ) {

		$views['bpmj_eddpc_renewals'] = 'bpmj_eddpc_customer_renewals_view';

	}

	return $views;
}
add_filter( 'edd_customer_views', 'bpmj_eddpc_customer_view', 10, 1 );


/**
 * Dodanie tabelki z wynikami
 */
function bpmj_eddpc_customer_renewals_view( $customer ) {

	$access_time = get_user_meta( $customer->user_id, "_bpmj_eddpc_access", true );
	
	if ( is_array( $access_time ) ){
		foreach ( $access_time as $id => $access ){
			bpmj_eddpc_user_update_total_time( $customer->user_id, $id );
		}
	}
	
	$access_time = get_user_meta( $customer->user_id, "_bpmj_eddpc_access", true );
?>
	<div class="customer-notes-header">
		<div class="customer-view-avatar">
			<?php echo get_avatar( $customer->email, 80 ); ?> 
		</div>
		<div class="customer-view-desc">
			<span class="name"><?php echo $customer->name; ?></span>
			<span><?php echo $customer->email; ?></span>
		</div>
	</div>

	<?php if ( is_array( $access_time ) ) : ?>
	<div id="customer-tables-wrapper" class="customer-section">
		<h3><?php _e( 'Validity of access', 'edd-paid-content' ); ?></h3>

		<table class="wp-list-table widefat striped downloads">
			<thead>
				<tr>
					<th><?php echo edd_get_label_singular(); ?></th>
					<th><?php _e( 'Validity of access', 'edd-paid-content' ); ?></th>
					<th width="120px"><?php _e( 'Actions', 'edd-paid-content' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ( $access_time as $id => $access ) : ?>
				<tr>
					<td><a href="<?php echo esc_url( admin_url( 'post.php?action=edit&post=' . $id ) ); ?>"><?php echo get_the_title( $id ); ?></a></td>
					<td class="access-time-<?php echo $id; ?>">
						<?php 
							if( $access['access_time'] ){
								echo bpmj_eddpc_date_i18n( 'd.m.Y - H:i:s', $access['access_time'] ); 
							} else {
								_e( 'No limit', 'edd-paid-content' );
							}
						?>
					</td>
					<td>
						<a title="<?php esc_attr_e( 'View', 'edd-paid-content' ); ?>" href="<?php echo esc_url( admin_url( 'post.php?action=edit&post=' . $id ) ); ?>">
							<?php _e( 'View produkt', 'edd-paid-content' ); ?>
						</a><br />
						<a title="<?php esc_attr_e( 'Edit', 'edd-paid-content' ); ?>" href="#" data-action="edit-access" data-id="<?php echo $id; ?>">
							<?php _e( 'Edit', 'edd-paid-content' ); ?>
						</a><br />
						<a title="<?php esc_attr_e( 'Delete', 'edd-paid-content' ); ?>" href="#" data-action="delete-access" data-download-id="<?php echo $id; ?>" data-user-id="<?php echo $customer->user_id; ?>">
							<?php _e( 'Delete', 'edd-paid-content' ); ?>
						</a>
					</td>
				</tr>
				
				<tr class="download-<?php echo $id; ?> edit-access" style="display: none;">
					<td colspan="3">		
						<div class="column-container">
							<div class="half-column">
								<h3><?php _e( 'Edit access validity', 'edd-paid-content' ); ?></h3>
								
								<label>
									<input type="checkbox" data-action="no-limit-access" data-download-id="<?php echo $id; ?>" data-user-id="<?php echo $customer->user_id; ?>" <?php if( !$access['access_time'] ) echo 'checked'; ?>>
									Brak limitu
								</label>
								
								<div class="calendar" <?php if( !$access['access_time'] ) echo 'style="display: none;"'; ?>>
									<input id="datetimepicker-<?php echo $id; ?>" class="access-time-input" type="text" value="<?php if($access['access_time']) echo bpmj_eddpc_date_i18n( 'd.m.Y H:i:s', $access['access_time'] ); else echo 0; ?>">
									<button type="button" data-action="save-access" data-download-id="<?php echo $id; ?>" data-user-id="<?php echo $customer->user_id; ?>" class="button button-primary"><?php _e( 'Zapisz ważność dostępu', 'edd-paid-content' ); ?></button>
								</div>
							</div>
							
							<div class="half-column">
								<?php
									$minus = '';
								
									$time = $access['total_time'];
									if($time < 0) {
										$time = -$time;
										$minus = '-';
									}

									$days = floor($time / (60 * 60 * 24));
									$time -= $days * (60 * 60 * 24);
									
									$hours = floor($time / (60 * 60));
									$time -= $hours * (60 * 60);
									
									$minutes = floor($time / 60);
									$time -= $minutes * 60;
									
									$seconds = floor($time);
									$time -= $seconds;
								?>
								<h3><?php _e( 'Edit total time', 'edd-paid-content' ); ?></h3>
								<input id="total-time-<?php echo $id; ?>" class="widefat" type="text" value="<?php echo "{$minus}{$days} dni {$hours} h {$minutes} m {$seconds} s"; ?>">
								<label><?php _e( 'The time must be entered in the schedule:<br><b>0 dni 0 h 0 m 0 s</b>', 'edd-paid-content' ); ?></label>
								<button type="button" data-action="save-total-time" data-download-id="<?php echo $id; ?>" data-user-id="<?php echo $customer->user_id; ?>" class="button button-primary"><?php _e( 'Zapisz łączny czas', 'edd-paid-content' ); ?></button>
							</div>
						</div>
						
						<div class="loader-container" style="display: none;">
							<div class="loader">Loading...</div>
						</div>
					</td>
				</tr>
				
			<?php endforeach; ?>
			</tbody>
		</table>

	</div>
	<?php else: ?>
		<p><?php _e( 'No results!', 'edd-paid-content' ); ?><p>
	<?php endif;
}
