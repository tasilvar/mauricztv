<?php

namespace bpmj\wpidea\modules\affiliate_program\api\controllers;

use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\modules\affiliate_program\core\repositories\Interface_Commission_Repository;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Status;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;

class Admin_Affiliate_Ajax_Controller extends Ajax_Controller
{
    private Interface_Commission_Repository $commission_repository;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Interface_Commission_Repository $commission_repository
    ) {
        $this->commission_repository = $commission_repository;

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

    public function change_status_action(Current_Request $current_request): string
    {
        $id = $current_request->get_query_arg('id');
        $status = $current_request->get_query_arg('status');

        $commission = $this->commission_repository->find_by_id($id);
        $commission->change_status(new Status($this->get_new_status($status)));

        $this->commission_repository->update($commission);

        return $this->return_as_json(self::STATUS_SUCCESS, [
            'message' => $this->translator->translate('affiliate_program.actions.change_status.success')
        ]);
    }

    public function change_status_bulk_action(Current_Request $current_request): string
    {
        $request_body = $current_request->get_decoded_raw_post_data();
        $ids = $request_body['ids'] ?? [];

        foreach ($ids as $id) {
            $commission = $this->commission_repository->find_by_id((int)$id);

            if ($commission) {
                $changed_status = $this->get_new_status($commission->get_status()->get_value());
                $commission->change_status(new Status($changed_status));

                $this->commission_repository->update($commission);
            }
        }

        return $this->return_as_json(
            self::STATUS_SUCCESS,
            [
                'message' => $this->translator->translate('affiliate_program.actions.change_status.bulk.success')
            ]
        );
    }

    public function delete_action(Current_Request $current_request): string
    {
        $id = $current_request->get_query_arg('id');

        $this->commission_repository->delete($id);

        return $this->return_as_json(self::STATUS_SUCCESS, [
            'message' => $this->translator->translate('affiliate_program.actions.delete.success')
        ]);
    }

    public function delete_bulk_action(Current_Request $current_request): string
    {
        $request_body = $current_request->get_decoded_raw_post_data();
        $ids = $request_body['ids'] ?? [];

        foreach ($ids as $id) {
            $this->commission_repository->delete((int)$id);
        }

        return $this->return_as_json(
            self::STATUS_SUCCESS,
            [
                'message' => $this->translator->translate('affiliate_program.actions.delete.bulk.success')
            ]
        );
    }

    private function get_new_status(string $status): string
    {
        switch ($status) {
            case Status::STATUS_SETTLED:
                $status = Status::STATUS_UNSETTLED;
                break;
            case Status::STATUS_UNSETTLED:
                $status = Status::STATUS_SETTLED;
                break;
        }

        return $status;
    }
}