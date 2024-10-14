<?php

namespace bpmj\wpidea\modules\webhooks\core\exceptions;

use bpmj\wpidea\shared\exceptions\App_Exception;


class Webhook_Already_Exists_Exception extends App_Exception
{
    public function __construct()
    {
        parent::__construct('Webhook already exists', 200);
    }
}