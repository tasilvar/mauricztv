<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\database;

interface Interface_Sql_Helper
{
    public function process_where_condition_to_sql(array $conditions_array): string;

    public function process_having_condition_to_sql(array $conditions_array): string;

    public function process_order_by_clause(?Sort_By_Clause $sort_by = null): string;
}