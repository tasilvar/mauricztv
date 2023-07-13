<?php

namespace bpmj\wpidea\shared\infrastructure\routing;

class Routes
{
    private array $routes = [];

    public function add(string $route, string $controller): void
    {
        $this->routes[$route] = $controller;
    }

    public function get(): array
    {
        return $this->routes;
    }
}