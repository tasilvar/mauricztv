<?php namespace bpmj\wpidea\user;

use bpmj\wpidea\Current_Request;
use bpmj\wpidea\learning\course\Course;

class User_Query_Criteria
{
    private ?string $email = null;
    private ?string $login = null;
    private ?string $name = null;
    private ?array $course_ids = null;
    private ?bool $must_have_courses = null;
    private ?array $roles = null;

    public function get_from_query_filters(array $filters): User_Query_Criteria
    {
        $this->set_email($this->get_filter_value($filters, 'email'));
        $this->set_login($this->get_filter_value($filters, 'login'));
        $this->set_name($this->get_filter_value($filters, 'full_name'));
        $this->set_has_one_of_courses_with_id($this->get_filter_value($filters, 'courses'));
        $this->set_roles($this->get_filter_value($filters, 'roles'));

        return $this;
    }

    public function set_email(?string $email): User_Query_Criteria
    {
        $this->email = $email;

        return $this;
    }

    public function set_login(?string $login): User_Query_Criteria
    {
        $this->login = $login;

        return $this;
    }

    public function set_name(?string $name): User_Query_Criteria
    {
        $this->name = $name;

        return $this;
    }

    public function set_has_one_of_courses_with_id(?array $course_ids): User_Query_Criteria
    {
        $this->course_ids = $course_ids;

        return $this;
    }

    public function set_must_have_courses(?bool $value): User_Query_Criteria
    {
        $this->must_have_courses = $value;

        return $this;
    }

    public function get_email(): ?string
    {
        return $this->email;
    }

    public function get_login(): ?string
    {
        return $this->login;
    }

    public function get_name(): ?string
    {
        return $this->name;
    }

    public function get_course_ids(): ?array
    {
        return $this->course_ids;
    }

    public function get_must_have_courses(): ?bool
    {
        return $this->must_have_courses;
    }

    public function set_roles(?array $roles): User_Query_Criteria
    {
        $this->roles = $roles;

        return $this;
    }

    public function get_roles(): ?array
    {
        return $this->roles;
    }

    private function get_filter_value(array $filters, string $filter_name)
    {
        foreach ($filters as $filter) {
            if ($filter['id'] === $filter_name) {
                return $filter['value'];
            }
        }

        return null;
    }

}