<?php

namespace bpmj\wpidea\shared\infrastructure\controllers;

use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\shared\exceptions\App_Exception;

class Rest_Router
{
    private Interface_Filters $filters;
    private Interface_Rest_Controller_Error_Handler $error_handler;

    public function __construct(
        Interface_Filters $filters,
        Interface_Rest_Controller_Error_Handler $error_handler
    ) {
        $this->filters = $filters;
        $this->error_handler = $error_handler;

        $this->init();
    }

    private function init(): void
    {
        $this->filters->add('rest_dispatch_request', [$this, 'router'], 10, 4);
    }

    public function router($dispatch_result, object $request, string $route, array $handler)
    {
        try {
            return call_user_func($handler['callback'], $request);
        } catch (App_Exception $ex) {
            return $this->error_handler->prepare_response($ex->getCode(), $ex->getMessage());
        }

        return $this->error_handler->prepare_response(404, 'Route not found');
    }
}