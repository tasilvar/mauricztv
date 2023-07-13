<?php

namespace bpmj\wpidea\templates_system\templates;


use bpmj\wpidea\Metabox;
use bpmj\wpidea\templates_system\templates\repository\Repository as TemplatesRepository;

class Template_Metaboxes_Handler
{
    public const META_DISABLE_WPI_CSS = 'bpmj_disable_wpi_css';
    public const METABOX_TEMPLATE_OPTIONS = 'bpmj_template_options';

    public function handle(): void
    {
        $this->add_disable_template_styles_metabox();
    }

    private function add_disable_template_styles_metabox(): void
    {
        $metabox = new Metabox(self::METABOX_TEMPLATE_OPTIONS, __('Template options', BPMJ_EDDCM_DOMAIN), TemplatesRepository::TEMPLATES_POST_TYPE);
        $metabox
            ->add_select(
                self::META_DISABLE_WPI_CSS,
                __('You can choose to not use WP Idea CSS in this template (eg. if you want to apply your own styling)', BPMJ_EDDCM_DOMAIN),
                [
                    [
                        'label' => __('Do not disable', BPMJ_EDDCM_DOMAIN),
                        'value' => 0
                    ],
                    [
                        'label' => __('Disable WP Idea styles', BPMJ_EDDCM_DOMAIN),
                        'value' => 1
                    ],
                ],
                false
            )
            ->render();
    }
}