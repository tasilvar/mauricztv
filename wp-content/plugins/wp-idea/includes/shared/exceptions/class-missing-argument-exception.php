<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\shared\exceptions;

class Missing_Argument_Exception extends App_Exception
{
    public function __construct()
    {
        parent::__construct('Missing argument', 400);
    }
}