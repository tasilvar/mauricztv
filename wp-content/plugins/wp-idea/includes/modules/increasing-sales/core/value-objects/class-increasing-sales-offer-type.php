<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\increasing_sales\core\value_objects;

use Exception;

class Increasing_Sales_Offer_Type
{
    public const UPSELL = 'upsell';
    public const BUMP = 'bump';

    public const VALID_OFFER_TYPE = [
        self::UPSELL,
        self::BUMP
    ];

    private string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, self::VALID_OFFER_TYPE, true)) {
            throw new Exception('Invalid increasing sales make offer provided!');
        }

        $this->value = $value;
    }

    public function equals(Increasing_Sales_Offer_Type $other_value): bool
    {
        return $this->get_value() === $other_value->get_value();
    }

    public function get_value(): string
    {
        return $this->value;
    }
}