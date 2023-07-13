<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//add_action('woocommerce_payment_complete', 'bpmj_wpifirma_on_woo_complete_purchase'); // nie działa z bramką transferuj.pl (nie wywyołują payment_complete)
add_action( 'woocommerce_order_status_completed', 'bpmj_wpifirma_on_woo_complete_purchase' ); // alternatywa dla powyższego - do ustawień?

add_action( 'woocommerce_order_actions_start', 'bpmj_wpifirma_add_woo_metabox' );


function bpmj_wpifirma_on_woo_complete_purchase( $order_id ) {
	global $bpmj_wpifirma_settings;

	$order = new WC_Order( $order_id );

    $shipping = is_callable( array(
        $order,
        'get_shipping_total'
    ) ) ? $order->get_shipping_total() : $order->get_total_shipping();
	$total    = round( $order->get_total() - $shipping, 2 );

	// Jeśli kwota do zapłaty wynosi 0 - koniec
	if ( 0 == $total ) {
		return;
	}

	$invoice_factory = BPMJ_Invoice_Factory_Woocommerce::factory( $order, $bpmj_wpifirma_settings, new BPMJ_WP_iFirma() );
	if ( false === $invoice_factory->create_invoice_object() ) {
		return;
	}

	$invoice_factory->store_invoice();
}


/**
 * Register the metabox 
 */
function bpmj_wpifirma_add_woo_metabox() {
	?>
	<li class="wide">
	<form method="POST">
		<input type="submit"
		name="bpmj_send_payment_invoice" id="bpmj_send_payment_invoice" 
		class="button-secondary" 
		value="<?php _e('Wystaw fakturę', 'bpmj_wp_ifirma' ); ?> (WP iFirma)" /><br/>
		<?php wp_nonce_field('bpmj_wp_ifirma_woo_form_nonce', 'bpmj_wp_ifirma_woo_form_nonce'); ?>
	</form>
	</li>
	
	<?php

}

//add_action( 'save_post', 'bpmj_wpifirma_woo_send_invoice' );

function bpmj_wpifirma_woo_send_invoice( $post_id ){

	$order 		= wc_get_order( $post_id );

	$send_invoice = array_key_exists( 'bpmj_send_payment_invoice', $_POST );
	$order_completed = ( $order->data['status'] == 'completed' );
	if( $send_invoice ){

		if( $order_completed ){
			bpmj_wpifirma_on_woo_complete_purchase( $order->ID );
	
		} else {
			_e( 'Możesz wystawić fakturę tylko dla zakończonych płatności', 'bpmj_wp_ifirma' );
		}

	}
}

add_action( 'bpmj_send_payment_invoice_form_after', 'bpmj_invoice_notice' );

function bpmj_invoice_notice(){
	 _e( 'Wystawiono fakturę.', 'bpmj_wp_ifirma' );
}