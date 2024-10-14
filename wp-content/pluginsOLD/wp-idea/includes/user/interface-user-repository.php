<?php
namespace bpmj\wpidea\user;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;

interface Interface_User_Repository
{
    public function find_by_id(User_ID $id): ?Interface_User;

    public function find_by_email(string $email): ?Interface_User;

    public function save(Interface_User $user): void;

    public function find_by_criteria(
        User_Query_Criteria $criteria,
        int $page = 1,
        int $per_page = 25,
        ?Sort_By_Clause $sort_by = null
    ): User_Collection;

    public function count_by_criteria(User_Query_Criteria $criteria): int;

    public function delete(Interface_User $user): void;
}