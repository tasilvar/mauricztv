<?php
namespace bpmj\wpidea\sales\product;


use bpmj\wpidea\sales\product\api\Product_API;

class Custom_Purchase_Links_Helper
{
    public const PRODUCT_OPTION_SLUG = 'custom_purchase_link';
    public const NO_CUSTOM_PURCHASE_LINK = '';

    public static function feature_is_active(): bool
    {
        $product_api = Product_API::get_instance();
        return $product_api->has_access_to_custom_purchase_links();
    }

    public static function get_custom_purchase_link_as_string(int $product_id): string
    {
        if(!self::feature_is_active()){
            return self::NO_CUSTOM_PURCHASE_LINK;
        }

        return get_post_meta($product_id, self::PRODUCT_OPTION_SLUG, true) ?? self::NO_CUSTOM_PURCHASE_LINK;
    }
}
