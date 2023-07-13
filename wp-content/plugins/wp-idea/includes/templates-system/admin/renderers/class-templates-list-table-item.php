<?php

namespace bpmj\wpidea\templates_system\admin\renderers;

use bpmj\wpidea\admin\helpers\html\Interface_Renderable;
use bpmj\wpidea\admin\tables\Abstract_Enhanced_Table_Item;
use bpmj\wpidea\admin\tables\Label;

class Templates_List_Table_Item extends Abstract_Enhanced_Table_Item
{
    private $name;

    private $action_edit;

    private $action_restore;

    public function __construct(string $name, Interface_Renderable $action_edit, Interface_Renderable $action_restore)
    {
        $this->name = $name;
        $this->action_edit = $action_edit;
        $this->action_restore = $action_restore;
    }

    public function get_values(): array
    {
        return [
            $this->name,
            $this->action_edit,
            $this->action_restore
        ];
    }

    public static function get_labels(): array
    {
        return [
            Label::create(__('Template type', BPMJ_EDDCM_DOMAIN)),
            Label::create_hidden('action-edit'),
            Label::create_hidden('action-restore'),
        ];
    }
}
