<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\course;

interface Interface_Readable_Author_Repository
{
    public function find(Author_ID $id): Author;
}