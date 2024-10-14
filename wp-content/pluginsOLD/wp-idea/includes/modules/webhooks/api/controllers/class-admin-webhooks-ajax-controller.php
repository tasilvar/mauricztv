<?php

namespace bpmj\wpidea\modules\webhooks\api\controllers;

use bpmj\wpidea\admin\pages\webhooks\Webhooks_Table_Data_Parser;
use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\modules\webhooks\core\repositories\Interface_Webhook_Repository;
use bpmj\wpidea\modules\webhooks\core\value_objects\Webhook_Status;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;

class Admin_Webhooks_Ajax_Controller extends Ajax_Controller
{
    private Interface_Webhook_Repository $webhook_repository;
    private Webhooks_Table_Data_Parser $data_parser;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Interface_Webhook_Repository $webhook_repository,
        Webhooks_Table_Data_Parser $data_parser
    ) {
        $this->webhook_repository = $webhook_repository;
        $this->data_parser = $data_parser;

        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'roles' => Caps::ROLES_ADMINS_SUPPORT,
            'allowed_methods' => [Request_Method::POST]
        ];
    }

    public function delete_action(Current_Request $current_request): string
    {
        $id = $current_request->get_query_arg('id');

        $this->webhook_repository->remove($id);

        return $this->return_as_json(
            self::STATUS_SUCCESS,
            [
                'message' => $this->translator->translate('webhooks.actions.delete.success')
            ]
        );
    }

    public function change_status_action(Current_Request $current_request): string
    {
        $id = $current_request->get_query_arg('id');

        $webhook = $this->webhook_repository->find_by_id($id);

        if (!$webhook) {
            return $this->fail($this->translator->translate('webhooks.actions.status.error.message'));
        }

        $status = $webhook->get_status()->get_value();

        $status = $status ? Webhook_Status::SUSPENDED : Webhook_Status::ACTIVE;

        $wpi_webhook = [
            'id' => $webhook->get_id()->to_int(),
            'url' => $webhook->get_url()->get_value(),
            'type_of_event' => $webhook->get_type_of_event()->get_value(),
            'status' => $status
        ];

        $webhook = $this->data_parser->webhook_data_array_to_webhook_model($wpi_webhook);

        $this->webhook_repository->update($webhook);

        return $this->success([]);
    }
}


