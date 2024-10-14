<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\payments_history;

use bpmj\wpidea\admin\tables\dynamic\data\Dynamic_Table_Data_Usage_Context;
use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\sales\order\Interface_Orders_Repository;
use bpmj\wpidea\sales\order\Order_Query_Criteria;

class Order_Table_Data_Provider implements Interface_Dynamic_Table_Data_Provider
{

    private Interface_Orders_Repository $orders_repository;
    private Payment_Table_Row_Parser $row_parser_for_display;
    private Payment_Export_Row_Parser $row_parser_for_export;

    public function __construct(
        Interface_Orders_Repository $orders_repository,
        Payment_Table_Row_Parser $row_parser_for_display,
        Payment_Export_Row_Parser $row_parser_for_export
    )
    {
        $this->orders_repository = $orders_repository;
        $this->row_parser_for_display = $row_parser_for_display;
        $this->row_parser_for_export = $row_parser_for_export;
    }

    public function get_rows(array $filters, Sort_By_Clause $sort_by, int $per_page, int $page, Dynamic_Table_Data_Usage_Context $context): array
    {
        $criteria_array = [
            'perPage' => $per_page,
            'page'    => $page,
            'filters' => $filters,
            'sortBy'  => $sort_by
        ];

        $criteria = new Order_Query_Criteria($criteria_array);
        $payments = $this->orders_repository->find_by_criteria($criteria);

        $rows = [];

        foreach ($payments as $payment) {
            $rows[] = $this->get_row_parser($context)->get_parsed_row($payment);
        }

        return $rows;
    }

    public function get_total(array $filters): int
    {
        $criteria_array = [
            'perPage' => -1,
            'page'    => 1,
            'filters' => $filters,
            'sortBy' => new Sort_By_Clause()
        ];

        $criteria = new Order_Query_Criteria($criteria_array);

        return $this->orders_repository->count_by_criteria($criteria);
    }

    private function get_row_parser(Dynamic_Table_Data_Usage_Context $context): Abstract_Payment_Row_Parser
    {
        switch ($context->get_value()) {
            default:
            case Dynamic_Table_Data_Usage_Context::DISPLAY_DATA:
                return $this->row_parser_for_display;

            case Dynamic_Table_Data_Usage_Context::EXPORT_DATA:
                return $this->row_parser_for_export;
        }
    }
}