<?php

namespace bpmj\wpidea\admin\video\attachment;

class Attachment_Extender{
    const VIDEO_ID_FIELD_NAME = 'video_id';
    const VIDEO_UPLOAD_STATUS_FIELD_NAME = 'video_upload_status';

    const ALLOWED_EXTERNAL_VIDEO_HOSTS = array(
        'vimeo.com'
    );

    public function __construct() {
        
        add_filter( 'wp_get_attachment_url', [$this, 'get_attachment_url'], 10, 2 );
        
        add_filter( 'wp_prepare_attachment_for_js', [$this, 'display_original_names'],  10, 3 );

        $this->add_initial_actions();
    }

    

    /**
     * Force WP to return attachment file url instead of the relative file path
     *
     * @param string $file
     * @param int $attachment_id
     * @return string
     */
    public function get_attachment_url($file, $attachment_id)
    {
        $attachment_file = $this->_get_attachment_file( $attachment_id );

        if( $this->is_one_of_allowed_hosts( $attachment_file ) ){
            return $this->remove_uploads_dir_from_url( $attachment_file );
        }

        return $file;
    }

    private function _get_attachment_file( $attachment_id )
    {
        return get_attached_file( $attachment_id, true );
    }

    /**
     * Remove uploads dir from url 
     * like '/wp-content/uploads/http://{allowed_host}/video.mp4'. 
     * It's necessary because Wordpress is trying to relativize the path
     *
     * @param string $url
     * @return string
     */
    private function remove_uploads_dir_from_url( $url )
    {
		// Get upload directory.
        $uploads = wp_get_upload_dir();

        $string_to_remove = $uploads['basedir'] . '/';

        if( 0 === strpos( $url, $string_to_remove) ){
            return str_replace( $string_to_remove, '', $url );
        }

        return $url;
    }

    /**
     * Return true if path contains one of the allowed hosts 
     *
     * @see Attachment_Extender::ALLOWED_EXTERNAL_VIDEO_HOSTS
     * @param string $path
     * @return boolean
     */
    private function is_one_of_allowed_hosts( $path )
    {
        foreach (self::ALLOWED_EXTERNAL_VIDEO_HOSTS as $key => $host) {
            if( false !== strpos( $path, $host ) ){
                return true;
            }
        }

        return false;
    }

     /**
     * Setup
     *
     * @return void
     */
    private function add_initial_actions()
    {
        $this->add_custom_attachment_fields();
    }

    /**
     * Add custom fields to the WP attachment
     *
     * @return void
     */
    private function add_custom_attachment_fields()
    {
        add_action( 'after_setup_theme', array( $this, 'add_attachment_metaboxes' ));        
    }

    /**
     * Add metaboxed to the WP attachment
     *
     * @return void
     */
    public function add_attachment_metaboxes()
    {
        $this->_create_metabox( self::VIDEO_ID_FIELD_NAME, __( 'Vimeo API video ID', BPMJ_EDDCM_DOMAIN ) );
        $this->_create_metabox( self::VIDEO_UPLOAD_STATUS_FIELD_NAME, __( 'Vimeo API video upload status', BPMJ_EDDCM_DOMAIN ) );
    }
    
    private function _create_metabox( $name, $label )
    {        
        Attachment_Meta::create([
            $name => [
                'label' => $label
            ]
        ]);
    }
    
    /**
     * Display original names instead of vimeo given ones on all media library pages
     * @param array $response
     * @param WP_Post $attachment
     * @param array|false $meta
     * @return array|void
     */
    
    public function display_original_names($response, $attachment,  $meta) {
        {
          if( 'video' === $response['type']) 
            {
                $response['filename'] = get_the_title( $attachment->ID) ;

            }  
           return $response;
        }
    }
}