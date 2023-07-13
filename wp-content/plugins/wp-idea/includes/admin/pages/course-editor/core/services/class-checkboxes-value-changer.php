<?php

namespace bpmj\wpidea\admin\pages\course_editor\core\services;

use bpmj\wpidea\admin\pages\product_editor\core\services\Checkboxes_Value_Changer as Product_Checkboxes_Value_Changer;
use bpmj\wpidea\admin\pages\course_editor\core\configuration\General_Course_Group;

class Checkboxes_Value_Changer extends Product_Checkboxes_Value_Changer
{
    protected function get_checkboxes_to_change_to_true_or_false(): array
    {
        return array_merge(parent::get_checkboxes_to_change_to_true_or_false(), [
            General_Course_Group::ENABLE_CERTIFICATE_NUMBERING,
            General_Course_Group::RECURRING_PAYMENTS,
            General_Course_Group::VARIABLE_PRICING
        ]);
    }

    protected function get_checkboxes_to_reverse_value_to_true_or_false(): array
    {
        return array_merge(parent::get_checkboxes_to_reverse_value_to_true_or_false(), [
            General_Course_Group::SALES_DISABLED,
            General_Course_Group::HIDE_FROM_LIST,
            General_Course_Group::HIDE_PURCHASE_BUTTON,
            General_Course_Group::DISABLE_CERTIFICATES,
            General_Course_Group::DISABLE_EMAIL_SUBSCRIPTION
        ]);
    }
}