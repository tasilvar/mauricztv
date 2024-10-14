<?php

namespace bpmj\wpidea\admin\pages\customers;

use bpmj\wpidea\admin\Notices;
use bpmj\wpidea\Caps;
use bpmj\wpidea\wolverine\user\User;

class Customers
{
    public const PAGE = 'wp-idea-customers';
    public const DETAILS_VIEW_OVERVIEW = 'overview';
    public const DETAILS_VIEW_DELETE = 'delete';
    public const DETAILS_VIEW_NOTES = 'notes';
    public const DETAILS_VIEW_TOOLS = 'tools';

    private const MESSAGE_CUSTOMER_DELETE_NO_CONFIRM = 'customer_delete_no_confirm';
    private const MESSAGE_CUSTOMER_DELETE_FAILED = 'customer_delete_failed';
    private const MESSAGE_CUSTOMER_DELETE_INVALID_ID = 'customer_delete_invalid_id';

    public function __construct()
    {
        add_action( 'edd_delete-customer', [ $this, 'delete_customer' ] );
        add_action( 'admin_notices', [ $this, 'customer_notices' ] );
    }

    public function delete_customer()
    {
        if ( ! is_admin() || ! User::currentUserHasAnyOfTheRoles( [ Caps::CAP_DELETE_CUSTOMERS ] ) ) {
            _e( 'You do not have permission to delete this customer.', BPMJ_EDDCM_DOMAIN );
            return;
        }

        $args = $_POST;

        $customer_id   = (int)$args['customer_id'];
        $confirm       = ! empty( $args['edd-customer-delete-confirm'] ) ? true : false;
        $remove_data   = ! empty( $args['edd-customer-delete-records'] ) ? true : false;
        $nonce         = $args['_wpnonce'];

        if ( ! wp_verify_nonce( $nonce, 'delete-customer' ) ) {
            wp_die( __( 'Cheatin\' eh?!', BPMJ_EDDCM_DOMAIN ) );
        }

        if ( ! $confirm ) {
            wp_redirect( admin_url( 'admin.php?page=' . self::PAGE . '&view=' . self::DETAILS_VIEW_DELETE . '&wp-idea-message=' . self::MESSAGE_CUSTOMER_DELETE_NO_CONFIRM ) );
            exit;
        }

        if ( edd_get_errors() ) {
            wp_redirect( admin_url( 'admin.php?page=' . self::PAGE . '&view=' . self::DETAILS_VIEW_OVERVIEW . '&id=' . $customer_id ) );
            exit;
        }

        $customer = new \EDD_Customer( $customer_id );

        do_action( 'edd_pre_delete_customer', $customer_id, $confirm, $remove_data );

        $success = false;

        if ( $customer->id > 0 ) {

            $payments_array = explode( ',', $customer->payment_ids );
            $success        = EDD()->customers->delete( $customer->id );

            if ( $success ) {

                if ( $remove_data ) {

                    // Remove all payments, logs, etc
                    foreach ( $payments_array as $payment_id ) {
                        edd_delete_purchase( $payment_id, false, true );
                    }

                } else {

                    // Just set the payments to customer_id of 0
                    foreach ( $payments_array as $payment_id ) {
                        edd_update_payment_meta( $payment_id, '_edd_payment_customer_id', 0 );
                    }

                }

                $redirect = admin_url( 'admin.php?page=' . self::PAGE . '&edd-message=customer-deleted' );

            } else {
                $redirect = admin_url( 'admin.php?page=' . self::PAGE . '&view=' . self::DETAILS_VIEW_DELETE . '&wp-idea-message=' . self::MESSAGE_CUSTOMER_DELETE_FAILED );
            }

        } else {
            $redirect = admin_url( 'admin.php?page=' . self::PAGE . '&view=' . self::DETAILS_VIEW_DELETE . '&wp-idea-message=' . self::MESSAGE_CUSTOMER_DELETE_INVALID_ID );
        }

        wp_redirect( $redirect );
        exit;
    }

    public function customer_notices()
    {
        if ( isset( $_GET['wp-idea-message'] ) && self::MESSAGE_CUSTOMER_DELETE_NO_CONFIRM === $_GET['wp-idea-message'] ) {
            WPI()->notices->display_notice( __( 'Please confirm you want to delete this customer.', BPMJ_EDDCM_DOMAIN ), Notices::TYPE_SUCCESS );
        }

        if ( isset( $_GET['wp-idea-message'] ) && self::MESSAGE_CUSTOMER_DELETE_FAILED === $_GET['wp-idea-message'] ) {
            WPI()->notices->display_notice( __( 'Error deleting customer.', BPMJ_EDDCM_DOMAIN ), Notices::TYPE_SUCCESS );
        }

        if ( isset( $_GET['wp-idea-message'] ) && self::MESSAGE_CUSTOMER_DELETE_INVALID_ID === $_GET['wp-idea-message'] ) {
            WPI()->notices->display_notice( __( 'Invalid Customer ID.', BPMJ_EDDCM_DOMAIN ), Notices::TYPE_SUCCESS );
        }
    }
}
