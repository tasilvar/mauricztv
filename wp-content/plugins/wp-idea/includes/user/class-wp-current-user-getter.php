<?php namespace bpmj\wpidea\user;

class Wp_Current_User_Getter implements Interface_Current_User_Getter
{
    private $repository;

    public function __construct(User_Wp_Repository $repository)
    {
        $this->repository = $repository;
    }

    public function get(): ?Interface_User
    {
        $current_user_id = get_current_user_id();
        return $current_user_id ? $this->repository->find_by_id(new User_ID($current_user_id)) : null;
    }
}