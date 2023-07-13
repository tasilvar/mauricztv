<?php

namespace bpmj\wpidea\templates_system\admin\modules\guide;

use bpmj\wpidea\admin\helpers\html\Button;
use bpmj\wpidea\admin\helpers\html\Info_Box;
use bpmj\wpidea\admin\helpers\html\Link;
use bpmj\wpidea\admin\helpers\html\Paragraph;
use bpmj\wpidea\Helper;
use bpmj\wpidea\helpers\Interface_Debug_Helper;
use bpmj\wpidea\templates_system\admin\Template_Groups_Page;
use bpmj\wpidea\templates_system\groups\Template_Groups_Repository;
use bpmj\wpidea\templates_system\Templates_System;
use bpmj\wpidea\View;

class New_Templates_Guide implements Interface_Templates_Guide
{
    private $template_groups_page;

    private $template_groups_repository;

    private $templates_system;

    private $debug_helper;

    public function __construct(
        Template_Groups_Page $template_groups_page,
        Template_Groups_Repository $template_groups_repository,
        Templates_System $templates_system,
        Interface_Debug_Helper $debug_helper
    ) {
        $this->template_groups_page = $template_groups_page;
        $this->template_groups_repository = $template_groups_repository;
        $this->templates_system = $templates_system;
        $this->debug_helper = $debug_helper;
    }

    public function print_before_layout_settings_info(): void
    {
        if($this->debug_helper->is_dev_mode_enabled()) {
            $this->print_disable_new_templates_button();
        }

        if(!$this->templates_system->is_new_templates_info_visible()) {
            return;
        }

        if(!$this->templates_system->is_new_templates_system_enabled_by_user()) {
            return;
        }

        echo View::get_admin('/templates/guide/new-templates-enabled');
    }

    public function print_layout_settings_info(): void
    {
        $info_box = Info_Box::create(__('Template settings have been moved', BPMJ_EDDCM_DOMAIN));

        $info_box->set_size(Info_Box::SIZE_SMALL);

        $link_url = $this->template_groups_page->get_url();

        $info_box->add_paragraph(sprintf(__('We have implemented a new template system in WP Idea, which will allow you to better control the appearance of your platform! From now on, the settings and selection of templates can be found on %sthe Templates page%s', BPMJ_EDDCM_DOMAIN), '<a href="' . $link_url . '">', '</a>'));

        $info_box->print_html();
    }

    public function print_color_settings_info(): void
    {
        $active_group = $this->template_groups_repository->find_active();

        if($active_group === null) {
            return;
        }

        $active_group_name = $active_group->get_name();
        $active_group_link = Link::create($active_group_name, $active_group->get_edit_url());

        $templates_settings_link = Link::create(__('Go to the templates page', BPMJ_EDDCM_DOMAIN), $this->template_groups_page->get_url());
        $templates_settings_link->add_class('wpi-button')->add_class('inline-button')->add_class('layout-settings__template-group-settings-link');

        $p = Paragraph::create(__('Below you can find color settings for the template', BPMJ_EDDCM_DOMAIN) . ' ' . $active_group_link->get_html() . '.');
        $p->append_text($templates_settings_link->get_html());
        $p->add_class('layout-settings__below-you-can-find-color-settings');

        $p->print_html();
    }

    private function print_disable_new_templates_button(): void
    {
        $button = Button::create(__('Disable new templates'), Button::TYPE_WARNING);
        $button
            ->add_data('loading', __('Loading', BPMJ_EDDCM_DOMAIN) . '...')
            ->add_class('disable-new-templates-system-dev');

        $button->print_html();
    }
}