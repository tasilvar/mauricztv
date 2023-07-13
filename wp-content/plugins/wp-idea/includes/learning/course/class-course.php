<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\course;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\wolverine\user\backend\User;

class Course
{
    protected $id;
    protected $title;
    protected $author_id;
    protected $product_id;
    protected $certificate_template_id;

    public function __construct(Course_ID $id, string $title, Author_ID $author_id, ?ID $product_id = null, ?ID $certificate_template_id = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->author_id = $author_id;
        $this->product_id = $product_id;
        $this->certificate_template_id = $certificate_template_id;
    }

    public function get_id(): Course_ID
    {
        return $this->id;
    }

    public function get_title(): string
    {
        return $this->title;
    }

    public function get_author_id(): Author_ID
    {
        return $this->author_id;
    }

    public function get_product_id(): ?ID
    {
        return $this->product_id;
    }

    public function get_certificate_template_id(): ?ID
    {
        return $this->certificate_template_id;
    }

}
