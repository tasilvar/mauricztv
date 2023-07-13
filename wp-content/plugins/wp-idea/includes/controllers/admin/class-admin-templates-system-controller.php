<?php
namespace bpmj\wpidea\controllers\admin;

use bpmj\wpidea\admin\helpers\utils\Snackbar;
use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\controllers\Base_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\templates_system\Templates_System;
use bpmj\wpidea\translator\Interface_Translator;

class Admin_Templates_System_Controller extends Ajax_Controller
{
    private $templates_system;

    private $snackbar;

    public function __construct(
        Access_Control $access_control,
        Templates_System $templates_system,
        Snackbar $snackbar,
        Interface_Translator $transtalor,
        Interface_Redirector $redirector
    ) {
        $this->templates_system = $templates_system;
        $this->snackbar = $snackbar;

        parent::__construct($access_control, $transtalor, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'roles' => Caps::ROLES_ADMINS_SUPPORT,
            'allowed_methods' => [Request_Method::POST]
        ];
    }

    public function enable_new_templates_system_action(Current_Request $current_request): string
    {
        $this->templates_system->set_new_templates_as_enabled_by_user();
        $this->templates_system->show_new_templates_info();

        $this->snackbar->display_message_on_next_request(__('New templates system successfully enabled!', BPMJ_EDDCM_DOMAIN));
        return $this->return_as_json(self::STATUS_SUCCESS);
    }

    public function disable_new_templates_system_action(Current_Request $current_request): string
    {
        $this->templates_system->set_new_templates_as_disabled_by_user();

        $this->snackbar->display_message_on_next_request(__('New templates system successfully disabled!', BPMJ_EDDCM_DOMAIN));
        return $this->return_as_json(self::STATUS_SUCCESS);
    }

    public function disable_as_dev_new_templates_system_action(Current_Request $current_request): string
    {
        $this->templates_system->disable_new_templates();

        $this->snackbar->display_message_on_next_request(__('New templates system successfully disabled!', BPMJ_EDDCM_DOMAIN));
        return $this->return_as_json(self::STATUS_SUCCESS);
    }

    public function hide_new_templates_info_action(Current_Request $current_request): string
    {
        $this->templates_system->hide_new_templates_info();
        return $this->return_as_json(self::STATUS_SUCCESS);
    }
}
