<?php
namespace bpmj\wpidea\modules\webhooks\api\controllers;

use bpmj\wpidea\admin\pages\webhooks\Webhooks_Table_Data_Parser;
use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Base_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\data_types\Url;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\modules\webhooks\core\repositories\Interface_Webhook_Repository;
use bpmj\wpidea\modules\webhooks\infrastructure\persistence\Webhook_Query_Criteria;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;
use Exception;
use OutOfBoundsException;

class Admin_Webhooks_Controller extends Base_Controller
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

    public function add_webhook_action(Current_Request $current_request): void
    {
        $wpi_webhook = $current_request->get_request_arg('wpi_webhook');
        if(!$wpi_webhook){
            $this->redirector->redirect_back();
        }

        try {
            $wpi_webhook_url = new Url($wpi_webhook['url']);
        }catch(Exception $e){
            $this->redirector->redirect_back();
        }

        if(!$this->url_exists_in_table($wpi_webhook_url->get_value())){
            $webhook = $this->data_parser->webhook_data_array_to_webhook_model($wpi_webhook);
            $this->webhook_repository->save($webhook);
        }

        $this->redirector->redirect($wpi_webhook['redirect_webhook_page']);
    }

    public function edit_webhook_action(Current_Request $current_request): void
    {
        $wpi_webhook = $current_request->get_request_arg('wpi_webhook');
        if(!$wpi_webhook){
            $this->redirector->redirect_back();
        }

        try {
            $wpi_webhook_url = new Url($wpi_webhook['url']);
        }catch(Exception $e){
            $this->redirector->redirect_back();
        }

        try {
            $wpi_webhook_id  = new ID($wpi_webhook['id']);
        }catch(OutOfBoundsException $e){
            $this->redirector->redirect_back();
        }

        $webhook = $this->data_parser->webhook_data_array_to_webhook_model($wpi_webhook);

        if(!$webhook){
            $this->redirector->redirect_back();
        }

        $this->webhook_repository->update($webhook);

        $this->redirector->redirect($wpi_webhook['redirect_webhook_page']);
    }

    private function url_exists_in_table(string $url): bool
    {
        $criteria = new Webhook_Query_Criteria(null, $url);
        $count = $this->webhook_repository->count_by_criteria($criteria);

        return ($count > 0);
    }
}
