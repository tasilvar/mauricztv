<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\tables\dynamic\module\exceptions;

use Exception;

class Dynamic_Tables_Module_Exception extends Exception
{
    public const MESSAGE_MODULE_SHOULD_BE_INITIALIZED_ONLY_ONCE = 'Module should be initialized only once!';
}