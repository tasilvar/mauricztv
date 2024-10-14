<?php

namespace bpmj\wpidea\admin\settings\core\entities\fields;

class Field_Set_End extends Non_Savable_Field
{
    public function __construct()
    {
        parent::__construct('', '');
    }

    public function render_to_string(): string
    {
        return "</div>";
    }
}