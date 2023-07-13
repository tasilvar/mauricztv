<?php
declare(strict_types=1);

namespace bpmj\wpidea\data_types\mail;
use Exception;

class Subject
{
    private $subject;

    public function __construct(string $subject)
    {
        if(empty($subject)) {
            throw new Exception('Invalid message');
        }

        $this->subject = $subject;
    }

    public function get_value(): string
    {
        return $this->subject;
    }
}