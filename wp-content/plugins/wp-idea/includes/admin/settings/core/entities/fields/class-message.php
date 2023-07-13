<?php

namespace bpmj\wpidea\admin\settings\core\entities\fields;

use bpmj\wpidea\admin\settings\web\Settings_Info_Box;

class Message extends Non_Savable_Field
{
    private string $message;
    private string $type;

    public function __construct(
        string $text,
        string $type = Settings_Info_Box::INFO_BOX_TYPE_DEFAULT
    )
    {
        parent::__construct('', '');

        $this->message = $text;
        $this->type = $type;
    }

    public function render_to_string(): string
    {
        return $this->render_info_box_to_string($this->message, $this->type);
    }
}