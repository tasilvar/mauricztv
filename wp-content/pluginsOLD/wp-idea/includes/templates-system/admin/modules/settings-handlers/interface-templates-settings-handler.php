<?php

namespace bpmj\wpidea\templates_system\admin\modules\settings_handlers;

interface Interface_Templates_Settings_Handler
{
    public function add_menu_pages(): void;

    public function should_color_settings_be_displayed(): bool;

    public function should_template_option_be_visible_on_the_settings_page(string $option_name): bool;

    public function should_download_section_position_field_be_displayed(): bool;

    public function get_current_template_options(): ?array;

    public function legacy_templates_settings_in_use(): bool;

    public function get_override_all_option_value(): ?string;

    public function get_custom_css_field_value(): ?string;
}