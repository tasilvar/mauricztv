<?php

namespace bpmj\wpidea;

use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\wolverine\user\User;
use bpmj\wpidea\wolverine\user\UserInterface;
use WP_Error;

class User_Security
{
    public const SKIP_CSRF_ACCESS_MD5_IPS_FILTER = 'skip_csrf_access_md5_ips_filter';
    const SKIP_CSRF_ACCESS_MD5_IPS = ['23c5661fe82f36d00042ae705db74d77'];
    const MAX_FAILED_LOGIN_COUNT_TO_BE_BANNED = 3;
    const BAN_TIME_IN_MINUTES = 15;

    const CSRF_TOKEN = 'csrf_token';

    /**
     * @var Current_Request
     */
    private $current_request;

    private Interface_Translator $translator;

    public function __construct(
        Current_Request $current_request,
        Interface_Translator $translator
    )
    {
        $this->current_request = $current_request;
        $this->translator = $translator;

        $this->add_actions();
    }

    public function add_actions()
    {
        add_action('login_form', [$this, 'add_hidden_csrf_token_in_login_form']);
        add_filter('wp_authenticate_user', [$this, 'login_csrf_token_validation'],10,2);
        add_action('wp_login_failed', [$this, 'after_failed_login']);
        add_action('wp_login', [$this, 'before_success_login']);
    }

    public function before_success_login($username)
    {
        $user = User::findByLogin($username);
        if($user){
            $user->resetFailedLoginCount();
        }
    }

    public function add_hidden_csrf_token_in_login_form()
    {
        echo '<input type="hidden" name="csrf_token" value="'.$this->get_or_create_csrf_token().'">';
    }

    public function login_csrf_token_validation($wp_user, $password)
    {
        $user = User::loadByWPUser($wp_user);

        /*
         * disabled temporarily - should be possible to turn off by user settings
        if(!$this->skip_csrf_token_validation()){
            if(!$this->valid_csrf_token()){
                return new WP_Error( 'user_not_verified', __( 'Wrong csrf token!', BPMJ_EDDCM_DOMAIN) );
            }
        }
        */

        if ($user && $user->isBanned()) {
            return new WP_Error('user_not_verified', $this->generate_user_not_verified_message($user));
        }

        return $wp_user;
    }

    public function after_failed_login($username_or_email) {


        $user = User::findByLogin($username_or_email) ?? User::findByEmail($username_or_email);

        if(!$user){
            $this->reset_csrf_token();
            return $username_or_email;
        }

        if($user->isBanned()){
            return $username_or_email;
        }

        $user->increaseFailedLoginCount();

        if($user->getFailedLoginCount() >= self::MAX_FAILED_LOGIN_COUNT_TO_BE_BANNED){
            $user->ban(self::BAN_TIME_IN_MINUTES);
            $user->resetFailedLoginCount();
            return $username_or_email;
        }

        return $username_or_email;
    }

    private function skip_csrf_token_validation()
    {
        return (in_array($this->current_request->get_md5_user_ip(), apply_filters( self::SKIP_CSRF_ACCESS_MD5_IPS_FILTER, self::SKIP_CSRF_ACCESS_MD5_IPS)));
    }

    private function valid_csrf_token(): bool
    {
        $result = isset($_POST[self::CSRF_TOKEN]) && $_POST[self::CSRF_TOKEN] === $_SESSION[self::CSRF_TOKEN];
        $this->reset_csrf_token();
        return $result;
    }

    private function get_or_create_csrf_token()
    {
        return $_SESSION[self::CSRF_TOKEN] ?? $this->generate_csrf_token_and_set_in_session();
    }

    private function reset_csrf_token()
    {
        $_SESSION[self::CSRF_TOKEN] = null;
    }

    private function generate_csrf_token_and_set_in_session()
    {
        $csrf_token = uniqid();
        $_SESSION[self::CSRF_TOKEN] = $csrf_token;
        return $csrf_token;
    }

    private function generate_user_not_verified_message(UserInterface $user): string
    {
        if ($user->isBannedForever()) {
            return $this->translator->translate('user_security.user_banned.forever');
        }

        $remaining_ban_time_in_minutes = $user->getRemainingBanTimeInMinutes();
        $msg = $this->translator->translate('user_security.user_banned.temporarily');
        return $msg . ($remaining_ban_time_in_minutes > 0
            ? $remaining_ban_time_in_minutes . ' min.'
            : $this->translator->translate('user_security.user_banned.just_a_moment'));
    }

}
