<?php

namespace bpmj\wpidea\controllers\admin;

use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\exceptions\No_Required_Variables_Exception;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\sales\product\service\Product_Creator_Service;
use bpmj\wpidea\translator\Interface_Translator;

class Admin_Edit_Product_Controller extends Ajax_Controller
{
    private Product_Creator_Service $product_creator_service;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Product_Creator_Service $product_creator_service
    ) {
        $this->product_creator_service = $product_creator_service;

        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'roles' => Caps::ROLES_ADMINS_SUPPORT,
            'caps' => [Caps::CAP_MANAGE_PRODUCTS],
            'allowed_methods' => [Request_Method::POST],
        ];
    }

    public function get_variable_prices_action(Current_Request $current_request): string
    {
        $post_id = $current_request->get_request_arg('post_id');

        if (!$post_id) {
            throw new No_Required_Variables_Exception($this->translator);
        }

        $data = $this->product_creator_service->get_variable_prices($post_id);

        return $this->return_as_json(self::STATUS_SUCCESS, ['html' => $data]);
    }

    public function save_variable_prices_action(Current_Request $current_request): string
    {
        $product_id = $current_request->get_request_arg('product_id');
        if (!$product_id) {
            throw new No_Required_Variables_Exception($this->translator);
        }

        $fields = [
            '_edd_price_options_mode' => $current_request->get_body_arg('_edd_price_options_mode'),
            'edd_variable_prices' => $current_request->get_body_arg('edd_variable_prices'),
            '_edd_default_price_id' => $current_request->get_body_arg('_edd_default_price_id')
        ];

        $data = $this->product_creator_service->save_variable_prices((int)$product_id, $fields);

        return $this->return_as_json(self::STATUS_SUCCESS, $data);
    }
}