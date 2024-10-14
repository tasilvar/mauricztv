<?php
namespace bpmj\wpidea\admin\settings\core\entities\fields;

use bpmj\wpidea\helpers\Translator_Static_Helper;

class Button_Setting_Field extends Abstract_Setting_Field
{
    private string $url = '';

    public function render_to_string(): string
    {
        if(!$this->is_visible()){
            return '';
        }
        
        return $this->get_field_wrapper_start()
            . $this->get_link_item()
            . $this->get_field_wrapper_end();
    }

    public function set_url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    private function get_link_item(): string
    {
        return "<a href='{$this->url}' target='_blank' class='wpi-button wpi-button--clean configuration-button'>" . Translator_Static_Helper::translate('settings.popup.button.configure') . '</a>';
    }
}
