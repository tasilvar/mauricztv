<?php

namespace bpmj\wpidea\admin\settings\core\entities\fields;

use bpmj\wpidea\View;

class Certificates_Templates_Table_Field extends Abstract_Setting_Field
{
    private array $certificates;

    public function __construct(
        string $name,
        string $label,
        array $certificates

    )
    {
        parent::__construct($name, $label);
        $this->certificates = $certificates;
    }

    public function render_to_string(): string
    {
        if (!$this->is_visible()) {
            return '';
        }
        return $this->get_field_wrapper_start('max-width-none certificates-field-wrapper').
                    $this->get_certificates()
              .$this->get_field_wrapper_end();
    }

    private function get_certificates(): string
    {
        return View::get_admin('/certificate-template/list-certificate-template', [
            'certificates' => $this->certificates
        ]);
    }
}