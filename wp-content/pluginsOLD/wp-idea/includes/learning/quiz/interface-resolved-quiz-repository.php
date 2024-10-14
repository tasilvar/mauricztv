<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\quiz;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;

interface Interface_Resolved_Quiz_Repository
{
    public function find_by_criteria(
        Resolved_Quiz_Query_Criteria $criteria,
        int $per_page = 0,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null
    ): array;

    public function count_by_criteria(Resolved_Quiz_Query_Criteria $criteria): int;

    public function find_by_id(int $id_quiz): ?Resolved_Quiz;

    public function save_configuration_of_questions(Quiz_ID $quiz_id, array $configuration_of_questions): void;

    public function get_configuration_of_questions(Quiz_ID $quiz_id): array;

    public function save_time_is_up(Quiz_ID $quiz_id): void;

    public function get_time_is_up(Quiz_ID $quiz_id): bool;
}