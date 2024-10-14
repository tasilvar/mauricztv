<?php

namespace bpmj\wpidea\user;

class User_Presenter
{
    private Interface_User $user;

    public function __construct(
        Interface_User $user
    ) {
        $this->user = $user;
    }

    public function to_array(): array
    {
        return [
            'id' => $this->user->get_id()->to_int(),
            'first_name' => (string)$this->user->get_first_name(),
            'last_name' => (string)$this->user->get_last_name(),
            'full_name' => $this->get_full_name(),
            'login' => (string)$this->user->get_login(),
            'email' => (string)$this->user->get_email(),
        ];
    }

    public function get_full_name(): string
    {
        return trim($this->user->get_first_name() . ' ' . $this->user->get_last_name());
    }
}