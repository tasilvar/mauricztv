<?php

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\database;

class Sql_Helper implements Interface_Sql_Helper
{
    public function process_where_condition_to_sql(array $conditions_array): string
    {
        $key_word = 'WHERE';

        return $this->process_conditions_to_sql($conditions_array, $key_word);
    }

    public function process_having_condition_to_sql(array $conditions_array): string
    {
        $key_word = 'HAVING';

        return $this->process_conditions_to_sql($conditions_array, $key_word);
    }

    public function process_order_by_clause(?Sort_By_Clause $sort_by = null): string
    {
        $sql_string = '';

        if (is_null($sort_by)) {
            return $sql_string;
        }

        foreach ($sort_by->get_all() as $index => $sort_by_condition) {
            /** @var Sort_By $sort_by_condition */
            if ($index === 0) {
                $sql_string .= 'ORDER BY';
            }

            if ($index > 0) {
                $sql_string .= ',';
            }

            $sql_string .= ' ' . $sort_by_condition->property;
            $sql_string .= ' ' . ($sort_by_condition->desc ? 'DESC' : 'ASC');
        }

        return $sql_string;
    }

    private function process_conditions_to_sql(array $conditions_array, string $key_word): string
    {
        $sql = '';

        if (empty($conditions_array)) {
            return $sql;
        }

        foreach ($conditions_array as $index => $condition) {
            [$column, $operator, $value] = $condition;

            if ($index === 0) {
                $sql .= $key_word . ' ';
            } else {
                $sql .= ' AND ';
            }

            if (in_array($operator, ['=', '>', '>=', '<', '<='])) {
                $value = "'{$value}'";
            }

            if ($operator === 'MIN') {
                $operator = '>=';
            }

            if ($operator === 'MAX') {
                $operator = '<=';
            }

            if ($operator === 'LIKE') {
                $value = "'%{$value}%'";
            }

            if ($operator === 'IN') {
                if (is_array($value)) {
                    $value = $this->apply_quotes_to_string_elements($value);
                    $value = implode(',', $value);
                }

                $value = "({$value})";
            }

            $sql .= "{$column} {$operator} {$value}";
        }

        return $sql;
    }

    private function apply_quotes_to_string_elements(array $array): array
    {
        $result = [];
        foreach ($array as $value) {
            if (is_string($value)) {
                $value = "'{$value}'";
            }

            $result[] = $value;
        }
        return $result;
    }
}