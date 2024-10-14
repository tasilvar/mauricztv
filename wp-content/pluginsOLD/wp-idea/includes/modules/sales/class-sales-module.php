<?php

namespace bpmj\wpidea\modules\sales;

use bpmj\wpidea\modules\sales\orders\api\controllers\Orders_Controller;
use bpmj\wpidea\shared\abstractions\modules\Interface_Module;

class Sales_Module implements Interface_Module
{
    public function __construct(Orders_Controller $orders_controller)
    {
    }

    public function init(): void
    {
    }

    public function get_routes(): array
    {
        return [];
    }

    public function get_translations(): array
    {
        return [];
    }
}