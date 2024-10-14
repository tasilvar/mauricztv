<?php
namespace bpmj\wpidea\controllers;

use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Request_Method;

class Payment_Controller extends Ajax_Controller
{

    public function behaviors(): array
    {
        return [
            'allowed_methods' => [Request_Method::POST]
        ];
    }


    public function process_checkout_action(Current_Request $current_request): string
    {
        edd_process_purchase_form();
        return $this->return_as_json(self::STATUS_SUCCESS, []);
    }

    public function load_gateway_action(Current_Request $current_request): string
    {
        $edd_payment_mode = $current_request->get_body_arg('edd_payment_mode');
        if (!$edd_payment_mode) {
            return $this->return_as_json(self::STATUS_ERROR);
        }

        ob_start();
        do_action( 'edd_purchase_form' );
        $form = ob_get_clean();

        return $this->return_as_json(self::STATUS_SUCCESS, ['form' => $form]);
    }

    public function add_to_cart_action(Current_Request $current_request): string
    {
        $download_id = $current_request->get_body_arg('download_id');

        if(!$download_id){
            return $this->return_as_json(self::STATUS_ERROR);
        }

        $to_add = [];
        $price_ids = $current_request->get_body_arg('price_ids');
        $post_data_var = $current_request->get_body_arg('post_data');

        if ($price_ids && is_array($price_ids)) {
            foreach ($price_ids as $price) {
                $to_add[] = ['price_id' => $price];
            }
        }

        $items = '';

        foreach ($to_add as $options){
            $price_id = $options['price_id'];

            if($download_id == $price_id ) {
                $options = [];
            }

            parse_str( $post_data_var, $post_data );

            if(isset($price_id) && isset($post_data['edd_download_quantity_' . $price_id ])){
                $options['quantity'] = absint($post_data['edd_download_quantity_' . $price_id]);
            } else {
                $options['quantity'] = isset($post_data['edd_download_quantity']) ? absint( $post_data['edd_download_quantity']) : 1;
            }
            $key = edd_add_to_cart($download_id, $options );

            $item = [
                'id'      => $download_id,
                'options' => $options
            ];

            $item   = apply_filters( 'edd_ajax_pre_cart_item_template', $item );
            $items .= html_entity_decode( edd_get_cart_item_template( $key, $item, true ), ENT_COMPAT, 'UTF-8' );
        }

        $return = [
            'subtotal'      => html_entity_decode( edd_currency_filter( edd_format_amount( edd_get_cart_subtotal() ) ), ENT_COMPAT, 'UTF-8' ),
            'total'         => html_entity_decode( edd_currency_filter( edd_format_amount( edd_get_cart_total() ) ), ENT_COMPAT, 'UTF-8' ),
            'cart_item'     => $items,
            'cart_quantity' => html_entity_decode( edd_get_cart_quantity() )
        ];

        if ( edd_use_taxes() ) {
            $cart_tax = (float) edd_get_cart_tax();
            $return['tax'] = html_entity_decode( edd_currency_filter( edd_format_amount( $cart_tax ) ), ENT_COMPAT, 'UTF-8' );
        }

        return $this->return_as_json(self::STATUS_SUCCESS, $return);
    }


}
