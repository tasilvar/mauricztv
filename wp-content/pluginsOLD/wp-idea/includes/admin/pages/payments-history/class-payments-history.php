<?php

namespace bpmj\wpidea\admin\pages\payments_history;

use bpmj\wpidea\admin\Notices;

class Payments_History
{
    public function __construct()
    {
        add_action( 'edd_updated_edited_purchase', [ $this, 'updated_edited_purchase_action_redirect'] );
        add_action( 'admin_notices', [ $this, 'payment_history_notices' ] );
    }

    public function updated_edited_purchase_action_redirect( $payment_id )
    {
        wp_safe_redirect( admin_url( 'admin.php?page=wp-idea-payment-history&view=order-details&wp-idea-message=payment_updated&id=' . $payment_id ) );
        exit;
    }

    public function payment_history_notices()
    {
        if ( isset( $_GET['wp-idea-message'] ) && 'payment_deleted' === $_GET['wp-idea-message'] ) {
            WPI()->notices->display_notice( __( 'The payment has been deleted.', BPMJ_EDDCM_DOMAIN ), Notices::TYPE_SUCCESS );
        }

        if ( isset( $_GET['wp-idea-message'] ) && 'payment_updated' === $_GET['wp-idea-message'] ) {
            WPI()->notices->display_notice(__( 'The payment has been successfully updated.', BPMJ_EDDCM_DOMAIN ), Notices::TYPE_SUCCESS );
        }

        if ( isset( $_GET['wp-idea-message'] ) && 'payment_created' === $_GET['wp-idea-message'] ) {
            WPI()->notices->display_notice(__( 'The payment has been created.', BPMJ_EDDCM_DOMAIN ), Notices::TYPE_SUCCESS );
        }

        if ( isset( $_GET['wp-idea-message'] ) && 'payment_note_deleted' === $_GET['wp-idea-message'] ) {
            WPI()->notices->display_notice(__( 'The payment note has been created.', BPMJ_EDDCM_DOMAIN ), Notices::TYPE_SUCCESS );
        }
    }
}
