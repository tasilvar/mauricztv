<?php

namespace bpmj\wpidea\templates_system\admin\modules\settings_handlers;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\Caps;
use bpmj\wpidea\templates_system\admin\renderers\Template_Groups_Page_Renderer;
use bpmj\wpidea\templates_system\admin\renderers\Legacy_Templates_List_Renderer;
use bpmj\wpidea\templates_system\groups\settings\Template_Group_Settings;
use bpmj\wpidea\templates_system\groups\Template_Groups_Repository;
use bpmj\wpidea\templates_system\groups\Template_Group;
use bpmj\wpidea\templates_system\admin\renderers\Templates_List_Renderer;
use bpmj\wpidea\View;

class New_Templates_System_Settings_Handler implements Interface_Templates_Settings_Handler
{

    private Template_Groups_Page_Renderer $template_groups_renderer;

    private Legacy_Templates_List_Renderer $legacy_templates_list_renderer;
    private Template_Groups_Repository $template_groups_repository;
    private Templates_List_Renderer $templates_list_renderer;

    public function __construct(
        Template_Groups_Page_Renderer $template_groups_renderer,
        Legacy_Templates_List_Renderer $legacy_templates_list_renderer,
        Templates_List_Renderer $templates_list_renderer,
        Template_Groups_Repository $template_groups_repository
    ) {
        $this->template_groups_renderer = $template_groups_renderer;
        $this->legacy_templates_list_renderer = $legacy_templates_list_renderer;
        $this->templates_list_renderer = $templates_list_renderer;
        $this->template_groups_repository = $template_groups_repository;
    }

    public function add_menu_pages(): void
    {
        if($this->should_legacy_templates_list_solution_be_used()) {
            $this->add_legacy_templates_list_solution_menu_pages();

            return;
        }

        $this->add_templates_list_menu_page();
        $this->create_temporary_template_changer();
    }

    private function create_temporary_template_changer(): void
    {
        add_submenu_page(
            null,
            __('Templates', BPMJ_EDDCM_DOMAIN),
            '',
            Caps::CAP_MANAGE_SETTINGS,
            'wp-idea-template-changer',
            [$this, 'temporary_template_changer']
        );
    }

    public function temporary_template_changer(): void
    {
        echo View::get_admin('/templates/guide/new-templates-enabled');
    }

    public function should_template_option_be_visible_on_the_settings_page(string $option_name): bool
    {
        return !in_array($option_name, [
            'list_number',
            'template',
            'override_all',
            'lesson_navigation_section',
            'download_section',
            'lesson_progress_section'
        ], true);
    }

    public function should_color_settings_be_displayed(): bool
    {
        $active_template_group = $this->template_groups_repository->find_active();

        if ($active_template_group === null) {
            return false;
        }

        if (!$active_template_group->supports_legacy_color_settings()) {
            return false;
        }

        return true;
    }

    public function should_download_section_position_field_be_displayed(): bool
    {
        return false;
    }

    public function get_current_template_options(): ?array
    {
        $active_template_group = $this->template_groups_repository->find_active();

        if ($active_template_group === null) {
            return null;
        }

        $active_template_group_settings = $active_template_group->get_settings();

        return $active_template_group_settings->get_all()->getArrayCopy();
    }

    public function legacy_templates_settings_in_use(): bool
    {
        return false;
    }

    public function get_override_all_option_value(): ?string
    {
        $active_group = $this->template_groups_repository->find_active();

        if($active_group === null) {
            return null;
        }

        return $active_group->get_option(Template_Group_Settings::OPTION_OVERRIDE_ALL);
    }

    public function get_custom_css_field_value(): ?string
    {
        $active_group = $this->template_groups_repository->find_active();

        if($active_group === null) {
            return null;
        }

        return $active_group->get_option(Template_Group_Settings::OPTION_CUSTOM_CSS);
    }

    private function should_legacy_templates_list_solution_be_used(): bool
    {
        return !$this->is_scarlet_template_in_use();
    }

    private function is_scarlet_template_in_use(): bool
    {
        $active_group = $this->template_groups_repository->find_active();

        if ($active_group === null) {
            return false;
        }

        return $active_group->get_base_template() === Template_Group::BASE_TEMPLATE_SCARLET;
    }

    private function add_legacy_templates_list_solution_menu_pages(): void
    {
        add_submenu_page(
            'wp-idea',
            __('Templates', BPMJ_EDDCM_DOMAIN),
            __('Templates', BPMJ_EDDCM_DOMAIN),
            Caps::CAP_MANAGE_SETTINGS,
            Admin_Menu_Item_Slug::TEMPLATE_GROUPS,
            [$this->template_groups_renderer, 'render_page']
        );

        add_submenu_page(
            null,
            __('Templates list', BPMJ_EDDCM_DOMAIN),
            '',
            Caps::CAP_MANAGE_SETTINGS,
            Admin_Menu_Item_Slug::TEMPLATES_LIST,
            [$this->legacy_templates_list_renderer, 'render_page']
        );
    }

    private function add_templates_list_menu_page(): void
    {
        add_submenu_page(
            'wp-idea',
            __('Templates', BPMJ_EDDCM_DOMAIN),
            __('Templates', BPMJ_EDDCM_DOMAIN),
            Caps::CAP_MANAGE_SETTINGS,
            Admin_Menu_Item_Slug::TEMPLATE_GROUPS,
            [$this->templates_list_renderer, 'render_page']
        );
    }
}
