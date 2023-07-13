<?php
namespace bpmj\wpidea\routing;


class Action
{

    const MIN_ROUTE_ARGS = 2;
    const MAX_ROUTE_ARGS = 3;
    private $route_name;
    private $action_name;
    private $controller_name;

    private const ACTION_PREFIX = '_action';
    public const ROUTE_SLUG_SEPARATOR = '/';
    public const ADMIN_SLUG = 'admin';


    public function is_not_empty(): bool
    {
        return ($this->route_name && $this->action_name);
    }

    public function set_controller_name(?string $controller_name): void
    {
        $this->controller_name = $controller_name;
    }

    public function set_route_name($route_name): void
    {
        $this->route_name = $route_name;
    }

    public function get_route_name(): string
    {
        return $this->route_name;
    }

    public function get_controller_name(): string
    {
        return $this->controller_name;
    }

    public function get_action_name(): string
    {
        return $this->action_name;
    }

    public function set_action_name($action_name): void
    {
        $this->action_name = $action_name;
    }

    public function get_action_name_with_prefix(): string
    {
        return $this->action_name . self::ACTION_PREFIX;
    }

    public function is_admin(): bool
    {
        $exploded_route_name = explode(Action::ROUTE_SLUG_SEPARATOR, $this->route_name);
        if(isset($exploded_route_name[1])){
            if($exploded_route_name[0] == Action::ADMIN_SLUG){
                return true;
            }
        }

        return false;
    }

    public function load_from_route_arg(?string $route_arg): void
    {
        // exmaple $route_arg = admin/route_name/action_name
        $exploded_route_arg = explode(self::ROUTE_SLUG_SEPARATOR, $route_arg);
        if(count($exploded_route_arg) < self::MIN_ROUTE_ARGS || count($exploded_route_arg) > self::MAX_ROUTE_ARGS){
            return;
        }

        $this->route_name = $this->get_route_name_from_exploded_route_args($exploded_route_arg);
        $this->action_name = $this->get_action_name_from_exploded_route_args($exploded_route_arg);
    }

    private function get_action_name_from_exploded_route_args(array $route_args): ?string
    {
        if($route_args[0] == self::ADMIN_SLUG){
            return $route_args[2];
        }

        return $route_args[1];
    }

    private function get_route_name_from_exploded_route_args(array $route_args): ?string
    {
        if($route_args[0] == self::ADMIN_SLUG){
            return self::ADMIN_SLUG . self::ROUTE_SLUG_SEPARATOR . $route_args[1];
        }

        return $route_args[0];
    }

}
