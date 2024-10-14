<?php

namespace bpmj\wpidea\templates_system;

use bpmj\wpidea\options\Interface_Options;
use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\templates_system\admin\{Template_Groups_Manager,
	Templates_Editor,
	Templates_Integrity_Checker,
	Templates_List_Page_Redirector,
	Templates_System_Requirements_Handler};
use bpmj\wpidea\templates_system\templates\{repository\Repository as TemplatesRepository,
	Template_Actions_Handler,
	Template_Metaboxes_Handler};

class Templates_System
{
    private const OPTION_EXPERIMENTAL_TEMPLATES_ENABLED = 'new_templates_system_enabled';

    private const OPTION_TEMPLATES_SYSTEM_STATUS = 'new_templates_system_status';
    private const OPTION_VALUE_TEMPLATES_SYSTEM_DISABLED = 'disabled';
    private const OPTION_VALUE_TEMPLATES_SYSTEM_ENABLED_BY_USER = 'enabled_by_user';
    private const OPTION_VALUE_TEMPLATES_SYSTEM_DISABLED_BY_USER = 'disabled_by_user';

    private const OPTION_NEW_TEMPLATES_ENABLED_INFO_STATUS = 'new_templates_enabled_info_status';
    private const OPTION_VALUE_NEW_TEMPLATES_ENABLED_INFO_HIDDEN = 'info_hidden';
    private const OPTION_VALUE_NEW_TEMPLATES_ENABLED_INFO_VISIBLE = 'info_visible';

    protected $overridden_blocks = [];

    /**
     * @var Templates_Editor
     */
    public $editor;

    /**
     * @var Template_Groups_Manager
     */
    private $groups_manager;

    /**
     * @var Templates_System_Requirements_Handler
     */
    private $requirements_handler;

    /**
     * @var TemplatesRepository
     */
    private $templates_repository;

    /**
     * @var Template_Actions_Handler
     */
    private $template_actions_handler;

    /**
     * @var Template_Metaboxes_Handler
     */
    private $template_metaboxes_handler;

    /**
     * @var Blocks_Frontend_Handler
     */
    private $blocks_frontend_handler;

    /**
     * @var Templates_List_Page_Redirector
     */
    private $templates_list_page_redirector;

    /**
     * @var Interface_Options
     */
    private $options;

    /**
     * @var LMS_Settings
     */
    private $lms_settings;

    /**
     * @var Templates_Integrity_Checker
     */
    private $integrity_checker;

    public function __construct(
        Templates_List_Page_Redirector $templates_list_page_redirector,
        Blocks_Frontend_Handler $blocks_frontend_handler,
        Template_Groups_Manager $groups_manager,
        Templates_System_Requirements_Handler $requirements_handler,
        TemplatesRepository $templates_repository,
        Template_Actions_Handler $template_actions_handler,
        Template_Metaboxes_Handler $template_metaboxes_handler,
        Templates_Editor $editor,
        Interface_Options $options,
        LMS_Settings $lms_settings,
        Templates_Integrity_Checker $integrity_checker
    ) {
        $this->blocks_frontend_handler = $blocks_frontend_handler;
        $this->groups_manager = $groups_manager;
        $this->requirements_handler = $requirements_handler;
        $this->templates_repository = $templates_repository;
        $this->editor = $editor;
        $this->template_actions_handler = $template_actions_handler;
        $this->template_metaboxes_handler = $template_metaboxes_handler;
        $this->templates_list_page_redirector = $templates_list_page_redirector;
        $this->options = $options;
        $this->lms_settings = $lms_settings;
        $this->integrity_checker = $integrity_checker;
    }

    public function init(): void
    {
        $this->setup_templates_repository();

        $this->init_blocks_frontend();

        if(is_admin() && wp_doing_ajax()) {
            $this->init_template_groups_ajax_actions();
        }

        if(is_admin() && !wp_doing_ajax()) {
            $this->handle_template_metaboxes();

            $this->handle_template_admin_actions();

            $this->handle_requirements_checking();

            $this->init_templates_list_page_redirector();

            $this->init_editor();
        }
    }

    public function override_block_content($block_class, $new_content): void
    {
        $this->overridden_blocks[$block_class] = $new_content;
    }

    public function get_overridden_block_content($block_class)
    {
        if(empty($this->overridden_blocks[$block_class])) return null;

        return $this->overridden_blocks[$block_class];
    }

    protected function setup_templates_repository(): void
    {
        $this->templates_repository->setup();
    }

    protected function init_editor(): void
    {
        if($this->editor) {
            $this->editor->init();
        }
    }

    protected function handle_template_metaboxes(): void
    {
        $this->template_metaboxes_handler->handle();
    }

    protected function handle_requirements_checking(): void
    {
        $this->requirements_handler->handle();
    }

    private function handle_template_admin_actions(): void
    {
        $this->template_actions_handler->handle();
    }

    private function init_template_groups_ajax_actions(): void
    {
        $this->groups_manager->init();
    }

    private function init_blocks_frontend(): void
    {
        $this->blocks_frontend_handler->handle();
    }

    private function init_templates_list_page_redirector(): void
    {
        $this->templates_list_page_redirector->init();
    }

    public function is_new_templates_system_enabled(): bool
    {
        $option_value = $this->options->get(self::OPTION_TEMPLATES_SYSTEM_STATUS);

        return !in_array($option_value, [
            self::OPTION_VALUE_TEMPLATES_SYSTEM_DISABLED,
            self::OPTION_VALUE_TEMPLATES_SYSTEM_DISABLED_BY_USER
        ], true);
    }

    public function disable_new_templates(): void
    {
        $this->options->set(self::OPTION_TEMPLATES_SYSTEM_STATUS, self::OPTION_VALUE_TEMPLATES_SYSTEM_DISABLED);
    }

    public function set_new_templates_as_enabled_by_user(): void
    {
        $this->options->set(self::OPTION_TEMPLATES_SYSTEM_STATUS, self::OPTION_VALUE_TEMPLATES_SYSTEM_ENABLED_BY_USER);

        $this->integrity_checker->ensure_integrity();
    }

    public function set_new_templates_as_disabled_by_user(): void
    {
        $this->options->set(self::OPTION_TEMPLATES_SYSTEM_STATUS, self::OPTION_VALUE_TEMPLATES_SYSTEM_DISABLED_BY_USER);
    }

    public function is_new_templates_info_visible(): bool
    {
        return $this->options->get(self::OPTION_NEW_TEMPLATES_ENABLED_INFO_STATUS) === self::OPTION_VALUE_NEW_TEMPLATES_ENABLED_INFO_VISIBLE;
    }

    public function hide_new_templates_info(): void
    {
        $this->options->set(self::OPTION_NEW_TEMPLATES_ENABLED_INFO_STATUS, self::OPTION_VALUE_NEW_TEMPLATES_ENABLED_INFO_HIDDEN);
    }

    public function show_new_templates_info(): void
    {
        $this->options->set(self::OPTION_NEW_TEMPLATES_ENABLED_INFO_STATUS, self::OPTION_VALUE_NEW_TEMPLATES_ENABLED_INFO_VISIBLE);
    }

    public function is_new_templates_system_disabled_by_user(): bool
    {
        return $this->templates_system_status_equals(self::OPTION_VALUE_TEMPLATES_SYSTEM_DISABLED_BY_USER);
    }

    public function is_new_templates_system_enabled_by_user()
    {
        return $this->templates_system_status_equals(self::OPTION_VALUE_TEMPLATES_SYSTEM_ENABLED_BY_USER);
    }

    private function templates_system_status_equals(string $status): bool
    {
        return $this->options->get(self::OPTION_TEMPLATES_SYSTEM_STATUS) === $status;
    }

    public function is_legacy_experimental_templates_option_turned_on(): bool
    {
        if($this->lms_settings->get('template') !== 'scarlet') {
            return false;
        }

        return $this->lms_settings->get(self::OPTION_EXPERIMENTAL_TEMPLATES_ENABLED) ?? false;
    }
}