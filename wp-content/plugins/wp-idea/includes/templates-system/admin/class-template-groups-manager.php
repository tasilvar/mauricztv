<?php

namespace bpmj\wpidea\templates_system\admin;

use bpmj\wpidea\templates_system\admin\ajax\Template_Groups_Ajax_Actions;

class Template_Groups_Manager
{
    /**
     * @var Template_Groups_Ajax_Actions
     */
    private $ajax_actions;

    public function __construct(
        Template_Groups_Ajax_Actions $ajax_actions
    ) {
        $this->ajax_actions = $ajax_actions;
    }

    public function init(): void
    {
        $this->ajax_actions->init();
    }
}