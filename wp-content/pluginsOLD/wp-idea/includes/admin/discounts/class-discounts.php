<?php

namespace bpmj\wpidea\admin\discounts;

use bpmj\wpidea\admin\Notices;
use bpmj\wpidea\Caps;
use bpmj\wpidea\wolverine\user\User;
use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\admin\helpers\utils\Snackbar;

class Discounts implements Interface_Initiable
{
    private Interface_Translator $translator;
    private Snackbar $snackbar;

    public function __construct(
        Interface_Translator $translator,
        Snackbar $snackbar
    )
    {
        $this->translator = $translator;
        $this->snackbar = $snackbar;
    }

    public function init(): void
    {
        $this->init_actions();
    }

    private function init_actions(): void
    {
        add_action( 'admin_init', [ $this, 'process_actions' ] );
        add_action( 'wp_idea_edit_discount', [ $this, 'edit_discount' ] );
        add_action( 'wp_idea_add_discount', [ $this, 'add_discount' ] );

        $this->discount_notices();
    }

    public function process_actions(): void
    {
        if ( isset( $_POST['wp-idea-action'] ) || isset( $_GET['wp-idea-action'] ) ) {
            if ( 'add_discount' === $_REQUEST['wp-idea-action'] ) {
                do_action( 'wp_idea_add_discount' );
            }

            if ( 'edit_discount' === $_REQUEST['wp-idea-action'] ) {
                do_action( 'wp_idea_edit_discount' );
            }
        }
    }

    public function edit_discount(): void
    {
        if ( ! isset( $_POST['wp-idea-discount-nonce'] ) || ! wp_verify_nonce( $_POST['wp-idea-discount-nonce'], 'edd_discount_nonce' ) ) {
            return;
        }

        if( ! User::currentUserHasAnyOfTheRoles( [ Caps::CAP_MANAGE_DISCOUNTS ] ) ) {
            wp_die( __( 'You do not have permission to edit discount codes', BPMJ_EDDCM_DOMAIN ), __( 'Error', BPMJ_EDDCM_DOMAIN ), [ 'response' => 403 ] );
        }

        $discount = [];

        foreach ( $_POST as $key => $value ) {

            if ( $key != 'wp-idea-discount-nonce' && $key != 'wp-idea-action' && $key != 'discount-id' && $key != 'wp-idea-redirect' ) {

                if ( is_string( $value ) || is_int( $value ) ) {

                    $discount[ $key ] = strip_tags( addslashes( $value ) );

                } elseif ( is_array( $value ) ) {

                    $discount[ $key ] = array_map( 'absint', $value );

                }

            }

        }

        $old_discount     = edd_get_discount_by( 'code', $_POST['code'] );
        $discount['uses'] = edd_get_discount_uses( $old_discount->ID );

        if ( edd_store_discount( $discount, $_POST['discount-id'] ) ) {

            wp_redirect( add_query_arg( 'wp-idea-message', 'discount_updated', $_POST['wp-idea-redirect'] ) ); edd_die();

        } else {

            wp_redirect( add_query_arg( 'wp-idea-message', 'discount_update_failed', $_POST['wp-idea-redirect'] ) ); edd_die();

        }
    }

    public function add_discount(): void
    {
        if( ! User::currentUserHasAnyOfTheRoles( [ Caps::CAP_MANAGE_DISCOUNTS ] ) ) {
            wp_die( __( 'You do not have permission to create discount codes', BPMJ_EDDCM_DOMAIN ), __( 'Error', BPMJ_EDDCM_DOMAIN ), [ 'response' => 403 ] );
        }

        if ( isset( $_POST['wp-idea-dcg-discount-nonce'] ) && wp_verify_nonce( $_POST['wp-idea-dcg-discount-nonce'], 'edd_dcg_discount_nonce' ) ) {
            // Setup the discount code details
            $posted = array();

            foreach ( $_POST as $key => $value ) {
                if ( $key != 'wp-idea-dcg-discount-nonce' && $key != 'wp-idea-action' && $key != 'wp-idea-redirect' ) {
                    if ( is_string( $value ) || is_int( $value ) )
                        $posted[ $key ] = strip_tags( addslashes( $value ) );
                    elseif ( is_array( $value ) )
                        $posted[ $key ] = array_map( 'absint', $value );
                }
            }

            if (!isset($posted['number-codes'])) return;

            // Check number of codes is number and greater than 0
            if (floor($posted['number-codes']) == $posted['number-codes'] && $posted['number-codes'] > 0) {
                $code = $posted;
                unset($code['number-codes']);
                unset($code['code-type']);
                unset($code['code-limit']);

                $result = true;
                // Loop through and generate code, check code doesnt exist _edd_discount_code
                for ($i = 1; $i <= $posted['number-codes']; $i++) {
                    $code['name'] = $posted['name'] .'-'. $i;
                    $code['code'] = edd_dcg_create_code($posted['code-type'], $posted['code-limit']);
                    $code['status'] = 'active';
                    $result = edd_store_discount( $code );
                    if (!$result) break;
                }

                if ( $result ) {
                    $args = array( 	'wp-idea-message' => 'discounts_added',
                        'wp-idea-number' => $posted['number-codes'] );
                    wp_redirect( add_query_arg( $args , $_POST['wp-idea-redirect'] ) ); edd_die();
                } else {
                    wp_redirect( add_query_arg( 'wp-idea-message', 'discount_add_failed', $_POST['wp-idea-redirect'] ) ); edd_die();
                }

            }

            return;
        }

        if ( ! isset( $_POST['wp-idea-discount-nonce'] ) || ! wp_verify_nonce( $_POST['wp-idea-discount-nonce'], 'edd_discount_nonce' ) ) {
            return;
        }

        $posted = [];

        foreach ( $_POST as $key => $value ) {

            if ( $key != 'wp-idea-discount-nonce' && $key != 'wp-idea-action' && $key != 'wp-idea-redirect' ) {

                if ( is_string( $value ) || is_int( $value ) ) {

                    $posted[ $key ] = strip_tags( addslashes( $value ) );

                } elseif ( is_array( $value ) ) {

                    $posted[ $key ] = array_map( 'absint', $value );

                }
            }

        }

        // Ensure this discount doesn't already exist
        if ( ! edd_get_discount_by_code( $posted['code'] ) ) {

            // Set the discount code's default status to active
            $posted['status'] = 'active';

            if ( edd_store_discount( $posted ) ) {

                wp_redirect( add_query_arg( 'wp-idea-message', 'discount_added', $_POST['wp-idea-redirect'] ) ); edd_die();

            } else {

                wp_redirect( add_query_arg( 'wp-idea-message', 'discount_add_failed', $_POST['wp-idea-redirect'] ) ); edd_die();

            }

        } else {

            wp_redirect( add_query_arg( 'wp-idea-message', 'discount_exists', $_POST['wp-idea-redirect'] ) ); edd_die();

        }
    }

    private function discount_notices(): void
    {
        if ( isset( $_GET['wp-idea-message'] ) && 'discount_updated' === $_GET['wp-idea-message'] ) {
            $this->snackbar->display_message($this->translator->translate('discount_codes.actions.edit.success'));
        }

        if ( isset( $_GET['wp-idea-message'] ) && 'discount_added' === $_GET['wp-idea-message'] ) {
            $this->snackbar->display_message($this->translator->translate('discount_codes.actions.add.success'));
        }
    }
}
