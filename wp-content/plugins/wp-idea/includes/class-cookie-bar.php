<?php

namespace bpmj\wpidea;

use bpmj\wpidea\settings\LMS_Settings;

class Cookie_Bar
{
    public function __construct()
    {
        $this->add_actions();
    }

    protected function add_actions()
    {
        if ( ! isset( $_COOKIE['hide_cookie_bar'] ) && LMS_Settings::get_option('cookie-bar' ) ) {
            add_action( 'wp_footer', [$this, 'display_cookie_bar'] );
        }
    }

    public function display_cookie_bar()
    {
        $privacy_policy_page = LMS_Settings::get_option('cookie-bar-privacy-policy' );
        $privacy_policy_page_url = '';
        if ( ! empty( $privacy_policy_page ) )
            $privacy_policy_page_url = get_the_permalink( $privacy_policy_page );

        echo View::get('/elements/cookie-bar', [
            'cookie_bar_content' => LMS_Settings::get_option('cookie-bar-content'),
            'privacy_policy_page_url' => $privacy_policy_page_url,
            'privacy_policy_page_title' => get_the_title( $privacy_policy_page ),
            'cookie_bar_button_text' => LMS_Settings::get_option('cookie-bar-button-text'),
            'site_path' => $this->get_site_path(),
        ]);
    }

    public function get_site_path()
    {
        $site_url = get_site_url();
        $site_path = parse_url($site_url, PHP_URL_PATH);
        if ( empty($site_path) )
            $site_path = '/';

        return $site_path;
    }
}
