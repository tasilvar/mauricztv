<?php

namespace bpmj\wpidea\modules\learning\notes\core\services;

use bpmj\wpidea\modules\learning\notes\core\entities\Note;
use bpmj\wpidea\modules\learning\notes\core\exceptions\{Invalid_Courses_Content_Id_Exception, Note_Not_Found_Exception};
use bpmj\wpidea\modules\learning\notes\core\repositories\Interface_Note_Repository;
use bpmj\wpidea\modules\learning\notes\core\value_objects\{Lesson_ID, Module_ID, Note_ID, User_ID};
use OutOfBoundsException;

class Note_Service implements Interface_Note_Service
{
    private Interface_Note_Repository $note_repository;

    public function __construct(
        Interface_Note_Repository $note_repository
    ) {
        $this->note_repository = $note_repository;
    }

    /**
     * @throws Invalid_Courses_Content_Id_Exception
     * @throws OutOfBoundsException
     */

    public function add(User_ID $user_id, ?Lesson_ID $lesson_id, ?Module_ID $module_id, string $contents): void
    {
        if (!$lesson_id && !$module_id) {
            throw new Invalid_Courses_Content_Id_Exception();
        }

        $note = new Note(
            null,
            $user_id,
            $lesson_id,
            $module_id,
            $contents
        );

        $this->note_repository->add($note);
    }

    public function find_by_id(Note_Id $id): ?Note
    {
        $note = $this->note_repository->find_by_id($id);

        if (!$note) {
            return null;
        }

        return $note;
    }

    /**
     * @throws Invalid_Courses_Content_Id_Exception
     * @throws OutOfBoundsException
     */

    public function find_by_user_and_courses_content_id(User_ID $user_id, ?Lesson_ID $lesson_id, ?Module_ID $module_id): ?Note
    {
        if (!$lesson_id && !$module_id) {
            throw new Invalid_Courses_Content_Id_Exception();
        }

        if ($lesson_id) {
            $note = $this->note_repository->find_by_user_and_lesson_id($user_id, $lesson_id);
        }

        if ($module_id) {
            $note = $this->note_repository->find_by_user_and_module_id($user_id, $module_id);
        }

        if (!$note) {
            return null;
        }

        return $note;
    }

    /**
     * @throws Note_Not_Found_Exception
     */

    public function update(Note_ID $id, string $contents): void
    {
        $note = $this->note_repository->find_by_id($id);

        if (!$note) {
            throw new Note_Not_Found_Exception();
        }

        $note->change_content($contents);

        $this->note_repository->update($note);
    }

    public function delete(Note_ID $id): void
    {
        $this->note_repository->delete($id);
    }
}
