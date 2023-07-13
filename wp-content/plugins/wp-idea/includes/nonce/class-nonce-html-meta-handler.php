<?php
namespace bpmj\wpidea\nonce;

class Nonce_Html_Meta_Handler
{
    public function __construct()
    {
        $this->add_hooks();
    }

    public function add_hooks()
    {
        add_action( 'admin_head', function (){
            echo '<meta name="' . Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME . '" content="' . Nonce_Handler::create() .  '" />';
        } );
        add_action( 'wp_head', function (){
            echo '<meta name="' . Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME . '" content="' . Nonce_Handler::create() .  '" />';
        } );
    }
}
