<?php
namespace bpmj\wpidea;

class View_Hooks {

    const RENDER_INLINE_ELEMENTS_IN_HYPERLINK_PRODUCT = 'render_inline_elements_in_hyperlink_product_title';
    const RENDER_INLINE_ELEMENTS_IN_ADD_TO_CART_HYPERLINK = 'render_inline_elements_in_add_to_cart_hyperlink_product';

    const RENDER_INLINE_ELEMENTS_IN_ADD_TO_CART_VARIANT_OPTIONS = 'render_inline_elements_in_add_to_cart_variant_options_product';
    const RENDER_INLINE_ELEMENTS_IN_PRODUCT_LIST_BY_DOWNLOADS = 'render_inline_elements_in_product_list_by_downloads';
    const RENDER_INLINE_ELEMENTS_IN_PRODUCT_LIST_BY_PRODUCTS_ARRAY = 'render_inline_elements_in_product_list_by_products_array';

    const RENDER_HEAD_ELEMENTS_IN_PURCHASE = 'render_head_elements_in_purchase';
    const RENDER_HEAD_ELEMENTS_IN_CHECKOUT = 'render_head_elements_in_checkout';
    const RENDER_HEAD_ELEMENTS_IN_PRODUCT_PAGE = 'render_head_elements_in_product_page';
    const RENDER_INLINE_ELEMENTS_IN_REMOVE_FROM_CART_HYPERLINK = 'render_inline_elements_in_remove_from_cart_hyperlink';

    const BEFORE_PRODUCT_LIST_ITEM = 'before_product_list_item';

    public static function run(string $hook_name, ...$params): void
    {
        do_action($hook_name, ...$params);
    }

    public static function on(string $hook_name, $callback, int $priority = 10, int $accepted_args = 1): void
    {
        add_action($hook_name, $callback, $priority, $accepted_args);
    }


}
