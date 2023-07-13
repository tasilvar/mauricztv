<?php

namespace bpmj\wpidea\controllers;

use bpmj\wpidea\Caps;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\integrations\invoices\email\Invoice_Email_Sending_Service;
use bpmj\wpidea\user\Interface_Current_User_Getter;
use bpmj\wpidea\sales\order\Interface_Orders_Repository;

class Orders_Controller extends Ajax_Controller
{
    private Invoice_Email_Sending_Service $invoice_email_sending_service;
    private Interface_Current_User_Getter $current_user_getter;
    private Interface_Orders_Repository $orders_repository;

    public function __construct(
        Invoice_Email_Sending_Service $invoice_email_sending_service,
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Interface_Current_User_Getter $current_user_getter,
        Interface_Orders_Repository $orders_repository
    ) {
        $this->invoice_email_sending_service = $invoice_email_sending_service;
        $this->current_user_getter = $current_user_getter;
        $this->orders_repository = $orders_repository;

        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'roles' => Caps::ROLES_ADMINS_SUPPORT_SUBSCRIBER,
            'allowed_methods' => [Request_Method::POST]
        ];
    }

    public function resend_invoices_for_order_action(Current_Request $current_request): string
    {
        $order_id = $current_request->get_body_arg('order_id');
        $order_id = is_numeric($order_id) ? (int)$order_id : null;

        if(!$order_id) {
            return $this->return_as_json(self::STATUS_ERROR, [
                'error_details' => 'No order ID provided.',
                'message' => htmlspecialchars($this->translator->translate('user_account.orders.send_invoice_again.something_went_wrong'))
            ]);
        }

        if(!$this->order_belongs_to_user($order_id)) {
            return $this->return_as_json(self::STATUS_ERROR, [
                'error_details' => 'No permissions to perform this action.',
                'message' => htmlspecialchars($this->translator->translate('user_account.orders.send_invoice_again.something_went_wrong'))
            ]);
        }

        $this->invoice_email_sending_service->send_invoices_for_order_by_email($order_id);

        return $this->return_as_json(self::STATUS_SUCCESS, [
            'message' => htmlspecialchars($this->translator->translate('user_account.orders.send_invoice_again.done'))
        ]);
    }

    private function order_belongs_to_user(int $order_id): bool
    {
        try {
            $order_user_id = $this->orders_repository->find_by_id($order_id)->get_user_id();
            $current_user_id = $this->current_user_getter->get()->get_id()->to_int();
        } catch (\Exception|\Error $e) {
            return false;
        }

        return $order_user_id === $current_user_id;
    }
}
