<?php namespace bpmj\wpidea\helpers;

class Wp_Response_Helper implements Interface_Response_Helper
{

    private $debug_helper;

    public function __construct(Interface_Debug_Helper $debug_helper)
    {
        $this->debug_helper = $debug_helper;
    }

    public function die_with_http_status(
        int $status_code,
        ?string $message = null,
        ?string $debug_mode_info = null
    ): void {
        status_header($status_code);
        nocache_headers();
        if ($this->debug_helper->in_debug_mode()) {
            $message .= ' ' . $debug_mode_info;
        }
        die($message);
    }

}