<?php

namespace bpmj\wpidea\modules\increasing_sales\api\controllers;

use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\modules\increasing_sales\core\services\Offer_Applier;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;

class Increasing_Sales_Ajax_Controller extends Ajax_Controller
{
    private const SPECIAL_OFFER_ID_FIELD_NAME = 'publigo_special_offer_id';
    private Offer_Applier $offer_applier;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Offer_Applier $offer_applier
    ) {
        $this->offer_applier = $offer_applier;

        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'allowed_methods' => [Request_Method::POST]
        ];
    }

    public function update_cart_action(Current_Request $current_request): string
    {
        $fields_value = $current_request->get_body_arg('fields_value');
        $offer_id = null;

        foreach ($fields_value as $value) {
            $name = $value['name'];
            $value = $value['value'];

            if ($name === self::SPECIAL_OFFER_ID_FIELD_NAME) {
                $offer_id = (int)$value;
            }
        }

        if ($offer_id) {
            $this->offer_applier->find_and_apply_offer_by_offer_id($offer_id);
        }

        return $this->return_as_json(
            self::STATUS_SUCCESS,
            [
                'message' => 'ok'
            ]
        );
    }
}


