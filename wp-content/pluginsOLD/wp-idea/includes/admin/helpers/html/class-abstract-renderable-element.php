<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\admin\helpers\html;

abstract class Abstract_Renderable_Element implements Interface_Renderable
{
    private $classes;

    private $data = '';

    /**
     * @var null|Dashicon
     */
    private $dashicon;

    public function add_class(string $class_name): self
    {
        if (!empty($this->classes)) {
            $this->classes .= ' ';
        }
        $this->classes .= $class_name;

        return $this;
    }

    public function get_classes(): string
    {
        return $this->classes ?? '';
    }

    public function set_classes(string $classes): self
    {
        $this->classes = $classes;

        return $this;
    }

    public function set_dashicon(Dashicon $dashicon): self
    {
        $this->dashicon = $dashicon;

        return $this;
    }

    protected function get_dashicon(): ?Dashicon
    {
        return $this->dashicon;
    }

    public function get_dashicon_html(): string
    {
        return $this->has_dashicon() ? $this->get_dashicon()->get_html() : '';
    }

    public function has_dashicon(): bool
    {
        return $this->get_dashicon() !== null;
    }

    public function add_data(string $name, ?string $value = null): self
    {
        if (!empty($this->data)) {
            $this->data .= ' ';
        }
        $this->data .= "data-{$name}";

        if ($value !== null) {
            $this->data .= "='{$value}'";
        }

        return $this;
    }

    public function get_data(): string
    {
        return $this->data;
    }

    public function print_html(): void
    {
        echo $this->get_html();
    }
}