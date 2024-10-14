<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\logs\core\events\external\handlers;

use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\sales\product\core\event\Product_Field_Value_Changed_Event_Payload;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_Current_User_Getter;
use Psr\Log\LoggerInterface;

class Product_Deleted_Handler implements Interface_Event_Handler
{
    private Interface_Current_User_Getter $current_user_getter;
    private Interface_Events $events;
    private LoggerInterface $logger;
    private Interface_Translator $translator;

    public function __construct(
        Interface_Current_User_Getter $current_user_getter,
        Interface_Events $events,
        LoggerInterface $logger,
        Interface_Translator $translator
    ) {
        $this->current_user_getter = $current_user_getter;
        $this->events = $events;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::COURSE_DELETED, function (Product_Field_Value_Changed_Event_Payload $payload) {
            $this->handle_course_deleted_logs($payload);
        });

        $this->events->on(Event_Name::SERVICES_DELETED, function (Product_Field_Value_Changed_Event_Payload $payload) {
            $this->handle_services_deleted_logs($payload);
        });

        $this->events->on(Event_Name::DIGITAL_PRODUCT_DELETED, function (Product_Field_Value_Changed_Event_Payload $payload) {
            $this->handle_digital_product_deleted_logs($payload);
        });

        $this->events->on(Event_Name::PHYSICAL_PRODUCT_DELETED, function (Product_Field_Value_Changed_Event_Payload $payload) {
            $this->handle_physical_product_deleted_logs($payload);
        });

        $this->events->on(Event_Name::BUNDLE_DELETED, function (Product_Field_Value_Changed_Event_Payload $payload) {
            $this->handle_bundle_deleted_logs($payload);
        });
    }

    private function handle_course_deleted_logs(Product_Field_Value_Changed_Event_Payload $payload): void
    {
        $this->handle_product_deleted_logs($payload, Product_Field_Value_Changed_Event_Payload::SETTINGS_TYPE_COURSE);
    }

    private function handle_services_deleted_logs(Product_Field_Value_Changed_Event_Payload $payload): void
    {
        $this->handle_product_deleted_logs($payload, Product_Field_Value_Changed_Event_Payload::SETTINGS_TYPE_SERVICE);
    }

    private function handle_digital_product_deleted_logs(Product_Field_Value_Changed_Event_Payload $payload): void
    {
        $this->handle_product_deleted_logs($payload, Product_Field_Value_Changed_Event_Payload::SETTINGS_TYPE_DIGITAL_PRODUCT);
    }

    private function handle_physical_product_deleted_logs(Product_Field_Value_Changed_Event_Payload $payload): void
    {
        $this->handle_product_deleted_logs($payload, Product_Field_Value_Changed_Event_Payload::SETTINGS_TYPE_PHYSICAL_PRODUCT);
    }

    private function handle_bundle_deleted_logs(Product_Field_Value_Changed_Event_Payload $payload): void
    {
        $this->handle_product_deleted_logs($payload, Product_Field_Value_Changed_Event_Payload::SETTINGS_TYPE_BUNDLE);
    }

    private function handle_product_deleted_logs(Product_Field_Value_Changed_Event_Payload $payload, string $product_type): void
    {
        $field_label = $payload->get_field_label();

        if (!$field_label) {
            return;
        }

        $current_user = $this->current_user_getter->get();
        $current_user_login = $current_user ? $current_user->get_login() : '';

        $this->logger->info(
            sprintf(
                $this->translator->translate("logs.log_message.{$product_type}_{$payload->get_source_type()}.deleted"),
                $field_label,
                $payload->get_item_id(),
                $current_user_login
            )
        );
    }
}