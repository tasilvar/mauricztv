<?php

namespace bpmj\wpidea\admin\support;

use bpmj\wpidea\admin\support\diagnostics\Diagnostics;
use bpmj\wpidea\admin\support\diagnostics\items\Abstract_Diagnostics_Item;
use bpmj\wpidea\translator\Interface_Translator;


class Rules
{

    private string $title;
    private string $info;
    private array $rules = [];
    private Interface_Translator $translator;
    private Diagnostics $diagnostics;

    public function __construct(Interface_Translator $translator, Diagnostics $diagnostics = null)
    {
        $this->translator = $translator;

        if ($diagnostics) {
            $this->diagnostics = $diagnostics;
        }

        $this->set_info();
        $this->set_rules();
    }

    private function set_info(): void
    {
        $this->title = __('Support rules', BPMJ_EDDCM_DOMAIN);

        $this->info = $this->translator->translate('support.rules.before_contacting');
    }

    private function set_rules(): void
    {
        $this->check_diagnostics();

        $this->add_rule(array(
            'title' => __('Look for answers <strong>in the documentation</strong>', BPMJ_EDDCM_DOMAIN),
            'subtitle' => sprintf(
                __('Solutions to many common problems can be found in our <a href="%s" target="_BLANK">FAQ section</a>', BPMJ_EDDCM_DOMAIN),
                Links::FAQ
            ),
            'icon' => 'media-document'
        ));

        $this->add_rule(array(
            'title' => __('Familiarize yourself with our <strong>Golden Support Rules</strong>', BPMJ_EDDCM_DOMAIN),
            'subtitle' => sprintf(__('Be sure to take a look <a href="%s" target="_BLANK">here</a>', BPMJ_EDDCM_DOMAIN), Links::GOLDEN_RULES),
            'icon' => 'heart'
        ));
    }

    private function check_diagnostics(): void
    {
        if (empty($this->diagnostics)) {
            return;
        }

        foreach ($this->diagnostics->get_items() as $item) {
            if ($item->check_status() === Abstract_Diagnostics_Item::STATUS_OK) {
                continue;
            }

            if (empty($item->get_solve_hint())) {
                continue;
            }

            $this->add_rule(array(
                'title' => $item->get_solve_hint(),
                'subtitle' => $item->get_solve_instructions(),
                'icon' => $item->get_solve_icon() ?: 'update'
            ));
        }
    }

    public function add_rule(array $args): void
    {
        $default_args = array(
            'title' => '',
            'subtitle' => '',
            'icon' => 'lightbulb' //dashicon, @see https://developer.wordpress.org/resource/dashicons
        );

        $rule = (object)array_merge($default_args, $args);

        $this->rules[] = $rule;
    }

    public function get_rules(): array
    {
        return $this->rules;
    }

    public function get_title(): string
    {
        return $this->title;
    }

    public function get_info(): string
    {
        return $this->info;
    }
}
