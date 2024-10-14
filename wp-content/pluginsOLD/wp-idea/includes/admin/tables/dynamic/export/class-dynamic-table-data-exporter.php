<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\tables\dynamic\export;

use bpmj\wpidea\tools\Data_Exporter;

class Dynamic_Table_Data_Exporter
{
    private Data_Exporter $exporter;

    public function __construct(
        Data_Exporter $exporter
    )
    {
        $this->exporter = $exporter;
    }

    public function export_rows(array $rows, array $table_columns_config, array $hidden_columns): void
    {
        $export_columns = $this->get_columns_to_export($hidden_columns, $table_columns_config);

        $data = array_merge(
            [$this->get_export_headers($export_columns)],
            $this->remove_hidden_columns_from_rows_data($rows, $export_columns)
        );

        $this->exporter->output_array_to_csv($data);
        die;
    }

    private function get_export_headers(array $export_columns): array
    {
        $headers = [];

        foreach ($export_columns as $property => $label) {
            $headers[] = $label;
        }

        return $headers;
    }

    private function get_columns_to_export(array $columns_to_skip, array $columns_config): array
    {
        $columns_to_export = [];

        foreach ($columns_config as $column_config) {
            if(in_array($column_config['property'], $columns_to_skip, true)) {
                continue;
            }

            $property_name = $column_config['use_json_property_as_label'] ?? $column_config['property'];

            $columns_to_export[$property_name] = $column_config['label'];
        }

        return $columns_to_export;
    }

    private function remove_hidden_columns_from_rows_data(array $rows, array $export_columns): array
    {
        $filtered_rows = [];

        foreach ($rows as $index => $row) {
            $filtered_row = [];

            foreach ($export_columns as $column_name => $column_label) {
                $filtered_row[$column_name] = $row[$column_name];
            }

            $filtered_rows[] = $filtered_row;
        }

        return $filtered_rows;
    }
}