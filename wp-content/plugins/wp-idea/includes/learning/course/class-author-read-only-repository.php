<?php
declare(strict_types=1);

namespace bpmj\wpidea\learning\course;

class Author_Read_Only_Repository implements Interface_Readable_Author_Repository
{
    private const META_FIRST_NAME = 'first_name';
    private const META_LAST_NAME = 'last_name';

    public function find(Author_ID $id): Author
    {
        return new Author(
            $id,
            $this->get_author_first_name($id),
            $this->get_author_last_name($id)
        );
    }

    protected function get_author_first_name(Author_ID $author_id): string
    {
        return get_the_author_meta( self::META_FIRST_NAME, $author_id->to_int() );
    }

    protected function get_author_last_name(Author_ID $author_id): string
    {
        return get_the_author_meta( self::META_LAST_NAME, $author_id->to_int() );
    }
}