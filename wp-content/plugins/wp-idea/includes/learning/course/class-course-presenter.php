<?php namespace bpmj\wpidea\learning\course;

class Course_Presenter
{

    /** @var Course */
    private $course;

    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    public function to_array(): array
    {
        return [
            'id'    => $this->course->get_id(),
            'title' => $this->course->get_title()
        ];
    }
}