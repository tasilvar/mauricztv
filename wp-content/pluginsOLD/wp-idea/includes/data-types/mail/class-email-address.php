<?php
declare(strict_types=1);

namespace bpmj\wpidea\data_types\mail;
use Exception;

class Email_Address
{
    private $email_adress;

    public function __construct(string $email_adress)
    {
        if(!filter_var($email_adress, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid e-mail');
        }

        $this->email_adress = $email_adress;
    }

    public function get_value(): string
    {
      return $this->email_adress;
    }
}