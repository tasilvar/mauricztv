<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\course\content;

interface Interface_Readable_Course_Content_Repository
{
    public function find_by_query(string $query): Course_Content_Collection;
    public function find_by_id(Course_Content_ID $course_content_id): ?Course_Content;
}
