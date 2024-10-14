<?php

namespace bpmj\wpidea\admin\pages\service_editor\core\configuration;

use bpmj\wpidea\admin\pages\product_editor\core\configuration\Abstract_Link_Generator_Group;
use bpmj\wpidea\admin\pages\service_editor\Service_Editor_Page_Renderer;

class Link_Generator_Group extends Abstract_Link_Generator_Group
{
    protected function get_id_query_arg_name(): string
    {
        return Service_Editor_Page_Renderer::SERVICE_ID_QUERY_ARG_NAME;
    }
}