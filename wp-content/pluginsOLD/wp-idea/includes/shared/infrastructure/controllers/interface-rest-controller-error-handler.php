<?php

namespace bpmj\wpidea\shared\infrastructure\controllers;

interface Interface_Rest_Controller_Error_Handler
{
    public function prepare_response(int $code, string $message): object;
}

