<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\discount_codes\core\repositories;

use bpmj\wpidea\sales\discount_codes\core\collections\Discount_Collection;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Discount_ID;

interface Interface_Discount_Repository
{
    public function find_all(): Discount_Collection;

    public function find_by_criteria(
        Discount_Query_Criteria $criteria,
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null,
        $exclude_slow_data = false
    ): Discount_Collection;

    public function count_by_criteria(
        Discount_Query_Criteria $criteria
    ): int;

    public function delete(Discount_ID $id): void;
}