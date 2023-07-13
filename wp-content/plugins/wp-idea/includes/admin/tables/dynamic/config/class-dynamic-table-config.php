<?php

namespace bpmj\wpidea\admin\tables\dynamic\config;

use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\admin\tables\dynamic\Table_Filters;
use bpmj\wpidea\user\User_Capability_Collection;
use bpmj\wpidea\user\User_Role_Collection;

class Dynamic_Table_Config
{
    private string $table_id;

    private Interface_Dynamic_Table_Data_Provider $data_provider;

    private string $data_source_url;

    private array $columns;

    private int $default_items_per_page = 25;

    private ?int $user_items_per_page = null;

    private array $items_per_page_presets = [5, 10, 25, 50, 100];

    private array $default_hidden_columns = [];

    private ?array $user_hidden_columns = null;

    private bool $save_table_config = true;

    private ?string $save_table_config_url;

    private ?string $export_table_data_url = null;

    private array $top_panel_buttons = [];

    private array $i18n = [];

    private array $row_actions = [];

    private bool $allow_columns_hiding = true;

    private bool $show_refresh_button = false;

    private bool $disable_multi_sort = false;

    private array $bulk_actions = [];

    private Table_Filters $filters;

    private bool $disable_export = false;

    private User_Capability_Collection $required_caps;

    private User_Role_Collection $required_roles;
    private bool $disable_pagination = false;

    /**
     * @see /docs/code/admin/tables/dynamic/dynamic-table.md
     */
    public function __construct(
        string                                $table_id,
        Interface_Dynamic_Table_Data_Provider $data_provider,
        array                                 $columns
    )
    {
        $this->table_id = $table_id;
        $this->data_provider = $data_provider;
        $this->columns = $columns;
        $this->required_caps = new User_Capability_Collection();
        $this->required_roles = new User_Role_Collection();
    }

    public function get_table_id(): string
    {
        return $this->table_id;
    }

    public function show_refresh_button(): self
    {
        $this->show_refresh_button = true;

        return $this;
    }

    public function disable_multi_sort(): self
    {
        $this->disable_multi_sort = true;

        return $this;
    }

    public function set_row_actions(array $actions): self
    {
        $this->row_actions = $actions;

        return $this;
    }

    public function set_top_panel_buttons(array $top_panel_buttons): self
    {
        $this->top_panel_buttons = $top_panel_buttons;

        return $this;
    }

    public function set_bulk_actions(array $actions): self
    {
        $this->bulk_actions = $actions;

        return $this;
    }

    public function disable_saving_table_settings_on_change(): self
    {
        $this->save_table_config = false;

        return $this;
    }

    public function disable_allow_columns_hiding(): self
    {
        $this->allow_columns_hiding = false;

        return $this;
    }

    public function disable_pagination(): self
    {
        $this->disable_pagination = true;

        return $this;
    }

    public function set_default_hidden_columns(array $hidden_columns): self
    {
        $this->default_hidden_columns = $hidden_columns;

        return $this;
    }

    public function set_default_items_per_page(int $items_per_page): self
    {
        $this->default_items_per_page = $items_per_page;

        return $this;
    }

    public function set_items_per_page_presets(array $items_per_page_presets): self
    {
        $this->items_per_page_presets = $items_per_page_presets;

        return $this;
    }

    public function set_i18n_strings(array $translations): void
    {
        $this->i18n = $translations;
    }

    public function set_save_table_settings_url(string $url): void
    {
        $this->save_table_config_url = $url;
    }

    public function set_filters(Table_Filters $filters): void
    {
        $this->filters = $filters;
    }

    public function set_user_settings_values(?int $per_page, ?array $hidden_columns): void
    {
        $this->user_items_per_page = $per_page;
        $this->user_hidden_columns = $hidden_columns;
    }

    public function disable_export(): self
    {
        $this->disable_export = true;

        return $this;
    }

    public function set_export_url(string $url): void
    {
        $this->export_table_data_url = $url;
    }

    public function set_data_source_url(string $url): void
    {
        $this->data_source_url = $url;
    }

    public function get_data_source_url(): ?string
    {
        return $this->data_source_url;
    }

    public function get_data_provider(): Interface_Dynamic_Table_Data_Provider
    {
        return $this->data_provider;
    }

    public function get_columns(): array
    {
        return $this->columns;
    }

    public function get_required_caps(): User_Capability_Collection
    {
        return $this->required_caps;
    }

    public function set_required_caps(User_Capability_Collection $caps): self
    {
        $this->required_caps = $caps;

        return $this;
    }

    public function get_required_roles(): User_Role_Collection
    {
        return $this->required_roles;
    }

    public function set_required_roles(User_Role_Collection $roles): self
    {
        $this->required_roles = $roles;

        return $this;
    }

    public function get_prepared_json(): string
    {
        $object = new \stdClass();

        $object->tableId = $this->table_id;
        $object->dataSourceUrl = $this->get_data_source_url();
        $object->columns = $this->get_prepared_columns();
        $object->itemsPerPage = $this->user_items_per_page ?? $this->default_items_per_page;
        $object->itemsPerPagePresets = $this->items_per_page_presets;
        $object->hiddenColumns = $this->user_hidden_columns ?? $this->default_hidden_columns;
        $object->saveTableConfig= $this->save_table_config;
        $object->saveTableConfigUrl = $this->save_table_config_url;
        $object->exportTableDataUrl = $this->disable_export ? null : $this->export_table_data_url;
        $object->topPanelButtons = $this->top_panel_buttons;
        $object->actions = $this->row_actions;
        $object->bulkActions= $this->bulk_actions;
        $object->allowColumnsHiding = $this->allow_columns_hiding;
        $object->showRefreshButton = $this->show_refresh_button;
        $object->disableMultiSort = $this->disable_multi_sort;
        $object->i18n = $this->i18n;
        $object->filters = $this->filters->get_all();
        $object->disablePagination = $this->disable_pagination;

        return json_encode($object);
    }

    private function get_prepared_columns(): array
    {
        $parsed = [];

        foreach ($this->columns as $column) {
            $object = new \stdClass();
            $object->property = $column['property'];
            $object->label = $column['label'];
            $object->filter = $column['filter'] ?? null;
            $object->sortable = $column['sortable'] ?? true;
            $object->filterOptions = $column['filter_options'] ?? null;
            $object->alwaysVisible = $column['always_visible'] ?? false;
            $object->type = $column['type'] ?? null;
            $object->prefix = $column['prefix'] ?? null;
            $object->suffix = $column['suffix'] ?? null;
            $object->useJsonPropertyAsLink = $column['use_json_property_as_link'] ?? null;
            $object->useJsonPropertyAsCurrency = $column['use_json_property_as_currency'] ?? null;
            $object->useJsonPropertyAsLabel = $column['use_json_property_as_label'] ?? null;
            $object->maxLength = $column['max_length'] ?? null;
            $object->buttons = $column['buttons'] ?? null;
            $object->customColumnClass = $column['custom_column_class'] ?? null;

            $parsed[] = $object;
        }

        return $parsed;
    }
}