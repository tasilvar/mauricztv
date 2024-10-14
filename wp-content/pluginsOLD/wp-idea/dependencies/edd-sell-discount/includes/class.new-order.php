<?php

/**
 * File is creating special dicount code
 * if order contains such a products
 */

if (!defined('ABSPATH'))
    exit;


class BPMJ_EDD_Sell_Discount_New_Order
{

    /**
     * @var integer
     */
    protected $order_id;

    /**
     * @var array|object
     */
    protected $products;


    /**
     * BPMJ_EDD_Sell_Discount_New_Order constructor.
     */
    public function __construct()
    {
        add_action('edd_complete_purchase', array($this, 'init'));
    }


    public function init($payment_id)
    {
        $this->order_id = $payment_id;
        $this->products = edd_get_payment_meta_cart_details($this->order_id);


        // All coupon codes to generate
        $coupons = $this->products_with_coupon_codes();
        if (!$coupons)
            return;

        // Generate coupons
        $generatedCodes = array();
        foreach ($coupons as $coupon) {

            // Custom expiration time
            if (!empty($coupon['time']) && !empty($coupon['time_type'])) {
                $current_date = current_time('Y-m-d');
                $coupon['_edd_discount_expiration'] = date('Y-m-d', strtotime($current_date . $coupon['time'] . ' ' . $coupon['time_type']));
            }

            $generatedCodes[] = array(
                'code' => $this->generate_discount($coupon),
                'data' => $coupon
            );

        }

        update_post_meta($this->order_id, '_edd-sell-discount-generated-codes', wp_slash( json_encode( $generatedCodes ) ) );
    }


    /**
     * Check if bought products have coupon
     * codes to generate
     * @return array|bool
     */
    protected function products_with_coupon_codes()
    {
        $result = array();

        foreach ($this->products as $key => $product) {
            $id = $product['id'];
            $coupon = absint(get_post_meta($id, '_edd-sell-discount-code', true));

            /**
             * Check if coupon exists
             * [solution from EDD_Coupon class]
             */
            $discount = WP_Post::get_instance($coupon);

            if ($discount) {
                $result[$key] = $this->parse_discount_data(get_post_meta($discount->ID));

                $result[$key]['_edd_discount_name'] = $product['name'] . ' [' . $this->order_id . ']';
                $result[$key]['_edd_discount_max_uses'] = $product['quantity'];

                $result[$key]['time'] = get_post_meta($id, '_edd-sell-discount-time', true);
                $result[$key]['time_type'] = get_post_meta($id, '_edd-sell-discount-time-type', true);
            }
        }

        // Return
        if ($result === array())
            return false;

        return $result;
    }


    /**
     * Parse all discount data
     * @param $data array
     * @return array
     */
    protected function parse_discount_data($data)
    {
        if (!empty($data)) {
            foreach ($data as $key => $option) {
                if (EDD_Sell_Discount()->is_serial($option[0])) {
                    $data[$key] = unserialize($option[0]);
                } else {
                    $data[$key] = $option[0];
                }
            }
        }

        return $data;
    }


    /**
     * Generate discount code
     * @param $max
     * @param $type
     * @param $amount
     * @param $expiration
     * @return mixed
     */
    protected function generate_discount($meta)
    {
        $meta['_edd_discount_code'] = strtoupper( wp_generate_password(8, false, false) );
        $meta['_edd_discount_start'] = date('Y-m-d');
        $meta['_edd_discount_uses'] = 0;
        $meta['_edd_discount_max'] = $meta['_edd_discount_max_uses'];

        $couponID = wp_insert_post(array(
            'post_type' => 'edd_discount',
            'post_title' => $meta['_edd_discount_name'],
            'post_status' => 'active'
        ));

        foreach ($meta as $key => $value) {
            update_post_meta($couponID, $key, $value);
        }
        update_post_meta($couponID, '_edd_sell_discount_code', 1);

        return $meta['_edd_discount_code'];
    }
}

new BPMJ_EDD_Sell_Discount_New_Order();