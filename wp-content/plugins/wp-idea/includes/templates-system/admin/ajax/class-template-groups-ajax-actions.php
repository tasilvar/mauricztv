<?php

namespace bpmj\wpidea\templates_system\admin\ajax;

use bpmj\wpidea\templates_system\admin\renderers\Template_Groups_Page_Renderer;

class Template_Groups_Ajax_Actions
{
    private $template_groups_page_renderer;

    public function __construct(Template_Groups_Page_Renderer $template_groups_page_renderer)
    {
        $this->template_groups_page_renderer = $template_groups_page_renderer;
    }

    public function init(): void
    {
        if (!is_admin() || !wp_doing_ajax()) {
            return;
        }

        $this->add_actions();
    }

    private function add_actions(): void
    {
        add_action('wp_ajax_' . Group_Settings_Ajax_Handler::AJAX_GET_SETTINGS_ACTION_NAME, [
            $this->template_groups_page_renderer,
            'render_settings_popup_content'
        ]);

        add_action('wp_ajax_' . Group_Settings_Ajax_Handler::AJAX_ACTION_GET_GOOGLE_FONTS_LIST, [
            Group_Settings_Ajax_Handler::class,
            'ajax_get_google_fonts_list'
        ]);

        add_action('wp_ajax_' . Group_Settings_Ajax_Handler::AJAX_ACTION_SAVE_SETTINGS, [
            Group_Settings_Ajax_Handler::class,
            'ajax_store_settings'
        ]);
    }
}