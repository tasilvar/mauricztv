<?php

namespace bpmj\wpidea\admin\tables\simple;

use bpmj\wpidea\View;

class Simple_Table
{
    private $headers = [];

    private $items = [];

    private $class;

    public function __construct(string $class = '')
    {
        $this->class = $class;
    }

    public static function create(string $class = ''): self
    {
        return new self($class);
    }

    public function add_header(string $name, string $slug = null): self
    {
        $header_slug = $slug ?? sanitize_title($name);

        $this->headers[$header_slug] = $name;

        return $this;
    }

    public function set_items(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function print(): void
    {
        echo $this->get_html();
    }

    public function get_html(): string
    {
        return View::get_admin('/utils/tables/simple/table', [
            'headers' => $this->headers,
            'items' => $this->items,
            'class' => $this->class
        ]);
    }
}