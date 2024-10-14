<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Dodanie opcji wysyłki do ustawień
 *
 * @return array of settings.
 */
function bpmj_eddpc_add_settings( $settings ) {
	$shipping_admin_settings = array(
		array(
			'id'   => 'paid_content',
			'name' => '<strong id="homepay_area">' . __( 'Settings<br>EDD Paid Content', 'edd-paid-content' ) . '</strong>',
			'desc' => __( 'Manage the settings of EDD Paid Content', 'edd-paid-content' ),
			'type' => 'header'
		),
		array(
			'id'   => 'bpmj_renewal_discount',
			'name' => __( 'Discount codes', 'edd-paid-content' ),
			'desc' => __( 'Select if you want to generate discount codes. This code can be added to the reminder.', 'edd-paid-content' ),
			'type' => 'checkbox',
			'size' => 'regular'
		),
		array(
			'id'   => 'paid_content_renewal_discount_value_type',
			'name' => __( 'Value and type of discount code', 'edd-paid-content' ),
			'desc' => __( 'Select the value of the discount code and its type (percentage or amount).', 'edd-paid-content' ),
			'type' => 'renewal_discount',
			'size' => 'regular'
		),
		array(
			'id'      => 'bpmj_renewal_discount_time',
			'name'    => __( 'Discount code validity period', 'edd-paid-content' ),
			'desc'    => __( 'Determine how long the discount code should be valid from the moment it is generated.', 'edd-paid-content' ),
			'type'    => 'select',
			'options' => array(
				'+1day'    => __( 'One day', 'edd-paid-content' ),
				'+2days'   => __( 'Two days', 'edd-paid-content' ),
				'+3days'   => __( 'Three days', 'edd-paid-content' ),
				'+5days'   => __( 'Five days', 'edd-paid-content' ),
				'+1week'   => __( 'One week', 'edd-paid-content' ),
				'+2weeks'  => __( 'Two weeks', 'edd-paid-content' ),
				'+1month'  => __( 'One month', 'edd-paid-content' ),
				'no-limit' => __( 'No time limit', 'edd-paid-content' )
			),
			'size'    => 'regular',
		),
		array(
			'id'   => 'bpmj_expired_access_report_email',
			'name' => __( 'Email address where reports will be sent', 'edd-paid-content' ),
			'desc' => __( 'A report about expired user subscriptions will be sent to this email every day.<br>Leave this blank if you don’t want to receive this report.', 'edd-paid-content' ),
			'type' => 'text',
			'size' => 'regular'
		),
		array(
			'id'   => 'paid_content_renewal_times',
			'name' => __( 'Reminder Hours', 'edd-paid-content' ),
			'desc' => __( 'What time should notifications be sent? The minimum interval is 5 hours.', 'edd-paid-content' ),
			'type' => 'renewal_times',
			'size' => 'regular'
		),
		array(
			'id'   => 'paid_content_renewal',
			'name' => __( 'Reminders', 'edd-paid-content' ),
			'desc' => __( 'Set reminders for users about expiring access time to the content.', 'edd-paid-content' ),
			'type' => 'renewal',
			'size' => 'regular'
		),
		array(
			'id'   => 'disable_demo_sales',
			'name' => __( 'Disable demo sales', 'edd-paid-content' ),
			'type' => 'checkbox',
			'desc' => __( 'Demo product is a product that costs 0. Each demo can be purchased only once per email address.', 'edd-paid-content' ),
		)
	);

	return array_merge( $settings, $shipping_admin_settings );
}

add_filter( 'edd_settings_extensions', 'bpmj_eddpc_add_settings' );


/**
 * Renewal Callback
 *
 * Renderuje pole przypomnień w Ustawienia -> Dodatki
 *
 * @param array $args Arguments passed by the setting
 *
 * @global $edd_options Array of all the EDD Options
 * @return void
 */
function edd_renewal_callback( $args ) {

	$add_url         = esc_url( admin_url( 'edit.php?post_type=download&page=bpmj-eddpc-add-renewal' ) );
	$renewal_options = get_option( 'bmpj_eddpc_renewal' );
	?>
    <table id="edd_paid_content_renewal" class="wp-list-table widefat fixed posts">
        <thead>
        <tr>
            <th class="type"><?php _e( 'Reminder type', 'edd-paid-content' ); ?></th>
            <th class="subject"><?php _e( 'Subject', 'edd-paid-content' ); ?></th>
            <th class="send-period"><?php _e( 'Sending period', 'edd-paid-content' ); ?></th>
            <th class="actions"><?php _e( 'Actions', 'edd-paid-content' ); ?></th>
        </tr>
        </thead>

		<?php if ( is_array( $renewal_options ) ) { ?>
            <tbody>
			<?php
			foreach ( $renewal_options as $key => $option ) {
				$edit_url   = esc_url( admin_url( 'edit.php?post_type=download&page=bpmj-eddpc-edit-renewal&renewal-id=' . $key ) );
				$delete_url = add_query_arg( array( 'bpmj_eddpc_action' => 'delete-renewal', 'renewal-id' => $key ) );
				$type       = ! empty( $option[ 'type' ] ) ? $option[ 'type' ] : 'renewal';
				?>
                <tr>
                    <td><?php echo $type === 'renewal' ? __( 'Renewal', 'edd-paid-content' ) : __( 'Payment', 'edd-paid-content' ); ?></td>
                    <td><?php echo $option[ 'subject' ]; ?></td>
                    <td><?php echo bpmj_eddpc_renewal_period_description( $option[ 'send_period' ], $type ); ?></td>
                    <td>
                        <a class="bpmj-eddpc-renewal-edit"
                           href="<?php echo $edit_url; ?>"><?php _e( 'Edit', 'edd-paid-content' ); ?></a> |
                        <a class="bpmj-eddpc-renewal-delete"
                           href="<?php echo $delete_url; ?>"><?php _e( 'Delete', 'edd-paid-content' ); ?></a>
                    </td>
                </tr>
			<?php } ?>
            </tbody>
		<?php } ?>

    </table>
    <p>
        <a href="<?php echo $add_url; ?>" class="button-secondary"
           id="edd_paid_content_add_renewal"><?php _e( 'Add a renewal reminder', 'edd-paid-content' ); ?></a>
		<?php
		$add_payment_notice_label = __( 'Add a payment reminder', 'edd-paid-content' );
		if ( bpmj_eddpc_recurring_payments_possible() ):
			?>
            <a href="<?php echo $add_url; ?>&amp;bpmj-renewal-type=payment" class="button-secondary"
               id="edd_paid_content_add_payment_notice"><?php echo $add_payment_notice_label; ?></a>
		<?php
		else:
			?>
            <button disabled="disabled" class="button-secondary"
                    title="<?php esc_attr_e( 'You cannot add reminders - none of the enabled payment methods supports recurring payments.', 'edd-paid-content' ); ?>"><?php echo $add_payment_notice_label; ?></button>
		<?php
		endif;
		?>
    </p>
    <label><?php echo $args[ 'desc' ]; ?></label>
	<?php
}


/**
 * Renewal Times Callback
 *
 * Renderuje pole wyboru godzin
 */
function edd_renewal_times_callback( $args ) {
	global $edd_options;
	?>

	<?php _e( 'Od', 'bpmj_eddpc' ); ?> <input type="text" id="timeStart"
                                              value="<?php echo isset( $edd_options[ 'bpmj_renewals_start' ] ) ? $edd_options[ 'bpmj_renewals_start' ] : 14; ?>">
	<?php _e( 'do', 'bpmj_eddpc' ); ?> <input type="text" id="timeEnd"
                                              value="<?php echo isset( $edd_options[ 'bpmj_renewals_end' ] ) ? $edd_options[ 'bpmj_renewals_end' ] : 19; ?>">
	<?php _e( 'every day.', 'bpmj_eddpc' ); ?>

    <label><?php echo $args[ 'desc' ]; ?></label>
	<?php
}


/**
 * Renewal Discount Callback
 *
 * Renderuje pole wyboru wartości i typu zniżki
 */
function edd_renewal_discount_callback( $args ) {
	global $edd_options;
	?>

    <input type="number" name="edd_settings[bpmj_renewal_discount_value]"
           value="<?php echo isset( $edd_options[ 'bpmj_renewal_discount_value' ] ) ? $edd_options[ 'bpmj_renewal_discount_value' ] : ''; ?>">
    <select name="edd_settings[bpmj_renewal_discount_type]">
        <option value="percent" <?php echo isset( $edd_options[ 'bpmj_renewal_discount_type' ] ) && $edd_options[ 'bpmj_renewal_discount_type' ] == 'percent' ? 'selected' : ''; ?>>
            %
        </option>
        <option value="flat" <?php echo isset( $edd_options[ 'bpmj_renewal_discount_type' ] ) && $edd_options[ 'bpmj_renewal_discount_type' ] == 'flat' ? 'selected' : ''; ?>><?php echo edd_get_currency(); ?></option>
    </select>

    <label><?php echo $args[ 'desc' ]; ?></label>
	<?php
}


/**
 * Add Commissions link
 * *
 * @access      private
 * @since       1.0
 * @return      void
 */
function bpmj_eddpc_add_submenu() {

	add_submenu_page( 'edit.php?post_type=download', __( 'Add reminder', 'edd-paid-content' ), __( 'Add reminder', 'edd-paid-content' ), 'manage_shop_settings', 'bpmj-eddpc-add-renewal', 'bpmj_eddpc_add_renewal' );
	add_submenu_page( 'edit.php?post_type=download', __( 'Edit reminder', 'edd-paid-content' ), __( 'Edit reminder', 'edd-paid-content' ), 'manage_shop_settings', 'bpmj-eddpc-edit-renewal', 'bpmj_eddpc_edit_renewal' );

	add_action( 'admin_head', 'bpmj_eddpc_hide_renewal_page' );
}

add_action( 'admin_menu', 'bpmj_eddpc_add_submenu', 10 );

/**
 * Remvoes the License Renewal Notice menu link
 *
 * @access      private
 * @since       3.0
 * @return      void
 */
function bpmj_eddpc_hide_renewal_page() {
	remove_submenu_page( 'edit.php?post_type=download', 'bpmj-eddpc-edit-renewal' );
	remove_submenu_page( 'edit.php?post_type=download', 'bpmj-eddpc-add-renewal' );
}


/**
 * Renders the add / edit renewal notice screen
 *
 * @since 3.0
 */
function bpmj_eddpc_add_renewal() {
	include BPMJ_EDD_PC_INCLUDES . '/admin/add-renewal.php';
}

function bpmj_eddpc_edit_renewal() {
	include BPMJ_EDD_PC_INCLUDES . '/admin/edit-renewal.php';
}


/**
 * Add and Edit fields
 */
function bpmj_eddpc_save_renewal() {

	if ( $_POST && isset( $_POST[ 'bpmj_eddpc_action' ] ) ) {

		if ( ! is_admin() ) {
			return;
		}

		if ( ! current_user_can( 'manage_shop_settings' ) ) {
			wp_die( __( 'You do not have permission to add reminders', 'edd-paid-content' ), __( 'Error', 'edd-paid-content' ), array( 'response' => 401 ) );
		}

		if ( ! wp_verify_nonce( $_POST[ 'bpmj_eddpc_renewal_nonce' ], 'bpmj_eddpc_renewal_nonce' ) ) {
			wp_die( __( 'Invalid verification', 'edd-paid-content' ), __( 'Error', 'edd-paid-content' ), array( 'response' => 401 ) );
		}


		$action          = $_POST[ 'bpmj_eddpc_action' ];
		$renewal_options = get_option( 'bmpj_eddpc_renewal' );

		switch ( $action ) {

			// Add new renewal
			case 'add':

				$subject      = isset( $_POST[ 'subject' ] ) ? sanitize_text_field( $_POST[ 'subject' ] ) : __( 'Access to protected content will expire soon', 'bmpj_eddpc' );
				$period       = bpmj_eddpc_renewal_period_combine_inputs( 1, 'months', '-' );
				$message      = isset( $_POST[ 'message' ] ) ? wp_kses( stripslashes( $_POST[ 'message' ] ), wp_kses_allowed_html( 'post' ) ) : false;
				$type         = isset( $_POST[ 'bpmj_eddpc_renewal_type' ] ) ? $_POST[ 'bpmj_eddpc_renewal_type' ] : 'renewal';
				$charge_modes = empty( $_POST[ 'charge-mode' ] ) ? array(
					'automatic',
					'manual'
				) : $_POST[ 'charge-mode' ];

				$renewal_options[] = array(
					'subject'      => $subject,
					'message'      => $message,
					'send_period'  => $period,
					'type'         => $type,
					'charge_modes' => $charge_modes,
				);

				update_option( 'bmpj_eddpc_renewal', $renewal_options );
				break;


			// Edit renewal
			case 'edit':

				$id = isset( $_POST[ 'id' ] ) ? $_POST[ 'id' ] : false;
				if ( $id !== false ) {
					$subject      = isset( $_POST[ 'subject' ] ) ? sanitize_text_field( $_POST[ 'subject' ] ) : __( 'Access to protected content will expire soon', 'bmpj_eddpc' );
					$period       = bpmj_eddpc_renewal_period_combine_inputs( 1, 'months', '-' );
					$message      = isset( $_POST[ 'message' ] ) ? wp_kses( stripslashes( $_POST[ 'message' ] ), wp_kses_allowed_html( 'post' ) ) : false;
					$type         = isset( $_POST[ 'bpmj_eddpc_renewal_type' ] ) ? $_POST[ 'bpmj_eddpc_renewal_type' ] : 'renewal';
					$charge_modes = empty( $_POST[ 'charge-mode' ] ) ? array(
						'automatic',
						'manual'
					) : $_POST[ 'charge-mode' ];

					$renewal_options[ absint( $id ) ] = array(
						'subject'      => $subject,
						'message'      => $message,
						'send_period'  => $period,
						'type'         => $type,
						'charge_modes' => $charge_modes,
					);

					update_option( 'bmpj_eddpc_renewal', $renewal_options );
				}
				break;

		}

		wp_redirect( admin_url( 'edit.php?post_type=download&page=edd-settings&tab=extensions' ) );
		exit;
	}
}

add_action( 'admin_init', 'bpmj_eddpc_save_renewal' );


/**
 * Delete renewal
 */
function bpmj_eddpc_delete_renewal() {
	if ( isset( $_GET[ 'bpmj_eddpc_action' ] ) && $_GET[ 'bpmj_eddpc_action' ] == 'delete-renewal' ) {
		if ( isset( $_GET[ 'renewal-id' ] ) ) {

			$renewal_options = get_option( 'bmpj_eddpc_renewal' );
			$id              = $_GET[ 'renewal-id' ];

			unset( $renewal_options[ $id ] );

			update_option( 'bmpj_eddpc_renewal', $renewal_options );

			wp_redirect( remove_query_arg( array( 'bpmj_eddpc_action', 'renewal-id' ) ) );
			exit;
		}
	}
}

add_action( 'admin_init', 'bpmj_eddpc_delete_renewal' );
