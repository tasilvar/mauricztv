<?php

namespace bpmj\wpidea\admin\tables\wp;

use bpmj\wpidea\admin\tables\{Abstract_Enhanced_Table_Item,
    Enhanced_Table_Items_Collection,
    Label,
    styles\Default_Style,
    styles\Interface_Table_Style};
use bpmj\wpidea\admin\helpers\html\Interface_Renderable;
use Exception;
use WP_List_Table;

class Enhanced_WP_Table extends WP_List_Table
{
    public $per_page = 30;

    public $count = 0;

    public $total = 0;

    /**
     * @var Enhanced_Table_Items_Collection
     */
    private $items_collection;

    private const SCREEN_PLACEHOLDER = 'screen_placeholder';

    private $items_class;

    private $views;

    private $style;

    public function __construct() {
        parent::__construct([
            'ajax'      => false,
            'screen'    => self::SCREEN_PLACEHOLDER // otherwise WP throws an error when the table is rendered
        ]);
    }

    public function prepare_items() {
        $columns  = $this->get_columns();
        $hidden   = [];
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = [ $columns, $hidden, $sortable ];

        $this->items = $this->get_data();

        $this->set_pagination_args( [
            'total_items' => $this->total,
            'per_page' => $this->per_page,
            'total_pages' => ceil( $this->total / $this->per_page )
        ] );
    }

    public function count_items(): int
    {
        return $this->total;
    }

    public function set_items_class(string $items_class): self
    {
        $this->items_class = $items_class;

        return $this;
    }

    public function add_views(array $views): self
    {
        $this->views = $views;

        return $this;
    }

    public function set_style(Interface_Table_Style $style): self
    {
        $this->style = $style;

        return $this;
    }

    protected function column_default($item, $column_name)
    {
        return $item[ $column_name ];
    }

    protected function get_data(): array
    {
        $data = [];
        $collection = $this->get_items_collection();

        foreach ($collection as $index => $item) {
            $row = [];
            foreach ($item->get_values() as $item_prop_index => $value) {
                $label = $item->get_labels()[$item_prop_index];
                $label_text = $label instanceof Label ? $label->get_text() : $label;

                if($value instanceof Interface_Renderable) {
                    $value = $value->get_html();
                }

                $row[$this->get_slug_from_label($label_text)] = $value;
            }
            $data[] = $row;
        }

        return $data;
    }

    public function get_columns() {
        $columns = [];
        $table_item_labels = $this->items_class::get_labels();

        foreach ($table_item_labels as $item_prop_index => $label) {
            $text = $label instanceof Label ? $label->get_text() : $label;
            $hide_column_text = $label instanceof Label && $label->is_of_type(Label::TYPE_HIDDEN);

            $columns[$this->get_slug_from_label($text)] = $hide_column_text ? '' : $text;
        }

        return $columns;
    }

    public function get_table_classes()
    {
        $style = $this->style ?? new Default_Style();

        return $style->get_classes();
    }

    public function get_views() {
        return $this->views;
    }

    public function echo_views() {
        $this->views();
    }

    public function get_paged() {
        return isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
    }

    private function get_slug_from_label(string $label): string
    {
        return sanitize_title($label);
    }

    private function set_total(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    private function get_items_collection(): Enhanced_Table_Items_Collection
    {
        return $this->items_collection;
    }

    public function set_items_collection(Enhanced_Table_Items_Collection $collection): self
    {
        $this->items_collection = $collection;

        return $this;
    }

    public function add_item(Abstract_Enhanced_Table_Item $item): self
    {
        if(!isset($this->items_collection)) {
            throw new Exception("Collection not initialized, use set_items_collection method first!");
        }

        $this->items_collection->append($item);

        $this->set_total($this->count_items() + 1);

        return $this;
    }
}

