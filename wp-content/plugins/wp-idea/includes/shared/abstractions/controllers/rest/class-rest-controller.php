<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\shared\abstractions\controllers\rest;

use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\shared\infrastructure\controllers\Interface_Rest_Registration_Service;
use bpmj\wpidea\shared\infrastructure\controllers\Rest_Router;
use http\Exception\InvalidArgumentException;

abstract class Rest_Controller
{

    public const READABLE = 'GET';
    public const CREATABLE = 'POST';
    public const EDITABLE = 'POST, PUT, PATCH';
    public const DELETABLE = 'DELETE';
    public const ALLMETHODS = 'GET, POST, PUT, PATCH, DELETE';

    private array $routes = [];

    protected const RETURN_ARRAY_SUCCESS = ['status' => 'ok'];

    private const URL_NAMESPACE = 'wp-idea/v2';

    private Interface_Actions $actions;
    private Interface_Rest_Registration_Service $rest_service;

    public function __construct(
        Interface_Actions $actions,
        Interface_Rest_Registration_Service $rest_service,
        Rest_Router $rest_router
    ) {
        $this->actions = $actions;
        $this->rest_service = $rest_service;

        $this->init();
    }

    private function init(): void
    {
        $this->register_routes();
        $this->actions->add('rest_api_init', [$this, 'init_routes']);
    }

    protected abstract function register_routes(): void;

    public function init_routes(): void
    {
        foreach ($this->routes as $endpoint => $args) {
            $this->rest_service->register_route(self::URL_NAMESPACE, $endpoint, $args);
        }
    }

    public function register_route(
        string $endpoint,
        string $methods,
        string $action,
        ?callable $callback_permissions = null
    ): void {
        $callback = [$this, $action];

        if(!is_callable($callback)) {
            throw new InvalidArgumentException();
        }

        $this->routes[$endpoint] = [
            'methods' => $methods,
            'callback' => $callback,
            'permission_callback' => $callback_permissions ?? '__return_true'
        ];
    }
}