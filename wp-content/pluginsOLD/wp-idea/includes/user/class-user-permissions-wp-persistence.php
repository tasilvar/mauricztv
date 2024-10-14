<?php namespace bpmj\wpidea\user;

class User_Permissions_Wp_Persistence
{
    public function get_wp_user_roles(int $user_id): array
    {
        $user_meta = get_userdata($user_id);

        return $user_meta->roles;
    }

    public function get_wp_user_caps(User_ID $id): array
    {
        $user_meta = get_userdata($id->to_int());

        return $user_meta->allcaps;
    }

    public function get_all_wp_roles(): array
    {
        return wp_roles()->roles;
    }
}