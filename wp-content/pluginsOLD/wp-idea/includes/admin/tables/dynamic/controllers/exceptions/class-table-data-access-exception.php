<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\tables\dynamic\controllers\exceptions;

use Exception;

class Table_Data_Access_Exception extends Exception
{
    public const MESSAGE_NO_CURRENT_USER_FOUND = 'No current user found.';
    public const MESSAGE_CURRENT_USER_HAS_NONE_OF_THE_REQUIRED_PERMISSION = 'Current user has none of the required permissions.';
}