<?php
declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\webhooks;

use bpmj\wpidea\admin\tables\dynamic\data\Dynamic_Table_Data_Usage_Context;
use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\webhooks\core\repositories\Interface_Webhook_Repository;

class Webhook_Table_Data_Provider implements Interface_Dynamic_Table_Data_Provider
{

    private Webhooks_Table_Data_Parser $data_parser;
    private Interface_Webhook_Repository $webhook_repository;

    public function __construct(
        Webhooks_Table_Data_Parser $data_parser,
        Interface_Webhook_Repository $webhook_repository
    )
    {
        $this->data_parser = $data_parser;
        $this->webhook_repository = $webhook_repository;
    }

    public function get_rows(array $filters, Sort_By_Clause $sort_by, int $per_page, int $page, Dynamic_Table_Data_Usage_Context $context): array
    {
        $criteria = $this->data_parser->get_criteria_from_filters_array($filters);

        $sort_by->reset();
        
        return $this->data_parser->parse_webhook_objects_to_plain_array(
            $this->webhook_repository->find_by_criteria($criteria, $per_page, $page, $sort_by)
        );
    }

    public function get_total(array $filters): int
    {
        $criteria = $this->data_parser->get_criteria_from_filters_array($filters);

        return $this->webhook_repository->count_by_criteria($criteria);
    }
}