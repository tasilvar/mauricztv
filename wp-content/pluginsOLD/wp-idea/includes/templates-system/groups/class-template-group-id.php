<?php

namespace bpmj\wpidea\templates_system\groups;

use bpmj\wpidea\helpers\Guid_Generator;

class Template_Group_Id
{
    private $id;

    private function __construct(string $id = null)
    {
        $this->id = $id ?: Guid_Generator::generate();
    }

    public static function create(): self
    {
        return new self();
    }

    public static function from_string(string $group_id): self
    {
        return new self($group_id);
    }

    public function equals(self $other_id): bool
    {
        return $this->stringify() === $other_id->stringify();
    }

    public function stringify(): string
    {
        return $this->id;
    }
}