<?php

/**
 * Admin Messages
 *
 * @since 1.6
 * @return void
 */
function edds_admin_messages() {

	if ( isset( $_GET['edd-message'] ) && 'connect-to-stripe' === $_GET['edd-message'] ) {
		add_settings_error( 'edds-notices', 'edds-connect-to-stripe', __( 'Connect your Stripe account using the "Connect with Stripe" button below.', 'edds' ), 'updated' );
		// I feel dirty, but EDD does not remove `edd-message` params from settings URLs and the message carries to all links if not removed, and well I wanted this all to work without touching EDD core yet.
		add_filter( 'wp_parse_str', function( $ar ) {
			if( isset( $ar['edd-message'] ) && 'connect-to-stripe' === $ar['edd-message'] ) {
				unset( $ar['edd-message'] );
			}
			return $ar;
		});
	}

	if( isset( $_GET['edd_gateway_connect_error'], $_GET['edd-message'] ) ) {
		echo '<div class="notice notice-error"><p>' . sprintf( __( 'There was an error connecting your Stripe account. Message: %s. Please <a href="%s">try again</a>.', 'edds' ), esc_html( urldecode( $_GET['edd-message'] ) ), esc_url( admin_url( 'edit.php?post_type=download&page=edd-settings&tab=gateways&section=edd-stripe' ) ) ) . '</p></div>';
		add_filter( 'wp_parse_str', function( $ar ) {
			if( isset( $ar['edd_gateway_connect_error'] ) ) {
				unset( $ar['edd_gateway_connect_error'] );
			}

			if( isset( $ar['edd-message'] ) ) {
				unset( $ar['edd-message'] );
			}
			return $ar;
		});
	}

	settings_errors( 'edds-notices' );
}
add_action( 'admin_notices', 'edds_admin_messages' );

/**
 * Add payment meta item to payments that used an existing card
 *
 * @since 2.6
 * @param $payment_id
 * @return void
 */
function edds_show_existing_card_meta( $payment_id ) {
	$payment = new EDD_Payment( $payment_id );
	$existing_card = $payment->get_meta( '_edds_used_existing_card' );
	if ( ! empty( $existing_card ) ) {
		?>
		<div class="edd-order-stripe-existing-card edd-admin-box-inside">
			<p>
				<span class="label"><?php _e( 'Used Existing Card:', 'edds' ); ?></span>&nbsp;
				<span><?php _e( 'Yes', 'edds' ); ?></span>
			</p>
		</div>
		<?php
	}
}
add_action( 'edd_view_order_details_payment_meta_after', 'edds_show_existing_card_meta', 10, 1 );

/**
 * Handles redirects to the Stripe settings page under certain conditions.
 *
 * @since 2.6.14
 */
function edds_stripe_connect_test_mode_toggle_redirect() {

	// Check for our marker
	if( ! isset( $_POST['edd-test-mode-toggled'] ) ) {
		return;
	}

	if( ! current_user_can( 'manage_shop_settings' ) ) {
		return;
	}

	if( ! edd_is_gateway_active( 'stripe' ) ) {
		return;
	}

	/**
	 * Filter the redirect that happens when options are saved and
	 * add query args to redirect to the Stripe settings page
	 * and to show a notice about connecting with Stripe.
	 */
	add_filter( 'wp_redirect', function( $location ) {
		if( false !== strpos( $location, 'page=edd-settings' ) && false !== strpos( $location, 'settings-updated=true' ) ) {
			$location = add_query_arg(
				array(
					'section' => 'edd-stripe',
					'edd-message' => 'connect-to-stripe',
				),
				$location
			);
		}
		return $location;
	} );

}
add_action( 'admin_init', 'edds_stripe_connect_test_mode_toggle_redirect' );
