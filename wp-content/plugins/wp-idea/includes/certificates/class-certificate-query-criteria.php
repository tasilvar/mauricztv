<?php namespace bpmj\wpidea\certificates;

use bpmj\wpidea\learning\course\Course_ID;
use Carbon\Carbon;

class Certificate_Query_Criteria
{
    private ?Course_ID $course_id = null;

    private ?string $name_query = null;

    private ?string $email_query = null;

    private ?string $certificate_number_query = null;

    private ?\DateTime $dated_from = null;

    private ?\DateTime $dated_to = null;

    private ?string $sort_by_column = null;

    private bool $sort_direction_ascending = true;

    public function get_course_id(): ?Course_ID
    {
        return $this->course_id;
    }

    public function set_course_id(Course_ID $id): self
    {
        $this->course_id = $id;

        return $this;
    }

    public function get_name_query(): ?string
    {
        return $this->name_query;
    }

    public function set_name_query(?string $name_query): self
    {
        $this->name_query = $name_query;

        return $this;
    }

    public function get_email_query(): ?string
    {
        return $this->email_query;
    }

    public function set_email_query(?string $email_query): self
    {
        $this->email_query = $email_query;

        return $this;
    }

    public function get_certificate_number_query(): ?string
    {
        return $this->certificate_number_query;
    }

    public function set_certificate_number_query(?string $certificate_number_query): self
    {
        $this->certificate_number_query = $certificate_number_query;

        return $this;
    }

    public function get_dated_from(): ?\DateTime
    {
        return $this->dated_from;
    }

    public function set_dated_from(?\DateTime $date): self
    {
        $this->dated_from = $date;

        return $this;
    }

    public function get_dated_to(): ?\DateTime
    {
        return $this->dated_to;
    }

    public function set_dated_to(?\DateTime $date): self
    {
        $this->dated_to = $date;

        return $this;
    }

    public function set_sort_by_column(string $column): self
    {
        $this->sort_by_column = $column;

        return $this;
    }

    public function get_sort_by_column(): ?string
    {
        return $this->sort_by_column;
    }

    public function set_sort_direction_ascending(bool $is_asc): self
    {
        $this->sort_direction_ascending = $is_asc;

        return $this;
    }

    public function get_sort_direction_ascending(): bool
    {
        return $this->sort_direction_ascending;
    }

}