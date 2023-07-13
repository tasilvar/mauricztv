<?php namespace bpmj\wpidea\certificates;

use bpmj\wpidea\data_types\certificate\Certificate_Number;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\user\User_ID;
use DateTime;

class Certificate implements Interface_Certificate
{
    private Certificate_ID $id;

    private User_ID $user_id;

    private Course_ID $course_id;

    private DateTime $created;

    private ?Certificate_Number $certificate_number;

    public function __construct(
        Certificate_ID $id,
        User_ID $user_id,
        Course_ID $course_id,
        DateTime $created,
        ?Certificate_Number $certificate_number
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->course_id = $course_id;
        $this->created = $created;
        $this->certificate_number = $certificate_number;
    }

    public function get_id(): Certificate_ID
    {
        return $this->id;
    }

    public function get_user_id(): User_ID
    {
        return $this->user_id;
    }

    public function get_course_id(): Course_ID
    {
        return $this->course_id;
    }

    public function get_created(): DateTime
    {
        return $this->created;
    }

    public function get_certificate_number(): ?Certificate_Number
    {
        return $this->certificate_number;
    }
}