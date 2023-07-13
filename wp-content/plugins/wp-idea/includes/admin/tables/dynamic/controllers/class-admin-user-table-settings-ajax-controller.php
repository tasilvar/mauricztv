<?php

namespace bpmj\wpidea\admin\tables\dynamic\controllers;

use bpmj\wpidea\admin\tables\dynamic\user_settings\Interface_User_Table_Settings_Service;
use bpmj\wpidea\Caps;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;

class Admin_User_Table_Settings_Ajax_Controller extends Ajax_Controller
{
    private Interface_User_Table_Settings_Service $user_table_settings_service;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Interface_User_Table_Settings_Service $user_table_settings_service
    ) {
        $this->user_table_settings_service = $user_table_settings_service;

        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'caps' => [Caps::CAP_MANAGE_POSTS],
            'allowed_methods' => [Request_Method::POST]
        ];
    }

    public function save_user_settings_action(Current_Request $current_request): string {
        $request_body = $current_request->get_decoded_raw_post_data();

        $table_id = $request_body['tableId'];
        $hidden_columns = $request_body['hiddenColumns'];
        $per_page = $request_body['pageSize'];

        $this->user_table_settings_service->save_hidden_columns_option($table_id, $hidden_columns);
        $this->user_table_settings_service->save_results_per_page_option($table_id, $per_page);

        return $this->return_as_json(
            self::STATUS_SUCCESS
        );
    }
}
