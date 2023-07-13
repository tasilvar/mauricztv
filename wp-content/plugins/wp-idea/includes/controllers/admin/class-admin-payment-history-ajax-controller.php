<?php

namespace bpmj\wpidea\controllers\admin;

use bpmj\wpidea\sales\order\Interface_Orders_Repository;
use bpmj\wpidea\sales\order\services\Interface_Orders_Service;
use bpmj\wpidea\admin\pages\payments_history\Payments_Data_Exporter;
use bpmj\wpidea\admin\pages\payments_history\Payments_Page_Renderer;
use bpmj\wpidea\Caps;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\sales\order\Order_Query_Criteria;
use bpmj\wpidea\admin\pages\payments_history\Payment_Table_Row_Parser;

class Admin_Payment_History_Ajax_Controller extends Ajax_Controller
{
    private Interface_Orders_Repository $payment_repository;

    private Interface_Orders_Service $payments_service;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Interface_Orders_Repository $payment_repository,
        Interface_Orders_Service $payments_service
    ) {
        $this->payment_repository = $payment_repository;
        $this->payments_service = $payments_service;

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

    public function resend_email_action(Current_Request $current_request): string
    {
        $id = $current_request->get_query_arg('id');

        $this->payments_service->resend_notification($id);

        return $this->return_as_json(
            self::STATUS_SUCCESS,
            [
                'message' => $this->translator->translate('orders.actions.resend.success')
            ]
        );
    }

    public function resend_email_bulk_action(Current_Request $current_request): string
    {
        $request_body = $current_request->get_decoded_raw_post_data();
        $ids = $request_body['ids'] ?? [];

        foreach ($ids as $id) {
            $this->payments_service->resend_notification((int)$id);
        }

        return $this->return_as_json(
            self::STATUS_SUCCESS,
            [
                'message' => $this->translator->translate('orders.actions.resend.bulk.success')
            ]
        );
    }

    public function delete_action(Current_Request $current_request): string
    {
        $id = $current_request->get_query_arg('id');

        $this->payment_repository->remove($id);

        return $this->return_as_json(
            self::STATUS_SUCCESS,
            [
                'message' => $this->translator->translate('orders.actions.delete.success')
            ]
        );
    }

    public function delete_bulk_action(Current_Request $current_request): string
    {
        $request_body = $current_request->get_decoded_raw_post_data();
        $ids = $request_body['ids'] ?? [];

        foreach ($ids as $id) {
            $this->payment_repository->remove((int)$id);
        }

        return $this->return_as_json(
            self::STATUS_SUCCESS,
            [
                'message' => $this->translator->translate('orders.actions.delete.bulk.success')
            ]
        );
    }
}
