<?php
namespace bpmj\wpidea\controllers\admin;

use bpmj\wpidea\admin\pages\logs\Logs_Table_Data_Parser;
use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\controllers\Base_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\Helper;
use bpmj\wpidea\helpers\Interface_Debug_Helper;
use bpmj\wpidea\infrastructure\logs\persistence\Log_Query_Criteria;
use bpmj\wpidea\infrastructure\logs\repository\Interface_Log_Repository;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;

class Admin_Logs_Controller extends Base_Controller
{
    /**
     * @var Interface_Log_Repository
     */
    private $log_repository;

    /** @var Interface_Debug_Helper  */
    private $debug_helper;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Interface_Log_Repository $log_repository,
        Interface_Debug_Helper $debug_helper
    ) {
        $this->log_repository = $log_repository;
        $this->debug_helper   = $debug_helper;

        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'roles' => Caps::ROLES_ADMINS_SUPPORT,
            'allowed_methods' => [Request_Method::GET]
        ];
    }

    public function delete_all_logs_action(Current_Request $current_request): void
    {
        if(!$this->debug_helper->is_dev_mode_enabled()) {
            $this->redirector->redirect_back();
            return;
        }
        
        $this->log_repository->remove_by_criteria(new Log_Query_Criteria());

        $this->redirector->redirect_back();
    }
}
