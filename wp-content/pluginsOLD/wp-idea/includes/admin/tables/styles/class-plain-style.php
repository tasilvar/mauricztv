<?php

namespace bpmj\wpidea\admin\tables\styles;

class Plain_Style implements Interface_Table_Style
{
    public function get_classes(): array
    {
        return [
            'wpi-enhanced-table',
            'wpi-enhanced-table--plain'
        ];
    }
}