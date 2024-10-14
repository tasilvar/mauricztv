<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\tables\dynamic\controllers\access;

use bpmj\wpidea\admin\tables\dynamic\controllers\exceptions\Table_Data_Access_Exception;
use bpmj\wpidea\Caps;
use bpmj\wpidea\user\Interface_Current_User_Getter;
use bpmj\wpidea\user\Interface_User_Permissions_Service;
use bpmj\wpidea\user\User_Capability_Collection;
use bpmj\wpidea\user\User_Role_Collection;
use bpmj\wpidea\user\User_Role_Factory;

class Dynamic_Table_Data_Permissions_Validator
{
    private Interface_User_Permissions_Service $user_permissions_service;
    private Interface_Current_User_Getter $current_user_getter;

    public function __construct(
        Interface_User_Permissions_Service $user_permissions_service,
        Interface_Current_User_Getter $current_user_getter
    )
    {
        $this->user_permissions_service = $user_permissions_service;
        $this->current_user_getter = $current_user_getter;
    }

    public function verify_permissions(User_Capability_Collection $required_caps, User_Role_Collection $required_roles): void
    {
        if($required_caps->is_empty() && $required_roles->is_empty()) {
            return;
        }

        $current_user = $this->current_user_getter->get();

        if(is_null($current_user)) {
            throw new Table_Data_Access_Exception(Table_Data_Access_Exception::MESSAGE_NO_CURRENT_USER_FOUND);
        }

        if(!$required_roles->is_empty() && $this->user_permissions_service->has_any_of_the_roles($current_user, $required_roles)) {
            return;
        }

        if(!$required_caps->is_empty() && $this->user_permissions_service->has_any_of_the_caps($current_user, $required_caps)) {
            return;
        }

        throw new Table_Data_Access_Exception(Table_Data_Access_Exception::MESSAGE_CURRENT_USER_HAS_NONE_OF_THE_REQUIRED_PERMISSION);
    }
}