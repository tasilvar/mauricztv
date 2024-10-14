<?php namespace bpmj\wpidea\learning\course;

use Iterator;

class Course_Collection implements Iterator
{
    private $courses = [];
    private $current_position = 0;

    public function add(Course $course): Course_Collection
    {
        $this->courses[] = $course;

        return $this;
    }

    public function remove(Course $course_to_remove): bool
    {
        $result = false;
        foreach ($this->courses as $key => $course) {
            if ($course->get_id() === $course_to_remove->get_id()) {
                unset($this->courses[$key]);
                $result = true;
            }
        }

        return $result;
    }

    public function to_array(): array
    {
        $result = [];
        foreach ($this->courses as $course) {
            $result[] = (new Course_Presenter($course))->to_array();
        }

        return $result;
    }

    public function current(): Course
    {
        return $this->courses[$this->current_position];
    }

    public function next(): void
    {
        ++$this->current_position;
    }

    public function key(): int
    {
        return $this->current_position;
    }

    public function valid(): bool
    {
        return isset($this->courses[$this->current_position]);
    }

    public function rewind(): void
    {
        $this->current_position = 0;
    }
}
