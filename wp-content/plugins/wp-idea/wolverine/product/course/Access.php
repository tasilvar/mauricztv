<?php
namespace bpmj\wpidea\wolverine\product\course;

use bpmj\wpidea\wolverine\product\Access as BaseAccess;

class Access extends BaseAccess
{
    public function checkUserAccess($userId)
    {
        $course = WPI()->courses->get_course_by_product( $this->productId );
        $coursePageId = get_post_meta($course->ID, 'course_id', true);
        $restrictedTo  = [['download' => $this->productId]];
        
        $access = bpmj_eddpc_user_can_access($userId, $restrictedTo, $coursePageId);
    
        return $access;
    }
}