<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\digital_products\model;

class Included_File
{
    private ?int $id;

    private string $name;

    private string $url;

    public function __construct(
        ?int $id, string $name, string $url
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
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