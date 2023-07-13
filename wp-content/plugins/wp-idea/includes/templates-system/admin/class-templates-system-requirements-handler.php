<?php

namespace bpmj\wpidea\templates_system\admin;

use bpmj\wpidea\admin\Notices;
use bpmj\wpidea\templates_system\templates\repository\Repository as TemplatesRepository;

class Templates_System_Requirements_Handler
{
    public function handle(): void
    {
        add_action('admin_init', [$this, 'check_gutenberg_enabled']);
    }

    private function gutenberg_enabled(): bool
    {
        if(!function_exists('use_block_editor_for_post_type')) return false;

        return use_block_editor_for_post_type(TemplatesRepository::TEMPLATES_POST_TYPE);
    }

    private function display_gutenberg_disabled_error(): void
    {
        WPI()->notices->display_notice(__('The new WP Idea templates system makes extensive use of the Gutenberg editor so it should be enabled on your platform.', BPMJ_EDDCM_DOMAIN), Notices::TYPE_ERROR);
    }

    public function check_gutenberg_enabled(): void
    {
        if(!$this->gutenberg_enabled()){
            $this->display_gutenberg_disabled_error();
        }
    }
}