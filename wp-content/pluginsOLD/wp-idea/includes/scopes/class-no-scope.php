<?php

namespace bpmj\wpidea\scopes;

class No_Scope extends Abstract_Scope
{
    public function check_scope(): bool
    {
        return true;
    }
}
