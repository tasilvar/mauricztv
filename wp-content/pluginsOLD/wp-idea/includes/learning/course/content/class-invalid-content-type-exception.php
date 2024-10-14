<?php

namespace bpmj\wpidea\learning\course\content;

use bpmj\wpidea\shared\exceptions\App_Exception;


class Invalid_Content_Type_Exception extends App_Exception
{
    public function __construct()
    {
        parent::__construct('Invalid Content Type');
    }
}