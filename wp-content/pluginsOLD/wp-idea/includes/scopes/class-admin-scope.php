<?php

namespace bpmj\wpidea\scopes;


if (!defined('ABSPATH'))
    exit;

class Admin_Scope extends Abstract_Scope
{
    public function check_scope(): bool
    {
        return is_admin();
    }
}
