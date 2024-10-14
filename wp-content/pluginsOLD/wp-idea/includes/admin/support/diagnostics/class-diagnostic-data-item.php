<?php

namespace bpmj\wpidea\admin\support\diagnostics;

class Diagnostic_Data_Item
{
    private $label;

    private $value;

    public function __construct( $label, $value )
    {
        $this->label = $label;
        $this->value = $value;
    }

    public function get_label()
    {
        return $this->label;
    }

    public function get_value()
    {
        return $this->value;
    }
}

