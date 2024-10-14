<?php

namespace bpmj\wpidea;

use bpmj\wpidea\admin\Remote_Notifications as Admin_Remote_Notification;

class Remote_Notifications_Checker
{
    const JSON_URL = 'http://info.wpidea.pl/hello.json';

    const REMOTE_CHECK_INTERVAL = 'twicedaily';

    const REMOTE_NOTICES_REQUEST_ACTION_NAME = 'bpmj_eddcm_cron_check_notifications';

    public function __construct()
    {
        $this->init_hooks();
        $this->add_schedule_to_check_remote_notices();
    }

    public function init_hooks()
    {
        add_action( self::REMOTE_NOTICES_REQUEST_ACTION_NAME, [ $this, 'check_notifications' ] );
    }

    protected function add_schedule_to_check_remote_notices()
    {
        if ( ! wp_next_scheduled( self::REMOTE_NOTICES_REQUEST_ACTION_NAME ) )
            wp_schedule_event( time(), self::REMOTE_CHECK_INTERVAL, self::REMOTE_NOTICES_REQUEST_ACTION_NAME );
    }

    public function check_notifications()
    {
        $response = wp_remote_get( self::JSON_URL );
        if ( is_wp_error( $response ) )
            return;

        $response = json_decode( $response['body'], true );

        set_transient(
            Admin_Remote_Notification::REMOTE_NOTICES_TRANSIENT_NAME,
            $response['notifications'],
            Admin_Remote_Notification::REMOTE_NOTICES_TRANSIENT_TIME
        );
    }
}
