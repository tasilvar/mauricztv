<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\learning\notes\core\services;

use bpmj\wpidea\modules\learning\notes\core\entities\Note;
use bpmj\wpidea\modules\learning\notes\core\value_objects\{ Note_ID, User_ID, Lesson_ID, Module_ID };

interface Interface_Note_Service
{
    public function add(User_ID $user_id, ?Lesson_ID $lesson_id, ?Module_ID $module_id, string $contents): void;

    public function find_by_id(Note_Id $id): ?Note;

    public function find_by_user_and_courses_content_id(User_ID $user_id, ?Lesson_ID $lesson_id, ?Module_ID $module_id): ?Note;

    public function update(Note_ID $id, string $contents): void;

    public function delete(Note_ID $id): void;
}