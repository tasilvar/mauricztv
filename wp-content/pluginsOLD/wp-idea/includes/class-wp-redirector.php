<?php

namespace bpmj\wpidea;

class WP_Redirector implements Interface_Redirector
{
    public function redirect(string $location, int $status_code = 302): void
    {
        wp_redirect( $location, $status_code );
        exit;
    }

    public function redirect_back(): void
    {
        $location = $_SERVER['HTTP_REFERER'];
        wp_safe_redirect($location);
        exit;
    }
}