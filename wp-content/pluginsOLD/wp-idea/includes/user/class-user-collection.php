<?php namespace bpmj\wpidea\user;

use \Iterator;

class User_Collection implements Iterator
{

    private int $current_position = 0;

    private array $users = [];

    public function add(Interface_User $user): User_Collection
    {
        $this->users[] = $user;

        return $this;
    }

    public function remove(Interface_User $user_to_remove): bool
    {
        $result = false;
        foreach ($this->users as $key => $user) {
            if ($user->get_id()->to_int() === $user_to_remove->get_id()->to_int()) {
                unset($this->users[$key]);
                $result = true;
            }
        }

        return $result;
    }

    public function to_array(): array
    {
        $result = [];

        foreach ($this->users as $user) {
            $presenter = new User_Presenter($user);
            $result[]  = $presenter->to_array();
        }

        return $result;
    }

    public function size(): int
    {
        return sizeof($this->users);
    }

    public function get_first(): User
    {
        return $this->users[array_key_first($this->users)];
    }

    public function current(): Interface_User
    {
        return $this->users[$this->current_position];
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
        return isset($this->users[$this->current_position]);
    }

    public function rewind(): void
    {
        $this->current_position = 0;
    }
}