<?php

namespace bpmj\wpidea\modules\affiliate_program\core\exceptions;

use bpmj\wpidea\shared\exceptions\App_Exception;


class Affiliate_Id_Already_In_Use_Exception extends App_Exception
{
    public function __construct()
    {
        parent::__construct('This affiliate ID is already in use.', 400);
    }
}
