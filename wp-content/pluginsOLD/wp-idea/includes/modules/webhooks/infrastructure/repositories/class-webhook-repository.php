<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\webhooks\infrastructure\repositories;

use bpmj\wpidea\data_types\{ID, Url};
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\webhooks\core\entities\Webhook;
use bpmj\wpidea\modules\webhooks\core\entities\Webhook_Collection;
use bpmj\wpidea\modules\webhooks\core\repositories\Interface_Webhook_Repository;
use bpmj\wpidea\modules\webhooks\core\value_objects\{Webhook_Types_Of_Events};
use bpmj\wpidea\modules\webhooks\core\value_objects\Webhook_Status;
use bpmj\wpidea\modules\webhooks\infrastructure\persistence\Interface_Webhooks_Persistence;
use bpmj\wpidea\modules\webhooks\infrastructure\persistence\Webhook_Query_Criteria;


class Webhook_Repository implements Interface_Webhook_Repository
{
    private Interface_Webhooks_Persistence $webhooks_persistence;


    public function __construct(
        Interface_Webhooks_Persistence $webhooks_persistence
    ) {
        $this->webhooks_persistence = $webhooks_persistence;
    }

    public function find_by_id(int $id): ?Webhook
    {
        $webhook_row = $this->webhooks_persistence->find_by_id($id);
        if(!$webhook_row){
            return null;
        }

        return $this->table_rows_to_webhooks_model($webhook_row)->get_first();
    }

    public function count_by_criteria(Webhook_Query_Criteria $criteria): int
    {
        return $this->webhooks_persistence->count_by_criteria($criteria);
    }

    public function find_by_criteria(Webhook_Query_Criteria $criteria, int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): Webhook_Collection
    {
        $results = $this->webhooks_persistence->find_by_criteria($criteria, $per_page, $page, $sort_by);

        return $this->table_rows_to_webhooks_model($results);
    }

    public function save(Webhook $webhook): void
    {
        $this->webhooks_persistence->insert($webhook);
    }

    public function update(Webhook $webhook): void
    {
        $this->webhooks_persistence->update($webhook);
    }

    public function remove(int $id): void
    {
        $this->webhooks_persistence->delete($id);
    }

    private function table_rows_to_webhooks_model(array $rows): Webhook_Collection
    {
        $webhooks = new Webhook_Collection();
        foreach ($rows as $row) {
            $webhooks->add( $this->table_row_to_webhook_model($row) );
        }

        return $webhooks;
    }

    private function table_row_to_webhook_model(array $row): Webhook
    {
        $type_of_event = new Webhook_Types_Of_Events( $row['type_of_event'] );
        $url           = new Url( $row['url'] );
        $status        = new Webhook_Status( (int)$row['status'] );

        $webhook = new Webhook($type_of_event,  $url, $status);

        if($row['id']) {
            $id = new ID( (int)$row['id'] );
            $webhook->set_id($id);
        }

        return $webhook;
    }

}