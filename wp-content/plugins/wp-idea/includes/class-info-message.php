<?php

namespace bpmj\wpidea;

/**
 *
 * The class responsible for displaying info messages
 *
 */
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

class Info_Message {
	
	public $message;

    public $subtitle;

    public $icon = 'welcome-learn-more';    

    public function __construct($message, $subtitle = null, $icon = null) {
        $this->message = $message;
        
        if( $subtitle ) $this->subtitle = $subtitle;
        
		if( $icon ) $this->icon = $icon;
    }
    
    /**
     * Get message HTML
     *
     * @return string
     */
    public function get()
    {
        $subtitle = $this->subtitle ? "<p>{$this->subtitle}</p>" : '';

        $message = <<<HTML
            <div class='bpmj_edd_info_message'>
                <div class='bpmj_edd_info_message__icon-wrap'>
                    <span class="dashicons dashicons-{$this->icon} bpmj_edd_info_message__icon"></span>
                </div>

                <div class='bpmj_edd_info_message__content'>
                    <h2>{$this->message}</h2>
                    {$subtitle}
                </div>
                
            </div>
HTML;
// ^- closing marker cannot be indented

        return $message;
    }

    /**
     * Echo message HTML
     */
    public function render()
    {
        echo $this->get();
    }
}
