<?php

namespace bpmj\wpidea\templates_system\admin\helpers;

use bpmj\wpidea\templates_system\groups\Template_Group;

class Default_Groups_Creator
{
    public function create(): void
    {
        $this->check_preinstalled_groups();
    }

    private function check_preinstalled_groups(): void
    {
        foreach (Template_Group::BASE_TEMPLATES as $base_template_name) {
            $preinstalled_group = $this->find_or_create_group_for_base_template($base_template_name);

            if (isset($preinstalled_group)) {
                $this->maybe_activate_group($preinstalled_group);
            }
        }
    }

    private function get_preinstalled_groups_names(): array
    {
        return [
            Template_Group::BASE_TEMPLATE_SCARLET => __('Scarlet', BPMJ_EDDCM_DOMAIN),
            Template_Group::BASE_TEMPLATE_CLASSIC => __('Classic', BPMJ_EDDCM_DOMAIN),
        ];
    }

    private function get_active_template(): ?string
    {
        return WPI()->templates->get_current_template();
    }

    private function maybe_activate_group(Template_Group $group): void
    {
        if($group->get_base_template() !== $this->get_active_template()) {
            return;
        }

        if($group->is_active()) {
            return;
        }

        $group->set_as_active();
    }

    private function find_group_for_base_template(string $base_template_name): ?Template_Group
    {
        return Template_Group::find_preinstalled_for_template($base_template_name);
    }

    private function create_group_for_base_template($base_template_name): ?Template_Group
    {
        $name = $this->get_preinstalled_groups_names()[$base_template_name] ?? null;

        if(empty($name)) {
            return null;
        }

        return Template_Group::create_preinstalled_for_template($name, $base_template_name);
    }

    private function find_or_create_group_for_base_template(string $base_template_name): ?Template_Group
    {
        return $this->find_group_for_base_template($base_template_name) ?? $this->create_group_for_base_template($base_template_name);
    }
}