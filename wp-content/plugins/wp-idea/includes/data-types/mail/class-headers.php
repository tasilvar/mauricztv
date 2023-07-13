<?php
declare(strict_types=1);

namespace bpmj\wpidea\data_types\mail;

class Headers
{
    private $headers;

    public function __construct(?string $headers = '')
    {
        $this->headers = $headers;
    }

    public function get_value(): ?string
    {
        return $this->headers;
    }
}