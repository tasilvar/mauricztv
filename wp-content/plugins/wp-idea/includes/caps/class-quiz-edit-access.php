<?php

namespace bpmj\wpidea\caps;

// Exit if accessed directly
use bpmj\wpidea\Caps;

if ( !defined( 'ABSPATH' ) )
	exit;

class Quiz_Edit_Access extends Access {
    
    public function verifyPage( $post = null )
    {
        if( !empty( $post ) && ($post instanceof \WP_Post) && 'test' === get_post_meta( $post->ID, 'mode', true ) ){
            return $this->grant_access();
        }

        return parent::verifyPage($post);
    }

    public function grant_access()
    {
        if(isset( $this->all_caps[ Caps::CAP_MANAGE_QUIZZES ] ) && $this->all_caps[ Caps::CAP_MANAGE_QUIZZES ] ){
            $this->all_caps['edit_post'] = true;
            $this->all_caps['edit_others_posts'] = true;
            $this->all_caps['edit_published_posts'] = true;
        }
        
        return $this->all_caps;
    }
}