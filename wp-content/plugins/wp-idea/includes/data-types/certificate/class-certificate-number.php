<?php
declare(strict_types=1);

namespace bpmj\wpidea\data_types\certificate;
use Exception;

class Certificate_Number
{
    private string $value;

    public function __construct(string $value) {

        if(!preg_match('/[\d]/', $value)){
           throw new Exception('Invalid certificate number');
        }

        $this->value = $value;
    }

    public function get_value(): string
    {
        return $this->value;
    }
}