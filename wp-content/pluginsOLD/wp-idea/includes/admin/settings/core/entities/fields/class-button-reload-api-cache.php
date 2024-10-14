<?php

namespace bpmj\wpidea\admin\settings\core\entities\fields;

use bpmj\wpidea\admin\settings\core\collections\Additional_Fields_Collection;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;

class Button_Reload_Api_Cache extends Abstract_Setting_Field
{
    protected string $url;

    public function __construct(
         string $name,
         ?string $label = null,
         ?string $description = null,
         ?string $tooltip = null,
         ?Additional_Fields_Collection $additional_fields = null,
         $value = null
    )
    {
        parent::__construct($name, $label, $description, $tooltip);
        $this->name = $name;
        $this->label = $label;
        $this->description = $description;
        $this->tooltip = $tooltip;
        $this->additional_fields = $additional_fields;
        $this->value = $value;
    }

    public function set_url(string $url): self
    {
        $this->url = $url;

        return $this;
    }
    
    
    public function render_to_string(): string
    {
        if (!$this->is_visible()) {
            return '';
        }

        return $this->get_field_wrapper_start().'
        <a class="wpi-button wpi-button--clean sync-button" 
        href="'.$this->url.'" 
        >'.$this->value.'</a>
        '. $this->get_field_wrapper_end(); 
    }
}