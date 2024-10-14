<?php

namespace bpmj\wpidea\templates_system\admin;

use bpmj\wpidea\templates_system\admin\helpers\Default_Groups_Creator;
use bpmj\wpidea\templates_system\Default_Templates_Creator;
use bpmj\wpidea\templates_system\groups\Template_Group;
use bpmj\wpidea\templates_system\templates\Template;

class Templates_Integrity_Checker
{
    /**
     * @var Default_Templates_Creator
     */
    private $default_templates_creator;

    /**
     * @var Default_Groups_Creator
     */
    private $default_groups_creator;

    public function __construct(
        Default_Templates_Creator $default_templates_creator,
        Default_Groups_Creator $preinstalled_groups_handler
    ) {
        $this->default_templates_creator = $default_templates_creator;
        $this->default_groups_creator = $preinstalled_groups_handler;
    }

    public function ensure_integrity(): void
    {
        $this->create_default_templates_if_do_not_exist();

        $this->ensure_each_template_is_assigned_to_a_group();

        $this->remove_templates_with_generic_classes();

        $this->ensure_there_are_no_duplicated_templates();
    }

    private function ensure_each_template_is_assigned_to_a_group(): void
    {
        $templates_without_group = Template::find_not_assigned_to_a_group();
        $default_group = Template_Group::find_preinstalled_for_template(Template_Group::BASE_TEMPLATE_SCARLET);

        foreach ($templates_without_group as $template) {
            /** @var Template $template */
            $template->add_to_group($default_group->get_id());
        }
    }

    private function create_default_templates_if_do_not_exist(): void
    {
        $this->default_groups_creator->create();
        $this->default_templates_creator->create();
    }

    private function ensure_there_are_no_duplicated_templates(): void
    {
        foreach (Template::find_all() as $template) {
            /** @var Template $template */
            $template_class = get_class($template);

            if($template_class === Template::class) {
                continue;
            }

            $active_template = $template_class::find_active_one_in_group($template->get_group_id());

            if($active_template === null) {
                continue;
            }

            $is_template_active_one_for_this_class = ($active_template->get_id() === $template->get_id());
            $is_template_preinstalled = $template->is_basic();

            if(!$is_template_preinstalled) {
                continue;
            }

            if($is_template_active_one_for_this_class) {
                continue;
            }

            $template->delete();
        }
    }

    private function remove_templates_with_generic_classes(): void
    {
        foreach (Template::find_all() as $template) {
            $template_class = get_class($template);

            if ($template_class === Template::class) {
                $template->delete();
            }
        }
    }
}