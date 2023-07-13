<?php

namespace bpmj\wpidea\admin\video;

use bpmj\wpidea\admin\Notices;
use bpmj\wpidea\admin\video\attachment\Video_Attachment;
use bpmj\wpidea\admin\video\events\Video_Event_Name;
use bpmj\wpidea\events\Interface_Events;

class Video {
    protected Video_Attachment $_attachment;

    protected Video_Api $_video_api;

    private Videos_Cron_Actions $_videos_cron_actions;

    private Interface_Events $events;

    public function __construct( $attachment_id, Interface_Events $events, Videos_Cron_Actions $videos_cron_actions) {
        $this->_attachment  = new Video_Attachment( $attachment_id );        
        $this->_video_api   = new Video_Api( Api_Credentials::get_wpi_key(), Api_Credentials::get_host() );       
        $this->_videos_cron_actions = $videos_cron_actions;
        $this->events = $events;

        $videos_cron_actions->init();
    }

    public static function create($attachment_id, Interface_Events $events, Videos_Cron_Actions $videos_cron_actions): self
    {
        return new self($attachment_id, $events, $videos_cron_actions);
    }

    /**
     * Uplooad video to remote and delete old file afterwards
     *
     * @return void
     */
    public function upload_to_remote()
    {
        if( $this->_attachment->has_corresponding_video_on_remote() ) return;

        $upload_response = $this->_upload_to_remote();
        
        if( $upload_response ) $this->_update_video_details_and_status( $upload_response );
    }

    /**
     * Delete video file from remote
     *
     * @return void
     */
    public function delete_from_remote()
    {
        if( ! $this->_attachment->has_corresponding_video_on_remote() ) return;

        $this->_video_api->delete_video( $this->_attachment->get_remote_video_id() );

        $this->events->emit(Video_Event_Name::REMOTE_VIDEO_DELETED, $this->_attachment->get_id());
    }

    /**
     * Update video metadata and schedule cron status check
     *
     * @param array $upload_response
     * @return void
     */
    private function _update_video_details_and_status( $upload_response )
    {
        $video_data = new Video_Data( $upload_response, $this->_attachment->get_id() );

        $video_data->set_source_attachment_file( get_attached_file( $this->_attachment->get_id() ) );

        self::update_attachment_meta( $video_data );

        $this->events->emit(Video_Event_Name::VIDEO_STATUS_UPDATED, $this->_attachment->get_id());

        $this->_videos_cron_actions->schedule_video_status_check();
    }

    /**
     * Update video details in the attachment metadata
     *
     * @param Video_Data $video_data
     * @return void
     */
    public static function update_attachment_meta( Video_Data $video_data )
    {
        $attachment = new Video_Attachment( $video_data->get_attachment_id() );

        $attachment->set_video_id( $video_data->get_video_id() );
            
        $attachment->set_video_upload_status( $video_data->get_video_status() );
        
        if( ! $video_data->get_video_url() ) return;
        
        $attachment->set_video_file_url( $video_data->get_video_url() );
    }

    public static function remove_source_video_file( Video_Data $video_data )
    {
        wp_delete_file( $video_data->get_source_attachment_file() );
    }


    /**
     * Upload file associated with the attachment to the remote
     *
     * @return array|false
     */
    protected function _upload_to_remote()
    {
        try {
            $response = $this->_video_api->upload_to_remote( $this->_attachment->get_url() );
    
            if( $response ) return $response;
            
            return false;
        } catch (\Exception $e) {
            self::show_error_notice( $e->getMessage() );

            return false;
        }
    }

    public static function show_error_notice( $message = null )
    {
        $notice_message = __( 'Unfortunately, video upload to Vimeo failed', BPMJ_EDDCM_DOMAIN );

        $notice_message .= $message ? ': ' . $message : '.';

        WPI()->notices->add_dismissible_notice( $notice_message, Notices::TYPE_ERROR );
    }
}