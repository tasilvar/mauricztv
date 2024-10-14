<?php

namespace bpmj\wpidea\caps;

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

abstract class Access {
    /**
     * @var Access
     */
    private $next;

    public $all_caps;

    public function __construct( array $all_caps ) {
        $this->all_caps = $all_caps;
    }

    /**
     * This method can be used to build a chain of Caps_Access objects.
     */
    public function thenCheck(Access $next)
    {
        $this->next = $next;

        return $next;
    }

    public function verifyPage( $post = null )
    {
        if (!$this->next) {
            return false;
        }

        return $this->next->verifyPage($post);
    }

    public function grant_access(){
        return $this->all_users_cap;
    }
}