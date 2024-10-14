<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\database;

class Sort_By
{
    public string $property;

    public bool $desc;

    public function __construct(
        string $property,
        bool $desc = false
    )
    {
        $this->property = $property;
        $this->desc = $desc;
    }
}