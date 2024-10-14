<?php

namespace bpmj\wpidea\controllers\admin;

use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\exceptions\No_Required_Variables_Exception;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\modules\opinions\api\Opinions_API;
use bpmj\wpidea\modules\opinions\core\value_objects\Opinion_Status;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;

class Admin_Opinions_Controller extends Ajax_Controller
{
    private Opinions_API $opinions_api;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Opinions_API $opinions_api
    ) {
        $this->opinions_api = $opinions_api;

        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'roles' => Caps::ROLES_ADMINS_SUPPORT,
            'caps' => [Caps::CAP_MANAGE_PRODUCTS],
            'allowed_methods' => [Request_Method::POST],
        ];
    }

    public function change_status_action(Current_Request $current_request):string
    {
        $id = $current_request->get_query_arg('id');
        $new_status = $current_request->get_query_arg('new_status');

        if (!$id) {
            throw new No_Required_Variables_Exception($this->translator);
        }

        $this->opinions_api->change_status(new ID($id), new Opinion_Status($new_status));

        return $this->success();
    }
}