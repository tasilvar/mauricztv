<?php namespace bpmj\wpidea\controllers\admin;

use bpmj\wpidea\Caps;
use bpmj\wpidea\certificates\Certificate_ID;
use bpmj\wpidea\certificates\Interface_Certificate_Repository;
use bpmj\wpidea\certificates\regenerator\Interface_Certificate_Regenerator;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;

class Admin_Certificates_Ajax_Controller extends Ajax_Controller
{
    private Interface_Certificate_Repository $certificate_repository;

    private Interface_Certificate_Regenerator $certificate_regenerator;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Interface_Certificate_Repository $certificate_repository,
        Interface_Certificate_Regenerator $certificate_regenerator
    ) {
        $this->certificate_repository  = $certificate_repository;
        $this->certificate_regenerator = $certificate_regenerator;
        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'caps'            => [Caps::CAP_MANAGE_CERTIFICATES],
            'allowed_methods' => [Request_Method::POST, Request_Method::GET]
        ];
    }

    public function regenerate_action(Current_Request $current_request): void
    {
        $cert_id = $current_request->get_query_arg('id');
        $cert    = $this->certificate_repository->find_by_id(new Certificate_ID($cert_id));
        $this->certificate_regenerator->regenerate($cert);

        $this->redirector->redirect_back();
    }
}