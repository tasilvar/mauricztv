<?php

namespace bpmj\wpidea\templates_system;

use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\templates_system\templates\Template;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;

class Blocks_Frontend_Handler
{
    private Default_Templates $default_templates;
    private Interface_Translator $translator;
    private Interface_View_Provider $view_provider;
    private Interface_Settings $settings;

    public function __construct(
        Default_Templates $default_templates,
        Interface_Translator $translator,
        Interface_View_Provider $view_provider,
        Interface_Settings $settings
    )
    {
        $this->default_templates = $default_templates;
        $this->translator = $translator;
        $this->view_provider = $view_provider;
        $this->settings = $settings;
    }

    public function handle(): void
    {
        $this->init_templates_blocks_frontend();
    }

    private function init_templates_blocks_frontend(): void
    {
        foreach ($this->default_templates->get_all() as $template_group => $templates) {
            foreach ($templates as $template_class) {
                /** @var Template $template */
                $template = new $template_class();

                $template->set_translator($this->translator);
                $template->set_view_provider($this->view_provider);
                $template->set_settings($this->settings);

                $template->init_blocks_frontend();
            }
        }
    }
}