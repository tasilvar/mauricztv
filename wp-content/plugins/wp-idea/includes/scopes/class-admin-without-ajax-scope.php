<?php

namespace bpmj\wpidea\scopes;


if (!defined('ABSPATH'))
    exit;

class Admin_Without_Ajax_Scope extends Abstract_Scope
{
    protected $dependents_scopes = [
        Admin_Scope::class   => true,
        Ajax_Scope::class    => false
    ];

    public function check_scope(): bool
    {
        return true; // only dependents
    }
}
