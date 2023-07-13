<?php

namespace bpmj\wpidea\admin\helpers\html;

class Dashicon extends Abstract_Renderable_Element
{
    private $icon;

    private $classes;

    public function __construct(string $icon, string $classes)
    {
        $this->icon = $icon;
        $this->classes = $classes;
    }


    public static function create(string $icon, string $classes = ''): self
    {
        return new self($icon, $classes);
    }

    public function get_html(): string
    {
        return "<span class='dashicons dashicons-{$this->icon} {$this->classes}'></span>";
    }
}
