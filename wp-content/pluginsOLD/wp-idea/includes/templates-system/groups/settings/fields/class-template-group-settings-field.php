<?php

namespace bpmj\wpidea\templates_system\groups\settings\fields;

use bpmj\wpidea\templates_system\groups\settings\Template_Group_Settings;

abstract class Template_Group_Settings_Field
{
    private $name;

    private $label;

    private $default_value;

    private $corresponding_legacy_template_field;

    private $corresponding_legacy_template;

    private $value;

    public function __construct(string $name, string $label, string $default_value = '')
    {
        $this->name = $name;
        $this->label = $label;
        $this->default_value = $default_value;
    }

    public function set_corresponding_legacy_template_field(string $field, string $legacy_base_template): self
    {
        $this->corresponding_legacy_template_field = $field;
        $this->corresponding_legacy_template = $legacy_base_template;

        return $this;
    }

    public function get_name(): string
    {
        return $this->name;
    }

    public function get_label(): string
    {
        return $this->label;
    }

    public function get_default_value(): string
    {
        return $this->default_value;
    }

    public function get_corresponding_legacy_template_field(): string
    {
        return $this->corresponding_legacy_template_field;
    }

    public function set_value(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function get_value(): ?string
    {
        $value = $this->value;

        if (isset($value)) {
            return $value;
        }

        if ($this->get_corresponding_legacy_template_field()) {
            $legacy_template_settings = WPI()->settings->get_layout_template_settings_array($this->corresponding_legacy_template);
            $legacy_value = $legacy_template_settings[$this->get_corresponding_legacy_template_field()] ?? null;

            if (isset($legacy_value)) {
                return $legacy_value;
            }
        }

        return $this->get_default_value();
    }

    abstract public function get_html(): string;

    public function __toString(): string
    {
        return $this->get_value() ?? '';
    }

    public function set_default(string $value): self
    {
        $this->default_value = $value;

        return $this;
    }

    public function get_hint(): ?string
    {
        return $this->get_hints()[$this->get_name()] ?? null;
    }

    private function get_hints(): array
    {
        return [
            Template_Group_Settings::OPTION_OVERRIDE_ALL => __('Enable this mode if you want to convert your site into a "courses only" platform. Your chosen WordPress theme will be completely overridden. Don\'t enable this option if you want to use WP Idea courses as a side feature of your site.', BPMJ_EDDCM_DOMAIN)
        ];
    }
}