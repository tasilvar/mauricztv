<?php namespace bpmj\wpidea\certificates;

use bpmj\wpidea\data_types\certificate\Certificate_Number;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\user\User_ID;

interface Interface_Certificate
{
    public function get_id(): Certificate_ID;

    public function get_user_id(): User_ID;

    public function get_course_id(): Course_ID;

    public function get_created(): \DateTime;

    public function get_certificate_number(): ?Certificate_Number;
}