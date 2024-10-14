<?php

namespace bpmj\wpidea\templates_system;

use bpmj\wpidea\templates_system\groups\Template_Group;
use bpmj\wpidea\templates_system\templates\Template;

class Default_Templates_Creator
{
    /** @var Default_Templates */
    private $default_templates;

    public function __construct(Default_Templates $default_templates)
    {
        $this->default_templates = $default_templates;
    }

    public function create(): void
    {
        $this->create_default_templates_if_not_exist();
    }

    private function create_default_templates_if_not_exist(): void
    {
        foreach ($this->default_templates->get_all() as $template_group_base_template => $templates) {
            $group = Template_Group::find_preinstalled_for_template($template_group_base_template);

            if(is_null($group)) {
                continue;
            }

            foreach ($templates as $template) {
                /** @var Template $template */
                $default_template_exists = $default_template_id = $template::default_template_exists($group->get_id());

                if(!$default_template_exists) $template::create(true, true, $group->get_id());

                if($default_template_exists) new $template($default_template_id);
            }
        }
    }
}