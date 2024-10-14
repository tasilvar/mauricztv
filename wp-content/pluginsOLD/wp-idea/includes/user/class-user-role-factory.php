<?php namespace bpmj\wpidea\user;

class User_Role_Factory
{
    public function create_from_name(string $role_name): User_Role
    {
        return new User_Role($role_name);
    }

    public function create_many_from_name(array $role_names): User_Role_Collection
    {
        $collection = new User_Role_Collection();

        foreach ($role_names as $role_name) {
            $collection->add($this->create_from_name($role_name));
        }

        return $collection;
    }
}