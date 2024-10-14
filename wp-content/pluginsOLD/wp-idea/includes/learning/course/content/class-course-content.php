<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\course\content;

class Course_Content
{
    protected Course_Content_ID $id;
    protected Course_Content_Type $type;
    protected string $title;
    protected string $content;

    public function __construct(Course_Content_ID $id, Course_Content_Type $type, string $title, string $content)
    {
        $this->id = $id;
        $this->type = $type;
        $this->title = $title;
        $this->content = $content;
    }

    public function get_id(): Course_Content_ID
    {
        return $this->id;
    }

    public function get_type(): Course_Content_Type
    {
        return $this->type;
    }

    public function get_title(): string
    {
        return $this->title;
    }

    public function get_content(): string
    {
        return $this->content;
    }

}
