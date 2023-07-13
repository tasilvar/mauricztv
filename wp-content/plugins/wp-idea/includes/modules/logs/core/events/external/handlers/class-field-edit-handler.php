<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\logs\core\events\external\handlers;

use bpmj\wpidea\admin\settings\core\events\Settings_Field_Value_Changed_Event_Payload;
use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\sales\product\core\event\Product_Field_Value_Changed_Event_Payload;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_Current_User_Getter;
use Psr\Log\LoggerInterface;

class Field_Edit_Handler implements Interface_Event_Handler
{
    private const ON = 'on';
    private const OFF = 'off';
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
        $this->events->on(Event_Name::SETTINGS_FIELD_VALUE_UPDATED, function (Settings_Field_Value_Changed_Event_Payload $payload) {
            $this->handle_settings_edit_logs($payload);
        });

        $this->events->on(Event_Name::COURSE_FIELD_VALUE_UPDATED, function (Product_Field_Value_Changed_Event_Payload $payload) {
            $this->handle_course_edit_logs($payload);
        });

        $this->events->on(Event_Name::COURSE_VARIABLE_PRICES_UPDATED, function (Product_Field_Value_Changed_Event_Payload $payload) {
            $this->handle_course_variable_prices_edit_logs($payload);
        });

        $this->events->on(Event_Name::COURSE_STRUCTURE_UPDATED, function (Product_Field_Value_Changed_Event_Payload $payload) {
            $this->handle_course_structure_edit_logs($payload);
        });

        $this->events->on(Event_Name::SERVICES_FIELD_VALUE_UPDATED, function (Product_Field_Value_Changed_Event_Payload $payload) {
            $this->handle_services_edit_logs($payload);
        });

        $this->events->on(Event_Name::DIGITAL_PRODUCT_FIELD_VALUE_UPDATED, function (Product_Field_Value_Changed_Event_Payload $payload) {
            $this->handle_digital_product_edit_logs($payload);
        });

        $this->events->on(Event_Name::PHYSICAL_PRODUCT_FIELD_VALUE_UPDATED, function (Product_Field_Value_Changed_Event_Payload $payload) {
            $this->handle_physical_product_edit_logs($payload);
        });

        $this->events->on(Event_Name::BUNDLE_FIELD_VALUE_UPDATED, function (Product_Field_Value_Changed_Event_Payload $payload) {
            $this->handle_bundle_edit_logs($payload);
        });

        $this->events->on(Event_Name::BUNDLE_VARIABLE_PRICES_UPDATED, function (Product_Field_Value_Changed_Event_Payload $payload) {
            $this->handle_bundle_variable_prices_edit_logs($payload);
        });
    }

    private function handle_settings_edit_logs(Settings_Field_Value_Changed_Event_Payload $payload): void
    {
        $field_label = $payload->get_field_label();

        if (!$field_label) {
            return;
        }

        $current_user = $this->current_user_getter->get();
        $current_user_login = $current_user ? $current_user->get_login() : '';

        $this->logger->info(
            sprintf(
                $this->get_translate_message_log_for_settings($payload),
                $field_label,
                $current_user_login,
                $payload->get_new_field_value(),
                $payload->get_old_field_value()
            )
        );
    }

    private function handle_course_edit_logs(Product_Field_Value_Changed_Event_Payload $payload): void
    {
        $this->handle_products_edit_logs($payload, Product_Field_Value_Changed_Event_Payload::SETTINGS_TYPE_COURSE);
    }

    private function handle_course_variable_prices_edit_logs(Product_Field_Value_Changed_Event_Payload $payload): void
    {
        $this->handle_products_edit_logs($payload, Product_Field_Value_Changed_Event_Payload::SETTINGS_TYPE_COURSE);
    }

    private function handle_course_structure_edit_logs(Product_Field_Value_Changed_Event_Payload $payload): void
    {
        $this->handle_products_edit_logs($payload, Product_Field_Value_Changed_Event_Payload::SETTINGS_TYPE_COURSE);
    }

    private function handle_services_edit_logs(Product_Field_Value_Changed_Event_Payload $payload): void
    {
        $this->handle_products_edit_logs($payload, Product_Field_Value_Changed_Event_Payload::SETTINGS_TYPE_SERVICE);
    }

    private function handle_digital_product_edit_logs(Product_Field_Value_Changed_Event_Payload $payload): void
    {
        $this->handle_products_edit_logs($payload, Product_Field_Value_Changed_Event_Payload::SETTINGS_TYPE_DIGITAL_PRODUCT);
    }

    private function handle_physical_product_edit_logs(Product_Field_Value_Changed_Event_Payload $payload): void
    {
        $this->handle_products_edit_logs($payload, Product_Field_Value_Changed_Event_Payload::SETTINGS_TYPE_PHYSICAL_PRODUCT);
    }

    private function handle_bundle_edit_logs(Product_Field_Value_Changed_Event_Payload $payload): void
    {
        $this->handle_products_edit_logs($payload, Product_Field_Value_Changed_Event_Payload::SETTINGS_TYPE_BUNDLE);
    }

    private function handle_bundle_variable_prices_edit_logs(Product_Field_Value_Changed_Event_Payload $payload): void
    {
        $this->handle_products_edit_logs($payload, Product_Field_Value_Changed_Event_Payload::SETTINGS_TYPE_BUNDLE);
    }

    private function handle_products_edit_logs(Product_Field_Value_Changed_Event_Payload $payload, string $product_type): void
    {
        $field_label = $payload->get_field_label();

        if (!$field_label) {
            return;
        }

        $current_user = $this->current_user_getter->get();
        $current_user_login = $current_user ? $current_user->get_login() : '';

        $this->logger->info(
            sprintf(
                $this->get_translate_message_log_for_product($payload, $product_type),
                $field_label,
                $payload->get_item_id(),
                $current_user_login,
                $payload->get_new_field_value(),
                $payload->get_old_field_value()
            )
        );
    }

    private function get_translate_message_log_for_settings(Settings_Field_Value_Changed_Event_Payload $payload): string
    {
        if ($payload->is_toggle()) {
            return $this->translator->translate("logs.log_message.main_settings_edited.toggle." . $payload->get_new_field_value());
        }

        return $this->translator->translate("logs.log_message.main_settings_edited");
    }

    private function get_translate_message_log_for_product(Product_Field_Value_Changed_Event_Payload $payload, string $product_type): string
    {
        $source_type = $payload->get_source_type();

        if ($payload->is_toggle()) {
            $new_field_value = $payload->get_new_field_value();
            $toggle_value = ($new_field_value === '1' || $new_field_value === self::ON) ? self::ON : self::OFF;
            return $this->translator->translate("logs.log_message.{$product_type}_{$source_type}.toggle." . $toggle_value);
        }

        return $this->translator->translate("logs.log_message.{$product_type}_{$source_type}");
    }
}