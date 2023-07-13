<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\service_editor;

use bpmj\wpidea\admin\settings\core\services\Interface_Active_Settings_Groups_Provider;
use bpmj\wpidea\admin\pages\service_editor\core\configuration\{General_Services_Group, Mailings_Group, Link_Generator_Group, Invoices_Group};
use bpmj\wpidea\admin\pages\product_editor\core\configuration\Discount_Codes_Group;

class Service_Editor_Active_Groups_Provider implements Interface_Active_Settings_Groups_Provider
{
    public function get_groups(): array
    {
        return [
            General_Services_Group::class,
            Link_Generator_Group::class,
            Invoices_Group::class,
            Mailings_Group::class,
            Discount_Codes_Group::class
        ];
    }
}