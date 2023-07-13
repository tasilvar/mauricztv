<?php

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\database;

class Wpdb implements Interface_Database
{
    private Interface_Sql_Helper $sql_helper;

    public function __construct(
        Interface_Sql_Helper $sql_helper
    )
    {
        $this->sql_helper = $sql_helper;
    }

    public function create_table_if_not_exists(
        string $table_name,
        array $columns,
        ?string $primary_key = null,
        ?array $other_keys = null
    ): void {
        global $wpdb;

        $table_name = $this->get_wp_table_name($table_name);
        $charset_collate = $wpdb->get_charset_collate();
        $columns_sql = implode(', ', $columns);
        $primary_key_sql = $primary_key ? ", PRIMARY KEY ($primary_key)" : '';
        $coma_between_primary_and_other_keys = $primary_key && $other_keys ? ', ' : '';
        $other_keys_sql = $other_keys ? implode(', ', $other_keys) : '';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            $columns_sql
            $primary_key_sql
            $coma_between_primary_and_other_keys
            $other_keys_sql
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function get_wp_table_name(string $table_name): string
    {
        global $wpdb;
        return $wpdb->prefix . $table_name;
    }

    public function insert(string $table_name, array $values): void
    {
        global $wpdb;

        $table_name = $wpdb->prefix . $table_name;

        $wpdb->insert($table_name, $values);
    }

    public function add_column_in_table(string $table_name, string $column_name, string $type): void
    {
        global $wpdb;

        $table_name = $wpdb->prefix . $table_name;

        $row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '{$table_name}' AND column_name = '{$column_name}'");

        if(empty($row)) {
            $wpdb->query("ALTER TABLE {$table_name} ADD {$column_name} {$type}");
        }
    }

    public function get_results(
        string $table_name,
        array $select_columns,
        array $where = [],
        int $limit = 0,
        int $offset = 0,
        ?Sort_By_Clause $sort_by = null
    ): array {
        global $wpdb;

        $table_name = $wpdb->prefix . $table_name;
        $columns_sql = implode(', ', $select_columns);

        $where_sql = $this->sql_helper->process_where_condition_to_sql($where);
        $limit = $limit ?: PHP_INT_MAX;
        $limit_sql = "LIMIT {$offset}, {$limit}";
        $order_by_sql = $this->sql_helper->process_order_by_clause($sort_by);

        return $wpdb->get_results(
            "SELECT {$columns_sql} FROM {$table_name} {$where_sql} {$order_by_sql} {$limit_sql}",
            ARRAY_A
        );
    }

    public function get_sum(
        string $table_name,
        array $columns,
        array $where = [],
        int $limit = 0,
        int $offset = 0,
        ?Sort_By_Clause $sort_by = null,
        ?string $group_by = null,
        ?array $additional_columns_to_select = null
    ): array {
        global $wpdb;

        $table_name = $wpdb->prefix . $table_name;
        $where_sql = $this->sql_helper->process_where_condition_to_sql($where);
        $limit = $limit ?: PHP_INT_MAX;
        $limit_sql = "LIMIT {$offset}, {$limit}";
        $order_by_sql = $this->sql_helper->process_order_by_clause($sort_by);
        $group_by_select_column_sql = $group_by ? $group_by . ',' : '';
        $group_by_sql = $group_by ? 'GROUP BY ' . $group_by : '';
        $additional_columns_to_select_sql = $additional_columns_to_select ? implode(
                ',',
                $additional_columns_to_select
            ) . ',' : '';
        $sums_sql = '';
        foreach ($columns as $column) {
            $sums_sql .= "SUM({$column}) as {$column}_sum,";
        }
        $sums_sql .= substr($sums_sql, 0, -1);

        $query_result = $wpdb->get_results(
            "
                SELECT {$additional_columns_to_select_sql}{$group_by_select_column_sql}{$sums_sql}
                FROM {$table_name} 
                {$where_sql} 
                {$group_by_sql}
                {$order_by_sql} 
                {$limit_sql}",
            ARRAY_A
        );

        return $query_result;
    }


    public function count(string $table_name, array $where = []): int
    {
        global $wpdb;

        $table_name = $wpdb->prefix . $table_name;
        $where_sql = $this->sql_helper->process_where_condition_to_sql($where);

        $results = $wpdb->get_results("SELECT COUNT(*) as count FROM {$table_name} {$where_sql}");

        return (int)$results[0]->count;
    }

    public function update_rows(string $table_name, array $set, array $where = []): void
    {
        global $wpdb;

        if (!empty($set)) {
            $table_name = $wpdb->prefix . $table_name;
            $where_sql = $this->sql_helper->process_where_condition_to_sql($where);
            $set_sql = $this->array_set_changes_to_string($set);

            $wpdb->query("UPDATE {$table_name} SET {$set_sql} {$where_sql}");
        }
    }

    public function delete_rows(
        string $table_name,
        array $where = [],
        int $limit = 0,
        int $offset = 0,
        ?Sort_By_Clause $sort_by = null
    ): void {
        global $wpdb;

        $table_name = $wpdb->prefix . $table_name;
        $where_sql = $this->sql_helper->process_where_condition_to_sql($where);
        $order_by_sql = $this->sql_helper->process_order_by_clause($sort_by);

        if (!$offset && !$limit) {
            $wpdb->query("DELETE FROM {$table_name} {$where_sql} {$order_by_sql}");

            return;
        }

        $limit = $limit ?: PHP_INT_MAX;
        $limit_sql = "LIMIT {$offset}, {$limit}";

        $ids_to_delete = $wpdb->get_results(
            "SELECT id from {$table_name} {$where_sql} {$order_by_sql} {$limit_sql}",
            ARRAY_A
        );
        $ids_to_delete = implode(
            ', ',
            array_map(static function ($row) {
                return $row['id'];
            }, $ids_to_delete)
        );

        if (empty($ids_to_delete)) {
            return;
        }

        $wpdb->query("DELETE FROM {$table_name} WHERE id IN({$ids_to_delete})");
    }

    private function array_set_changes_to_string(array $set): string
    {
        $set_sql = '';
        foreach ($set as $index => [$column, $value]) {
            if ($index !== 0) {
                $set_sql .= ', ';
            }

            $set_sql .= "{$column} = '{$value}'";
        }

        return $set_sql;
    }

    public function execute(string $sql_query)
    {
        global $wpdb;

        return $wpdb->get_results($sql_query, ARRAY_A);
    }

    public function prepare_table_name(string $table_name): string
    {
        global $wpdb;

        return $wpdb->prefix . $table_name;
    }
}