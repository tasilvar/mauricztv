<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\settings;

use bpmj\wpidea\admin\settings\core\services\Interface_Active_Settings_Groups_Provider;
use bpmj\wpidea\admin\settings\core\configuration\General_Settings_Group;
use bpmj\wpidea\admin\settings\core\configuration\Accounting_Settings_Group;
use bpmj\wpidea\admin\settings\core\configuration\Payments_Settings_Group;
use bpmj\wpidea\admin\settings\core\configuration\Design_Settings_Group;
use bpmj\wpidea\admin\settings\core\configuration\Integrations_Settings_Group;
use bpmj\wpidea\admin\settings\core\configuration\Cart_Settings_Group;
use bpmj\wpidea\admin\settings\core\configuration\Messages_Settings_Group;
use bpmj\wpidea\admin\settings\core\configuration\Gift_Settings_Group;
use bpmj\wpidea\admin\settings\core\configuration\Certificate_Settings_Group;
use bpmj\wpidea\admin\settings\core\configuration\Analytics_Settings_Group;
use bpmj\wpidea\admin\settings\core\configuration\Modules_Settings_Group;
use bpmj\wpidea\admin\settings\core\configuration\Advanced_Settings_Group;

class App_Settings_Active_Groups_Provider implements Interface_Active_Settings_Groups_Provider
{
    public function get_groups(): array
    {
        return [
            General_Settings_Group::class,
            Accounting_Settings_Group::class,
            Payments_Settings_Group::class,
            Design_Settings_Group::class,
            Integrations_Settings_Group::class,
            Cart_Settings_Group::class,
            Messages_Settings_Group::class,
            Gift_Settings_Group::class,
            Certificate_Settings_Group::class,
            Analytics_Settings_Group::class,
            Modules_Settings_Group::class,
            Advanced_Settings_Group::class
        ];
    }
}