<?php namespace bpmj\wpidea\certificates;

use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;
use bpmj\wpidea\user\Interface_User_Repository;

class Certificate_Display_Service
{
    private Interface_Readable_Course_Repository $course_repository;
    private Interface_User_Repository $user_repository;

    public function __construct(
        Interface_Readable_Course_Repository $course_repository,
        Interface_User_Repository $user_repository
    ) {
        $this->course_repository = $course_repository;
        $this->user_repository   = $user_repository;
    }

    public function get_display_model_collection(Certificate_Collection $collection
    ): Certificate_Display_Model_Collection {
        $display_model_collection = new Certificate_Display_Model_Collection();
        foreach ($collection as $certificate) {
            $course        = $this->course_repository->find_by_id($certificate->get_course_id());
            $user          = $this->user_repository->find_by_id($certificate->get_user_id());
            $display_model = new Certificate_Display_Model($certificate, $course, $user);
            $display_model_collection->add($display_model);
        }

        return $display_model_collection;
    }

}