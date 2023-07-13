<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$id                = isset( $_GET[ 'renewal-id' ] ) ? $_GET[ 'renewal-id' ] : 'none';
$notification_type = 'renewal';
if ( $id !== 'none' ) {
	$renewal = get_option( 'bmpj_eddpc_renewal' );
	$renewal = $renewal[ $id ];
	if ( isset( $renewal[ 'type' ] ) ) {
		$notification_type = $renewal[ 'type' ];
	}
} else {
	echo '<script> window.location.replace("' . admin_url( 'edit.php?post_type=download&page=edd-settings&tab=extensions' ) . '"); </script>';
}
?>

<?php if ( 'renewal' === $notification_type ): ?>
    <h2><?php _e( 'Edit a renewal reminder', 'edd-paid-content' ); ?></h2>
<?php else: ?>
    <h2><?php _e( 'Edit a payment reminder', 'edd-paid-content' ); ?></h2>
<?php endif; ?>

<form id="bpmj-eddpc-renewal-form" action="" method="post">
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row" valign="top">
                <label for="bpmj-eddpc-renewal-subject"><?php _e( 'Message subject', 'edd-paid-content' ); ?></label>
            </th>
            <td>
                <input name="subject" id="bpmj-eddpc-renewal-subject" class="bpmj-eddpc-renewal-subject regular-text"
                       type="text" value="<?php echo $renewal[ 'subject' ]; ?>"/>
                <p class="description"><?php _e( 'Subject of the reminder message', 'edd-paid-content' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row" valign="top">
                <label for="bpmj-eddpc-renewal-send-period"><?php _e( 'Sending period', 'edd-paid-content' ); ?></label>
            </th>
            <td>
				<?php
				bpmj_eddpc_renewal_period_input( $notification_type, $renewal[ 'send_period' ] );
				?>
                <p class="description"><?php _e( 'When should an email be sent?', 'edd-paid-content' ); ?></p>
            </td>
        </tr>
		<?php if ( 'payment' === $notification_type ):
			$charge_modes = isset( $renewal[ 'charge_modes' ] ) ? $renewal[ 'charge_modes' ] : array(
				'automatic',
				'manual'
			);
			?>
            <tr>
                <th scope="row" valign="top">
					<?php _e( 'Send a notification for:', 'edd-paid-content' ); ?>
                </th>
                <td>
                    <label><input type="checkbox" name="charge-mode[]" value="automatic"
					              <?php if ( in_array( 'automatic', $charge_modes ) ): ?>checked="checked"<?php endif; ?>/> <?php _e( 'automatic', 'edd-paid-content' ); ?>
                    </label>
                    <br/>
                    <label><input type="checkbox" name="charge-mode[]" value="manual"
					              <?php if ( in_array( 'manual', $charge_modes ) ): ?>checked="checked"<?php endif; ?>/> <?php _e( 'standardowych (ręcznych)', 'edd-paid-content' ); ?>
                    </label>
                    <p class="description"><?php _e( 'Automatic payments are those where the customer is automatically charged (e.g. from a credit card) (np. z karty kredytowej)', 'edd-paid-content' ); ?></p>
                </td>
            </tr>
		<?php endif; ?>
        <tr>
            <th scope="row" valign="top">
                <label for="bpmj-eddpc-renewal-message"><?php _e( 'Message', 'edd-paid-content' ); ?></label>
            </th>
            <td>
				<?php wp_editor( $renewal[ 'message' ], 'bpmj_eddpc_message', array( 'textarea_name' => 'message' ) ); ?>
                <p class="description"><?php _e( 'The content of this field will be sent in a reminder message. You can use the following tags:', 'edd-paid-content' ); ?></p>
                <ul>
                    <li><code>{name}</code> - <?php _e( 'Customer Name and Surname', 'edd-paid-content' ); ?></li>
                    <li><code>{product_name}</code> - <?php _e( 'Product name', 'edd-paid-content' ); ?></li>
					<?php if ( 'renewal' === $notification_type ): ?>
                        <li><code>{product_link}</code> - <?php _e( 'Product Link', 'edd-paid-content' ); ?></li>
                        <li><code>{expiration}</code> - <?php _e( 'Access expiration date', 'edd-paid-content' ); ?>
                        </li>
                        <li><code>{discount}</code> - <?php _e( 'Discount code', 'edd-paid-content' ); ?></li>
                        <li><code>{discount_link}</code>
                            - <?php _e( 'A direct link which adds a product to the cart together with a discount code', 'edd-paid-content' ); ?>
                        </li>
					<?php else: ?>
                        <li><code>{payment_date}</code> - <?php _e( 'Next payment date', 'edd-paid-content' ); ?>
                        </li>
                        <li><code>{amount}</code> - <?php _e( 'Payment amount', 'edd-paid-content' ); ?></li>
                        <li><code>{payment_link}</code>
                            - <?php _e( 'Link to the payment that allows cancellation', 'edd-paid-content' ); ?></li>
                        <li><code>{direct_payment_link}</code>
                            - <?php printf( __( 'Direct link to make the payment'
							                    . ' - in the case of automatic payments, text is substituted instead of the link: %s', 'edd-paid-content' ),
								__( 'Payment will be made automatically', 'edd-paid-content' ) ); ?>
                        </li>
					<?php endif; ?>
                </ul>
            </td>
        </tr>

        </tbody>
    </table>
    <p class="submit">
        <input type="hidden" name="bpmj_eddpc_renewal_type" value="<?php echo $notification_type; ?>"/>
        <input type="hidden" name="bpmj_eddpc_action" value="edit"/>
        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
        <input type="hidden" name="bpmj_eddpc_renewal_nonce"
               value="<?php echo wp_create_nonce( 'bpmj_eddpc_renewal_nonce' ); ?>"/>
        <input type="submit" value="<?php _e( 'Edit reminder', 'edd-paid-content' ); ?>" class="button-primary"/>
        <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=download&page=edd-settings&tab=extensions' ) ); ?>"
           class="button-secondary"><?php _e( 'Return to settings', 'edd-paid-content' ); ?></a>
    </p>
</form>
