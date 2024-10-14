<?php

namespace bpmj\wpidea\modules\affiliate_program\api\controllers;

use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Base_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\modules\affiliate_program\core\services\Interface_External_Landing_Link_Service;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\{External_Url, Product_ID};
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;
use Exception;
use OutOfBoundsException;

class Admin_External_Landing_Link_Controller extends Base_Controller
{
    private Interface_External_Landing_Link_Service $external_landing_link_service;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Interface_External_Landing_Link_Service $external_landing_link_service
    ) {
        $this->external_landing_link_service = $external_landing_link_service;

        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'roles' => Caps::ROLES_ADMINS_SUPPORT,
            'caps' => [Caps::CAP_MANAGE_SETTINGS],
            'allowed_methods' => [Request_Method::POST]
        ];
    }

    public function add_action(Current_Request $current_request): void
    {
        $redirection_link = $current_request->get_request_arg('redirection_link');

        if (!$redirection_link) {
            $this->redirector->redirect_back();
        }

        try {
            $product_id = new Product_ID($redirection_link['product']);
            $url = new External_Url($redirection_link['url']);
            
            $this->external_landing_link_service->add($product_id, $url);
        } catch (Exception | OutOfBoundsException $e) {
            $this->redirector->redirect_back();
        }

        $this->redirector->redirect($redirection_link['go_back_url']);
    }
}