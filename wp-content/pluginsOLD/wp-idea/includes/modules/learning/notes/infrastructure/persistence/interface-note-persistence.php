<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\learning\notes\infrastructure\persistence;

use bpmj\wpidea\modules\learning\notes\core\entities\Note;
use bpmj\wpidea\modules\learning\notes\core\value_objects\{Lesson_ID, Module_ID, Note_ID, User_ID};

interface Interface_Note_Persistence
{
    public function insert(Note $note): void;

    public function update(Note $note): void;

    public function find_by_id(Note_ID $id): array;

    public function find_by_user_and_lesson_id(User_ID $user_id, Lesson_ID $lesson_id): array;

    public function find_by_user_and_module_id(User_ID $user_id, Module_ID $module_id): array;

    public function delete(Note_Id $id): void;

    public function setup(): void;
}