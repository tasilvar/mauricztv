<?php
namespace bpmj\wpidea\controllers;

use bpmj\wpidea\Current_Request;
use bpmj\wpidea\exceptions\Invalid_Token_Exception;
use bpmj\wpidea\exceptions\Method_Not_Allowed_Exception;
use bpmj\wpidea\exceptions\No_Permission_To_Run_Action_Exception;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\wolverine\user\User;

if (!defined('ABSPATH')) {
    exit;
}

class Access_Control
{
    private $nonce;

    private $roles;
    private $caps;
    private $allowed_methods;
    private $current_request;
    private $translator;

    private $action_roles;
    private $action_caps;
    private $action_allowed_methods;

    public function __construct(Nonce_Handler $nonce, Current_Request $current_request, Interface_Translator $translator)
    {
        $this->nonce = $nonce;
        $this->current_request = $current_request;
        $this->translator = $translator;
    }

    public function check_access(string $action_name): bool
    {
        
        if(!($this->check_roles($action_name) || $this->check_caps($action_name))){
            throw new No_Permission_To_Run_Action_Exception($this->translator);
        }
       
        if(!$this->check_allowed_methods($action_name)){
            throw new Method_Not_Allowed_Exception($this->translator);
        }

        if(!$this->is_action_publicly_accessible($action_name) && !$this->check_nonce()){
            throw new Invalid_Token_Exception($this->translator);
        }

        return true;
    }

    private function check_roles(string $action_name): bool
    {
        $roles = $this->action_roles[$action_name] ?? $this->roles ?? [];
        $are_caps_empty = $this->are_caps_empty($action_name);
        
        if(!$roles && $are_caps_empty){
            return true;
        }

        return $this->check_user_permission($roles);
    }
    
       private function check_caps(string $action_name): bool 
    {
        $caps = $this->action_caps[$action_name] ?? $this->caps ?? null;
         
        if(!$caps){
            return false;
        } 

        return $this->check_user_caps($caps);
    }
    
    private function are_caps_empty(string $action_name): bool 
    {
        $caps = $this->action_caps[$action_name] ?? $this->caps ?? null;
        if (!$caps) return true;
        
        return false;
    }

    private function is_action_publicly_accessible(string $action_name): bool
    {
        $roles = $this->action_roles[$action_name] ?? $this->roles ?? null;
        if(!$roles){
            return true;
        }

        return false;
    }

    private function check_allowed_methods(string $action_name): bool
    {
        $allowed_methods = $this->action_allowed_methods[$action_name] ?? $this->allowed_methods ?? null;
        if(!$allowed_methods){
            return true;
        }

        return $this->check_request_methods($allowed_methods);
    }

    private function check_request_methods(array $allowed_methods): bool
    {
        if (in_array($this->current_request->get_request_method(), $allowed_methods, true)) {
            return true;
        }
        return false;
    }

    private function check_user_permission(array $roles): bool
    {
        return User::currentUserHasAnyOfTheRoles( $roles );
    }
    
    private function check_user_caps(array $caps): bool
    {
        return  User::currentUserHasAnyOfTheCapabilities( $caps );
    }

    private function check_nonce(): bool
    {
        return $this->nonce->verify();
    }

    public function set_roles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }
    
    public function set_caps(array $caps): self
    {
        $this->caps = $caps;
        return $this;
    }

    public function set_allowed_methods(array $allowed_methods): self
    {
        $this->allowed_methods = $allowed_methods;
        return $this;
    }

    public function set_roles_for_action(string $action_name, array $roles): self
    {
        $this->action_roles[$action_name] = $roles;
        return $this;
    }
    
    public function set_caps_for_action(string $action_name, array $caps): self
    {
        $this->action_caps[$action_name] = $caps;
        return $this;
    }

    public function set_allowed_methods_for_action(string $action_name, array $allowed_methods): self
    {
        $this->action_allowed_methods[$action_name] = $allowed_methods;
        return $this;
    }


}
