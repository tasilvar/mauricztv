<?php

namespace bpmj\wpidea\modules\affiliate_program\core\repositories;

use bpmj\wpidea\modules\affiliate_program\core\entities\Partner_Collection;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Affiliate_ID;
use bpmj\wpidea\modules\affiliate_program\core\entities\Partner;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Partner_ID;
use bpmj\wpidea\user\User_ID;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;

interface Interface_Partner_Repository
{
    public function create(Partner $partner): void;

    public function find_by_id(Partner_ID $id): ?Partner;

    public function find_by_affiliate_id(Affiliate_ID $affiliate_id): ?Partner;

    public function find_by_user_id(User_ID $id): ?Partner;

    public function find_by_criteria(
        Partner_Query_Criteria $criteria,
        int $page = 0,
        int $per_page = 10,
        Sort_By_Clause $sort_by = null
    ): Partner_Collection;

    public function count_by_criteria(Partner_Query_Criteria $criteria): int;
}