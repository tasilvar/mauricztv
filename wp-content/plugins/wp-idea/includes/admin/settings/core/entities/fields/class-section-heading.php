<?php

namespace bpmj\wpidea\admin\settings\core\entities\fields;

class Section_Heading extends Non_Savable_Field
{
    private string $text;

    public function __construct(
        string $text
    )
    {
        parent::__construct('', '');
        $this->text = $text;
    }

    public function render_to_string(): string
    {
        return "<h2 class='section-heading'>{$this->text}</h2>";
    }
}