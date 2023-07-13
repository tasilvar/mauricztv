<?php
/**
 * This file is licenses under proprietary license
 */
namespace bpmj\wpidea\helpers;

use bpmj\wpidea\sales\product\Invoice_Tax_Payer_Helper;
use bpmj\wpidea\sales\product\Meta_Helper as Product_Meta_Helper;

class Price_Formatting
{
    public const MULTIPLY_BY_1 = 1;
    public const MULTIPLY_BY_10 = 10;
    public const MULTIPLY_BY_100 = 100;
    public const NO_DIVISION = 1;
    public const DIVIDE_BY_100 = 100;

    public static function round_and_format_to_int($number, $multiply_by = self::MULTIPLY_BY_1): int
    {
        $number = (((float)$number) * $multiply_by);
        return (int)round($number);
    }

    public static function format_to_float(int $number, $divide_by = self::NO_DIVISION): float
    {
        $number = (($number) / $divide_by);

        return (float)$number;
    }
}
