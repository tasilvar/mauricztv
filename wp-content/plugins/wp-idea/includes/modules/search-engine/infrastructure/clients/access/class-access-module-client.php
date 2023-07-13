<?php

namespace bpmj\wpidea\modules\search_engine\infrastructure\clients\access;

use bpmj\wpidea\modules\search_engine\core\clients\access\Interface_Access_Module_Client;
use bpmj\wpidea\sales\product\api\Interface_Product_API;

class Access_Module_Client implements Interface_Access_Module_Client
{
    public function __construct(
        Interface_Product_API $product_api
    ) {
        $this->product_api = $product_api;
    }

    public function check_if_user_has_access_to_content(int $content_id, int $user_id): bool
    {
        $restricted = bpmj_eddpc_is_restricted($content_id);

        if (!$restricted) {
            return true;
        }

        $access = bpmj_eddpc_user_can_access($user_id, $restricted, $content_id);
        if ('valid' === $access['status']) {
            return true;
        }

        return false;
    }

    public function check_if_user_has_access_to_course_product(int $product_id, int $user_id): bool
    {
        return $this->product_api->check_if_user_has_access_to_course_product($product_id, $user_id);
    }
}