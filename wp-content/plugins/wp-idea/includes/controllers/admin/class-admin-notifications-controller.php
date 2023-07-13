<?php
namespace bpmj\wpidea\controllers\admin;

use bpmj\wpidea\notices\User_Notice_Service;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\admin\pages\notifications\Notifications_Page_Renderer;
use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Base_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;

class Admin_Notifications_Controller extends Base_Controller
{

    private const VALUE_DISABLED = 'off';

    private Interface_Settings $settings;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Interface_Settings $settings
    ) {
        $this->settings = $settings;

        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'roles' => Caps::ROLES_ADMINS_SUPPORT,
            'allowed_methods' => [Request_Method::POST]
        ];
    }

    public function save_notifications_action(Current_Request $current_request): void
    {
        $notifications = $current_request->get_body_arg(Notifications_Page_Renderer::INPUT_GROUP_NAME, [
            Current_Request::ALLOW_HTML,
            Current_Request::ALLOW_STYLE_ATTRIBUTES
        ]);

        if(!$notifications){
            $this->redirector->redirect_back();
        }

        $this->settings->set(User_Notice_Service::ALLOW_USER_NOTICE_OPTION_KEY, $notifications[User_Notice_Service::ALLOW_USER_NOTICE_OPTION_KEY] ?? self::VALUE_DISABLED);
        $this->settings->set(User_Notice_Service::USER_NOTICE_CONTENT_OPTION_KEY, $notifications[User_Notice_Service::USER_NOTICE_CONTENT_OPTION_KEY]);
        $this->settings->set(User_Notice_Service::USER_NOTICE_CLOSE_BUTTON_OPTION_KEY, $notifications[User_Notice_Service::USER_NOTICE_CLOSE_BUTTON_OPTION_KEY] ?? self::VALUE_DISABLED);

        $this->redirector->redirect($notifications['redirect_notifications_page']);
    }
}
