<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\bundle_editor;

use bpmj\wpidea\admin\settings\core\services\Interface_Active_Settings_Groups_Provider;
use bpmj\wpidea\admin\pages\product_editor\core\configuration\Invoices_Group;
use bpmj\wpidea\admin\pages\bundle_editor\core\configuration\General_Bundle_Group;
use bpmj\wpidea\admin\pages\bundle_editor\core\configuration\Mailings_Group;
use bpmj\wpidea\admin\pages\bundle_editor\core\configuration\Content_Bundle_Group;

class Bundle_Editor_Active_Groups_Provider implements Interface_Active_Settings_Groups_Provider
{
    public function get_groups(): array
    {
        return [
            General_Bundle_Group::class,
            Content_Bundle_Group::class,
            Invoices_Group::class,
            Mailings_Group::class
        ];
    }
}