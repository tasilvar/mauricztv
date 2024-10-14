<?php

namespace bpmj\wpidea\learning\quiz\model;

class Quiz_File
{
    private ?int $id;
    private string $name;
    private string $url;

    private function __construct(
        ?int   $id,
        string $name,
        string $url
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
    }

    public static function create(
        ?int $id,
        string $name,
        string $url
    ): self
    {
        return new self(
            $id,
            $name,
            $url
        );
    }

    public function get_id(): ?int
    {
        return $this->id;
    }

    public function get_name(): string
    {
        return $this->name;
    }

    public function get_url(): string
    {
        return $this->url;
    }
}