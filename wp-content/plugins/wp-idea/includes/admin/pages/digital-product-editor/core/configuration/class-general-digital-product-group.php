<?php

namespace bpmj\wpidea\admin\pages\digital_product_editor\core\configuration;

use bpmj\wpidea\admin\pages\product_editor\core\configuration\Abstract_General_Products_Group;
use bpmj\wpidea\admin\pages\digital_product_editor\Digital_Product_Editor_Page_Renderer;

class General_Digital_Product_Group extends Abstract_General_Products_Group
{
    protected function get_translate_prefix(): string
    {
        return 'digital_product_editor';
    }

    protected function get_id_query_arg_name(): string
    {
        return Digital_Product_Editor_Page_Renderer::DIGITAL_PRODUCT_ID_QUERY_ARG_NAME;
    }
}