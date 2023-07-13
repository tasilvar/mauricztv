<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\webhooks;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\tables\dynamic\Interface_Dynamic_Table;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\modules\webhooks\api\controllers\Admin_Webhooks_Controller;
use bpmj\wpidea\modules\webhooks\core\value_objects\Webhook_Types_Of_Events;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\Packages;
use bpmj\wpidea\packages\Interface_Packages_API;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;
use Exception;

class Webhooks_Page_Renderer
{
    private Interface_Translator $translator;
    private Interface_View_Provider $view_provider;
    private Interface_Url_Generator $url_generator;
    private Interface_Webhooks_Form $webhooks_form;
    private Current_Request $current_request;
    private Webhook_Table_Config_Provider $config_provider;
    private Dynamic_Tables_Module $dynamic_tables_module;
    private Interface_Packages_API $packages_api;

    public function __construct(
        Interface_Translator $translator,
        Interface_View_Provider $view_provider,
        Interface_Url_Generator $url_generator,
        Interface_Webhooks_Form $webhooks_form,
        Current_Request $current_request,
        Webhook_Table_Config_Provider $config_provider,
        Dynamic_Tables_Module $dynamic_tables_module,
        Interface_Packages_API $packages_api
    ) {
        $this->translator = $translator;
        $this->view_provider = $view_provider;
        $this->url_generator = $url_generator;
        $this->webhooks_form = $webhooks_form;
        $this->current_request = $current_request;
        $this->config_provider = $config_provider;
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->packages_api = $packages_api;
    }

    public function render_page(): void
    {
        $request_view = $this->current_request->get_request_arg('view');
        if(isset($request_view)) {

            if (('add' === $request_view) || ('edit' === $request_view)) {
                $this->render_action_page();
                return;
            }

            if ('doc' === $request_view) {
                $this->render_documentation_page();
                return;
            }

        }

        $this->get_webhook_page_html();
    }

    public function render_page_wrong_plan(): void
    {
        $info_upgrade_license = $this->packages_api->render_no_access_to_feature_info(Packages::FEAT_WEBHOOKS);

        $this->get_webhook_page_html($info_upgrade_license);
    }

    private function render_action_page(): void
    {
            $fields_form = [];
            $id_webhook = null;

            if($this->current_request->query_arg_exists('id')){
                $id_webhook = (int) $this->current_request->get_request_arg('id');
                $fields_form = $this->webhooks_form->get_data_to_the_form_by_id($id_webhook);
            }

            echo $this->view_provider->get_admin('/pages/webhooks/form', [
                'page_title' => $this->webhooks_form->get_page_title($id_webhook),
                'action'    => $this->get_webhook_action_url($fields_form),
                'fields'    => $fields_form,
                'url_webhook_page' => $this->get_webhook_page_url(),
                'webhook_event_types' => $this->webhooks_form->get_webhook_event_types(),
                'translator' => $this->translator,
            ]);
    }

    private function render_documentation_page(): void
    {
            $type_of_event = null;

            if($this->current_request->query_arg_exists('event')){
                try {
                    $type_of_event = new Webhook_Types_Of_Events($this->current_request->get_request_arg('event'));
                }catch (Exception $e){
                    $type_of_event = null;
                }
            }

            if(!$type_of_event){
                $this->get_webhook_page_html();
            }else{
                $this->get_documentation_page_html($type_of_event);
            }
    }

    private function get_documentation_page_html(Webhook_Types_Of_Events $type_of_event): void
    {
        $page_name = $this->get_documentation_page_name($type_of_event);

        echo $this->view_provider->get_admin('/pages/webhooks/documentation/'.$page_name, [
            'page_title' => $this->translator->translate('webhooks.documentation.title'),
            'type_of_event' => $type_of_event->get_value(),
            'url_webhook_page' => $this->get_webhook_page_url(),
            'translator' => $this->translator
        ]);
    }

    private function get_documentation_page_name(Webhook_Types_Of_Events $type_of_event): string
    {
        switch ($type_of_event->get_value()) {
            case Webhook_Types_Of_Events::ORDER_PAID:
                return 'order-paid';
            case Webhook_Types_Of_Events::QUIZ_FINISHED:
                return 'quiz-finished';
            case Webhook_Types_Of_Events::CERTIFICATE_ISSUED:
                return 'certificate-issued';
            case Webhook_Types_Of_Events::STUDENT_ENROLLED_IN_COURSE:
                return 'student-enrolled-in-course';
            case Webhook_Types_Of_Events::COURSE_COMPLETED:
                return 'course-completed';
        }
    }

    private function get_webhook_page_html(string $info_upgrade_license = ''): void
    {
        echo $this->view_provider->get_admin('/pages/webhooks/index', [
            'table' => $this->prepare_table(),
            'page_title' => $this->translator->translate('webhooks.page_title'),
            'info_upgrade_license' => $info_upgrade_license
        ]);
    }

    private function prepare_table(): Interface_Dynamic_Table
    {
        return $this->dynamic_tables_module->create_table_from_config(
            $this->config_provider->get_config()
        );
    }

    private function get_webhook_action_url(array $fields_form): string
    {
        if($fields_form){
            return $this->url_generator->generate(Admin_Webhooks_Controller::class, 'edit_webhook', [
                Nonce_Handler::DEFAULT_ACTION_NAME => Nonce_Handler::create()
            ]);
        }

        return $this->url_generator->generate(Admin_Webhooks_Controller::class, 'add_webhook', [
            Nonce_Handler::DEFAULT_ACTION_NAME => Nonce_Handler::create()
        ]);
    }

    private function get_webhook_page_url(): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::WEBHOOKS
        ]);
    }

}