<?php

namespace bpmj\wpidea\view;

use bpmj\wpidea\View;

class View_Provider implements Interface_View_Provider
{
    private $view;

    public function __construct(
        View $view
    )
    {
        $this->view = $view;
    }

    public function get(string $name, array $params = []): string
    {
        return $this->view->get_view($name, $params, $this);
    }

    public function get_admin(string $name, array $params = []): string
    {
        return $this->view->get_admin_view($name, $params, $this);
    }
}