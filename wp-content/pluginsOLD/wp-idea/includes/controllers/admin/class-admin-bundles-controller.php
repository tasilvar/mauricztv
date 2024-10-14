<?php

namespace bpmj\wpidea\controllers\admin;

use bpmj\wpidea\app\bundles\Bundles_App_Service;
use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\exceptions\No_Required_Variables_Exception;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\translator\Interface_Translator;

class  Admin_Bundles_Controller extends Ajax_Controller
{
    private Interface_Events $events;
    private Bundles_App_Service $bundles_app_service;
    private Interface_Url_Generator $url_generator;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Bundles_App_Service $bundles_app_service,
        Interface_Url_Generator $url_generator,
        Interface_Events $events
    ) {
        $this->bundles_app_service = $bundles_app_service;
        $this->url_generator = $url_generator;
        $this->events = $events;

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

    public function get_popup_create_bundle_action(Current_Request $current_request): string
    {
        return $this->success(
            [
                'content' => $this->admin_view('/popup/create-product-popup-content', [
                    'title' => $this->translator->translate('bundles_popup_editor.title'),
                    'form_id' => 'bundles_popup_editor',
                    'create_product_url' => $this->get_create_bundle_url(),
                    'translator' => $this->translator
                ])
            ]
        );
    }

    public function delete_action(Current_Request $current_request): string
    {
        $id = $current_request->get_query_arg('id');

        if (!$id) {
            throw new No_Required_Variables_Exception($this->translator);
        }

        $product_id = (int)$id;

        $this->bundles_app_service->delete_product(new Product_ID($product_id));

        return $this->success(
            [
                'message' => $this->translator->translate('bundles_list.actions.delete.success')
            ]
        );
    }

    public function purchase_links_action(Current_Request $current_request): string
    {
        $id = $current_request->get_query_arg('id');

        if (!$id) {
            throw new No_Required_Variables_Exception($this->translator);
        }

        return $this->success(
            [
                'content' => $this->admin_view('/bundles/list/purchase-links-popup-content', [
                    'title' => $this->translator->translate('bundles_list.popup.purchase_links.title'),
                    'content' => $this->bundles_app_service->get_add_to_cart_popup_html(new Product_ID((int)$id))
                ])
            ]
        );
    }

    public function disable_sales_action(Current_Request $current_request): string
    {
        $id = $current_request->get_query_arg('id');

        if (!$id) {
            throw new No_Required_Variables_Exception($this->translator);
        }

        $this->bundles_app_service->toggle_sales(new Product_ID($id));

        return $this->success();
    }

    public function disable_sales_bulk_action(Current_Request $current_request): string
    {
        $request_body = $current_request->get_decoded_raw_post_data();
        $ids = $request_body['ids'] ?? [];

        foreach ($ids as $id) {
            $this->bundles_app_service->toggle_sales(new Product_ID($id));
        }

        return $this->success();
    }

    private function get_create_bundle_url(): string
    {
        return $this->url_generator->generate(Admin_Creator_Controller::class, 'create_bundle', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
        ]);
    }
}
