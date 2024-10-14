<?php

namespace bpmj\wpidea\scopes;

class Cron_Scope extends Abstract_Scope
{
    public function check_scope(): bool
    {
        return defined('DOING_CRON') && DOING_CRON;
    }
}
