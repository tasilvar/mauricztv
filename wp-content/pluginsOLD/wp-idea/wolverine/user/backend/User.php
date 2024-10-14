<?php
namespace bpmj\wpidea\wolverine\user\backend;

use bpmj\wpidea\Caps;
use bpmj\wpidea\wolverine\user\User as BaseUser;
use bpmj\wpidea\wolverine\user\UserInterface;
use bpmj\wpidea\wolverine\user\exception\NoUserException;
use bpmj\wpidea\wolverine\user\exception\UserAccessNotAllowedException;

class User extends BaseUser
{

    public static function getCurrent(): UserInterface
    {
        $id = self::getCurrentUserId();

        if(empty($id)) throw new NoUserException();

        if(!self::currentUserHasAnyOfTheCapabilities([Caps::CAP_MANAGE_POSTS])) throw new UserAccessNotAllowedException();

        return User::find($id);
    }
}
