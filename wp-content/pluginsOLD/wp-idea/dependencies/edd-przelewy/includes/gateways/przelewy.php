<?php
add_action('edd_gateway_przelewy_gateway', 'bpmj_prz_edd_process_payment');

function bpmj_prz_edd_process_payment(array $purchase_data): void
{

    global $edd_options;

    $payment_data = array(
        'price' => $purchase_data['price'],
        'date' => $purchase_data['date'],
        'user_email' => $purchase_data['user_email'],
        'purchase_key' => $purchase_data['purchase_key'],
        'currency' => $edd_options['currency'],
        'downloads' => $purchase_data['downloads'],
        'cart_details' => $purchase_data['cart_details'],
        'user_info' => $purchase_data['user_info'],
        'status' => 'pending'
    );

    $payment = edd_insert_payment($payment_data);

    if (!$payment) {

        edd_record_gateway_error(__('Błąd płatności', 'bpmj_prz_edd'), sprintf(__('Błąd bramki płatności.  Data: %s', 'bpmj_prz_edd'), json_encode($payment_data)), $payment);

        edd_send_back_to_checkout('?payment-mode=' . $purchase_data['post_data']['edd-gateway']);
    } else {

        $return_url = add_query_arg('payment-confirmation', 'przelewy', get_permalink($edd_options['success_page']));

        edd_empty_cart();

        wp_redirect($return_url);
        exit;
    }
}