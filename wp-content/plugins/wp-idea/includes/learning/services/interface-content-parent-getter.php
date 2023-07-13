<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\services;

use bpmj\wpidea\learning\course\content\Course_Content_ID;

interface Interface_Content_Parent_Getter
{
    public function get_parent_content_id_by_course_content_id(Course_Content_ID $id): ?Course_Content_ID;
}