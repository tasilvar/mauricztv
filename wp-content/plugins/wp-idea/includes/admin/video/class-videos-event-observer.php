<?php

namespace bpmj\wpidea\admin\video;

use bpmj\wpidea\admin\video\attachment\Video_Attachment;

class Videos_Event_Observer {
    /**
     * Videos_Manager instance
     *
     * @var Videos_Manager
     */
    private $_videos_manager_instance;

    /**
     * Instantiate Videos_Event_Observer
     *
     * @param Videos_Manager $videos_manager
     */
    public function __construct( $videos_manager ) {
        $this->_videos_manager_instance = $videos_manager;

        $this->_listen_to_file_upload();

        $this->_listen_to_attachment_delete();
    }

    /**
     * Trigger video upload to the remote after attachment has been added
     * 
     * Triggers on 'add_attachment' hook
     * 
     * @see https://developer.wordpress.org/reference/hooks/add_attachment/
     * 
     * @param int $post_ID
     * 
     */
    public function on_attachment_added($post_ID)
    {
        if(!$this->_is_supported_attachment_type($post_ID)) return;

        $this->rename_attached_file($post_ID);

        $this->trigger_upload($post_ID);
    }

    /**
     * Trigger video removal from the remote after attachment has been added
     * 
     * Triggers on 'delete_attachment' hook
     * 
     * @see https://developer.wordpress.org/reference/hooks/delete_attachment/
     * 
     * @param int $post_ID
     * 
     */
    public function on_attachment_deleted( $post_ID )
    {
        if( ! $this->_is_supported_attachment_type( $post_ID ) ) return;

        $this->trigger_delete( $post_ID );
    }

    protected function rename_attached_file($post_ID)
    {
        $file_path = get_attached_file($post_ID);
        $new_file_path = $this->get_sanitized_file_path($file_path);
    
        rename($file_path, $new_file_path);
        update_attached_file($post_ID, $new_file_path);
    }

    protected function get_sanitized_file_path($file)
    {        
        $pathinfo = pathinfo($file);
    
        $new_file_name = $this->remove_unwanted_characters_from_filename($pathinfo['filename']);

        $new_file = $pathinfo['dirname'] . '/' . $new_file_name . '.' . $pathinfo['extension'];

        return $new_file;
    }

    protected function remove_unwanted_characters_from_filename($file_name)
    {
        $file_name = sanitize_file_name($file_name);
        
        $file_name = preg_replace('/[^a-z0-9]+/', '-', strtolower($file_name));

        return $file_name;
    }

    protected function _is_supported_attachment_type( $attachment_id )
    {
        $attachment = new Video_Attachment( $attachment_id );

        return $attachment->is_supported_type();
    }

    protected function trigger_upload( $id )
    {
        $this->_videos_manager_instance->triggerAction( Videos_Manager::ACTION_UPLOAD, array('attachment_id' => $id ) );
    } 

    protected function trigger_delete( $id )
    {
        $this->_videos_manager_instance->triggerAction( Videos_Manager::ACTION_DELETE, array('attachment_id' => $id ) );
    }

    protected function _listen_to_file_upload()
    {
        add_action( 'add_attachment', array( $this, 'on_attachment_added' ), 10, 1 );
    }

    protected function _listen_to_attachment_delete()
    {
        add_action( 'delete_attachment', array( $this, 'on_attachment_deleted' ), 10, 1 );
    }
}