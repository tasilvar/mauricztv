<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\service_editor;

use bpmj\wpidea\app\services\Services_App_Service;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\service\model\Service_ID;
use WP_Post;

class Service_Editor_Page_Checker
{
    private Services_App_Service $services_app_service;

    private bool $is_service_offer_edit_page = false;

    public function __construct(
        Services_App_Service $services_app_service
    ) {
        $this->services_app_service = $services_app_service;
    }

    public function is_service_offer_edit_page(?int $current_post_id, ?string $current_post_type, ?string $action = null): bool
    {
        if ($this->is_service_offer_edit_page) {
            return true;
        }

        if($action !== 'edit') {
            return false;
        }

        if (is_null($current_post_id)) {
            return false;
        }

        if ($current_post_type !== Service_Editor_Handler::OFFER_POST_TYPE) {
            return false;
        }

        if (is_null(
            $this->services_app_service->find_service_by_offer_id(
                new Service_ID($current_post_id)
            )
        )) {
            return false;
        }

        $this->is_service_offer_edit_page = true;

        return true;
    }
}