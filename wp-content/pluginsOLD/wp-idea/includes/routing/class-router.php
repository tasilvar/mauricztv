<?php

namespace bpmj\wpidea\routing;

use bpmj\wpidea\controllers\Base_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\exceptions\Not_Found_Exception;
use bpmj\wpidea\helpers\Interface_Response_Helper;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\shared\infrastructure\routing\Routes;
use Psr\Container\ContainerInterface;

class Router implements Interface_Initiable
{
    public const WPI_AJAX_HANDLER = 'wpi_handler';
    private const WPI_AJAX_HANDLER_SLUG = 'wp_ajax_' . self::WPI_AJAX_HANDLER;
    public const ROUTE_SLUG = 'wpi_route';
    public const PATH_TO_ROUTING_FILE = BPMJ_EDDCM_DIR . 'config/routing.php';
    private const ADMIN_INIT_ACTION = 'admin_init';
    private const INIT_ACTION = 'init';

    private const PRIORITY_ACTION_INIT = 10;
    private const PRIORITY_ACTION_ADMIN_INIT = 0;

    private Routes $routes;

    private $current_request;
    private $container;
    private $events;
    private $response_helper;

    private array $externally_registered_routes = [];

    public function __construct(
        Routes $routes,
        Current_Request $current_request,
        ContainerInterface $container,
        Interface_Events $events,
        Interface_Response_Helper $response_helper
    ) {
        $this->routes = $routes;
        $this->current_request = $current_request;
        $this->container       = $container;
        $this->events          = $events;
        $this->response_helper = $response_helper;
    }

    public function register_controller(string $route_name, string $controller_class): void
    {
        $this->externally_registered_routes[$route_name] = $controller_class;
    }

    public function init(): void
    {
        $this->load_external_routes();

        $query_route = $this->current_request->get_query_arg(self::ROUTE_SLUG);
        if ( ! $query_route) {
            return;
        }
        $action = new Action();
        $action->load_from_route_arg($query_route);

        if ($action->is_not_empty()) {
            try {
                $controller_name = $this->get_controller_name_from_routes($action);
                $action->set_controller_name($controller_name);
                $this->init_action($action);
            } catch (Not_Found_Exception $e) {
                $this->events->emit(Event_Name::ROUTE_OR_CONTROLLER_NOT_FOUND, $query_route);
                $this->response_helper->die_with_http_status(404, '404 Not Found', $e->getMessage());
            }
        }
    }

    private function load_external_routes(): void
    {
        $external_routes = $this->routes->get();
        if ($external_routes) {
            $this->externally_registered_routes = array_merge($this->externally_registered_routes, $external_routes);
        }
    }

    private function init_action(Action $action): void
    {
        $action_name          = $action->is_admin() ? self::ADMIN_INIT_ACTION : self::INIT_ACTION;
        $priority_action_init = $action->is_admin() ? self::PRIORITY_ACTION_ADMIN_INIT : self::PRIORITY_ACTION_INIT;

        $this->events->on($action_name, function () use ($action) {
            $this->run_action($action);
        }, $priority_action_init);
    }

    private function get_controller_name_from_routes(Action $action): ?string
    {
        $routes          = $this->get_routes();
        $controller_name = $routes[$action->get_route_name()] ?? null;

        if ( ! $controller_name) {
            throw new Not_Found_Exception('Route does not exist!');
        }

        if ( ! class_exists($controller_name)) {
            throw new Not_Found_Exception('Controller does not exist!');
        }

        return $controller_name;
    }

    protected function get_routes(): array
    {
        $routes = include($this->get_path_routing_file());

        return array_merge($routes, $this->externally_registered_routes);
    }

    private function get_path_routing_file(): string
    {
        return self::PATH_TO_ROUTING_FILE;
    }

    private function run_action(Action $action): void
    {
        /** @var Base_Controller $controller */
        $controller = $this->container->get($action->get_controller_name());
        $controller->trigger_action($action, $this->current_request);
    }
}
