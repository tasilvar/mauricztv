<?php

namespace bpmj\wpidea\scopes;


if (!defined('ABSPATH'))
    exit;

class Ajax_Scope extends Abstract_Scope
{
    public function check_scope(): bool
    {
        return wp_doing_ajax();
    }
}
