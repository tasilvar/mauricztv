<?php

namespace bpmj\wpidea\caps;

// Exit if accessed directly
use bpmj\wpidea\Caps;

if ( !defined( 'ABSPATH' ) )
	exit;

class Edd_Download_Access extends Access {

    public function verifyPage( $post = null )
    {
        global $pagenow;

        $is_edit_page = ( 'edit.php' === $pagenow );
        $is_download_post_type = array_key_exists( 'post_type', $_GET ) && ( 'download' === $_GET['post_type'] );

        if( $is_edit_page && $is_download_post_type ){
            return $this->grant_access();
        }

        return parent::verifyPage($post);
    }

    public function grant_access()
    {
        if( $this->all_caps[ Caps::CAP_MANAGE_PRODUCTS ] ){
            $this->all_caps['edit_pages'] = true;
            $this->all_caps['edit_others_pages'] = true;
            $this->all_caps['edit_published_pages'] = true;
        }
        
        return $this->all_caps;
    }
}