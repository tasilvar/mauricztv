<?php

namespace bpmj\wpidea\admin\video;

use bpmj\wpidea\admin\video\settings\Videos_Settings;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\instantiator\Interface_Initiable;

class Videos_Manager implements Interface_Initiable
{
    public const ACTION_UPLOAD = 'action_upload';
    public const ACTION_DELETE = 'action_delete';

    private Interface_Events $events;
    private Videos_Cron_Actions $videos_cron_actions;

    public function __construct(
        Interface_Events $events,
        Videos_Cron_Actions $videos_cron_actions
    ) {
        $this->events = $events;
        $this->videos_cron_actions = $videos_cron_actions;
    }

    public function init(): void
    {
        new Videos_Event_Observer( $this );
    }

    /**
     * Trigger an action in the Videos_Manager
     *
     * Currently supports only 'upload' and 'delete' actions
     *
     * @todo: refactor tej metody, tutaj przydalby sie observer pattern + na teraz nie wiadomo co przekazac w payloadzie
     *
     * @param string $action_name
     * @param array $payload
     * @return void
     */
    public function triggerAction($action_name, $payload)
    {

        switch ($action_name) {
            case self::ACTION_UPLOAD:
                /**
                 * Disable upload actions if vimeo upload is disabled
                 */
                if( Videos_Settings::is_vimeo_upload_disabled() ) break;

                $this->_upload_video( !empty( $payload['attachment_id'] ) ? $payload['attachment_id'] : 0 );
                break;

            case self::ACTION_DELETE:
                $this->_delete_video( !empty( $payload['attachment_id'] ) ? $payload['attachment_id'] : 0 );
                break;

            default:
                break;
        }
    }

    protected function _upload_video( $attachment_id )
    {
        $video = Video::create( $attachment_id, $this->events, $this->videos_cron_actions );

        $video->upload_to_remote();
    }

    protected function _delete_video( $attachment_id )
    {
        $video = Video::create( $attachment_id, $this->events, $this->videos_cron_actions );

        $video->delete_from_remote();
    }
}