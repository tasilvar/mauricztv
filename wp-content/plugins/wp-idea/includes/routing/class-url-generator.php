<?php
namespace bpmj\wpidea\routing;

use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\environment\Interface_Site;

class Url_Generator implements Interface_Url_Generator
{
    private $site;
    private $controller_class;
    private $controller_name;
    private $action_name;
    private $args;

    public function __construct(Interface_Site $site)
    {
        $this->site = $site;
    }

    public function generate($controller_class, $action_name, $args = []): string
    {
        return $this->set_controller_class($controller_class)
            ->set_action_name($action_name)
            ->set_args($args)
            ->get();
    }

    private function set_controller_class(string $controller_class): self
    {
        $this->controller_class = $controller_class;

        $this->extract_controller_name();

        return $this;
    }

    private function extract_controller_name(): void
    {
        $exploded_controller = explode('\\', $this->controller_class);
        $this->controller_name = $exploded_controller[count($exploded_controller) - 1];
    }

    private function set_action_name(string $action_name): self
    {
        $this->action_name = $action_name;

        return $this;
    }

    private function set_args(array $args): self
    {
        $this->args = $args;

        return $this;
    }

    // Require an action parameter for admin-ajax.php.
    private function add_action_arg_if_not_exist()
    {
        if(!isset($this->args['action'])){
            $this->args['action'] = Router::WPI_AJAX_HANDLER;
        }
    }

    private function get(): string
    {
        $url = $this->site->get_base_url();

        if($this->is_ajax()){
            $url = $this->site->get_ajax_url();
            $this->add_action_arg_if_not_exist();
        } else if($this->is_admin()){
            $url = $this->site->get_admin_url();
        }

        $this->args[Router::ROUTE_SLUG]  = ($this->is_admin() ? Action::ADMIN_SLUG . '/' : '') . $this->get_controller_slug() .'/'.$this->action_name;
        return $url . '?' . urldecode(http_build_query($this->args)); // @todo urldecode dodałem bo nie przechodziły testy, ale to chyba był błąd???
    }

    private function is_ajax(): bool
    {
        return is_subclass_of($this->controller_class, Ajax_Controller::class);
    }

    private function get_controller_slug(): string
    {
        $exploded_controller_name = explode('_', $this->controller_name);
        unset($exploded_controller_name[count($exploded_controller_name) - 1]);
        if(0 === strcmp($exploded_controller_name[0], 'Admin')) {
            unset($exploded_controller_name[0]);
        }

        $exploded_controller_name = array_map('strtolower', $exploded_controller_name);

        return implode('_', $exploded_controller_name);
    }

    private function is_admin(): bool
    {
        $exploded_controller_name = explode('_', $this->controller_name);

        if(0 === strcmp($exploded_controller_name[0], 'Admin')) {
            return true;
        }

        return false;
    }

    public function generate_admin_page_url(string $path, array $args = []): string
    {
        return add_query_arg($args, admin_url($path));
    }

    public function get_dashboard_url(): string
    {
        return get_dashboard_url();
    }

}
