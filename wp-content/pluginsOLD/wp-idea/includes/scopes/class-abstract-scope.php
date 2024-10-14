<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\scopes;

abstract class Abstract_Scope
{
    protected $dependents_scopes = [];

    abstract public function check_scope(): bool;

    public function is_current_request_in_scope(): bool
    {
        return $this->check_dependents_scopes() && $this->check_scope();
    }

    private function check_dependents_scopes(): bool
    {
        foreach ($this->dependents_scopes as $dependents_scope => $expected_value) {
            $scope = new $dependents_scope();
            if($scope->is_current_request_in_scope() !== $expected_value){
                return false;
            }
        }
        return true;
    }
}
