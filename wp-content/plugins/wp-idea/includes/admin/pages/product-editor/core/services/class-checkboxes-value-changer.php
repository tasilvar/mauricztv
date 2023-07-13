<?php

namespace bpmj\wpidea\admin\pages\product_editor\core\services;

use bpmj\wpidea\admin\pages\product_editor\core\configuration\Abstract_General_Products_Group;
use bpmj\wpidea\admin\pages\bundle_editor\core\configuration\General_Bundle_Group;

class Checkboxes_Value_Changer
{
    private const CHECKBOXES_TO_CHANGE_TO_TRUE_OR_FALSE = [
        General_Bundle_Group::VARIABLE_PRICING,
        Abstract_General_Products_Group::PROMOTE_COURSE,
        Abstract_General_Products_Group::RECURRING_PAYMENTS
    ];

    private const CHECKBOXES_REVERSE_VALUE_TO_TRUE_OR_FALSE = [
        Abstract_General_Products_Group::SALES_DISABLED,
        Abstract_General_Products_Group::HIDE_FROM_LIST,
        Abstract_General_Products_Group::HIDE_PURCHASE_BUTTON
    ];

    private const ON = 'on';
    private const FALSE = false;
    private const TRUE = true;

    public function change_the_value(string $name, $value)
    {
        $field_value = $this->change_the_value_to_true_or_false($name, $value);
        return $this->reverse_the_value_to_true_or_false($name, $field_value);
    }

    private function change_the_value_to_true_or_false(string $name, $value)
    {
        if (in_array($name, $this->get_checkboxes_to_change_to_true_or_false(), true)) {
            if ($value !== self::ON && $value !== self::TRUE) {
                return self::FALSE;
            }
            return self::TRUE;
        }

        return $value;
    }

    private function reverse_the_value_to_true_or_false(string $name, $value)
    {
        if (in_array($name, $this->get_checkboxes_to_reverse_value_to_true_or_false(), true)) {
            if ($value !== self::ON && $value !== self::TRUE) {
                return self::TRUE;
            }
            return self::FALSE;
        }

        return $value;
    }

    protected function get_checkboxes_to_change_to_true_or_false(): array
    {
        return self::CHECKBOXES_TO_CHANGE_TO_TRUE_OR_FALSE;
    }

    protected function get_checkboxes_to_reverse_value_to_true_or_false(): array
    {
        return self::CHECKBOXES_REVERSE_VALUE_TO_TRUE_OR_FALSE;
    }
}