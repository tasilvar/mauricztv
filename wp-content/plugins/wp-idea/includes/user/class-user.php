<?php namespace bpmj\wpidea\user;

use bpmj\wpidea\data_types\personal_data\Full_Name;

class User implements Interface_User
{
    private User_ID $id;
    private ?string $login;
    private ?string $email;
    private ?string $first_name;
    private ?string $last_name;

    public function __construct(User_ID $id)
    {
        $this->id = $id;
    }

    public function get_id(): User_ID
    {
        return $this->id;
    }

    public function get_login(): ?string
    {
        return $this->login;
    }

    public function set_login(?string $login): Interface_User
    {
        $this->login = $login;

        return $this;
    }

    public function get_email(): ?string
    {
        return $this->email;
    }

    public function set_email(?string $email): Interface_User
    {
        $this->email = $email;

        return $this;
    }

    public function get_first_name(): ?string
    {
        return $this->first_name;
    }

    public function full_name(): ?Full_Name
    {
        if ($this->get_first_name() || $this->get_last_name()) {
            return new Full_Name($this->get_first_name(), $this->get_last_name());
        }
        return null;
    }

    public function set_first_name(?string $first_name): Interface_User
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function get_last_name(): ?string
    {
        return $this->last_name;
    }

    public function set_last_name(?string $last_name): Interface_User
    {
        $this->last_name = $last_name;

        return $this;
    }
}