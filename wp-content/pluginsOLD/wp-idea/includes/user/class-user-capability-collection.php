<?php namespace bpmj\wpidea\user;

class User_Capability_Collection implements \Iterator, \Countable
{
    private array $caps = [];

    private int $current_position = 0;

    public function add(User_Capability $cap): self
    {
        $this->caps[] = $cap;

        return $this;
    }

    public function count(): int
    {
        return count($this->caps);
    }

    public function current(): User_Capability
    {
        return $this->caps[$this->current_position];
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
        return array_key_exists($this->current_position, $this->caps);
    }

    public function rewind(): void
    {
        $this->current_position = 0;
    }

    public function is_empty(): bool
    {
        return empty($this->caps);
    }
}
