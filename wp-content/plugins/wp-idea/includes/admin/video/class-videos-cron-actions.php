<?php

namespace bpmj\wpidea\admin\video;

use bpmj\wpidea\admin\video\attachment\Video_Attachment;
use bpmj\wpidea\admin\video\events\Video_Event_Name;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\instantiator\Interface_Initiable;

class Videos_Cron_Actions implements Interface_Initiable
{
    const CRON_VIDEO_STATUS_CHECK_NAME = 'bpmj_eddcm_video_status_check_cron_action';

    const INTERVAL_30_SEC = 'bpmj_eddcm_30sec';

    const MANUAL_CRON_TRIGGER_PARAM = 'recheck_videos_status';

    /**
     * Video API
     *
     * @var Video_Api
     */
    private $_video_api;

    private Interface_Events $events;

    public function __construct(
        Interface_Events $events
    ) {
        $this->events = $events;
    }

    public function init(): void
    {
        $this->_video_api = new Video_Api(Api_Credentials::get_wpi_key(), Api_Credentials::get_host());
        
        add_filter('cron_schedules', [$this, 'add_custom_cron_interval']);   

        add_action(self::CRON_VIDEO_STATUS_CHECK_NAME, [$this, 'check_video_status']);

        $this->listen_to_manual_cron_trigger();
    }

    public function add_custom_cron_interval($schedules) { 
        $schedules[self::INTERVAL_30_SEC] = [
            'interval' => 30,
            'display'  => esc_html__('Every Thirty Seconds')
        ];

        return $schedules;
    }

    public function schedule_video_status_check()
    {
        if (!$this->get_next_scheduled_cron_check()) {
           $this->schedule_cron_check();
        }
    }

    protected function listen_to_manual_cron_trigger()
    {
        if($this->is_manual_cron_trigger_present()){
            $this->schedule_video_status_check();
        }
    }

    protected function is_manual_cron_trigger_present()
    {
        if(!is_admin()) return false;

        if(!empty($_GET[self::MANUAL_CRON_TRIGGER_PARAM])) return true;

        return false;
    }

    protected function get_next_scheduled_cron_check()
    {
        return wp_next_scheduled(self::CRON_VIDEO_STATUS_CHECK_NAME);
    }

    protected function schedule_cron_check()
    {
        return wp_schedule_event(time(), self::INTERVAL_30_SEC, self::CRON_VIDEO_STATUS_CHECK_NAME);
    }

    protected function cancel_cron_check()
    {
        $timestamp = $this->get_next_scheduled_cron_check();

        wp_unschedule_event($timestamp, self::CRON_VIDEO_STATUS_CHECK_NAME);
    }

    public function check_video_status()
    {
        $videos_in_progress = Video_Attachment::get_all_in_progress();

        if(empty($videos_in_progress)) {
            $this->events->emit(Video_Event_Name::VIDEO_STATUSES_CHECK_FINISHED);

            $this->cancel_cron_check();

            return;
        }

        foreach ($videos_in_progress as $key => $video_post) {
            $this->_check_video_status($this->get_video_data($video_post->ID));
        }

        $this->events->emit(Video_Event_Name::VIDEO_STATUSES_CHECK_FINISHED);
    }

    protected function get_video_data($post_id)
    {
        $att  = new Video_Attachment($post_id);
        
        $video_data = new Video_Data([
            'id' => $att->get_remote_video_id()
        ], $post_id);

        $video_data->set_source_attachment_file(get_attached_file($post_id));

        return $video_data;
    }

    private function _check_video_status( Video_Data $video_data )
    {
        $video_details = $this->_get_video_details_from_remote( $video_data->get_video_id() );

        $video_data->update_data_with_details($video_details ?: []);

        if( $video_data->is_video_ready() ){
            Video::update_attachment_meta( $video_data );
            Video::remove_source_video_file( $video_data );

            return true;
        } else if ($video_data->is_error()) {
            Video::update_attachment_meta( $video_data );
            Video::show_error_notice();

            return false;
        }

        return false;
    }

    /**
     * Get video data from the remote
     *
     * @param int $video_id
     * @return array|false
     */
    private function _get_video_details_from_remote( $video_id )
    {
        try {
            return $this->_video_api->get_video( $video_id );
        } catch (\Exception $e) {
            error_log( $e->getMessage() ); // ! @todo: dev only

            return false;
        }
    }
}