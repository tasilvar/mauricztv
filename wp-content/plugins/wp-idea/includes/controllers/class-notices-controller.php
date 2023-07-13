<?php

namespace bpmj\wpidea\controllers;

use bpmj\wpidea\Caps;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\notices\User_Notice_Service;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;

class Notices_Controller extends Ajax_Controller
{

    private $user_notice;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        User_Notice_Service $user_notice
    ) {
        parent::__construct($access_control, $translator, $redirector);
        $this->user_notice = $user_notice;
    }

    public function behaviors(): array
    {
        return [
            'roles'           => Caps::ROLES_ADMINS_SUPPORT_SUBSCRIBER,
            'allowed_methods' => [Request_Method::POST]
        ];
    }

    public function close_notice_action(Current_Request $current_request)
    {
        if ($this->user_notice->should_show_close_button()) {
            $this->user_notice->close_notice_for_current_user();
        }

        return $this->return_as_json(self::STATUS_SUCCESS, []);
    }
}
