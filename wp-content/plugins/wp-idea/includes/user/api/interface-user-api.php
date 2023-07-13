<?php
namespace bpmj\wpidea\user\api;

use bpmj\wpidea\user\User_ID;

interface Interface_User_API
{
    public function current_user_has_any_of_the_roles(array $roles): bool;

    public function current_user_has_role(string $role): bool;

    public function get_current_user_id(): ?User_ID;
}