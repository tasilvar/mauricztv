<?php

declare(strict_types=1);

namespace bpmj\wpidea\data_types\personal_data;

class Full_Name
{
    private $first_name;
    private $last_name;

    public function __construct(string $first_name, string $last_name)
    {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
    }

    public function get_full_name(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function get_first_name(): string
    {
        return $this->first_name;
    }

    public function get_last_name(): string
    {
        return $this->last_name;
    }
}