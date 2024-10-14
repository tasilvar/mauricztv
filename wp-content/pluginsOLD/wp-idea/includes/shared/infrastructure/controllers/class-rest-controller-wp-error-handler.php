<?php

namespace bpmj\wpidea\shared\infrastructure\controllers;

use WP_REST_Response;

class Rest_Controller_WP_Error_Handler implements Interface_Rest_Controller_Error_Handler
{

    public function prepare_response(int $code, string $message): object
    {
        return new WP_REST_Response(
            ['code' => $code, 'message' => $message, 'status' => 'error'],
            $code
        );
    }
}