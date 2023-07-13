<?php
namespace bpmj\wpidea\modules\opinions\api;

use bpmj\wpidea\modules\opinions\core\collections\Opinion_Collection;

class Opinions_API_Static_Helper
{
    private static Opinions_API $opinions_api;

    public static function init(Opinions_API $opinions_api): void
    {
        self::$opinions_api = $opinions_api;
    }

    public static function get_opinions_by_product_id(int $product_id, int $per_page = 0, int $page = 1): Opinion_Collection
    {
        return self::$opinions_api->get_opinions_by_product_id($product_id, $per_page, $page);
    }

    public static function get_count_opinions_by_product_id(int $product_id): int
    {
        return self::$opinions_api->get_count_opinions_by_product_id($product_id);
    }

    public static function is_enabled(): bool
    {
        return self::$opinions_api->is_enabled();
    }
}