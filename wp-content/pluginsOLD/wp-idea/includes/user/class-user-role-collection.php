<?php namespace bpmj\wpidea\user;

class User_Role_Collection implements \Iterator, \Countable
{
    private array $roles = [];

    private int $current_position = 0;

    public function add(User_Role $role): User_Role_Collection
    {
        $this->roles[] = $role;
        return $this;
    }

    public function count(): int
    {
        return count($this->roles);
    }


    public function current(): User_Role
    {
        return $this->roles[$this->current_position];
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
        return array_key_exists($this->current_position, $this->roles);
    }

    public function rewind(): void
    {
        $this->current_position = 0;
    }

    public function is_empty(): bool
    {
        return empty($this->roles);
    }
}