<?php

namespace bpmj\wpidea\modules\purchase_redirects\core\services;

use bpmj\wpidea\modules\purchase_redirects\core\repositories\Interface_Purchase_Redirect_Repository;

class Check_Redirects
{
    protected Interface_Purchase_Redirect_Repository $purchase_redirect_repository;
    protected $is_set_redirection = false;

    public function __construct(
        Interface_Purchase_Redirect_Repository $purchase_redirect_repository)
    {
        $this->purchase_redirect_repository = $purchase_redirect_repository;
    }

    public function has_any_redirects(): bool
    {
        $redirection_rules = $this->purchase_redirect_repository->get_redirections_in_array();

        if (!$redirection_rules){
            return false;
        }

        if (!count($redirection_rules) > 0){
            return false;
        }

        return true;
    }
}