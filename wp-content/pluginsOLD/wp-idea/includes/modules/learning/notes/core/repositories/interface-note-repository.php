<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\learning\notes\core\repositories;

use bpmj\wpidea\modules\learning\notes\core\entities\Note;
use bpmj\wpidea\modules\learning\notes\core\value_objects\{Lesson_ID, Module_ID, Note_ID, User_ID};

interface Interface_Note_Repository
{
    public function find_by_id(Note_ID $id): ?Note;

    public function find_by_user_and_lesson_id(User_ID $user_id, Lesson_ID $lesson_id): ?Note;

    public function find_by_user_and_module_id(User_ID $user_id, Module_ID $module_id): ?Note;

    public function add(Note $note): void;

    public function update(Note $note): void;

    public function delete(Note_ID $id): void;
}