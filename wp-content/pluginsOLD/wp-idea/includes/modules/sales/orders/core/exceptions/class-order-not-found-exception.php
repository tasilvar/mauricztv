<?php

namespace bpmj\wpidea\modules\sales\orders\core\exceptions;

use bpmj\wpidea\shared\exceptions\App_Exception;


class Order_Not_Found_Exception extends App_Exception
{
    public function __construct()
    {
        parent::__construct('Order not found', 404);
    }
}