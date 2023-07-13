<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\increasing_sales\infrastructure\persistence;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\increasing_sales\core\entities\Offer;
use bpmj\wpidea\modules\increasing_sales\core\collections\Offer_Collection;

interface Interface_Offers_Persistence
{
    public function setup(): void;

    public function insert(Offer $offer): void;

    public function update(Offer $offer): void;

    public function delete(int $id): void;

    public function count_by_criteria(Offer_Query_Criteria $criteria): int;

    public function find_by_id(int $id): ?Offer;

    public function find_by_criteria(
        Offer_Query_Criteria $criteria,
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null
    ): Offer_Collection;
}