<?php

namespace bpmj\wpidea\admin;

class Notice_Options_Item {
    const ID_PREFIX = 'notice_';

    public $id;

    public $message;

    public $type;

    public $custom_html_content;

    public function __construct( $message = null, $type = null, $custom_html_content = null ) {
        $this->id = self::ID_PREFIX . time();
        $this->message = $message;
        $this->type = $type;
        $this->custom_html_content = $custom_html_content;
    }
}
