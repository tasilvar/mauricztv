<?php

namespace bpmj\wpidea\learning\quiz\model;

use bpmj\wpidea\data_types\collection\Abstract_Iterator;

class Quiz_File_Collection extends Abstract_Iterator
{
    public function add(Quiz_File $item): self
    {
        return $this->add_item($item);
    }

    public function current(): Quiz_File
    {
        return $this->get_current_item();
    }
}