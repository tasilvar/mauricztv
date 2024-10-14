<?php

namespace bpmj\wpidea\admin\helpers\html;

use bpmj\wpidea\View;

class Link extends Abstract_Renderable_Element
{
    private $text;

    private $url;

    private $title;

    private $is_disabled = false;

    private string $target = '_self';

    private function __construct(string $text, string $url)
    {
        $this->text = $text;
        $this->url = $url;
    }

    public static function create(string $text, string $url): self
    {
        return new self($text, $url);
    }

    public function get_html(): string
    {
        return View::get_admin('/helpers/html/link', [
            'model' => $this,
        ]);
    }

    public function add_title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function set_as_disabled(): self
    {
        $this->is_disabled = true;

        return $this;
    }

    public function get_href(): string
    {
        return $this->is_disabled ? '' : "href='{$this->url}'";
    }

    public function get_title(): string
    {
        return $this->title !== null ? 'title="' . $this->title . '"' : '';
    }

    public function get_classes(): string
    {
        $classes = parent::get_classes();
        $classes .= $this->is_disabled ? ' disabled' : '';

        return $classes;
    }

    public function get_text(): string
    {
        return $this->text;
    }

    public function open_in_new_tab(): self
    {
        $this->target = '_blank';

        return $this;
    }

    public function get_target(): string
    {
        return $this->target;
    }
}
