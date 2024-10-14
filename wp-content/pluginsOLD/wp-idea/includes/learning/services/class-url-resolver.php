<?php

declare(strict_types=1);

namespace bpmj\wpidea\learning\services;

use bpmj\wpidea\data_types\Url;
use bpmj\wpidea\learning\course\content\Course_Content_ID;

class Url_Resolver implements Interface_Url_Resolver
{
    /**
     * @throws \bpmj\wpidea\data_types\exceptions\Invalid_Url_Exception
     */
    public function get_by_course_content_id(Course_Content_ID $id): ?Url
    {
        $url = get_permalink($id->to_int());
        if (!$url) {
            return null;
        }

        return new Url($url);
    }
}