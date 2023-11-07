<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\users;

use bpmj\wpidea\admin\renderers\Abstract_Page_Renderer;
use bpmj\wpidea\admin\tables\dynamic\Interface_Dynamic_Table;
use bpmj\wpidea\admin\tables\dynamic\module\Dynamic_Tables_Module;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;

class User_Page_Renderer extends Abstract_Page_Renderer
{
    private Interface_Translator $translator;
    private Interface_View_Provider $view_provider;
    private User_Table_Config_Provider $table_config_provider;
    private Dynamic_Tables_Module $dynamic_tables_module;
    private Current_Request $current_request;

    public function __construct(
        Interface_Translator $translator,
        Interface_View_Provider $view_provider,
        User_Table_Config_Provider $table_config_provider,
        Dynamic_Tables_Module $dynamic_tables_module,
        Current_Request $current_request
    ) {
        $this->translator = $translator;
        $this->view_provider = $view_provider;
        $this->table_config_provider = $table_config_provider;
        $this->dynamic_tables_module = $dynamic_tables_module;
        $this->current_request = $current_request;
    }

    public function get_rendered_page(): string
    {
        return $this->view_provider->get_admin('/pages/users/index', [
            'table' => $this->prepare_table(),
            'page_title' => $this->translator->translate('users.page_title'),
            'message' => $this->get_message_based_on_the_set_arg(),
        ]);
    }

    private function prepare_table(): Interface_Dynamic_Table
    {
        
        return $this->dynamic_tables_module->create_table_from_config(
            $this->table_config_provider->get_config()
        );
    }

    private function get_message_based_on_the_set_arg(): ?string
    {
        $reset_count = $this->current_request->get_request_arg('reset_count');
        $action = $this->current_request->get_request_arg('action');
        
        if ($action === 'added') {
            $message = $this->translator->translate('users.actions.added_user.success');
        }

        if ($action === 'deleted') {
            $message = $this->translator->translate('users.actions.delete.success');
        }

        if (is_numeric($reset_count)) {
            $message = ($reset_count > 1) ? sprintf(
                $this->translator->translate('users.actions.send_link.many_users.success'),
                $reset_count
            ) : $this->translator->translate('users.actions.send_link.success');
        }

        return $message ?? null;
    }
}
