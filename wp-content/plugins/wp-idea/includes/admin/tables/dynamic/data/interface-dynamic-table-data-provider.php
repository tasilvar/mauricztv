<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\tables\dynamic\data;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;

interface Interface_Dynamic_Table_Data_Provider
{
    public function get_rows(
        array $filters,
        Sort_By_Clause $sort_by,
        int $per_page,
        int $page,
        Dynamic_Table_Data_Usage_Context $context
    ): array;

    public function get_total(array $filters): int;
}