<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\digital_product_editor;

use bpmj\wpidea\app\digital_products\Digital_Products_App_Service;
use bpmj\wpidea\sales\product\model\Product_ID;
use WP_Post;

class Digital_Product_Editor_Page_Checker
{
    private Digital_Products_App_Service $digital_products_app_service;

    private bool $is_digital_product_offer_edit_page = false;

    public function __construct(
        Digital_Products_App_Service $digital_products_app_service
    )
    {
        $this->digital_products_app_service = $digital_products_app_service;
    }

    public function is_digital_product_offer_edit_page(?int $current_post_id, ?string $current_post_type): bool
    {
        if($this->is_digital_product_offer_edit_page) {
            return true;
        }

        if(is_null($current_post_id)) {
            return false;
        }

        if($current_post_type !== Digital_Product_Editor_Handler::OFFER_POST_TYPE) {
            return false;
        }

        if(is_null(
            $this->digital_products_app_service->find_digital_product_by_offer_id(
                new Product_ID($current_post_id)
            )
        )) {
            return false;
        }

        $this->is_digital_product_offer_edit_page = true;

        return true;
    }
}