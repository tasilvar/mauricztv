<?php

namespace bpmj\wpidea\caps;

// Exit if accessed directly
use bpmj\wpidea\Caps;

if ( !defined( 'ABSPATH' ) )
	exit;

class Settings_Access extends Access {
    
    public function verifyPage( $post = null )
    {
        global $pagenow;
        
        $page_param = !empty( $_GET['page'] ) ? $_GET['page'] : null;

        if( ( 'admin.php' === $pagenow ) && ( 'wp-idea-settings' ===  $page_param ) ){
            return $this->grant_access();
        }

        return parent::verifyPage($post);
    }

    public function grant_access()
    {
        if( $this->all_caps[ Caps::CAP_MANAGE_SETTINGS ] ){
            $this->all_caps['edit_pages'] = true;
            $this->all_caps['edit_others_pages'] = true;
            $this->all_caps['edit_published_pages'] = true;
        }

        return $this->all_caps;
    }
}