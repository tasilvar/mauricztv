<?php

namespace bpmj\wpidea\admin\support;

class Links
{
    public const DOCS = 'https://poznaj.publigo.pl/';
    public const SUPPORT_PROFITS = 'https://publigo.pl/';
    public const GOLDEN_RULES = 'https://poznaj.publigo.pl/articles/219967-zote-zasady-wsparcia';
    public const FAQ = 'https://poznaj.publigo.pl/';
    public const COMPARISON_TABLE = 'https://wpidea.pl/f/support/';

    public static function get_purchase_link(): string
    {
        return WPI()->packages->get_renewal_url();
    }

    public static function get_localized_comparison_table_link(): string
    {
        $locale_code = get_locale();
        $locale = $locale_code === 'pl_PL' ? 'pl' : strstr($locale_code, '_', true);

        return self::COMPARISON_TABLE . '?lang=' . $locale;
    }
}
