<?php namespace bpmj\wpidea\helpers;

interface Interface_Response_Helper
{
    public function die_with_http_status(
        int $status_code,
        ?string $message = null,
        ?string $debug_mode_info = null
    ): void;
}