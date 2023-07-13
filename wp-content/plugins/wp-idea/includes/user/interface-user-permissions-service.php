<?php namespace bpmj\wpidea\user;

interface Interface_User_Permissions_Service
{
    public function get_roles(Interface_User $user): User_Role_Collection;
    public function has_role(Interface_User $user, User_Role $role): bool;
    public function has_any_of_the_roles(Interface_User $user, User_Role_Collection $roles): bool;
    public function has_capability(Interface_User $user, User_Capability $tested_cap): bool;
    public function get_capabilities(Interface_User $user): User_Capability_Collection;
    public function has_any_of_the_caps(Interface_User $user, User_Capability_Collection $caps): bool;
    public function get_all_roles(): User_Role_Collection;
}