<?php

/**
 *
 * The class responsible for API
 *
 */

namespace bpmj\wpidea;

use EDD_Customer;
use EDD_Payment;
use WP_Error;
use WP_REST_Request;
use WP_REST_Server;
use bpmj\wpidea\API_V2;
use bpmj\wpidea\Caps;

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

class API
{

    const STATUS_OK = 'ok';
    const STATUS_ALREADY_PROCESSED = 'already processed';
    const STATUS_NO_DATA = 'no data';
    const STATUS_NO_PRODUCTS_DATA = 'no products data';
    const STATUS_NO_CUSTOMER_DATA = 'no customer data';
    const STATUS_PRODUCT_NOT_ADDED = 'product not added';

    public function __construct()
    {

        $this->includes();
        $this->hooks();
    }

    private function includes()
    {

    }

    private function hooks()
    {
        add_action('rest_api_init', function () {
            register_rest_route('wp-idea/v1', '/products/', array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_products'),
                'args' => $this->rest_arguments(),
                'permission_callback' => array($this, 'rest_permission')
            ));
            register_rest_route('wp-idea/v1', '/orders/', array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array($this, 'post_order'),
                'args' => $this->rest_arguments(),
                'permission_callback' => array($this, 'rest_permission')
            ));
        });

        add_filter('rest_authentication_errors', array($this, 'filter_wp_rest_api_access'));
    }

    public function get_products($data): array
    {
        $products = get_posts(array('post_type' => 'download', 'posts_per_page' => -1));

        $options = array();

        if (!empty($products)) {
            foreach ($products as $product) {
                if (edd_has_variable_prices($product->ID)) {
                    $prices = edd_get_variable_prices($product->ID);
                    foreach ($prices as $price_id => $price) {
                        $options[$product->ID . '_' . $price_id] = $product->post_title . ' - ' . $price['name'];
                    }
                } else {
                    $options[$product->ID] = $product->post_title;
                }
            }
        }

        return $options;
    }

    public function post_order(WP_REST_Request $request)
    {

        $json = $request->get_json_params();

        $res = $this->create_order($json);

        if (!$this->is_status_for_ok($res)) {
            return new WP_Error($res, $res, array('status' => 400));
        }

        return $this->prepare_status_array($res);
    }

    public function create_order($data): string
    {
        // workaround - if sales is disabled
        add_filter('bpmj_eddcm_can_purchase_product', array($this, 'bpmj_eddcm_can_always_purchase_product'), 10, 3);

        $r = $this->create_order_core($data);

        remove_filter('bpmj_eddcm_can_purchase_product', array($this, 'bpmj_eddcm_can_always_purchase_product'), 10);

        return $r;
    }

    private function create_order_core($data): string
    {
        // Disable sending purchase receipt
        remove_action('edd_complete_purchase', 'edd_trigger_purchase_receipt', 999, 1);

        if (empty($data)) {
            return self::STATUS_NO_DATA;
        }

        if (empty($data['products'])) {
            return self::STATUS_NO_PRODUCTS_DATA;
        }
        $products = $data['products'];

        if (isset($data['source']['id']) && isset($data['source']['url'])) {
            if (!$this->is_order_unique($data)) {
                return self::STATUS_ALREADY_PROCESSED;
            } else {
                $this->add_order_to_cache($data);
            }
        }

        //$cart = new EDD_Cart(); - since EDD 2.7
        $added = false;
        $payment = new EDD_Payment();
        foreach ($products as $download_id => $download_options) {
            $download_id = (int)$download_id; // gives possibility to add more than one products with the same ID (e.g. to add two or more variants)
            //$cart->add($id, $product);
            $download = edd_get_download($download_id);
            if (!empty($download)) {
                edd_add_to_cart($download_id, $download_options);
                $payment->add_download($download_id, $download_options);
                $added = true;
            }
        }

        if (!$added) {
            return self::STATUS_PRODUCT_NOT_ADDED;
        }

        if (empty($data['customer'])) {
            return self::STATUS_NO_CUSTOMER_DATA;
        }
        $customer = $data['customer'];

        if (isset($customer['email'])) {
            $payment->email = $customer['email'];
        } else {
            return self::STATUS_NO_CUSTOMER_DATA;
        }

        if (isset($customer['first_name'])) {
            $payment->first_name = $customer['first_name'];
        }

        if (isset($customer['last_name'])) {
            $payment->last_name = $customer['last_name'];
        }

        $address = isset($data['shipping_address']) ? $data['shipping_address'] : null;

        $newAddress = array();
        $newAddress['line1'] = isset($address['address1']) ? $address['address1'] : '';
        $newAddress['line2'] = isset($address['address2']) ? $address['address2'] : '';
        $newAddress['zip'] = isset($address['zip_code']) ? $address['zip_code'] : '';
        $newAddress['city'] = isset($address['city']) ? $address['city'] : '';
        $newAddress['state'] = '';
        $newAddress['country'] = isset($address['country_code']) ? strtoupper($address['country_code']) : '';

        $payment->address = $newAddress;

        $payment->cart_details = edd_get_cart_content_details(); //$cart->get_contents_details();
        $payment->sub_total = 0.00; //edd_cart_subtotal(); //$cart->get_subtotal();
        $payment->total = 0.00; //edd_get_cart_total(); //$cart->get_total();

        $payment->status = 'pending';
        $payment->save();

        $payment->update_meta('email', $customer['email']);

        $this->create_user($payment->ID, $payment->get_meta());

        $payment->status = 'complete';
        $payment->save();

        if (!empty($data['source']['id'])) {
            $note = sprintf(__('Order #%d added by external platform %s (%s)', BPMJ_EDDCM_DOMAIN), $data['source']['id'], $data['source']['platform'] ?? 'n/a', $data['source']['url']);
            $payment->add_note($note);
        }

        // Send NOW email witch purchase receipt
        if (empty($data['options']['disable_receipt'])) {
            edd_email_purchase_receipt($payment->ID, true);
        }

        return self::STATUS_OK;
    }

    private function create_user($payment_id, $payment_data)
    {
        if (!class_exists('EDD_Auto_Register')) {
            return;
        }

        if (empty($payment_data['user_info']['email']) && !empty($payment_data['email'])) {
            $payment_data['user_info']['email'] = $payment_data['email'];
        }

        $customer = new EDD_Customer($payment_data['user_info']['email']);
        $payment_ids = explode(',', $customer->payment_ids);
        if (is_array($payment_ids) && !empty($payment_ids)) {
            $payment_ids = array_map('absint', $payment_ids);

            if (1 === count($payment_ids) && in_array($payment_id, $payment_ids)) {
                remove_action('user_register', 'edd_connect_existing_customer_to_new_user', 10, 1);
                remove_action('user_register', 'edd_add_past_purchases_to_new_user', 10, 1);
            }
        }

        $user_id = edd_auto_register()->create_user($payment_data, $payment_id);

        if (empty($user_id) || is_wp_error($user_id)) {
            return;
        }
        $payment_meta = edd_get_payment_meta($payment_id);
        $payment_meta['user_info']['id'] = $user_id;
        edd_update_payment_meta($payment_id, '_edd_payment_user_id', $user_id);
        edd_update_payment_meta($payment_id, '_edd_payment_meta', $payment_meta);
    }

    private function is_order_unique($data): bool
    {
        $hash = $this->generate_hash($data);

        return false === get_transient($hash);
    }

    private function add_order_to_cache($data): bool
    {
        $hash = $this->generate_hash($data);

        return set_transient($hash, $hash, 7 * 24 * 60 * 60); // 7 days
    }

    private function generate_hash($data): string
    {
        return md5($data['source']['id'] . ($data['source']['platform'] ?? '') . $data['source']['url']);
    }

    private function is_status_for_ok($status): bool
    {
        return ($status === self::STATUS_OK || $status === self::STATUS_ALREADY_PROCESSED);
    }

    private function prepare_status_array($status): array
    {
        return ['status' => $status];
    }

    // REST

    private function rest_arguments(): array
    {
        $args = array();

        $args['token'] = array(
            'description' => esc_html__('The token parameter is used to authenticate the request.', 'wp-idea'),
            'type' => 'string'
        );
        $args['nonce'] = array(
            'description' => esc_html__('The nonce parameter is used as an additional value for token verification.', 'wp-idea'),
            'type' => 'string'
        );
        return $args;
    }

    public function rest_permission(WP_REST_Request $request): bool
    {
        $params = $request->get_params();

        if (empty($params['token'])) {
            return false;
        }

        if (!ctype_alnum($params['token'])) {
            return false;
        }

        if (empty($params['nonce'])) {
            return false;
        }

        if (!ctype_alnum($params['nonce'])) {
            return false;
        }

        if (!$this->is_nonce_changed($params['nonce'])) {
            return false;
        }
        $this->remember_nonce($params['nonce']);

        $token = $this->calculate_token($params['nonce'], get_option('bmpj_wpidea_vkey'));

        if (strcmp($params['token'], $token) !== 0) {
            return false;
        }

        return true;
    }

    public function calculate_token($nonce, $key): string
    {
        return md5($nonce . $key);
    }

    private function is_nonce_changed($new_nonce): bool
    {
        $last_nonce = get_transient('bpmj_api_nonce');

        if (0 === strcmp($new_nonce, $last_nonce)) {
            return false;
        }

        return true;
    }

    private function remember_nonce($nonce)
    {
        set_transient('bpmj_api_nonce', $nonce);
    }

    public function bpmj_eddcm_can_always_purchase_product($ret, $product_id, $price_id): bool
    {
        return true;
    }

	/*
	 * Security - disables access to rest api for Subscribers and not logged users (wpi endpoinds are excluded)
	 */
	public function filter_wp_rest_api_access($access) {

	    if($this->is_api_version_2()) {
	       return API_V2::filter_wp_rest_api_access($access);
	    }

		if( $this->is_not_one_of_blacklisted_endpoints() ) return $access;

        if (isset($_GET['nonce']) && isset($_GET['token']) && strpos($_SERVER['REQUEST_URI'], '/wp-json/wp-idea/') !== false) {
            $token = $this->calculate_token($_GET['nonce'], get_option('bmpj_wpidea_vkey'));
            if (strcmp($_GET['token'], $token) === 0) {
                return $access;
            }

            return new WP_Error('rest_token_error', __('REST API wrong token.', BPMJ_EDDCM_DOMAIN), array('status' => rest_authorization_required_code()));
        }

        if (!is_user_logged_in()) {
            return new WP_Error('rest_login_required', __('REST API restricted to authenticated users.', BPMJ_EDDCM_DOMAIN), array('status' => rest_authorization_required_code()));
        }

        if ($this->user_has_api_access()) return $access;

        return new WP_Error('rest_login_permissions', __('REST API restricted to specific roles.', BPMJ_EDDCM_DOMAIN), array('status' => rest_authorization_required_code()));
    }


    private function user_has_api_access(): bool
    {
        $user = wp_get_current_user();
        return $user->has_cap(Caps::CAP_MANAGE_POSTS);
    }

    private function get_blacklisted_endpoints(): array
    {
        return array(
            '/wp-json/wp/v2',
            '/wp-json/oembed',
            '/wp-json/wp-idea'
        );
    }

    private function is_not_one_of_blacklisted_endpoints(): bool
    {
        foreach ($this->get_blacklisted_endpoints() as $endpoint) {
            if (strpos($_SERVER['REQUEST_URI'], $endpoint) !== false) return false;
        }

		return true;
	}

    private function is_api_version_2(): bool
    {
        if( strpos( $_SERVER[ 'REQUEST_URI' ], '/wp-json/'.API_V2::URL_NAMESPACE ) !== false ) return true;

        return false;
    }
}
