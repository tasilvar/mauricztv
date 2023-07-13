<?php

namespace bpmj\wpidea\modules\purchase_redirects\api\controllers;

use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\modules\purchase_redirects\core\repositories\Interface_Purchase_Redirect_Repository;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;

class Admin_Purchase_Redirects_Ajax_Controller extends Ajax_Controller
{
    private Interface_Purchase_Redirect_Repository $purchase_redirect_repository;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Interface_Purchase_Redirect_Repository $purchase_redirect_repository
    ) {
        $this->purchase_redirect_repository = $purchase_redirect_repository;

        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'roles' => Caps::ROLES_ADMINS_SUPPORT,
            'caps' => [Caps::CAP_MANAGE_ORDERS],
            'allowed_methods' => [Request_Method::POST]
        ];
    }

    public function update_action(Current_Request $current_request): string
    {
        $request_body = $current_request->get_decoded_raw_post_data();

        $redirections_in_array = json_decode($request_body['data'], true) ?? [];

        $this->purchase_redirect_repository->update($redirections_in_array);

        return $this->return_as_json(
            self::STATUS_SUCCESS
        );
    }
}
