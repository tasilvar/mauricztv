<?php

namespace bpmj\wpidea\admin\settings\core\entities;

class Setting_Field_Validation_Result
{
    private bool $is_valid = true;

    private array $error_messages = [];

    public function add_error_message(string $message): self
    {
        $this->is_valid = false;
        $this->error_messages[] = $message;
        return $this;
    }

    public function is_valid(): bool
    {
        return $this->is_valid;
    }

    public function error_messages(): array
    {
        return $this->error_messages;
    }
}