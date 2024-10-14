<?php

namespace bpmj\wpidea\shared\infrastructure\controllers;

interface Interface_Rest_Registration_Service
{
    public function register_route(string $namespace, string $route, array $args): void;
}

