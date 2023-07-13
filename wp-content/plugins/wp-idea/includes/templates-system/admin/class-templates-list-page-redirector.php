<?php

namespace bpmj\wpidea\templates_system\admin;

use WP_Screen;

class Templates_List_Page_Redirector
{
    private $template_groups_page;

    private const SCREEN_ID = 'edit-wpi_page_templates';

    public function __construct(Template_Groups_Page $template_groups_page)
    {
        $this->template_groups_page = $template_groups_page;
    }

    public function init(): void
    {
        add_action('current_screen', [$this, 'redirect_if_on_default_wordpress_posts_list_screen']);
    }

    public function redirect_if_on_default_wordpress_posts_list_screen(WP_Screen $screen): void
    {
        if(self::SCREEN_ID !== $screen->id) {
            return;
        }

        $this->redirect_to_the_templates_page();
    }

    private function redirect_to_the_templates_page(): void
    {
        wp_safe_redirect($this->template_groups_page->get_url());
        exit;
    }
}