<?php
namespace bpmj\wpidea\admin\settings\core\entities\fields;

use bpmj\wpidea\admin\helpers\html\Button;
use bpmj\wpidea\admin\helpers\html\Popup;
use bpmj\wpidea\View;

class New_Version_Certificates_Popup_Field extends Abstract_Setting_Field
{

    private string $button_label;
    private string $message;

    public function __construct(
        string $name,
        string $button_label,
        string $message

    )
    {
        parent::__construct($name, '');
        $this->button_label = $button_label;
        $this->message = $message;
    }

    public function render_to_string(): string
    {
        if(!$this->is_visible()){
            return '';
        }
        
        return $this->get_field_wrapper_start()
            . $this->get_message()
            . $this->get_popup_html()
            . $this->get_field_wrapper_end();
    }

    private function get_message(): string
    {
        return "<div class='info-message'><p>{$this->message}</p></div>";
    }

    private function get_popup_html(): string
    {
        $popup = Popup::create(
            $this->get_name(),
            View::get_admin('/popup/new-certificate')
        );

        return Button::create($this->button_label, Button::TYPE_CLEAN, 'single-field-save-button')
            ->open_popup_on_click($popup)
            ->get_html();
    }
}
