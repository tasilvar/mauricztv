<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\service\model;

class Service
{
    private ?Service_ID $id;
    private Service_Name $name;

    private function __construct(
        ?Service_ID $id,
        Service_Name $name
    )
    {
        $this->id = $id;
        $this->name = $name;
    }

    public static function create(
        ?Service_ID $id,
        Service_Name $name
    ): self
    {
        return new self(
            $id,
            $name,
        );
    }

    public function get_id(): ?Service_ID
    {
        return $this->id;
    }

    public function get_name(): Service_Name
    {
        return $this->name;
    }
}
