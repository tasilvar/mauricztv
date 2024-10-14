<?php

namespace bpmj\wpidea\shared\infrastructure\controllers;

class Rest_Registration_Service implements Interface_Rest_Registration_Service
{

    public function register_route(string $namespace, string $route, array $args): void
    {
        register_rest_route($namespace, $route, $args);
    }
}