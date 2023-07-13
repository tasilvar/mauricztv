<?php

namespace bpmj\wpidea\modules\webhooks\core\exceptions;

use bpmj\wpidea\shared\exceptions\App_Exception;


class Webhook_Not_Found_Exception extends App_Exception
{
    public function __construct()
    {
        parent::__construct('Webhook not found', 404);
    }
}