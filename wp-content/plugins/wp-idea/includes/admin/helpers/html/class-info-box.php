<?php

namespace bpmj\wpidea\admin\helpers\html;

use bpmj\wpidea\View;

class Info_Box extends Abstract_Renderable_Element
{
    public const SIZE_DEFAULT = 'default';
    public const SIZE_SMALL = 'small';

    private $title;

    private $image_url;

    private $paragraphs = [];

    private $buttons = [];

    private $size = self::SIZE_DEFAULT;

    private function __construct(string $title, ?string $image_url)
    {
        $this->title = $title;
        $this->image_url = $image_url;
    }

    public static function create(string $title, ?string $image_url = null): self
    {
        return new self($title, $image_url);
    }

    public function add_paragraph(string $content): self
    {
        $this->paragraphs[] = $content;

        return $this;
    }

    public function add_button(Button $button): self
    {
        $this->buttons[] = $button;

        return $this;
    }

    public function get_html(): string
    {
        return View::get_admin('/helpers/html/info-box', [
            'title' => $this->title,
            'image_url' => $this->image_url,
            'paragraphs' => $this->paragraphs,
            'buttons' => $this->buttons,
            'size' => $this->size,
            'classes' => $this->get_classes(),
            'data' => $this->get_data(),
        ]);
    }

    public function set_size(string $size): self
    {
        $this->size = $size;

        return $this;
    }
}
