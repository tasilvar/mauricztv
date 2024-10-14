<?php

namespace bpmj\wpidea\admin\tables;

abstract class Abstract_Enhanced_Table_Item
{
    abstract public function get_values(): array;

    abstract public static function get_labels(): array;
}