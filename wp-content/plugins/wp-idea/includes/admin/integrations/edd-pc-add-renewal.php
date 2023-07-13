<?php
// Exit if accessed directly
use bpmj\wpidea\Packages;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$no_access         = WPI()->packages->no_access_to_feature( Packages::FEAT_SUBSCRIPTIONS );
$notification_type = empty( $_REQUEST[ 'bpmj-renewal-type' ] ) ? 'renewal' : $_REQUEST[ 'bpmj-renewal-type' ];
?>

<?php if ( 'renewal' === $notification_type ): ?>
    <h2><?php _e( 'Add a renewal reminder', 'edd-paid-content' ); ?></h2>
    <p>
		<?php _e( 'A renewal reminder is sent before access to the content expires if no recurring access payments have been set.', BPMJ_EDDCM_DOMAIN ); ?>
    </p>
<?php else: ?>
    <h2><?php _e( 'Add a payment reminder', 'edd-paid-content' ); ?></h2>
    <p>
		<?php _e( 'A payment reminder is sent before the next recurring payment is processed.', 'edd-paid-content' ); ?>
    </p>
<?php endif; ?>

<form id="bpmj-eddpc-renewal-form" action="" method="post">
    <table class="form-table">
        <tbody>
		<?php if ( $no_access ): ?>
            <tr>
                <th scope="row" valign="top">
                    <span class="text-danger"><?php _e( 'Upgrade needed', BPMJ_EDDCM_DOMAIN ); ?></span>
                </th>
                <td>
                    <p class="description"><span
                                class="text-danger"><?php echo WPI()->packages->feature_not_available_message( Packages::FEAT_SUBSCRIPTIONS, __( 'In order to use subscription options, you need to upgrade your license to level: "%s"', BPMJ_EDDCM_DOMAIN ) ); ?></span>
                    </p>
                </td>
            </tr>
		<?php endif; ?>
        <tr>
            <th scope="row" valign="top">
                <label for="bpmj-eddpc-renewal-subject"><?php _e( 'Message subject', 'edd-paid-content' ); ?></label>
            </th>
            <td>
                <input name="subject" id="bpmj-eddpc-renewal-subject" class="bpmj-eddpc-renewal-subject regular-text"
                       type="text" value="" <?php disabled( $no_access ); ?> />
                <p class="description"><?php _e( 'Subject of the reminder message', 'edd-paid-content' ); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row" valign="top">
                <label for="bpmj-eddpc-renewal-send-period"><?php _e( 'Sending period', 'edd-paid-content' ); ?></label>
            </th>
            <td>
	            <?php
	            bpmj_eddpc_renewal_period_input( $notification_type );
	            ?>
                <p class="description"><?php _e( 'When should an email be sent?', 'edd-paid-content' ); ?></p>
            </td>
        </tr>
		<?php if ( 'payment' === $notification_type ): ?>
            <tr>
                <th scope="row" valign="top">
					<?php _e( 'Send a notification for:', 'edd-paid-content' ); ?>
                </th>
                <td>
                    <label><input type="checkbox" name="charge-mode[]" value="automatic"
                                  checked="checked" <?php disabled( $no_access ); ?>/> <?php _e( 'automatic', 'edd-paid-content' ); ?>
                    </label>
                    <br/>
                    <label><input type="checkbox" name="charge-mode[]" value="manual"
                                  checked="checked" <?php disabled( $no_access ); ?>/> <?php _e( 'manual payments', 'edd-paid-content' ); ?>
                    </label>
                    <p class="description"><?php _e( 'Automatic payments are those where the customer is automatically charged (e.g. from a credit card)', 'edd-paid-content' ); ?></p>
                </td>
            </tr>
		<?php endif; ?>
        <tr>
            <th scope="row" valign="top">
                <label for="bpmj-eddpc-renewal-message"><?php _e( 'Message', 'edd-paid-content' ); ?></label>
            </th>
            <td>
				<?php wp_editor( '', 'bpmj_eddpc_message', array( 'textarea_name' => 'message' ) ); ?>
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
                        <li><code>{payment_method}</code>
                            - <?php _e( 'Payment method name', 'edd-paid-content' ); ?></li>
                        <li><code>{direct_payment_link}</code>
                            - <?php printf( __( 'Direct payment link'
			                                    . ' - for automatically charged payments, the following text is substituted instead of the link: %s', 'edd-paid-content' ),
				                __( 'Payment will be made automatically', 'edd-paid-content' ) ); ?>
                        </li>
	                <?php endif; ?>
                </ul>
            </td>
        </tr>

        </tbody>
    </table>
    <p class="submit">
	    <?php if ( ! $no_access ): ?>
            <input type="hidden" name="bpmj_eddpc_renewal_type" value="<?php echo $notification_type; ?>"/>
            <input type="hidden" name="bpmj_eddpc_action" value="add"/>
            <input type="hidden" name="bpmj_eddpc_renewal_nonce"
                   value="<?php echo wp_create_nonce( 'bpmj_eddpc_renewal_nonce' ); ?>"/>
            <input type="submit" value="<?php _e( 'Add reminder', 'edd-paid-content' ); ?>" class="button-primary"/>
		<?php endif; ?>
        <a href="<?php
        echo esc_url(admin_url('admin.php?page=' . \bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug::SETTINGS . '&autofocus=messages') ); ?>"
           class="button-secondary"><?php _e( 'Return to settings', 'edd-paid-content' ); ?></a>
    </p>
</form>
