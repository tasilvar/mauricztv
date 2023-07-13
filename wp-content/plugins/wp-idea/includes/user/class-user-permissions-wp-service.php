<?php

namespace bpmj\wpidea\user;

use bpmj\wpidea\Caps;

class User_Permissions_Wp_Service implements Interface_User_Permissions_Service
{

    private User_Permissions_Wp_Persistence $persistence;

    private User_Role_Factory $user_role_factory;

    public function __construct(
        User_Permissions_Wp_Persistence $persistence,
        User_Role_Factory $user_role_factory
    ) {
        $this->persistence = $persistence;
        $this->user_role_factory = $user_role_factory;
    }

    public function get_roles(Interface_User $user): User_Role_Collection
    {
        $roles = new User_Role_Collection();
        foreach ($this->persistence->get_wp_user_roles($user->get_id()->to_int()) as $wp_role_name) {
            $role = new User_Role($wp_role_name);
            $roles->add($role);
        }

        return $roles;
    }

    /**
     * @throws User_Exception
     */
    public function has_role(Interface_User $user, User_Role $role): bool
    {
        if (!$this->validate_role($role)) {
            return false;
        }

        foreach ($this->get_roles($user) as $user_role) {
            if ($user_role->equals($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws User_Exception
     */
    public function has_any_of_the_roles(Interface_User $user, User_Role_Collection $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->has_role($user, $role)) {
                return true;
            }
        }

        return false;
    }

    public function get_all_roles(): User_Role_Collection
    {
        $roles = new User_Role_Collection();
        foreach ($this->persistence->get_all_wp_roles() as $wp_role_name => $wp_role_display_name_and_caps) {
            $roles->add($this->user_role_factory->create_from_name($wp_role_name));
        }

        $this->add_the_missing_roles_to_user_role_collection_if_they_do_not_exists($roles);

        return $roles;
    }

    public function has_capability(Interface_User $user, User_Capability $tested_cap): bool
    {
        $caps = $this->get_capabilities($user);
        foreach ($caps as $user_cap) {
            if ($user_cap->equals($tested_cap)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws User_Exception
     */
    public function has_any_of_the_caps(Interface_User $user, User_Capability_Collection $caps): bool
    {
        foreach ($caps as $cap) {
            if ($this->has_capability($user, $cap)) {
                return true;
            }
        }

        return false;
    }

    public function get_capabilities(Interface_User $user): User_Capability_Collection
    {
        $caps_names = array_keys($this->persistence->get_wp_user_caps($user->get_id()));
        $caps = new User_Capability_Collection();

        foreach ($caps_names as $cap) {
            $cap = new User_Capability($cap);
            $caps->add($cap);
        }

        return $caps;
    }

    private function add_the_missing_roles_to_user_role_collection_if_they_do_not_exists(User_Role_Collection $roles): void
    {
        $roles_array = [];

        foreach ($roles as $role) {
            $roles_array[] = $role->get_name();
        }

        if (!in_array(Caps::ROLE_LMS_ADMIN, $roles_array, true)) {
            $roles->add($this->user_role_factory->create_from_name(Caps::ROLE_LMS_ADMIN));
        }

        if (!in_array(Caps::ROLE_LMS_ACCOUNTANT, $roles_array, true)) {
            $roles->add($this->user_role_factory->create_from_name(Caps::ROLE_LMS_ACCOUNTANT));
        }

        if (!in_array(Caps::ROLE_LMS_SUPPORT, $roles_array, true)) {
            $roles->add($this->user_role_factory->create_from_name(Caps::ROLE_LMS_SUPPORT));
        }

        if (!in_array(Caps::ROLE_LMS_ASSISTANT, $roles_array, true)) {
            $roles->add($this->user_role_factory->create_from_name(Caps::ROLE_LMS_ASSISTANT));
        }
    }

    /**
     * @throws User_Exception
     */
    protected function validate_role(User_Role $tested_role): bool
    {
        foreach ($this->get_all_roles() as $role) {
            if ($tested_role->equals($role)) {
                return true;
            }
        }
        return false;
    }
}