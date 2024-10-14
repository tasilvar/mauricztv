<?php
namespace bpmj\wpidea\controllers\admin;

use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\sales\discount_codes\core\repositories\Interface_Discount_Repository;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Discount_ID;
use Exception;

class Admin_Discounts_Ajax_Controller extends Ajax_Controller
{
    private Interface_Discount_Repository $repository;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Interface_Discount_Repository $repository
    ) {
        $this->repository = $repository;

        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'caps' => [Caps::CAP_MANAGE_DISCOUNTS],
            'allowed_methods' => [Request_Method::POST, Request_Method::GET]
        ];
    }

    public function delete_action(Current_Request $current_request): string
    {
        $id = $current_request->get_query_arg('id');

        try {
            $id = new Discount_ID($id);

            $this->repository->delete($id);
        } catch (Exception $e) {
            return $this->return_as_json(self::STATUS_ERROR, [
                'message' => $e->getMessage()
            ]);
        }

        return $this->return_as_json(self::STATUS_SUCCESS, [
            'message' => $this->translator->translate('discount_codes.actions.delete.success')
        ]);
    }
}