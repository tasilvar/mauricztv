<?php

namespace bpmj\wpidea\students\model;

use bpmj\wpidea\data_types\mail\Email_Address;
use bpmj\wpidea\data_types\personal_data\Full_Name;
use bpmj\wpidea\data_types\String_VO;
use bpmj\wpidea\students\vo\Student_ID;

class Student
{
    public String_VO $login;
    private Student_ID $id;
    private Full_Name $full_name;
    private Email_Address $email;

    private function __construct(
        Student_ID $id,
        Full_Name $full_name,
        Email_Address $email,
        String_VO $login
    )
    {
        $this->id = $id;
        $this->full_name = $full_name;
        $this->email = $email;
        $this->login = $login;
    }

    public static function create(
        Student_ID $id,
        Full_Name $full_name,
        Email_Address $email,
        String_VO $login
    ): self
    {
        return new self(
            $id,
            $full_name,
            $email,
            $login
        );
    }

    public function get_id(): Student_ID
    {
        return $this->id;
    }

    public function get_full_name(): Full_Name
    {
        return $this->full_name;
    }

    public function get_email(): Email_Address
    {
        return $this->email;
    }

    public function get_login(): String_VO
    {
        return $this->login;
    }

    public function to_array(): array
    {
        return [
            'id' => $this->get_id()->to_int(),
            'first_name' => $this->full_name->get_first_name(),
            'last_name' => $this->full_name->get_last_name(),
            'full_name' => $this->full_name->get_full_name(),
            'login' => $this->login->get_value(),
            'email' => $this->email->get_value(),
        ];
    }
}