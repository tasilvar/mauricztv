<?php

namespace bpmj\wpidea\ls_cache;

use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\app\digital_products\Digital_Products_App_Service;
use bpmj\wpidea\sales\product\model\Product_ID;

class Ls_Cache_Handler
{
    const COOKIE_NAME = 'wpi_cart_not_empty';
    const COOKIE_TIME = 365 * 24 * 60 * 60;

    private Interface_Actions $actions;
    private Digital_Products_App_Service $digital_products_app_service;
    
    /**
     * @var LS_Cache_Status_Checker
     */
    protected $status_checker;
    
    public function __construct(
        LS_Cache_Status_Checker $status_checker,
        Interface_Actions $actions,
        Digital_Products_App_Service $digital_products_app_service
    )
    {
        $this->status_checker = $status_checker;
        $this->actions = $actions;
        $this->digital_products_app_service = $digital_products_app_service;
        
        if (! $this->status_checker->is_caching_enabled() ) {
            return;
        }

        $this->add_hooks();
    }

    private function add_hooks()
    {
        $hooks_to_disable_cache = [
            'edd_add_to_cart',
            'edd_add_to_cart_item',
            'wp_ajax_edd_add_to_cart',
        ];

        foreach ( $hooks_to_disable_cache as $hook_name ) {
            add_action( $hook_name, [ $this, 'disable_cache' ] ) ;
        }
        
        $hooks_to_maybe_enable_cache = [
            'edd_ajax_remove_from_cart',
            'wp_ajax_edd_remove_from_cart',
            'edd_post_remove_from_cart',
        ];
        
        foreach ( $hooks_to_maybe_enable_cache as $hook_name ) {
            add_action( $hook_name, [ $this, 'maybe_enable_cache' ] ) ;
        }
        
        $this->actions->add(Action_Name::PROCESS_VERIFIED_DOWNLOAD, [$this, 'disable_cache_for_product_download']);
    }

    public function disable_cache($item)
    {
        setcookie( self::COOKIE_NAME, 'cart', time() + self::COOKIE_TIME, COOKIEPATH, COOKIE_DOMAIN );

        return $item;
    }
    
    public function maybe_enable_cache($item)
    {
        if($this->is_cart_emtpy()) {
            setcookie( self::COOKIE_NAME, 'cart', time() - self::COOKIE_TIME, COOKIEPATH, COOKIE_DOMAIN );
        }

        return $item;
    }

    public function disable_cache_for_product_download($download_id) : void
    {
        $is_digital_product_download = $this->digital_products_app_service->find_digital_product_by_offer_id(
            new Product_ID((int)$download_id)
        );

        if(!$is_digital_product_download){
            return;
        }

        $this->actions->do( 'litespeed_control_set_nocache', 'nocache due to it is a product download' );
        ob_end_clean();    
    }

    private function is_cart_emtpy() : bool
    {
        return empty(EDD()->session->get( 'edd_cart' ));
    }
}
