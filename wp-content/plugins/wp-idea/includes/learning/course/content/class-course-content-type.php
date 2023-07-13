<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\learning\course\content;

class Course_Content_Type
{
    public const TYPE_PANEL = 'home';
    public const TYPE_MODULE = 'full';
    public const TYPE_LESSON = 'lesson';
    public const TYPE_QUIZ = 'test';

    private const ALLOWED_TYPES = [
        self::TYPE_PANEL,
        self::TYPE_MODULE,
        self::TYPE_LESSON,
        self::TYPE_QUIZ
    ];

    private string $type;

    public function __construct(string $type)
    {
        if (!in_array($type, self::ALLOWED_TYPES)) {
            throw Invalid_Content_Type_Exception();
        }

        $this->type = $type;
    }

    public function get_value(): string
    {
        return $this->type;
    }
}