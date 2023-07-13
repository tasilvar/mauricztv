<?php

namespace bpmj\wpidea\admin\tables\styles;

class Default_Style implements Interface_Table_Style
{
    public function get_classes(): array
    {
        return [
            'fixed',
            'widefat',
            'striped',
            'table-view-list',
            'wpi-enhanced-table',
            'wpi-enhanced-table--default'
        ];
    }
}