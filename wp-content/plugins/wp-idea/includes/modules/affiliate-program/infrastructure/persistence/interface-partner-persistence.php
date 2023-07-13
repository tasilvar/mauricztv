<?php

namespace bpmj\wpidea\modules\affiliate_program\infrastructure\persistence;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Affiliate_ID;
use bpmj\wpidea\modules\affiliate_program\core\entities\Partner;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Partner_ID;
use bpmj\wpidea\user\User_ID;
use bpmj\wpidea\modules\affiliate_program\core\repositories\Partner_Query_Criteria;

interface Interface_Partner_Persistence
{
    public function setup(): void;

    public function insert(Partner $partner): void;

    public function find_by_id(Partner_ID $partner_id): ?Partner;

    public function find_by_user_id(User_ID $user_id): ?Partner;

    public function find_by_affiliate_id(Affiliate_ID $affiliate_id): ?Partner;

    public function find_by_criteria(
        Partner_Query_Criteria $criteria,
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null
    ): array;

    public function count_by_criteria(Partner_Query_Criteria $criteria): int;

}