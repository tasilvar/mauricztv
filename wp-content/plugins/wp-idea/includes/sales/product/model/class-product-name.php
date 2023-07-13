<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\model;

class Product_Name
{
    private string $name;

    public function __construct(
        string $name
    ) {
        $this->name = $name;
    }

    public function get_value(): string
    {
        return $this->name;
    }
}