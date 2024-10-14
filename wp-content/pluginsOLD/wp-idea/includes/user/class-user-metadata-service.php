<?php namespace bpmj\wpidea\user;

class User_Metadata_Service implements Interface_User_Metadata_Service
{
    private $current_user_getter;

    public function __construct(
        Interface_Current_User_Getter $current_user_getter
    )
    {
        $this->current_user_getter = $current_user_getter;
    }

    public function get(Interface_User $user, string $key)
    {
        return get_user_meta($user->get_id()->to_int(), $key, true);
    }

    public function store(Interface_User $user, string $key, $value): void
    {
        update_user_meta($user->get_id()->to_int(), $key, $value);
    }

    public function delete(Interface_User $user, string $key): void
    {
        delete_user_meta($user->get_id()->to_int(), $key);
    }

    public function delete_for_all_users(string $key): void
    {
        delete_metadata('user', 0, $key, false, true);
    }

    public function store_for_current_user(string $key, $value): void
    {
        $current_user = $this->current_user_getter->get();

        if(is_null($current_user)) {
            return;
        }

        $this->store($current_user, $key, $value);
    }

    public function get_for_current_user(string $key)
    {
        $current_user = $this->current_user_getter->get();

        if(is_null($current_user)) {
            return null;
        }

        return $this->get($current_user, $key);
    }
}