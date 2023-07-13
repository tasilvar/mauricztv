<?php namespace bpmj\wpidea\user;

class User_Capability_Factory
{
    public function create_from_name(string $cap_name): User_Capability
    {
        return new User_Capability($cap_name);
    }

    public function create_many_from_names(array $cap_names): User_Capability_Collection
    {
        $collection = new User_Capability_Collection();

        foreach ($cap_names as $cap_name) {
            $collection->add($this->create_from_name($cap_name));
        }

        return $collection;
    }
}
