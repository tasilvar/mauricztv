<?php

namespace bpmj\wpidea\admin\pages\product_editor\core\fields;

use bpmj\wpidea\admin\settings\core\collections\Additional_Fields_Collection;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;

class Code_Gtu_Field extends Abstract_Setting_Field
{
    private array $options = [];
    private ?array $variable_prices = null;

    public function __construct(
        string $name,
        ?string $label = null,
        ?string $description = null,
        ?string $tooltip = null,
        ?Additional_Fields_Collection $additional_settings_fields = null,
        ?array $options = [],
        $value = null
    ) {
        $this->options = $options;
        parent::__construct($name, $label, $description, $tooltip, $additional_settings_fields, $value);
    }

    public function set_variable_prices(?array $variable_prices): self
    {
        $this->variable_prices = $variable_prices;
        return $this;
    }

    public function render_to_string(): string
    {
        if (!$this->is_visible()) {
            return '';
        }

        if(!$this->variable_prices){
            return $this->field_gtu();
        }

        return $this->field_gtu_for_variable_prices();
    }

    private function field_gtu(): string
    {
        $options = $this->get_options_string($this->get_value());

        return $this->get_field_wrapper_start()
            . "<select
                    name='" . $this->get_name() . "'
                    id='" . $this->get_name() . "'
                    data-initial-value='" . $this->get_value() . "'
                    class='single-field wpi-select-field'
                    " . $this->get_disabled_html_attr() . "
            >
            " . $options . "</select>"
            . $this->get_field_wrapper_end();
    }

    private function field_gtu_for_variable_prices(): string
    {
        ob_start();

        foreach ( $this->variable_prices as $price_id => $variable_price ) {

            ?>
            <div class="single-field-wrapper">
                <label for="<?= $this->get_name().$price_id ?>" class="field-label">
                    <?= $variable_price[ 'name' ] ?>
                </label>
                <div class="single-input-wrapper ">
                    <?php
                     echo'<select name="'.$this->get_name().'" id="'.$this->get_name().$price_id.'" data-initial-value="none" class="single-field wpi-select-field">
                           '.$this->get_options_string($variable_price['gtu'] ?? '', (int)$price_id).'
                         </select>';
                    ?>
                </div>
            </div>
            <?php
        }

        return ob_get_clean();
    }

    private function get_options_string(?string $gtu, ?int $id = null): string
    {
        $html = '';
        foreach ($this->options as $value => $label) {
            $selected = '';
            if ((string)$value === $gtu) {
                $selected = 'selected="selected"';
            }

            $value = $id ? $id.'-'.$value : $value;

            $html .= "<option {$selected} value='{$value}'>{$label}</option>";
        }
        return $html;
    }

}
