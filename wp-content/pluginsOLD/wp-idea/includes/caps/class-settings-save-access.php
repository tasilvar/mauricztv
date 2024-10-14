<?php

namespace bpmj\wpidea\caps;

// Exit if accessed directly
use bpmj\wpidea\Caps;

if ( !defined( 'ABSPATH' ) )
	exit;

class Settings_Save_Access extends Access {
    
    public function verifyPage( $post = null )
    {
        global $pagenow;

        $is_settings_save_action = 
            ( 'options.php' === $pagenow )
            && (!empty($_POST['option_page']))
            && ( 'wp_idea' === $_POST['option_page'] ) 
            && ( 'update' === $_POST['action'] );
        if( $is_settings_save_action ){
            return $this->grant_access();
        }

        return parent::verifyPage($post);
    }

    public function grant_access()
    {
        if( $this->all_caps[ Caps::CAP_MANAGE_SETTINGS ] ){
            $this->all_caps['manage_options'] = true;
        }

        return $this->all_caps;
    }
}