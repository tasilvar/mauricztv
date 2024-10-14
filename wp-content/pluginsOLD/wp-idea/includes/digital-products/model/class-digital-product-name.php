<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\digital_products\model;

class Digital_Product_Name
{
    private string $name;

    public function __construct(
        string $name
    ) {
        $this->name = $name;
    }

    public function get_value(): string
    {
        return trim($this->name);
    }
}