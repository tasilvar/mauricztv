<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\webhooks\infrastructure\persistence;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\webhooks\core\entities\Webhook;

interface Interface_Webhooks_Persistence
{
    public function insert(Webhook $webhook): void;

    public function update(Webhook $webhook): void;

    public function count_by_criteria(Webhook_Query_Criteria $criteria): int;

    public function find_by_id(int $id): array;

    public function find_by_criteria(Webhook_Query_Criteria $criteria, int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): array;

    public function delete(int $id): void;

    public function setup(): void;
}