<?php

namespace bpmj\wpidea\admin\settings\core\entities\fields;

use bpmj\wpidea\admin\settings\core\collections\Additional_Fields_Collection;

class Select_Setting_Field extends Abstract_Setting_Field
{
    private ?array $options = null;
    private array $disabled_options = [];
    private $options_callback = null;

    private ?string $show_configuration_button_value = null;

    public function __construct(
        string $name,
        ?string $label = null,
        ?string $description = null,
        ?string $tooltip = null,
        ?Additional_Fields_Collection $additional_settings_fields = null,
        ?array $options = [],
        $value = null,
        ?callable $options_callback = null
    ) {
        if( null != $options_callback ) {
            $this->options_callback = $options_callback;
        }
        else {
            $this->options = $options;
        }
        parent::__construct($name, $label, $description, $tooltip, $additional_settings_fields, $value);
    }

	public function disable_option_by_value(string $option_value): void
	{
		$this->disabled_options[] = $option_value;
	}

    public function set_show_configuration_button_value(string $value): self
    {
        $this->show_configuration_button_value = $value;
        return $this;
    }

    public function get_show_configuration_button_value(): ?string
    {
        return $this->show_configuration_button_value;
    }

    public function render_to_string(): string
    {
        if (!$this->is_visible()) {
            return '';
        }

        $options = $this->get_options_string();

        $show_config_value_attribute = $this->get_show_configuration_button_value() ?
            "data-config-value='{$this->get_show_configuration_button_value()}'" : "";

        return $this->get_field_wrapper_start()
            . "<select
                    name='" . $this->get_name() . "'
                    id='" . $this->get_name() . "'
                    " . $show_config_value_attribute . "
                    data-initial-value='" . $this->get_value() . "'
                    class='single-field wpi-select-field'
                    " . $this->get_disabled_html_attr() . "
            >
            " . $options . "</select>"
            . $this->get_field_wrapper_end();
    }

    private function get_options_string(): string
    {
        $html = '';
        if(null == $this->options && null != $this->options_callback) {
            $this->options = call_user_func($this->options_callback);
        }

        foreach ($this->options as $value => $label) {
            $selected = '';
            if ((string)$value === $this->get_value()) {
                $selected = 'selected="selected"';
            }
			$disabled = in_array($value, $this->disabled_options) ? 'disabled' : '';
            $html .= "<option {$selected} value='{$value}' {$disabled}>{$label}</option>";
        }
        return $html;
    }

}
