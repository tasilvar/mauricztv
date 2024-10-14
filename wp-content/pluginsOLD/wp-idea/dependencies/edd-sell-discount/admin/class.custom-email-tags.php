<?php

/**
 * Add custom email tags
 */

if (!defined('ABSPATH'))
    exit;


class BPMJ_EDD_Sell_Discount_Custom_Email_Tags
{

    public function __construct()
    {
        add_filter('edd_email_tags', array($this, 'add'), 1);
    }


    /**
     * Add new email tag
     * @param $emailTags
     * @return array
     */
    public function add($emailTags)
    {
        // generated_discount_codes_details
        $emailTags[] = array(
            'tag' => 'generated_discount_codes_details',
            'description' => __('Displays a list of purchased discount codes with all the information', BPMJ_EDDCM_DOMAIN),
            'function' => 'BPMJ_EDD_Sell_Discount_Custom_Email_Tags::show_bought_codes_details'
        );

        // generated_discount_codes
        $emailTags[] = array(
            'tag' => 'generated_discount_codes',
            'description' => __('Displays the discount codes purchased, separated by a comma', BPMJ_EDDCM_DOMAIN),
            'function' => 'BPMJ_EDD_Sell_Discount_Custom_Email_Tags::show_bought_codes'
        );

        return $emailTags;
    }


    /**
     * Return list of bought discount codes
     * @param $paymentID
     * @return string
     */
    public static function show_bought_codes_details($paymentID)
    {
        $codes = json_decode(get_post_meta($paymentID, '_edd-sell-discount-generated-codes', true));
        if (!empty($codes) && $codes) {

            $html = __('Your discount codes: ', BPMJ_EDDCM_DOMAIN);
            $html .= '<ul>';
            foreach ($codes as $code) {
                $code_type = $code->data->_edd_discount_type == 'percent' ? '%' : edd_currency_symbol();
                $html .= sprintf('<li><code>%s</code> (%s%s)', $code->code, $code->data->_edd_discount_amount, $code_type);

                if (!empty($code->data->_edd_discount_expiration)) {
                    $html .= sprintf(', %s: %s', __('Expires', BPMJ_EDDCM_DOMAIN), $code->data->_edd_discount_expiration);
                }

                $html .= sprintf(', %s: %s</li>', __('Maximum number of uses', BPMJ_EDDCM_DOMAIN), $code->data->_edd_discount_max_uses);

                if( ! empty( $code->data->_edd_discount_product_reqs ) ) {
                    $products_titles = array();
                    foreach( $code->data->_edd_discount_product_reqs as $key => $value) {
                        $edd_product = edd_get_download( $value );
                        $products_titles[] = $edd_product->post_title;
                    }

                    $discount_product_info = '';
                    if( count( $products_titles ) == 1 )
                        $discount_product_info = sprintf( __( 'You can redeem this code to get a discount on the following product: %s.', BPMJ_EDDCM_DOMAIN ), $products_titles[0] );
                    else if( count( $products_titles ) > 1 )
                        $discount_product_info = sprintf( __( 'You can redeem this code to get a discount on the following product: %s.', BPMJ_EDDCM_DOMAIN ), implode( ', ', $products_titles ) );

                    $html .= $discount_product_info;
                }

            }
            $html .= '</ul>';
            return $html;

        } else {
            return '';
        }
    }


    /**
     * Return string of bought discount codes
     * @param $paymentID
     * @return string
     */
    public static function show_bought_codes($paymentID)
    {
        $codes = json_decode(get_post_meta($paymentID, '_edd-sell-discount-generated-codes', true));
        if (!empty($codes) && $codes) {

            $parsed_codes = array();
            foreach ($codes as $code) {
                $parsed_codes[] = '<code>' . $code->code . '</code>';
            }

            return implode(', ', $parsed_codes);

        } else {
            return '';
        }
    }
}

new BPMJ_EDD_Sell_Discount_Custom_Email_Tags();