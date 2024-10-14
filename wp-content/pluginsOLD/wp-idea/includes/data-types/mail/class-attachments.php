<?php
declare(strict_types=1);

namespace bpmj\wpidea\data_types\mail;

class Attachments
{
    private $attachments;

    public function __construct(?array $attachments = array())
    {
        $this->attachments = $attachments;
    }

    public function get_value(): ?array
    {
        return $this->attachments;
    }
}