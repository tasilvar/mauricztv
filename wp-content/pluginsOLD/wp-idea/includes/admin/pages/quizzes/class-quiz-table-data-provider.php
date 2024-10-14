<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\quizzes;

use bpmj\wpidea\admin\tables\dynamic\data\Dynamic_Table_Data_Usage_Context;
use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\learning\quiz\Interface_Resolved_Quiz_Repository;

class Quiz_Table_Data_Provider implements Interface_Dynamic_Table_Data_Provider
{
    private Quiz_Table_Data_Parser_For_Display $data_parser_for_display;
    private Quiz_Table_Data_Parser_For_Export $data_parser_for_export;
    private Interface_Resolved_Quiz_Repository $repository;

    public function __construct(
        Quiz_Table_Data_Parser_For_Display $data_parser_for_display,
        Quiz_Table_Data_Parser_For_Export $data_parser_for_export,
        Interface_Resolved_Quiz_Repository $repository
    )
    {
        $this->data_parser_for_display = $data_parser_for_display;
        $this->data_parser_for_export = $data_parser_for_export;
        $this->repository = $repository;
    }

    public function get_rows(array $filters, Sort_By_Clause $sort_by, int $per_page, int $page, Dynamic_Table_Data_Usage_Context $context): array
    {
        $criteria = $this->get_data_parser($context)->get_criteria_from_filters_array($filters);

        return $this->get_data_parser($context)->parse_models_to_plain_array(
            $this->repository->find_by_criteria($criteria, $per_page, $page, $sort_by)
        );
    }

    public function get_total(array $filters): int
    {
        $criteria = $this->get_data_parser()->get_criteria_from_filters_array($filters);

        return $this->repository->count_by_criteria($criteria);
    }

    private function get_data_parser(?Dynamic_Table_Data_Usage_Context $context = null): Quiz_Table_Data_Parser
    {
        $context_name = $context ? $context->get_value() : null;

        switch ($context_name) {
            default:
            case Dynamic_Table_Data_Usage_Context::DISPLAY_DATA:
                return $this->data_parser_for_display;

            case Dynamic_Table_Data_Usage_Context::EXPORT_DATA:
                return $this->data_parser_for_export;
        }
    }
}