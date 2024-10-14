<?php

namespace bpmj\wpidea\modules\increasing_sales\api;


class Increasing_Sales_API_Static_Helper
{
    private static Increasing_Sales_API $increasing_sales_api;

    public static function init(Increasing_Sales_API $increasing_sales_api): void
    {
        self::$increasing_sales_api = $increasing_sales_api;
    }

    public static function render_offers(): string
    {
        return self::$increasing_sales_api->render_offers();
    }
}