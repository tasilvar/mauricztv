<?php

namespace bpmj\wpidea\admin\settings\core\entities\fields;

class Field_Set_Start extends Non_Savable_Field
{
    public function __construct(string $name)
    {
        parent::__construct($name, '');
    }

    public function render_to_string(): string
    {
        $depends_on = $this->get_depends_on_dataset();

        return "<div class='fields-group' " . $depends_on . ">
                    <span class='fields-group__name'>" . $this->get_name() . "</span>";
    }
}