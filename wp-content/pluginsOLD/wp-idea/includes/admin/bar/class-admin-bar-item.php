<?php

namespace bpmj\wpidea\admin\bar;

class Admin_Bar_Item
{
    protected string $id;

    protected string $title;

    protected ?string $href;

    protected ?string $parent;

    protected bool $group;

    protected array $meta;

    public function __construct(string $id, string $title, ?string $href = null, ?string $parent = null, bool $group = false, array $meta = []) {
        $this->id = $id;
        $this->title = $title;
        $this->href = $href;
        $this->parent = $parent;
        $this->group = $group;
        $this->meta = $meta;
    }

    public function get_id(): string
    {
        return $this->id;
    }

    public function get_title(): string
    {
        return $this->get_item_html($this->title);
    }

    public function get_href(): ?string
    {
        return $this->href;
    }

    public function set_parent(string $parent): void
    {
        $this->parent = $parent;
    }

    public function get_parent(): ?string
    {
        return $this->parent;
    }

    public function get_group(): bool
    {
        return $this->group;
    }

    public function get_meta(): array
    {
        return $this->meta;
    }

    protected function get_item_html(string $title): string
    {
        return '<span class="ab-icon"></span><span class="ab-label">' . $title . '</span>';
    }
}