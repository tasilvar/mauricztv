<?php

namespace bpmj\wpidea\admin\pages\quiz_editor\infrastructure\services;

use bpmj\wpidea\admin\pages\quiz_editor\core\configuration\General_Quiz_Group;

class Checkboxes_Value_Changer
{
    private const CHECKBOXES_TO_CHANGE_TO_TRUE_OR_FALSE = [
        General_Quiz_Group::ATTEMPTS_MODE,
		General_Quiz_Group::TIME_MODE,
	    General_Quiz_Group::EVALUATED_BY_ADMIN_MODE,
	    General_Quiz_Group::RANDOMIZE_QUESTION_ORDER,
	    General_Quiz_Group::RANDOMIZE_ANSWER_ORDER,
	    General_Quiz_Group::CAN_SEE_ANSWERS_MODE,
	    General_Quiz_Group::ALSO_SHOW_CORRECT_ANSWERS
    ];

    private const ON = 'on';
    private const FALSE = false;
    private const TRUE = true;

    public function change_the_value(string $name, $value)
    {
	    return $this->change_the_value_to_true_or_false($name, $value);
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

    protected function get_checkboxes_to_change_to_true_or_false(): array
    {
        return self::CHECKBOXES_TO_CHANGE_TO_TRUE_OR_FALSE;
    }
}