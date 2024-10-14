<?php

namespace bpmj\wpidea\modules\search_engine\core\value_objects;

use Iterator;

class Search_Results_Collection implements Iterator
{

    private int $current_position = 0;

    private array $results = [];

    public function add(Search_Result $result): self
    {
        $this->results[] = $result;

        return $this;
    }

    public function remove(Search_Result $result_to_remove): bool
    {
        $result = false;
        foreach ($this->results as $key => $user) {
            if ($user->get_id()->to_int() === $result_to_remove->get_id()->to_int()) {
                unset($this->results[$key]);
                $result = true;
            }
        }

        return $result;
    }

    public function size(): int
    {
        return count($this->results);
    }

    public function get_first(): Search_Result
    {
        return $this->results[array_key_first($this->results)];
    }

    public function current(): Search_Result
    {
        return $this->results[$this->current_position];
    }

    public function next(): void
    {
        ++$this->current_position;
    }

    public function key(): int
    {
        return $this->current_position;
    }

    public function valid(): bool
    {
        return isset($this->results[$this->current_position]);
    }

    public function rewind(): void
    {
        $this->current_position = 0;
    }
}
