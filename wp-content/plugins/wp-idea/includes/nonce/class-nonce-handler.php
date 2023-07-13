<?php

namespace bpmj\wpidea\nonce;

use bpmj\wpidea\Current_Request;

class Nonce_Handler
{
    public const DEFAULT_REQUEST_VARIABLE_NAME = '_wpnonce';
    public const DEFAULT_ACTION_NAME = '_wpi_action';

    private $current_request;

    public function __construct(Current_Request $current_request)
    {
        $this->current_request = $current_request;
    }

    public static function create(?string $action_name = null)
    {
        return wp_create_nonce( $action_name ?? self::DEFAULT_ACTION_NAME );
    }

    public function verify(): bool
    {
        if(!$this->current_request->request_arg_exists(self::DEFAULT_REQUEST_VARIABLE_NAME)){
            return false;
        }

        $nonce_value = $this->current_request->get_request_arg(self::DEFAULT_REQUEST_VARIABLE_NAME);

        return wp_verify_nonce( $nonce_value, self::DEFAULT_ACTION_NAME);
    }

    public static function get_field(): string
    {
        return '<input type="hidden" name="'.self::DEFAULT_REQUEST_VARIABLE_NAME.'" value="'.self::create().'" >';
    }
}
