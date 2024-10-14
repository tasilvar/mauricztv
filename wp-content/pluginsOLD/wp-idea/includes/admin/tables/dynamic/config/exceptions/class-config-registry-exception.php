<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\tables\dynamic\config\exceptions;

use Exception;

class Config_Registry_Exception extends Exception
{
    public const MESSAGE_REGISTRY_SHOULD_BE_SET_UP_ONLY_ONCE = 'Registry should be set up only once!';
    public const MESSAGE_REGISTRY_IS_NOT_SET_UP = 'Registry has to be set up before being used. Call set_up() method.';
}