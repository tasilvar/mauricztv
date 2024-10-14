<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\physical_product_editor;

use bpmj\wpidea\service\model\Service_ID;
use bpmj\wpidea\app\physical_products\Physical_Products_App_Service;
use bpmj\wpidea\physical_product\dto\Physical_Product_DTO;
use bpmj\wpidea\physical_product\model\Physical_Product_ID;

class Physical_Product_Editor_Page_Checker
{
    private Physical_Products_App_Service $physical_products_app_service;

    private bool $is_physical_product_offer_edit_page = false;

    public function __construct(
        Physical_Products_App_Service $physical_products_app_service
    ) {
        $this->physical_products_app_service = $physical_products_app_service;
    }

    public function is_physical_product_offer_edit_page(
        ?int $current_post_id,
        ?string $current_post_type,
        ?string $action = null
    ): bool {
        if ($this->is_physical_product_offer_edit_page) {
            return true;
        }

        if ($action !== 'edit') {
            return false;
        }

        if (is_null($current_post_id)) {
            return false;
        }

        if ($current_post_type !== Physical_Product_Editor_Handler::OFFER_POST_TYPE) {
            return false;
        }

        if (is_null(
            $this->physical_products_app_service->find_physical_product_by_offer_id(
                new Physical_Product_ID($current_post_id)
            )
        )) {
            return false;
        }

        $this->is_physical_product_offer_edit_page = true;

        return true;
    }
}