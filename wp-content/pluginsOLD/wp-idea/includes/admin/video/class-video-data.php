<?php

namespace bpmj\wpidea\admin\video;

class Video_Data {
    const VIDEO_STATUS_AVAILABLE = 'complete';

    const VIDEO_STATUS_ERROR = 'error';

    const VIMEO_URL_BASE = 'https://player.vimeo.com/video/';

    private $id;

    private $vimeo_id;

    private $video_link;

    private $upload_status;

    private $attachment_id;

    private $source_attachment_file;

    private $uploaded_to;

    public function __construct( $video_data, $attachment_id = null ) {
        $this->id = array_key_exists( 'id', $video_data ) ? $video_data['id'] : null;

        $this->vimeo_id = array_key_exists( 'vimeo_id', $video_data ) ? $video_data['vimeo_id'] : null;

        $this->video_link = array_key_exists( 'video_link', $video_data ) ? $video_data['video_link'] : null;
        
        $this->upload_status = array_key_exists( 'upload_status', $video_data ) ? $video_data['upload_status'] : null;

        $this->attachment_id = $attachment_id;
    }

    public function update_data_with_details( $video_data )
    {
        $this->upload_status = array_key_exists( 'upload_status', $video_data ) ? $video_data['upload_status'] : null;

        $this->video_link = array_key_exists( 'video_link', $video_data ) ? $video_data['video_link'] : null;
    }

    public function is_video_ready()
    {
        return $this->upload_status == self::VIDEO_STATUS_AVAILABLE;
    }

    public function is_error()
    {
        return $this->upload_status == self::VIDEO_STATUS_ERROR;
    }

    public function get_video_url()
    {
        return $this->video_link;
    }

    public function set_video_url( $url )
    {
        return $this->video_link = $url;
    }

    public function get_video_id()
    {
        return $this->id;
    }

    public function get_vimeo_id()
    {
        return $this->vimeo_id;
    }

    public function get_attachment_id()
    {
        return $this->attachment_id;
    }

    public function set_attachment_id( $id )
    {
        return $this->attachment_id = $id;
    }

    public function get_video_status()
    {
        return $this->upload_status;
    }

    public function set_video_status( $status )
    {
        return $this->upload_status = $status;
    }

    public function get_source_attachment_file()
    {
        return $this->source_attachment_file;
    }

    public function set_source_attachment_file( $file_path )
    {
        $this->source_attachment_file = $file_path;
    }

    public function get_video_uploaded_to_link_string()
    {
        if( empty( $this->uploaded_to ) ) return '';

        $uploaded_to_link = get_the_permalink( $this->uploaded_to );
        $uploaded_to_title = get_the_title( $this->uploaded_to );

        return "<a href='{$uploaded_to_link}'>{$uploaded_to_title}</a>";
    }

    public function get_vimeo_embed_url()
    {
        return 'https://player.vimeo.com/video/' . $this->get_vimeo_id();
    }

    public function get_video_url_link_string()
    {
        $url = $this->get_vimeo_embed_url();

        if( empty( $url ) ) return '';

        $shortened_url = preg_replace('/(?<=^.{22}).{4,}(?=.{20}$)/', '...', $url);

        return "<a href='{$url}' target='_BLANK'><span class='dashicons dashicons-video-alt2'></span> {$shortened_url}</a>";
    }

    public function set_video_uploaded_to( $uploaded_to )
    {
        $this->uploaded_to = $uploaded_to;
    }
}
