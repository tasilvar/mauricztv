<?php

namespace bpmj\wpidea\admin\tables\styles;

class Wpi_Style implements Interface_Table_Style
{
    public function get_classes(): array
    {
        return [
            'fixed',
            'widefat',
            'striped',
            'table-view-list',
            'wpi-enhanced-table',
            'wpi-enhanced-table--wpi-style'
        ];
    }
}