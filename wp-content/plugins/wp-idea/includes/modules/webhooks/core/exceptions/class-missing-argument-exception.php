<?php

namespace bpmj\wpidea\modules\webhooks\core\exceptions;

use bpmj\wpidea\shared\exceptions\App_Exception;


class Missing_Argument_Exception extends App_Exception
{
    public function __construct()
    {
        parent::__construct('Missing argument', 400);
    }
}