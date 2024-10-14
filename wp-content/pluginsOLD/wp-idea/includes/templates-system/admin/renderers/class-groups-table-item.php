<?php

namespace bpmj\wpidea\templates_system\admin\renderers;

use bpmj\wpidea\admin\helpers\html\Button;
use bpmj\wpidea\admin\helpers\html\Interface_Renderable;
use bpmj\wpidea\admin\tables\Abstract_Enhanced_Table_Item;
use bpmj\wpidea\admin\helpers\html\Link;
use bpmj\wpidea\admin\tables\Label;

class Groups_Table_Item extends Abstract_Enhanced_Table_Item
{
    private $name;

    private $action_activate;

    private $edit;

    private $settings;

    private $colors;

    public function __construct(Link $name, Interface_Renderable $action_activate, Link $edit, Link $colors, Button $settings)
    {
        $this->name = $name;
        $this->action_activate = $action_activate;
        $this->colors = $colors;
        $this->edit = $edit;
        $this->settings = $settings;
    }

    public function get_values(): array
    {
        return [
            $this->name,
            $this->action_activate,
            $this->colors,
            $this->settings,
            $this->edit
        ];
    }

    public static function get_labels(): array
    {
        return [
            Label::create(__('Template name', BPMJ_EDDCM_DOMAIN)),
            Label::create(__('Is active?', BPMJ_EDDCM_DOMAIN)),
            Label::create_hidden('action-colors'),
            Label::create_hidden('action-settings'),
            Label::create_hidden('action-edit')
        ];
    }
}
