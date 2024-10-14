<?php
declare(strict_types=1);

namespace bpmj\wpidea\data_types;

class Error_Status_Code
{
    private int $error_code;

    public function __construct(int $error_code)
    {
        $this->error_code = $error_code;
    }

    public function get_value(): int
    {
      return $this->error_code;
    }
}