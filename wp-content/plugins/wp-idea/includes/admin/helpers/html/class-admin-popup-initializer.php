<?php

namespace bpmj\wpidea\admin\helpers\html;

use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\view\Interface_View_Provider;

class Admin_Popup_Initializer implements Interface_Initiable
{
    private Interface_Actions $actions;
    private Interface_View_Provider $view_provider;

    public function __construct(
        Interface_Actions $actions,
        Interface_View_Provider $view_provider
    ) {
        $this->actions = $actions;
        $this->view_provider = $view_provider;
    }

    public function init(): void
    {
        $this->actions->add('admin_footer', [$this, 'add_popup_to_admin_footer']);
    }

    public function add_popup_to_admin_footer()
    {
        echo $this->view_provider->get_admin('/helpers/html/wpi-popup');
    }
}