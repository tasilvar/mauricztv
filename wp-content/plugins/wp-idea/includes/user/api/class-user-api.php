<?php

namespace bpmj\wpidea\user\api;

use bpmj\wpidea\data_types\exceptions\Object_Uninitialized_Exception;
use bpmj\wpidea\user\Interface_Current_User_Getter;
use bpmj\wpidea\user\Interface_User;
use bpmj\wpidea\user\Interface_User_Permissions_Service;
use bpmj\wpidea\user\User_ID;
use bpmj\wpidea\user\User_Role_Factory;

class User_API implements Interface_User_API
{
    private static ?User_API $instance = null;
    private Interface_Current_User_Getter $current_user_getter;
    private User_Role_Factory $user_role_factory;
    private Interface_User_Permissions_Service $user_permissions_service;

    public function __construct(
        Interface_Current_User_Getter $current_user_getter,
        User_Role_Factory $user_role_factory,
        Interface_User_Permissions_Service $user_permissions_service
    ) {
        $this->current_user_getter = $current_user_getter;
        $this->user_role_factory = $user_role_factory;
        $this->user_permissions_service = $user_permissions_service;

        self::$instance = $this;
    }

    /**
     * @throws Object_Uninitialized_Exception
     */

    public static function get_instance(): User_API
    {
        if (!isset(self::$instance)) {
            throw new Object_Uninitialized_Exception();
        }

        return self::$instance;
    }

    public function current_user_has_any_of_the_roles(array $roles): bool
    {
        $user = $this->current_user_getter->get();

        if (!$user || !$roles) {
            return false;
        }

        $user_roles = $this->user_role_factory->create_many_from_name($roles);

        if (!$this->user_permissions_service->has_any_of_the_roles($user, $user_roles)) {
            return false;
        }

        return true;
    }

    public function current_user_has_role(string $role): bool
    {
        $user = $this->current_user_getter->get();

        if (!$user || !$role) {
            return false;
        }

        $user_role = $this->user_role_factory->create_from_name($role);

        if (!$this->user_permissions_service->has_role($user, $user_role)) {
            return false;
        }

        return true;
    }

    public function get_current_user_id(): ?User_ID
    {
        $user = $this->current_user_getter->get();

        if (!$user) {
            return null;
        }

        return $user->get_id();
    }

    public function get_current_user(): ?Interface_User
    {
        return $this->current_user_getter->get();
    }
}