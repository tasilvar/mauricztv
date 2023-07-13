<?php

namespace bpmj\wpidea\admin\pages\service_editor\core\configuration;

use bpmj\wpidea\admin\pages\product_editor\core\configuration\Abstract_General_Products_Group;
use bpmj\wpidea\admin\pages\service_editor\Service_Editor_Page_Renderer;

class General_Services_Group extends Abstract_General_Products_Group
{
    protected function get_translate_prefix(): string
    {
        return 'service_editor';
    }

    protected function get_id_query_arg_name(): string
    {
        return Service_Editor_Page_Renderer::SERVICE_ID_QUERY_ARG_NAME;
    }
}