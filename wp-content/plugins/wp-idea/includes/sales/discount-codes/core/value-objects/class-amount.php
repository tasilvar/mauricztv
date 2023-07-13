<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\discount_codes\core\value_objects;

use bpmj\wpidea\sales\discount_codes\core\exceptions\Invalid_Discount_Amount_Exception;

class Amount
{
    public const TYPE_PERCENTAGE = 'percent';
    public const TYPE_FLAT = 'flat';

    private const VALID_TYPES = [
        self::TYPE_PERCENTAGE,
        self::TYPE_FLAT
    ];

    private float $amount;
    private string $type;

    public function __construct(
        float $amount,
        string $type
    ) {
        if (!in_array($type, self::VALID_TYPES)) {
            throw new Invalid_Discount_Amount_Exception(
                'Discount type invalid! Provided: ' . $type . '. Should be one of: ' . implode(
                    ', ',
                    self::VALID_TYPES
                )
            );
        }

        $this->amount = $amount;
        $this->type = $type;
    }

    public function get_amount(): float
    {
        return $this->amount;
    }

    public function get_type(): string
    {
        return $this->type;
    }
}