<?php
namespace bpmj\wpidea\controllers;

use bpmj\wpidea\Current_Request;
use bpmj\wpidea\exceptions\Action_Not_Exist_Exception;
use bpmj\wpidea\exceptions\Incorrect_Return_Params_Exception;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\routing\Action;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\View;

abstract class Base_Controller
{
    private $access_control;
    protected $translator;
    protected $status = self::STATUS_SUCCESS;
    protected $action;
    protected $redirector;



    protected const STATUS_SUCCESS = 'success';
    protected const STATUS_ERROR = 'error';

    public function __construct(Access_Control $access_control, Interface_Translator $translator, Interface_Redirector $redirector)
    {
        $this->redirector = $redirector;
        $this->access_control = $access_control;
        $this->translator = $translator;
        $this->load_behaviors();
    }

    protected function after_permission_denied(string $error_message): void
    {
        wp_die( __( $error_message, BPMJ_EDDCM_DOMAIN ), __( 'Error', BPMJ_EDDCM_DOMAIN ), [ 'response' => 403 ] );
    }

    public function before_action(): void
    {
        // do something before run action
    }

    protected function behaviors(): array
    {
        // return array with behaviors
        return [];
    }

    private function load_behaviors(): void
    {
        $behaviors = $this->behaviors();

        if(isset($behaviors['roles'])){
            $this->access_control->set_roles($behaviors['roles']);
        }
        
        if(isset($behaviors['caps'])){
            $this->access_control->set_caps($behaviors['caps']);
        }

        if(isset($behaviors['allowed_methods'])){
            $this->access_control->set_allowed_methods($behaviors['allowed_methods']);
        }

        if(!isset($behaviors['rules'])){
            return;
        }
        foreach ($behaviors['rules'] as $action => $rules){
            if(isset($rules['roles'])){
                $this->access_control->set_roles_for_action($action, $rules['roles']);
            }
            
            if(isset($rules['caps'])){
                $this->access_control->set_caps_for_action($action, $rules['caps']);
            }

            if(isset($rules['allowed_methods'])){
                $this->access_control->set_allowed_methods_for_action($action, $rules['allowed_methods']);
            }
        }
    }

    public function trigger_action(Action $action, Current_Request $current_request)
    {
        $this->action = $action;
        try {
            $this->throw_exception_if_method_does_not_exist($action);

            $this->access_control->check_access($action->get_action_name_with_prefix());
            $this->before_action();
            $return_params = $this->{$action->get_action_name_with_prefix()}($current_request);
            $this->throw_exception_if_incorrect_params_returned($return_params);
            $this->send_success($return_params);

        } catch (\Exception $exception) {
            $this->send_error($exception);
        }
    }

    protected function send_error(\Exception $exception): void
    {
        wp_die( __( $exception->getMessage(), BPMJ_EDDCM_DOMAIN ), __( 'Error', BPMJ_EDDCM_DOMAIN ), [ 'response' => 403 ] );
    }

    protected function send_success($return_params): void
    {
        echo $return_params;
    }

    protected function check_returned_params($return_params): bool
    {
        return true;
    }

    private function throw_exception_if_incorrect_params_returned($return_params): void
    {
        if(!$this->check_returned_params($return_params)){
            throw new Incorrect_Return_Params_Exception($this->translator);
        }
    }

    protected function return_as_json(string $status, array $data = []): string
    {
        $this->status = $status;
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?? '';
    }

    protected function view(string $view_name, array $data = []): string
    {
        return View::get($view_name, $data);
    }

    protected function redirect(string $url): void
    {
        $this->redirector->redirect($url);
        exit;
    }

    protected function admin_view(string $view_name, array $data): string
    {
        return View::get_admin($view_name, $data);
    }

    private function throw_exception_if_method_does_not_exist(Action $action): void
    {
        if(!method_exists($this, $action->get_action_name_with_prefix())){
            throw new Action_Not_Exist_Exception($this->translator);
        }
    }


}
