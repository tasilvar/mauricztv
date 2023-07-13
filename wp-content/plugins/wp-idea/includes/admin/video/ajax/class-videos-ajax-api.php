<?php

namespace bpmj\wpidea\admin\video\ajax;

use bpmj\wpidea\admin\video\attachment\Video_Attachment;
use bpmj\wpidea\admin\video\settings\Videos_Settings;

class Videos_Ajax_Api {

    const ACTION_CHANGE_VIMEO_UPLOAD_ENABLED = 'change_vimeo_upload_enabled';
    const ACTION_GET_VIMEO_UPLOAD_ENABLED = 'get_vimeo_upload_enabled';
    const ACTION_GET_VIMEO_UPLOAD_STATUS = 'get_vimeo_upload_status';

    const PARAM_ATTACHMENT_ID = 'attachment_id';
    const PARAM_CHANGE_VIMEO_UPLOAD_ENABLED = 'vimeo_upload_enabled';

    public function __construct() {
        $this->_listen_to_ajax_vimeo_actions();        
    }

    /**
     * Bind methods with WP Ajax requests
     *
     * @return void
     */
    private function _listen_to_ajax_vimeo_actions()
    {
        add_action('wp_ajax_' . self::ACTION_CHANGE_VIMEO_UPLOAD_ENABLED, array( $this, 'change_vimeo_upload_enabled_status') );
        add_action('wp_ajax_' . self::ACTION_GET_VIMEO_UPLOAD_ENABLED, array( $this, 'get_vimeo_upload_enabled_status') );
        add_action('wp_ajax_' . self::ACTION_GET_VIMEO_UPLOAD_STATUS, array( $this, 'get_vimeo_upload_status') );
    }

    public function change_vimeo_upload_enabled_status()
    {
        $new_status = $this->_get_ajax_status_change_param_value();

        switch ($new_status) {
            case Videos_Settings::VIMEO_UPLOAD_ENABLED_ON:
                $this->_enable_vimeo_upload();
                break;

            case Videos_Settings::VIMEO_UPLOAD_ENABLED_OFF:
                $this->_disable_vimeo_upload();
                break;
            
            default:
                wp_send_json_error(); //send response and die
        }

        wp_send_json_success(); //send response and die
    }

    public function get_vimeo_upload_enabled_status()
    {
        $response_data = array();

        $response_data[ self::PARAM_CHANGE_VIMEO_UPLOAD_ENABLED ] = $this->_is_vimeo_upload_enabled();

        wp_send_json_success( $response_data );
    }

    public function get_vimeo_upload_status()
    {
        $id = !empty( $_REQUEST[ self::PARAM_ATTACHMENT_ID ] ) ? $_REQUEST[ self::PARAM_ATTACHMENT_ID ] : null;

        if( empty( $id ) ){
            wp_send_json_error(); //send response and die
        } else {
            $attachment = new Video_Attachment( $id );
            wp_send_json_success( $attachment->get_video_upload_status() ); //send response and die
        }
    }

    private function _get_ajax_status_change_param_value()
    {
        return !empty( $_REQUEST[ self::PARAM_CHANGE_VIMEO_UPLOAD_ENABLED ] ) ? $_REQUEST[ self::PARAM_CHANGE_VIMEO_UPLOAD_ENABLED ] : null;
    }

    /**
     * Enable upload to Vimeo in settings
     *
     * @return boolean
     */
    private function _enable_vimeo_upload()
    {
        return Videos_Settings::enable_vimeo_upload();
    }

    /**
     * Disable upload to Vimeo in settings
     *
     * @return boolean
     */
    private function _disable_vimeo_upload()
    {
        return Videos_Settings::disable_vimeo_upload();
    }

    /**
     * Returns true if Vimeo upload option is enabled
     *
     * @return boolean
     */
    private function _is_vimeo_upload_enabled()
    {
        return Videos_Settings::is_vimeo_upload_enabled();
    }

    /**
     * Returns true if Vimeo upload option is disabled
     *
     * @return boolean
     */
    private function _is_vimeo_upload_disabled()
    {
        return Videos_Settings::is_vimeo_upload_disabled();
    }
}