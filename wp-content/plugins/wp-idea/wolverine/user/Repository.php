<?php
namespace bpmj\wpidea\wolverine\user;

use WP_User;

class Repository
{
    const USER_META_BAN_DATE = 'ban_date';
    const USER_META_BAN_FOREVER = 'ban_forever';
    const USER_META_FAILED_LOGIN_COUNT = 'failed_login_count';

    public function find($id): ?UserData
    {
        $wpuser = get_user_by('ID', $id);

        return ($wpuser) ? $this->getUserData($wpuser) : null;
    }

    public function findBy($property, $value)
    {
        $wpuser = get_user_by($property, $value);

        return ($wpuser) ? $this->getUserData($wpuser) : null;
    }

    public function findUsersWithRole(string $role): array
    {
        return get_users( [ 'role__in' => [ $role ], 'orderby' => 'ID' ] );
    }

    public function deleteMetaForAllUsers($key): bool
    {
        return delete_metadata('user', 0, $key, false, true);
    }

    public function loadByWPUser(WP_User $wpUser)
    {
        return $this->getUserData($wpUser);
    }

    public function getUserPasswordResetLink($userId)
    {
        $wpuser         = get_user_by('ID', $userId);
        $key            = get_password_reset_key( $wpuser );
        $user_login     = $wpuser->user_login;

        return network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' );
    }

    public function store(UserData $userData)
    {
        return $this
            ->saveBanDate($userData->id, $userData->ban_date)
            ->saveBanForever($userData->id, $userData->ban_forever)
            ->saveFailedLoginCount($userData->id, $userData->failed_login_count);
    }

    private function saveBanDate($id, $banDate)
    {
        update_user_meta( $id, self::USER_META_BAN_DATE, $banDate);
        return $this;
    }

    private function saveBanForever($id, $banForever)
    {
        update_user_meta( $id, self::USER_META_BAN_FOREVER, $banForever);
        return $this;
    }

    private function saveFailedLoginCount($id, $failedLoginCount)
    {

        update_user_meta( $id, self::USER_META_FAILED_LOGIN_COUNT, $failedLoginCount);
        return $this;
    }

    protected function getUserData(\WP_User $wpuser)
    {
        $data = new UserData();
        $data->id = $wpuser->ID;
        $data->login = $wpuser->user_login;
        $data->first_name = $wpuser->first_name;
        $data->last_name = $wpuser->last_name;
        $data->email = $wpuser->user_email;
        $data->ban_forever = $wpuser->ban_forever;
        $data->ban_date = $wpuser->ban_date;
        $data->failed_login_count    = $wpuser->failed_login_count;

        return $data;
    }
}
