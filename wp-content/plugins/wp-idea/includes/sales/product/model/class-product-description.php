<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\model;

class Product_Description
{
    private string $description;

    public function __construct(
        string $description
    ) {
        $this->description = $description;
    }

    public function get_value(): string
    {
        return $this->description;
    }
}