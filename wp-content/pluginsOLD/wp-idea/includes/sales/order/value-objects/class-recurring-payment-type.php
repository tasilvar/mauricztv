<?php
declare(strict_types=1);

namespace bpmj\wpidea\sales\order\value_objects;

use bpmj\wpidea\sales\order\exceptions\Invalid_Recurring_Payment_Type_Exception;

class Recurring_Payment_Type
{
    public const RECURRING_PAYMENT_MANUAL = 'manual';
    public const RECURRING_PAYMENT_AUTOMATIC = 'automatic';
    public const RECURRING_PAYMENT_NO = 'no';
    public const ALL_TYPES = [
        self::RECURRING_PAYMENT_AUTOMATIC,
        self::RECURRING_PAYMENT_MANUAL,
        self::RECURRING_PAYMENT_NO
    ];

    private string $recurring_type;

    public function __construct(string $recurring_type)
    {
        if (!in_array($recurring_type, self::ALL_TYPES)) {
            throw new Invalid_Recurring_Payment_Type_Exception('Invalid recurring payment type');
        }

        $this->recurring_type = $recurring_type;
    }

    public function get_value(): string
    {
        return $this->recurring_type;
    }
}