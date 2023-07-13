<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\services;

use bpmj\wpidea\data_types\Url;
use bpmj\wpidea\learning\course\content\Course_Content_ID;

interface Interface_Url_Resolver
{
    public function get_by_course_content_id(Course_Content_ID $id): ?Url;
}