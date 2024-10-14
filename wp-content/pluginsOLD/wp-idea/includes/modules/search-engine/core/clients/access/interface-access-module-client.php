<?php

namespace bpmj\wpidea\modules\search_engine\core\clients\access;

interface Interface_Access_Module_Client
{
    public function check_if_user_has_access_to_content(int $content_id, int $user_id): bool;

    public function check_if_user_has_access_to_course_product(int $product_id, int $user_id): bool;
}