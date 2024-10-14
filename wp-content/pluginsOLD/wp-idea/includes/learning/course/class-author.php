<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\course;

class Author
{
    protected $id;
    protected $first_name;
    protected $last_name;

    public function __construct(Author_ID $id, string $first_name, string $last_name)
    {
        $this->id = $id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
    }

    public function get_id(): Author_ID
    {
        return $this->id;
    }

    public function get_first_name(): string
    {
        return $this->first_name;
    }

    public function get_last_name(): string
    {
        return $this->last_name;
    }

    public function get_name(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}