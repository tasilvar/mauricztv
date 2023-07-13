<?php

namespace bpmj\wpidea\modules\increasing_sales\api\controllers;

use bpmj\wpidea\admin\pages\webhooks\Webhooks_Table_Data_Parser;
use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\modules\increasing_sales\infrastructure\persistence\Interface_Offers_Persistence;
use bpmj\wpidea\modules\increasing_sales\infrastructure\persistence\Offer_Query_Criteria;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\modules\increasing_sales\core\value_objects\Increasing_Sales_Offer_Type;

class Admin_Increasing_Sales_Ajax_Controller extends Ajax_Controller
{
    private Interface_Offers_Persistence $offers_persistence;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Interface_Offers_Persistence $offers_persistence
    ) {
        $this->offers_persistence = $offers_persistence;

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

    public function delete_offer_action(Current_Request $current_request): string
    {
        $id = $current_request->get_query_arg('id');

        $this->offers_persistence->delete($id);

        return $this->return_as_json(
            self::STATUS_SUCCESS,
            [
                'message' => $this->translator->translate('increasing_sales.actions.delete.success')
            ]
        );
    }

    public function delete_offer_bulk_action(Current_Request $current_request): string
    {
        $request_body = $current_request->get_decoded_raw_post_data();
        $ids = $request_body['ids'] ?? [];

        foreach ($ids as $id) {
            $this->offers_persistence->delete((int)$id);
        }

        return $this->return_as_json(
            self::STATUS_SUCCESS,
            [
                'message' => $this->translator->translate('increasing_sales.actions.delete.bulk.success')
            ]
        );
    }
}


