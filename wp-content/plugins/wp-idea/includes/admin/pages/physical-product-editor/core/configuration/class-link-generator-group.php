<?php

namespace bpmj\wpidea\admin\pages\physical_product_editor\core\configuration;

use bpmj\wpidea\admin\pages\product_editor\core\configuration\Abstract_Link_Generator_Group;
use bpmj\wpidea\admin\pages\service_editor\Service_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\physical_product_editor\Physical_Product_Editor_Page_Renderer;

class Link_Generator_Group extends Abstract_Link_Generator_Group
{
    protected function get_id_query_arg_name(): string
    {
        return Physical_Product_Editor_Page_Renderer::PHYSICAL_PRODUCT_ID_QUERY_ARG_NAME;
    }
}