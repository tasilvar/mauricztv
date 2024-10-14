<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\tables\dynamic\user_settings;

interface Interface_User_Table_Settings_Service
{
    public function save_hidden_columns_option(string $table_id, array $columns): void;

    public function get_hidden_columns_option(string $table_id): ?array;

    public function save_results_per_page_option(string $table_id, int $per_page): void;

    public function get_results_per_page_option(string $table_id): ?int;
}
