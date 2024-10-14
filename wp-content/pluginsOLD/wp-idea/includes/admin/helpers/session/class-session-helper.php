<?php

namespace bpmj\wpidea\admin\helpers\session;

class Session_Helper
{
    public function is_session_started(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    public function start_session(): void
    {
        if(!$this->is_session_started()) {
            session_start();
        }
    }

    public function close_session(): void
    {
        session_write_close();
    }
}