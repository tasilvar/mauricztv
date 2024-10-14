<?php

declare(strict_types=1);

namespace bpmj\wpidea\learning\services;

use bpmj\wpidea\events\filters\Filter_Name;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\learning\course\content\Course_Content_ID;

class Content_Parent_Getter implements Interface_Content_Parent_Getter
{
    private Interface_Filters $filters;

    public function __construct(
        Interface_Filters $filters
    )
    {
        $this->filters = $filters;
    }

    public function get_parent_content_id_by_course_content_id(Course_Content_ID $id): ?Course_Content_ID
    {
        $parents = $this->filters->apply(Filter_Name::BREADCRUMBS_PARENTS_IDS, get_post_ancestors($id->to_int()));

        if (is_array($parents) && !empty($parents)) {
            $parent_id = (int)$parents[0];
            return $id->to_int() !== $parent_id ? new Course_Content_ID($parent_id) : null;
        }

        return null;
    }
}