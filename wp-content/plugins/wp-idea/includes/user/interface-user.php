<?php namespace bpmj\wpidea\user;

use bpmj\wpidea\data_types\personal_data\Full_Name;

interface Interface_User
{
    public function __construct(User_ID $id);

    public function get_id(): User_ID;

    public function get_login(): ?string;

    public function set_login(?string $login): Interface_User;

    public function get_email(): ?string;

    public function set_email(?string $email): Interface_User;

    public function get_first_name(): ?string;

    public function full_name(): ?Full_Name;

    public function set_first_name(?string $first_name): Interface_User;

    public function get_last_name(): ?string;

    public function set_last_name(?string $last_name): Interface_User;
}