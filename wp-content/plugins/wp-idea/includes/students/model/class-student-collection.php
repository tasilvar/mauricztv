<?php

namespace bpmj\wpidea\students\model;

use Iterator;
use bpmj\wpidea\data_types\collection\Abstract_Iterator;

class Student_Collection extends Abstract_Iterator
{
    public function add(Student $item): self
    {
        return $this->add_item($item);
    }

    public function current(): Student
    {
        return $this->get_current_item();
    }
}