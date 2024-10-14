<?php

namespace bpmj\wpidea\admin\helpers\html;

use bpmj\wpidea\View;

class Paragraph extends Abstract_Renderable_Element
{
    private $text;

    private function __construct(string $text)
    {
        $this->text = $text;
    }

    public static function create(string $text): self
    {
        return new self($text);
    }

    public function get_html(): string
    {
        return View::get_admin('/helpers/html/paragraph', [
            'model' => $this,
        ]);
    }

    public function get_text(): string
    {
        return $this->text;
    }

    public function append_text(string $text): self
    {
        $this->text .= $text;

        return $this;
    }
}
