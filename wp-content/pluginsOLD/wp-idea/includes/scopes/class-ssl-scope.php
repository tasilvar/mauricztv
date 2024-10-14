<?php

namespace bpmj\wpidea\scopes;

class Ssl_Scope extends Abstract_Scope
{
    public function check_scope(): bool
    {
        return is_ssl();
    }
}
