<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\customers;

use bpmj\wpidea\admin\tables\dynamic\data\Dynamic_Table_Data_Usage_Context;
use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\caps\Access_Filter_Name;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\infrastructure\database\Interface_Database;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\infrastructure\system\System;
use bpmj\wpidea\routing\Interface_Url_Generator;

class Customer_Table_Data_Provider implements Interface_Dynamic_Table_Data_Provider
{
    private const TABLE_NAME = 'edd_customers';
    private const MAX_VAL = 4294967295;
    private const STRING_TO_NUM_PATCH = '+0';

    private Interface_Database $db;
    private Interface_Filters $filters;
    private Interface_Url_Generator $url_generator;
    private System $system;

    private string $system_currency;

    public function __construct(
        Interface_Database $db,
        Interface_Filters $filters,
        Interface_Url_Generator $url_generator,
        System $system
    ) {
        $this->db = $db;
        $this->filters = $filters;
        $this->url_generator = $url_generator;
        $this->system = $system;
    }

    public function get_rows(
        array $filters,
        Sort_By_Clause $sort_by,
        int $per_page,
        int $page,
        Dynamic_Table_Data_Usage_Context $context
    ): array {

        $rows = [];

        $customers = $this->get_customers_array($filters, $per_page, $page, $sort_by);

        foreach ($customers as $customer) {
            $rows[] = [
                'id' => $customer['id'],
                'name' => $this->filters->apply(Access_Filter_Name::CUSTOMER_NAME, $customer['name'],
                    $customer['id']),
                'email' => $this->filters->apply(Access_Filter_Name::CUSTOMER_EMAIL, $customer['email'],
                    $customer['id']),
                'purchase_count' => $customer['purchase_count'],
                'purchase_value' => (float)$customer['purchase_value'],
                'currency' => $this->get_currency(),
                'date_created' => $customer['date_created'],
                'customer_data_url' => $this->get_customer_data_url((int)$customer['id']),
                'delete_customer_url' => $this->get_delete_customer_url((int)$customer['id'])
            ];
        }

        return $rows;
    }

    public function get_total(array $filters): int
    {
        $where = $this->parse_criteria_from_query_filters_to_where_clause($filters);
        return $this->db->count(self::TABLE_NAME, $where);
    }

    private function get_customers_array(
        array $filters,
        int $per_page,
        int $page,
        Sort_By_Clause $sort_by
    ): array {

        $limit = ($per_page > 0) ? $per_page : 0;
        $skip = !$limit ? 0 : ($per_page * ($page - 1));

        $where = $this->parse_criteria_from_query_filters_to_where_clause($filters);
        $this->sort_by_purchase_value_string_to_num_patch($sort_by);

        $sort_by->remove("created_at");

        return $this->db->get_results(self::TABLE_NAME, [
            'id',
            'email',
            'name',
            'purchase_value',
            'purchase_count',
            'date_created',
        ], $where, $limit, $skip, $sort_by);

    }

    private function parse_criteria_from_query_filters_to_where_clause(array $filters): array
    {
        $name = $this->get_filter_value_if_present($filters, 'name');
        $email = $this->get_filter_value_if_present($filters, 'email');
        $purchase_value = $this->get_filter_value_if_present($filters, 'purchase_value');
        $date_created = $this->get_filter_value_if_present($filters, 'date_created');

        $where = [];

        if (isset($name)) {
            $where[] = ['name', 'LIKE', $name];
        }

        if (isset($email)) {
            $where[] = ['email', 'LIKE', $email];
        }

        if (isset($purchase_value)) {
             $min = $purchase_value[0] ?? 0;
             $max = $purchase_value[1] ?? self::MAX_VAL;

            if (isset($min)) {
                $where[] = ['purchase_value', 'MIN', $min];
            }

            if (isset($max)) {
                $where[] = ['purchase_value', 'MAX', (float) $max];
            }
        }

        if (isset($date_created)) {
            $startDate = $date_created['startDate'];
            $endDate = $date_created['endDate'];

            if (isset($startDate)) {
                $where[] = ['date_created', '>=', $startDate];
            }

            if (isset($endDate)) {
                $where[] = ['date_created', '<=', $endDate];
            }
        }

        return $where;
    }

    private function get_filter_value_if_present(array $filters, string $filter_name)
    {
        return array_values(
                array_filter($filters, static function ($filter, $key) use ($filter_name) {
                    return $filter['id'] === $filter_name;
                }, ARRAY_FILTER_USE_BOTH)
            )[0]['value'] ?? null;
    }

    private function get_customer_data_url(int $customer_id): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => 'wp-idea-customers',
            'view' => 'overview',
            'id' => $customer_id
        ]);
    }

    private function get_delete_customer_url(int $customer_id): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => 'wp-idea-customers',
            'view' => 'delete',
            'id' => $customer_id
        ]);
    }

    private function get_currency(): string
    {
        if (!isset($this->system_currency)) {
            $this->system_currency = $this->system->get_system_currency();
        }

        return $this->system_currency;
    }

    private function sort_by_purchase_value_string_to_num_patch(Sort_By_Clause $sort_by): void
    {
        $sort_by_purchase_value = $sort_by->get('purchase_value');
        if (!$sort_by_purchase_value) return;
        
        $sort_by->remove('purchase_value');
        $sort_by->sort_by('purchase_value' . self::STRING_TO_NUM_PATCH, $sort_by_purchase_value->desc);
    }

}