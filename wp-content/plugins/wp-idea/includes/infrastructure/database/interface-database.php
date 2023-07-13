<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\database;

interface Interface_Database
{
    public function create_table_if_not_exists(
        string $table_name,
        array $columns,
        ?string $primary_key = null,
        ?array $other_keys = null
    ): void;

    public function insert(string $table_name, array $values): void;

    public function add_column_in_table(string $table_name, string $column_name, string $type): void;

    public function get_results(
        string $table_name,
        array $select_columns,
        array $where = [],
        int $limit = 0,
        int $offset = 0,
        ?Sort_By_Clause $sort_by = null
    ): array;

    public function get_sum(
        string $table_name,
        array $columns,
        array $where = [],
        int $limit = 0,
        int $offset = 0,
        ?Sort_By_Clause $sort_by = null,
        ?string $group_by = null,
        ?array $additional_columns_to_select = null
    ): array;

    public function count(string $table_name, array $where = []): int;

    public function update_rows(
        string $table_name,
        array $set,
        array $where = []
    ): void;

    public function delete_rows(
        string $table_name,
        array $where = [],
        int $limit = 0,
        int $offset = 0,
        ?Sort_By_Clause $sort_by = null
    ): void;

    public function execute(string $sql_query);

    public function prepare_table_name(string $table_name): string;
}