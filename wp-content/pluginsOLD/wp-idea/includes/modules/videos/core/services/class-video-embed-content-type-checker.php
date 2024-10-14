<?php

namespace bpmj\wpidea\modules\videos\core\services;

use bpmj\wpidea\learning\course\content\Interface_Readable_Course_Content_Repository;
use bpmj\wpidea\learning\course\content\Course_Content_ID;

class Video_Embed_Content_Type_Checker
{
    private Interface_Readable_Course_Content_Repository $course_content_repository;
    
    public function __construct(
        Interface_Readable_Course_Content_Repository $course_content_repository
    )
    {
        $this->course_content_repository = $course_content_repository;
    }

    public function can_video_be_embedded_on_current_page(): bool
    {
        $post_id = isset($_GET['post']) ? $_GET['post'] : (isset($_POST['post_ID']) ? $_POST['post_ID'] : null);
        
        if(!$post_id){
            return false;
        }

        $course_content_id = new Course_Content_ID($post_id);

        $result = $this->course_content_repository->find_by_id($course_content_id);

        if(empty($result)){
            return false;
        }

        return true;

    }
}