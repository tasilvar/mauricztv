<?php

namespace bpmj\wpidea\admin\tables;

use bpmj\wpidea\admin\tables\styles\Interface_Table_Style;
use bpmj\wpidea\admin\tables\wp\Enhanced_WP_Table;

class Enhanced_Table
{
    private $wp_table;

    private $views = [];

    private function __construct(Enhanced_Table_Items_Collection $collection, string $items_class)
    {
        $this->wp_table = new Enhanced_WP_Table();
        $this->wp_table->set_items_collection($collection);
        $this->wp_table->set_items_class($items_class);
    }

    public static function from_items_collection(Enhanced_Table_Items_Collection $collection, string $items_class): self
    {
        return new self($collection, $items_class);
    }

    public static function make_empty($items_class): self
    {
        return new self(new Enhanced_Table_Items_Collection(), $items_class);
    }

    public function set_items(Enhanced_Table_Items_Collection $collection): self
    {
        $this->wp_table->set_items_collection($collection);

        $this->wp_table->total = $collection->get_total();

        return $this;
    }

    public function add_item(Abstract_Enhanced_Table_Item $item): self
    {
        $this->wp_table->add_item($item);

        return $this;
    }

    public function render(): string
    {
        $this->wp_table->prepare_items();

        ob_start();

        $this->wp_table->display();

        return ob_get_clean();
    }

    public function count_rows(): int
    {
        return $this->wp_table->count_items();
    }

    public function render_views()
    {
        $this->wp_table->add_views($this->views);

        ob_start();

        $this->wp_table->echo_views();

        return ob_get_clean();
    }

    public function get_per_page(): int
    {
        return $this->wp_table->per_page;
    }

    public function set_per_page(int $per_page): self
    {
        $this->wp_table->per_page = $per_page;

        return $this;
    }

    public function get_page()
    {
        return $this->wp_table->get_paged();
    }

    public function add_view(string $query_param_name, string $query_param_value, string $label): self
    {
        $current_filter = $_GET[$query_param_name] ?? '';

        $view_url = sprintf('<a href="%s"%s>%s</a>',
            add_query_arg($query_param_name, $query_param_value),
            $current_filter === $query_param_value ? ' class="current"' : '',
            $label
        );

        $this->views["{$query_param_name}_{$query_param_value}"] = $view_url;

        return $this;
    }

    public function set_style(Interface_Table_Style $style): self
    {
        $this->wp_table->set_style($style);

        return $this;
    }
}