<?php

namespace bpmj\wpidea\students\persistence;

class Student_Query_Criteria
{
    private ?string $email = null;
    private ?string $login = null;
    private ?string $name = null;
    private ?array $courses_ids = null;

    public function get_from_query_filters(array $filters): self
    {
        $this->set_email($this->get_filter_value($filters, 'email'));
        $this->set_login($this->get_filter_value($filters, 'login'));
        $this->set_name($this->get_filter_value($filters, 'full_name'));
        $this->set_courses_ids($this->get_filter_value($filters, 'courses'));

        return $this;
    }


    public function set_email(?string $email): void
    {
        $this->email = $email;
    }

    public function set_login(?string $login): void
    {
        $this->login = $login;
    }

    public function set_name(?string $name): void
    {
        $this->name = $name;
    }

    public function set_courses_ids(?array $courses_ids): void
    {
        $this->courses_ids = $courses_ids;
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

    public function get_courses_ids(): ?array
    {
        return $this->courses_ids;
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