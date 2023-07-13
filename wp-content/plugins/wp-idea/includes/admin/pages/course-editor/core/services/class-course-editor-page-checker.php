<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\course_editor\core\services;

class Course_Editor_Page_Checker
{
    public function is_course_edit_page(?int $current_post_id, ?string $current_post_type, ?string $action = null, bool $edit_description = false): bool
    {
        if (is_null($current_post_id)) {
            return false;
        }

        if ($action !== 'edit') {
            return false;
        }
	    if (!$edit_description) {
		    return false;
	    }

	    if ($current_post_type !== 'courses' && $current_post_type !== 'download' && $current_post_type !== 'page') {
	        return false;
        }

	    return true;
    }
}