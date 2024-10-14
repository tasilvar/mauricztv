<?php

namespace bpmj\wpidea\admin\video\attachment;

use Exception;

class Video_Attachment {

    /**
     * @var array
     */
    const SUPPORTED_MIME_TYPES = array( 
        'video/mp4', //.mp4
        'video/x-ms-asf', 'video/x-ms-wmv', //.wmv
        'video/avi', 'application/x-troff-msvideo', 'video/msvideo', 'video/x-msvideo', //.avi
        'video/quicktime' //.mov
    );

    /**
     * @var int
     */
    private $attachment_id;

    /**
     * @param int $attachment_id
     */
    public function __construct( $attachment_id ) {
        $this->attachment_id = $attachment_id;
    }

    /**
     * Get attachment ID
     *
     * @return int
     */
    public function get_id()
    {
        return $this->attachment_id;
    }

    /**
     * Get Attachment URL
     * 
     * @return string
     */
    public function get_url()
    {
        if( ! $this->is_supported_type() ) return false;
        
        return wp_get_attachment_url( $this->get_id() );
    }

    public function set_video_file_url( $url )
    {
        return update_attached_file( $this->get_id(), $url );
    }

    public function set_video_id( $id )
    {
        return $this->_update_meta( self::get_video_id_field_name(), $id );
    }

    public function set_video_upload_status( $status )
    {
        return $this->_update_meta( self::get_video_upload_status_field_name(), $status );
    }

    public function get_video_upload_status()
    {
        return get_post_meta( $this->get_id(), self::get_video_upload_status_field_name(), true );
    }

    public function has_corresponding_video_on_remote()
    {
        return !empty( $this->get_remote_video_id() );
    }

    public function get_remote_video_id()
    {
        return get_post_meta( $this->get_id(), self::get_video_id_field_name(), true );
    }

    private function _update_meta( $meta_name, $value )
    {
        return update_post_meta( 
            $this->get_id(),
            $meta_name,
            $value 
        );
    }

    public static function get_video_id_field_name()
    {
        return "_" . Attachment_Extender::VIDEO_ID_FIELD_NAME;
    }

    public static function get_video_upload_status_field_name()
    {
        return "_" . Attachment_Extender::VIDEO_UPLOAD_STATUS_FIELD_NAME;
    }

    /**
     * Returns true if attachment is of a supported type
     *
     * @return bool
     */
    public function is_supported_type()
    {        
        if( in_array( get_post_mime_type( $this->get_id() ), SELF::SUPPORTED_MIME_TYPES ) ) return true;

        return false;
    }

    public static function get_all_in_progress()
    {
        $args = array(
            'post_type'   => 'attachment',
            'post_status' => 'inherit',
            'meta_query'  => array(
                array(
                    'key'     => self::get_video_upload_status_field_name(),
                    'value'   => 'in_progress'
                )
            )
        );
        $query = new \WP_Query($args);

        return $query->posts;
    }
}