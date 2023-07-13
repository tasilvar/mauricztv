<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\tables\dynamic\user_settings;

use bpmj\wpidea\user\Interface_User_Metadata_Service;

class User_Table_Settings_Service implements Interface_User_Table_Settings_Service
{
    private const HIDDEN_COLUMNS_META_NAME = '_hidden_columns';
    private const RESULTS_PER_PAGE_META_NAME = '_results_per_page';

    private $metadata_service;

    public function __construct(
        Interface_User_Metadata_Service $metadata_service
    )
    {
        $this->metadata_service = $metadata_service;
    }

    public function save_hidden_columns_option(string $table_id, array $columns): void
    {
        $this->metadata_service->store_for_current_user($table_id . self::HIDDEN_COLUMNS_META_NAME, $columns);
    }

    public function get_hidden_columns_option(string $table_id): ?array
    {
        $value = $this->metadata_service->get_for_current_user($table_id . self::HIDDEN_COLUMNS_META_NAME);

        return is_array($value) ? $value : null;
    }

    public function save_results_per_page_option(string $table_id, int $per_page): void
    {
        $this->metadata_service->store_for_current_user($table_id . self::RESULTS_PER_PAGE_META_NAME, $per_page);
    }

    public function get_results_per_page_option(string $table_id): ?int
    {
        $value = $this->metadata_service->get_for_current_user($table_id . self::RESULTS_PER_PAGE_META_NAME);

        return is_numeric($value) ? (int)$value : null;
    }
}