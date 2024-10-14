<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\bundle_editor\core\events\handlers;

use bpmj\wpidea\admin\pages\bundle_editor\core\configuration\General_Bundle_Group;
use bpmj\wpidea\admin\pages\bundle_editor\core\events\Event_Name;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\app\bundles\Bundles_App_Service;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\sales\product\model\Product_ID;

class Bundle_Editor_Field_Update_Handler implements Interface_Initiable
{
    private Bundles_App_Service $bundles_app_service;
    private Interface_Events $events;

    public function __construct(
        Bundles_App_Service $bundles_app_service,
        Interface_Events $events
    ) {
        $this->bundles_app_service = $bundles_app_service;
        $this->events = $events;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::FIELD_UPDATED, [$this, 'field_update'], 10, 2);
    }

    public function field_update(Abstract_Setting_Field $field, Product_ID $product_id): void
    {
        if (!$product_id) {
            return;
        }

        if ($field->get_name() === General_Bundle_Group::VARIABLE_PRICING) {
            $this->bundles_app_service->disable_sale($product_id);
        }
    }
}