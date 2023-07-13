<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\model;

class Product_Flat_Rate_Tax_Symbol
{
    private string $value;

    public function __construct(
        string $value
    ) {
        $this->value = $value;
    }

    public function get_value(): string
    {
        return $this->value;
    }
}