<?php namespace bpmj\wpidea\user;

interface Interface_User_Metadata_Service
{
    public function get(Interface_User $user, string $key);

    public function store(Interface_User $user, string $key, $value): void;

    public function delete(Interface_User $user, string $key): void;

    public function delete_for_all_users(string $key): void;

    public function store_for_current_user(string $key, $value): void;

    public function get_for_current_user(string $key);
}