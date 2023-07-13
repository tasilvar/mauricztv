<?php namespace bpmj\wpidea\user;

class User_Capability
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function get_name(): string
    {
        return $this->name;
    }

    public function equals(self $role): bool
    {
        return $this->get_name() === $role->get_name();
    }
}