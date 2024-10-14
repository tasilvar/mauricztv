<?php

namespace bpmj\wpidea\admin\helpers\html;

class Span implements Interface_Renderable
{
    private $text;

    private $classes;

    public function __construct(string $text, string $classes)
    {
        $this->text = $text;
        $this->classes = $classes;
    }


    public static function create(string $text, string $classes = ''): self
    {
        return new self($text, $classes);
    }

    public function get_html(): string
    {
        return "<span class='{$this->classes}'>{$this->text}</span>";
    }
}