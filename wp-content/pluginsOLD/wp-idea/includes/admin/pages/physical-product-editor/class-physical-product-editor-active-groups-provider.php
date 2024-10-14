<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\physical_product_editor;

use bpmj\wpidea\admin\settings\core\services\Interface_Active_Settings_Groups_Provider;
use bpmj\wpidea\admin\pages\physical_product_editor\core\configuration\{General_Physical_Products_Group, Mailings_Group, Link_Generator_Group, Invoices_Group};
use bpmj\wpidea\admin\pages\product_editor\core\configuration\Discount_Codes_Group;

class Physical_Product_Editor_Active_Groups_Provider implements Interface_Active_Settings_Groups_Provider
{
    public function get_groups(): array
    {
        return [
            General_Physical_Products_Group::class,
            Link_Generator_Group::class,
            Invoices_Group::class,
            Mailings_Group::class,
            Discount_Codes_Group::class
        ];
    }
}