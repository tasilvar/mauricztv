<?php

declare(strict_types=1);

namespace bpmj\wpidea\sales\order\client;

use bpmj\wpidea\data_types\personal_data\Full_Name;

class Client
{
    private $id;
    private $full_name;
    private $email;
    private $phone_no;

    public function __construct($id, $email, Full_Name $full_name, $phone_no = '')
    {
        $this->id = $id;
        $this->email = $email;
        $this->phone_no = $phone_no;
        $this->full_name = $full_name;
    }

    public function get_id(): ?int
    {
        return $this->id;
    }
    
    public function get_email(): string
    {
        return $this->email;
    }
     
    public function get_full_name(): string
    {
         return $this->full_name->get_full_name();
    }

    public function get_first_name(): string
    {
        return $this->full_name->get_first_name();
    }

    public function get_last_name(): string
    {
        return $this->full_name->get_last_name();
    }

    public function get_phone_no(): string
    {
        return $this->phone_no;
    }
}