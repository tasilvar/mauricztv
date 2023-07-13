<?php
declare(strict_types=1);

namespace bpmj\wpidea\data_types\mail;
use Exception;

class Message
{
    private $message;

    public function __construct(string $message)
    {
        if(empty($message)) {
            throw new Exception('Invalid message');
        }

        $this->message = $message;
    }

    public function get_value(): string
    {
        return $this->message;
    }

}