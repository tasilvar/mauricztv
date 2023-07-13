<?php
namespace bpmj\wpidea\sales\product;

use bpmj\wpidea\settings\LMS_Settings;

class Flat_Rate_Tax_Symbol_Helper
{

    protected const FLAT_RATE_01 = '2';
    protected const FLAT_RATE_02 = '3';
    protected const FLAT_RATE_03 = '5.5';
    protected const FLAT_RATE_04 = '8.5';
    protected const FLAT_RATE_05 = '10';
    protected const FLAT_RATE_06 = '12.5';
    protected const FLAT_RATE_07 = '15';
    protected const FLAT_RATE_08 = '17';
    protected const FLAT_RATE_09 = '12';
    protected const FLAT_RATE_10 = '14';

    public const NO_TAX_SYMBOL = '';

    public const ENABLE_CHECKBOX_NAME = 'enable_flat_rate_tax_symbol';
    public const FEATURE_ACTIVITY_OPTION_NAME = 'flat_rate_tax_symbol_activity';

    public const META_NAME = 'flat_rate_tax_symbol';

    public const AVAILABLE_TAX_SYMBOLS = [
        self::FLAT_RATE_01,
        self::FLAT_RATE_02,
        self::FLAT_RATE_03,
        self::FLAT_RATE_04,
        self::FLAT_RATE_05,
        self::FLAT_RATE_09,
        self::FLAT_RATE_06,
        self::FLAT_RATE_10,
        self::FLAT_RATE_07,
        self::FLAT_RATE_08,
    ];

    public static function is_enabled()
    {
        return LMS_Settings::get_option(self::ENABLE_CHECKBOX_NAME);
    }

}
