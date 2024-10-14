<?php
namespace bpmj\wpidea;

use bpmj\wpidea\wolverine\product\Product;
use bpmj\wpidea\infrastructure\system\System;
use bpmj\wpidea\modules\google_analytics\api\Google_Analytics_API_Static_Helper;

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class Data_Layers {

    const DEFAULT_CURRENCY_CODE = 'PLN';
    const DATA_LAYERS_PREFIX = 'gtm';
    const EMPTY_VALUE = '';

    const DATA_ATTRIBUTE = 'data-' . self::DATA_LAYERS_PREFIX;

    const ON_CLICK_METHOD = 'click';
    const ON_DISPLAY_METHOD = 'display';
    const ON_VIEWPORT_METHOD = 'viewport';
    const ONLY_VIEW = 'only_view';

    const EVENT_PRODUCT_IMPRESSIONS = 'productImpressions';
    const EVENT_PRODUCT_CLICK = 'productClick';
    const EVENT_PURCHASE = 'purchase';
    const EVENT_PRODUCT_DETAIL_IMPRESSION = 'productDetailImpression';


    const EVENT_ADD_TO_CART = 'addToCart';
    const EVENT_VIEW_PRODUCT_VARIANT = 'viewProductVariant';
    const EVENT_REMOVE_FROM_CART = 'removeFromCart';
    const EVENT_CHECKOUT = 'checkout';

    const LIST_HOME = 'Home';
    const LIST_OTHER = 'Other';
    const LIST_NOT_LISTED = 'Not Listed';
    const DEFAULT_QUANTITY = 1;
    const FIRST_STEP = 1;

    private $event;
    private $currency_code = self::DEFAULT_CURRENCY_CODE;
    private $action;
    private $method;
    private $product_required_params;
    private $action_field;
    private $products;

    private $matching_action_and_method_from_event = [
        self::EVENT_PRODUCT_IMPRESSIONS => [
            'action' => 'impressions',
            'method' => self::ON_VIEWPORT_METHOD,
            'product_required_params' => ['position', 'list']
        ],
        self::EVENT_PRODUCT_CLICK => [
            'action' => 'click',
            'method' => self::ON_CLICK_METHOD,
            'product_required_params' => ['position']
        ],
        self::EVENT_PRODUCT_DETAIL_IMPRESSION => [
            'action' => 'detail',
            'method' => self::ON_DISPLAY_METHOD,
            'product_required_params' => []
        ],
        self::EVENT_ADD_TO_CART => [
            'action' => 'add',
            'method' => self::ON_CLICK_METHOD,
            'product_required_params' => ['quantity']
        ],
        self::EVENT_VIEW_PRODUCT_VARIANT => [
            'action' => 'view',
            'method' => self::ONLY_VIEW,
            'product_required_params' => ['quantity']
        ],
        self::EVENT_REMOVE_FROM_CART => [
            'action' => 'remove',
            'method' => self::ON_CLICK_METHOD,
            'product_required_params' => ['quantity']
        ],
        self::EVENT_CHECKOUT => [
            'action' => 'checkout',
            'method' => self::ON_DISPLAY_METHOD,
            'product_required_params' => ['quantity']
        ],
        self::EVENT_PURCHASE => [
            'action' => 'purchase',
            'method' => self::ON_DISPLAY_METHOD,
            'product_required_params' => ['coupon', 'quantity']
        ],
    ];

    public function __construct(string $event)
    {
        $this->event = $event;
        $this->action = $this->matching_action_and_method_from_event[$event]['action'];
        $this->method = $this->matching_action_and_method_from_event[$event]['method'];
        $this->product_required_params = $this->matching_action_and_method_from_event[$event]['product_required_params'];
        $this->currency_code = System::get_currency();
    }

    public static function is_enabled()
    {
        return Analytics::is_gtm_enabled();
    }

    public function get_inline_elements()
    {
        $html = self::DATA_ATTRIBUTE . ' ';

        $html .= $this->get_data_element('event', $this->event);
        $html .= $this->get_data_element('action', $this->action);
        $html .= $this->get_data_element('method', $this->method);
        $html .= $this->get_data_element('currency-code', $this->currency_code);
        $html .= $this->get_data_element('action-field', $this->action_field);
        $html .= $this->get_products_data_elements();

        return $html;
    }

    public static function render_attributes(string $type, $data)
    {
        if(!self::is_enabled() && !Google_Analytics_API_Static_Helper::is_ga4_enabled()) {
            return '';
        }

        $data_layers = new self($type);

        switch ($type){
            case self::EVENT_ADD_TO_CART:
                $data_layers->add_product_by_id($data);
                break;
            case self::EVENT_VIEW_PRODUCT_VARIANT:
                $data_layers->add_product_by_id($data['product_id'], null, null, null, null, $data['variant']);
                break;
            case self::EVENT_PRODUCT_IMPRESSIONS:
                foreach ($data as $product_array) {
                    $data_layers->add_product_by_id($product_array['id'], __( 'Home', BPMJ_EDDCM_DOMAIN ), $product_array['position']);
                }
                break;
            case self::EVENT_PRODUCT_CLICK:
                $data_layers->add_product_by_id($data['product_id'], null, ($data['position'] - 1));
                $data_layers->set_action_field(['list' => $data['list']]);
                break;
            case self::EVENT_REMOVE_FROM_CART:
                $data_layers->add_product_by_id($data['product_id']);
                $data_layers->set_action_field(['list' => $data['list']]);
                break;
        }

        return $data_layers->get_inline_elements();
    }


    public static function render_script(string $type, $data)
    {

        if(!self::is_enabled()) {
            return '';
        }
        return '<script>
                window.dataLayer = window.dataLayer || [];
                dataLayer.push('.json_encode(Data_Layers::render_array_elements($type, $data)).')
              </script>';
    }

    public static function render_array_elements(string $type, $data)
    {
        if(!self::is_enabled()) {
            return '';
        }

        if(!$data){
            return '';
        }

        $data_layers = new self($type);

        switch ($type){
            case self::EVENT_PURCHASE:
                $payment = $data;
                $products = $payment->cart_details;
                $coupon = ($payment->discounts) ?? '';
                foreach ( $products as $key => $product ){
                    $price_id = $product['item_number']['options']['price_id'] ?? null;
                    $data_layers->add_product_by_id($product['id'], null, null, $coupon, edd_format_amount( $product[ 'price' ] ), $price_id);
                }
                $action_field = [
                    'id' => $payment->ID,
                    'affiliation' => $data_layers->get_brand(),
                    'revenue' =>  edd_get_payment_amount( $payment->ID ),
                    'tax' =>  edd_get_payment_tax( $payment->ID ),
                    'shipping' => 0.0,
                    'coupon' => $coupon
                ];
                $data_layers->set_action_field($action_field);
                break;

            case self::EVENT_CHECKOUT:
                foreach ($data as $product) {
                    $price_id = null;
                    if(isset($product['options']) && isset($product['options']['price_id'])){
                        $price_id = $product['options']['price_id'];
                    }
                    $data_layers->add_product_by_id($product['id'], null, null, null, null, $price_id);
                }
                $data_layers->set_action_field(['step' => Data_Layers::FIRST_STEP]);
                break;


            case self::EVENT_PRODUCT_DETAIL_IMPRESSION:
                $data_layers->add_product_by_id($data,null);
                $data_layers->set_list_action_field_by_http_referer();
                break;

        }


        return $data_layers->get_array_elements();
    }

    public function get_array_elements()
    {
        $data = [
            'event' => $this->event,
            'ecommerce' => [
                'currencyCode' => $this->currency_code

            ]
        ];

        if($data['event'] == self::EVENT_PRODUCT_IMPRESSIONS){
            $data['ecommerce'][$this->action] = $this->products;
        } else {
            $data['ecommerce'][$this->action] = [];
            if($this->action_field) {
                $data['ecommerce'][$this->action]['actionField'] = json_decode($this->action_field, true);
                $data['ecommerce'][$this->action]['products'] = $this->products;
            }
        }

        return $data;
    }

    private function get_products_data_elements()
    {
        $html = '';
        $html .= $this->get_data_element('product-count', count($this->products));

        foreach ($this->products as $key => $product){
            $html .= $this->generate_product_html(
                $key,
                $product
            );
        }

        return $html;
    }

    private function get_data_element($name, $value)
    {
        return self::DATA_ATTRIBUTE . '-' . $name ."='" . $value . "' ";
    }

    public function set_list_action_field_by_http_referer()
    {
        $this->action_field = json_encode(['list' => $this->get_list_by_http_referer()]);
    }

    public function get_list_by_http_referer()
    {
        $http_referer = $_SERVER['HTTP_REFERER'];

        if(!$http_referer){
            return __( self::LIST_NOT_LISTED, BPMJ_EDDCM_DOMAIN );
        }

        if($this->get_remote_host() == $http_referer){
            return __( self::LIST_HOME, BPMJ_EDDCM_DOMAIN );
        }

        return self::LIST_OTHER.'('.$http_referer.')';
    }

    public function get_remote_host()
    {
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
            $link = "https";
        } else {
            $link = "http";
        }
        $link .= "://";
        $link .= $_SERVER['HTTP_HOST'];
        $link .= '/';
        return $link;
    }

    public function set_action_field(array $action_field)
    {
        $this->action_field = json_encode($action_field);
    }

    public function add_product_by_id($id, $list = null, $position = null, $coupon = null, $another_price = null, $variant = null)
    {
        $repository = WPI()->repo_locator->getProductRepository();
        /** @var Product $product */
        $product = $repository->find($id);

        $name = $product->getName();
        $price = $another_price ?? $this->getPrice($product);

        if(!$variant && $product->hasVariants()){
            $variant = $product->getDefaultVariantId();
        }
        if($variant && $variantProduct = $product->getVariantProductByOptionId($variant)){
            $name = $name . ' - '. $variantProduct->getName();
            $price = $variantProduct->getPrice();
        }

        $this->add_product(
            $product->getId(),
            $name,
            $price,
            $this->get_product_category($product) ?? self::EMPTY_VALUE,
            $variant ?? $this->get_product_default_variant($product) ?? self::EMPTY_VALUE,
            $this->increase_position($position),
            $list,
            $coupon,
            $product->hasVariants()
        );

    }

    private function getPrice($product)
    {
        return $product->hasPromotionalPrice() ? $product->getPromotionalPrice() : $product->getPrice();
    }

    /**
     * @param $position
     * @return int
     * the position must start with 1
     */
    public function increase_position($position)
    {
        return $position + 1;
    }

    /**
     * @param Product $product
     * @return null
     */
    private function get_product_default_variant($product)
    {
        return $product->getDefaultVariantId();

    }

    private function get_product_category($product)
    {
        if(!$product->getCategories()){
            return null;
        }

        $categories = [];
        foreach ($product->getCategories() as $category){
            $categories[] = $category->getName();
        }
        return (!empty($categories)) ? implode(",", $categories) : null;
    }

    public function generate_product_html($key, $product)
    {
        $id = $product['id'];
        $name = $product['name'];
        $price = $product['price'];
        $category = $product['category'] ??  self::EMPTY_VALUE;
        $variant = $product['variant'] ?? self::EMPTY_VALUE;
        $quantity = $product['quantity'] ?? null;
        $position = $product['position'] ?? null;
        $list = $product['list'] ?? null;
        $coupon = $product['coupon'] ?? null;
        $has_variants = $product['has_variants'] ?? null;

        $html = ' ';

        $html .= $this->get_data_element('product-id['.$key.']', $id);
        $html .= $this->get_data_element('product-name['.$key.']', $name);
        $html .= $this->get_data_element('product-price['.$key.']', $price);
        $html .= $this->get_data_element('product-brand['.$key.']', $this->get_brand());
        $html .= $this->get_data_element('product-category['.$key.']', $category);
        $html .= $this->get_data_element('product-variant['.$key.']', $variant);
        $html .= $this->get_data_element('product-has-variants['.$key.']', $has_variants);

        if($this->check_is_element_required('quantity')){
            $html .= $this->get_data_element('product-quantity['.$key.']', $quantity);
        }
        if($this->check_is_element_required('position')){
            $html .= $this->get_data_element('product-position['.$key.']', $position);
        }
        if($this->check_is_element_required('list')){
            $html .= $this->get_data_element('product-list['.$key.']', $list);
        }
        if($this->check_is_element_required('coupon')){
            $html .= $this->get_data_element('product-coupon['.$key.']', $coupon);
        }

        return $html;
    }

    public function add_product($id, $name, $price, $category = self::EMPTY_VALUE, $variant = self::EMPTY_VALUE, $position = null, $list = null, $coupon = null, $has_variants = false)
    {
        $product['id'] = $id;
        $product['name'] = $name;
        $product['price'] = $price;
        $product['brand'] = $this->get_brand();
        $product['category'] = $category;
        $product['variant'] = $variant;
        $product['has_variants'] = $has_variants;


        if($this->check_is_element_required('quantity')){
            $product['quantity'] = self::DEFAULT_QUANTITY;
        }
        if($this->check_is_element_required('position')){
            $product['position'] = $position;
        }
        if($this->check_is_element_required('list')){
            $product['list'] = $list;
        }
        if($this->check_is_element_required('coupon')){
            $product['coupon'] = $coupon;
        }

        $this->products[] = $product;
    }

    private function check_is_element_required($name)
    {
        if (in_array($name, $this->product_required_params)) {
            return true;
        }
        return false;
    }

    public function get_brand()
    {
        return $_SERVER['HTTP_HOST'];
    }
}
