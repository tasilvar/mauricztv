<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\logs;

use bpmj\wpidea\admin\tables\dynamic\data\Dynamic_Table_Data_Usage_Context;
use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\infrastructure\logs\repository\Interface_Log_Repository;

class Log_Table_Data_Provider implements Interface_Dynamic_Table_Data_Provider
{
    private Logs_Table_Data_Parser $data_parser;
    private Interface_Log_Repository $log_repository;

    public function __construct(
        Logs_Table_Data_Parser $data_parser,
        Interface_Log_Repository $log_repository
    )
    {
        $this->data_parser = $data_parser;
        $this->log_repository = $log_repository;
    }

    public function get_rows(array $filters, Sort_By_Clause $sort_by, int $per_page, int $page, Dynamic_Table_Data_Usage_Context $context): array
    {
        $criteria = $this->data_parser->get_criteria_from_filters_array($filters);
        $per_page = ($per_page <= 0) ? 0 : $per_page;

        return $this->data_parser->parse_log_objects_to_plain_array(
            $this->log_repository->find_by_criteria($criteria, $per_page, $page, $sort_by)
        );
    }

    public function get_total(array $filters): int
    {
        $criteria = $this->data_parser->get_criteria_from_filters_array($filters);

        return $this->log_repository->count_by_criteria($criteria);
    }
}