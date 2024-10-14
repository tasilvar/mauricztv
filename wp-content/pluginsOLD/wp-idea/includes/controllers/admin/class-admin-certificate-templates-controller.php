<?php
namespace bpmj\wpidea\controllers\admin;

use bpmj\wpidea\admin\helpers\utils\Snackbar;
use bpmj\wpidea\Caps;
use bpmj\wpidea\certificates\Certificate_Template;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\exceptions\Certificate_Template_Not_Found_Exception;
use bpmj\wpidea\exceptions\Name_Exist_Exception;
use bpmj\wpidea\exceptions\No_Required_Variables_Exception;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;

class Admin_Certificate_Templates_Controller extends Ajax_Controller
{
    private $certificate_template;
    private $snackbar;


    public function __construct(Access_Control $access_control, Interface_Translator $translator, Interface_Redirector $redirector, Certificate_Template $certificate_template, Snackbar $snackbar)
    {
        $this->certificate_template = $certificate_template;
        $this->snackbar = $snackbar;
        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'roles' => Caps::ROLES_ADMINS_SUPPORT,
            'allowed_methods' => [Request_Method::POST],
            'rules' => [
                'set_default_action' => [
                    'allowed_methods' => [Request_Method::GET],
                ]
            ]
        ];
    }

    public function add_action(Current_Request $current_request): string
    {
        $page = $current_request->get_body_arg('page', [Current_Request::ALLOW_HTML, Current_Request::ALLOW_STYLE_ATTRIBUTES]);
        $name = $current_request->get_body_arg( 'name');

        if(!$page || !$name){
            $this->redirect_and_display_message($this->translator->translate(No_Required_Variables_Exception::MESSAGE), Certificate_Template::LIST_URL);
        }

        if($this->certificate_template->check_if_name_exists_except_id($name)){
            throw new Name_Exist_Exception($this->translator);
        }

        $certificate = $this->certificate_template;

        $certificate->set_page($page);
        $certificate->set_name($name);
        $certificate->save();

        return $this->return_as_json(self::STATUS_SUCCESS, ['redirect_url' => $certificate->get_edit_url()]);
    }


    public function edit_action(Current_Request $current_request): string
    {

        $page = $current_request->get_body_arg('page', [Current_Request::ALLOW_HTML, Current_Request::ALLOW_STYLE_ATTRIBUTES]);
        $name = $current_request->get_body_arg( 'name');
        $id = $current_request->get_body_arg('id');

        if(!$page || !$name || !$id){
            $this->redirect_and_display_message($this->translator->translate(No_Required_Variables_Exception::MESSAGE), Certificate_Template::LIST_URL);
        }

        if($this->certificate_template->check_if_name_exists_except_id($name, $id)){
            throw new Name_Exist_Exception($this->translator);
        }

        $certificate = $this->certificate_template->find_by_id($id);
        if(!$certificate){
            $this->redirect_and_display_message($this->translator->translate(Certificate_Template_Not_Found_Exception::MESSAGE), Certificate_Template::LIST_URL);
        }


        $certificate->set_page($page);
        $certificate->set_name($name);
        $certificate->save();

        return $this->return_as_json(self::STATUS_SUCCESS, ['redirect_url' => $certificate->get_edit_url()]);
    }

    public function delete_action(Current_Request $current_request): string
    {

        $id = $current_request->get_body_arg('id');

        if(!$id){
            WPI()->snackbar->display_message_on_next_request($this->translator->translate(No_Required_Variables_Exception::MESSAGE), Snackbar::TYPE_ERROR);
            throw new No_Required_Variables_Exception($this->translator);
        }

        $certificate = $this->certificate_template->find_by_id($id);
        $is_default = $certificate->get_is_default();


        if(!$certificate){
            $this->redirect_and_display_message($this->translator->translate(Certificate_Template_Not_Found_Exception::MESSAGE), Certificate_Template::LIST_URL);
        }

        $certificate->delete();

        if($is_default){
            $certificate->set_default_last();
        }

        return $this->return_as_json(self::STATUS_SUCCESS);
    }

    public function set_default_action(Current_Request $current_request): void
    {
        $id = $current_request->get_query_arg('id');

        if(!$id){
            WPI()->snackbar->display_message_on_next_request($this->translator->translate(No_Required_Variables_Exception::MESSAGE), Snackbar::TYPE_ERROR);
            throw new No_Required_Variables_Exception($this->translator);
        }

        $certificate = $this->certificate_template->find_by_id($id);

        if(!$certificate){
            $this->redirect_and_display_message($this->translator->translate(Certificate_Template_Not_Found_Exception::MESSAGE), Certificate_Template::LIST_URL);
        }

        $certificate->set_default();
        $this->redirect(admin_url(Certificate_Template::LIST_URL));
    }

    private function redirect_and_display_message(string $message, string $url): void
    {
        $this->snackbar->display_message_on_next_request($message, Snackbar::TYPE_ERROR);
        $this->redirect(admin_url($url));
    }

}
