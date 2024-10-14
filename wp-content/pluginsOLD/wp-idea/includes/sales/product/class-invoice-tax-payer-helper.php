<?php

namespace bpmj\wpidea\sales\product;

use bpmj\wpidea\settings\LMS_Settings;

class Invoice_Tax_Payer_Helper
{
    public const ENABLE_OPTION_NAME = 'invoices_is_vat_payer';

    public const DEFAULT_VAT_RATE_OPTION_NAME = 'invoices_default_vat_rate';

    public static function is_enabled(): bool
    {
        return 'yes' === LMS_Settings::get_option(self::ENABLE_OPTION_NAME);
    }

    public static function get_default_vat_rate(): string
    {
        $vat_rate = LMS_Settings::get_option(self::DEFAULT_VAT_RATE_OPTION_NAME);

        return empty( $vat_rate ) ? '23' : $vat_rate;
    }
}