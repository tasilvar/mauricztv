<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\model;

class Product_Short_Description
{
    private string $text;

    public function __construct(
        string $text
    ) {
        $this->text = $text;
    }

    public function get_value(): string
    {
        return $this->text;
    }
}