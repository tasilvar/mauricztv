<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\courses\core\dto;

class Price_Variants_DTO
{
    public bool $has_pricing_variants = false;

    public array $variable_prices = [];
}