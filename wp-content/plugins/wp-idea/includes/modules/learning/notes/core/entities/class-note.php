<?php
namespace bpmj\wpidea\modules\learning\notes\core\entities;

use bpmj\wpidea\modules\learning\notes\core\value_objects\{Lesson_ID, Module_ID, Note_ID, User_ID};

class Note
{
    private ?Note_ID $id;
    private User_ID $user_id;
    private ?Lesson_ID $lesson_id;
    private ?Module_ID $module_id;
    private string $content;

    public function __construct(
        ?Note_ID $id,
        User_ID $user_id,
        ?Lesson_ID $lesson_id = null,
        ?Module_ID $module_id = null,
        string $content
    )
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->lesson_id = $lesson_id;
        $this->module_id = $module_id;
        $this->content = $content;
    }

    public function get_id(): ?Note_ID
    {
        return $this->id;
    }

    public function get_user_id(): User_ID
    {
        return $this->user_id;
    }

    public function get_lesson_id(): ?Lesson_ID
    {
        return $this->lesson_id;
    }

    public function get_module_id(): ?Module_ID
    {
        return $this->module_id;
    }

    public function change_content(string $content): void
    {
        $this->content = $content;
    }

    public function get_content(): string
    {
        return $this->content;
    }
}