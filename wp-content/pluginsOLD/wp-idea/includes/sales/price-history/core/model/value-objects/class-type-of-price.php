<?php

namespace bpmj\wpidea\sales\price_history\core\model\value_objects;

use Exception;

class Type_Of_Price
{
    public const REGULAR = 'regular';
    public const PROMO = 'promo';
    public const ALL_TYPES = [
        self::REGULAR,
        self::PROMO,
    ];

    private string $type_of_price;

    public function __construct(string $type_of_price)
    {
        if (!in_array($type_of_price, self::ALL_TYPES, true)) {
            throw new Exception('Invalid type of price provided!');
        }

        $this->type_of_price = $type_of_price;
    }

    public function equals(Type_Of_Price $other_type_of_price): bool
    {
        return $this->get_value() === $other_type_of_price->get_value();
    }

    public function get_value(): string
    {
        return $this->type_of_price;
    }
}