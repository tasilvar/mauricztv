<?php
namespace bpmj\wpidea;

class View_Hooks_Data_Layer {

    private static $current_product_list_position = 0;

    public function __construct()
    {
        $this->attach_data_layer_to_hooks();
    }

    private function attach_data_layer_to_hooks(): void
    {
        View_Hooks::on(View_Hooks::BEFORE_PRODUCT_LIST_ITEM, [$this, 'increase_product_position']);

        View_Hooks::on(
            View_Hooks::RENDER_INLINE_ELEMENTS_IN_HYPERLINK_PRODUCT,
            [$this, View_Hooks::RENDER_INLINE_ELEMENTS_IN_HYPERLINK_PRODUCT], 10, 2
        );
        View_Hooks::on(
            View_Hooks::RENDER_INLINE_ELEMENTS_IN_ADD_TO_CART_HYPERLINK,
            [$this, View_Hooks::RENDER_INLINE_ELEMENTS_IN_ADD_TO_CART_HYPERLINK]
        );
        View_Hooks::on(
            View_Hooks::RENDER_INLINE_ELEMENTS_IN_ADD_TO_CART_VARIANT_OPTIONS,
            [$this, View_Hooks::RENDER_INLINE_ELEMENTS_IN_ADD_TO_CART_VARIANT_OPTIONS]
        );
        View_Hooks::on(
            View_Hooks::RENDER_INLINE_ELEMENTS_IN_REMOVE_FROM_CART_HYPERLINK,
            [$this, View_Hooks::RENDER_INLINE_ELEMENTS_IN_REMOVE_FROM_CART_HYPERLINK]
        );
        View_Hooks::on(
            View_Hooks::RENDER_INLINE_ELEMENTS_IN_PRODUCT_LIST_BY_DOWNLOADS,
            [$this, View_Hooks::RENDER_INLINE_ELEMENTS_IN_PRODUCT_LIST_BY_DOWNLOADS]
        );
        View_Hooks::on(
            View_Hooks::RENDER_INLINE_ELEMENTS_IN_PRODUCT_LIST_BY_PRODUCTS_ARRAY,
            [$this, View_Hooks::RENDER_INLINE_ELEMENTS_IN_PRODUCT_LIST_BY_PRODUCTS_ARRAY]
        );
        View_Hooks::on(
            View_Hooks::RENDER_HEAD_ELEMENTS_IN_PURCHASE,
            [$this, View_Hooks::RENDER_HEAD_ELEMENTS_IN_PURCHASE]
        );
        View_Hooks::on(
            View_Hooks::RENDER_HEAD_ELEMENTS_IN_CHECKOUT,
            [$this, View_Hooks::RENDER_HEAD_ELEMENTS_IN_CHECKOUT]
        );
        View_Hooks::on(
            View_Hooks::RENDER_HEAD_ELEMENTS_IN_PRODUCT_PAGE,
            [$this, View_Hooks::RENDER_HEAD_ELEMENTS_IN_PRODUCT_PAGE]
        );
    }

    public function increase_product_position()
    {
        ++$this::$current_product_list_position;
    }

    public function render_inline_elements_in_remove_from_cart_hyperlink($product_id)
    {
        echo Data_Layers::render_attributes(
            Data_Layers::EVENT_REMOVE_FROM_CART, [
            'product_id' => $product_id,
            'list' => __('Home', BPMJ_EDDCM_DOMAIN)
        ]);
    }

    public function render_inline_elements_in_add_to_cart_hyperlink_product($product_id)
    {
        echo Data_Layers::render_attributes(Data_Layers::EVENT_ADD_TO_CART, $product_id);
    }

    public function render_inline_elements_in_add_to_cart_variant_options_product($product_and_variant)
    {
        echo Data_Layers::render_attributes(Data_Layers::EVENT_VIEW_PRODUCT_VARIANT, $product_and_variant);
    }

    public function render_head_elements_in_product_page($product_id)
    {
        echo  Data_Layers::render_script(Data_Layers::EVENT_PRODUCT_DETAIL_IMPRESSION, $product_id);
    }

    public function render_head_elements_in_purchase($payment)
    {
        echo Data_Layers::render_script(Data_Layers::EVENT_PURCHASE, $payment);
    }

    public function render_head_elements_in_checkout($products)
    {
        echo Data_Layers::render_script(Data_Layers::EVENT_CHECKOUT, $products);
    }

    public function render_inline_elements_in_product_list_by_products_array($products)
    {
        $productsArray = [];
        $j = 0;
        foreach ($products as $key => $product){
            $productsArray[] = [
                'id' => $product->getId(),
                'position' => $j
            ];
            $j ++;
        }
        echo Data_Layers::render_attributes(Data_Layers::EVENT_PRODUCT_IMPRESSIONS, $productsArray);
    }

    public function render_inline_elements_in_product_list_by_downloads($downloads)
    {
        $products = [];
        $j = 0;
        while ( $downloads->have_posts() ) : $downloads->the_post();
            $products[] = [
                'id' => get_the_ID(),
                'position' => $j
            ];
            $j ++;
        endwhile;

        echo Data_Layers::render_attributes(Data_Layers::EVENT_PRODUCT_IMPRESSIONS, $products);
    }

    public function render_inline_elements_in_hyperlink_product_title($product_id)
    {
        echo Data_Layers::render_attributes(
            Data_Layers::EVENT_PRODUCT_CLICK, [
            'product_id' => $product_id,
            'position' => $this::$current_product_list_position,
            'list' => __('Home', BPMJ_EDDCM_DOMAIN)
        ]);
    }
}
