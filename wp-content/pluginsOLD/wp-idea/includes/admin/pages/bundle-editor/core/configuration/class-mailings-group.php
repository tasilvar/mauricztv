<?php

namespace bpmj\wpidea\admin\pages\bundle_editor\core\configuration;

use bpmj\wpidea\admin\pages\product_editor\core\configuration\Abstract_Mailings_Group;

class Mailings_Group extends Abstract_Mailings_Group
{
    protected function get_translate_prefix(): string
    {
        return 'service_editor';
    }
}