<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\webhooks\core\repositories;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\webhooks\core\entities\Webhook;
use bpmj\wpidea\modules\webhooks\core\entities\Webhook_Collection;
use bpmj\wpidea\modules\webhooks\infrastructure\persistence\Webhook_Query_Criteria;

interface Interface_Webhook_Repository
{
    public function find_by_id(int $id): ?Webhook;

    public function count_by_criteria(Webhook_Query_Criteria $criteria): int;

    public function find_by_criteria(Webhook_Query_Criteria $criteria, int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): Webhook_Collection;

    public function save(Webhook $webhook): void;

    public function update(Webhook $webhook): void;

    public function remove(int $id): void;
}