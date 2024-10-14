<?php
namespace bpmj\wpidea\controllers;

use bpmj\wpidea\Caps;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\exceptions\No_Permission_For_Course_Exception;
use bpmj\wpidea\exceptions\No_Required_Variables_Exception;
use bpmj\wpidea\exceptions\Quiz_Not_Exist_Exception;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;

class Quiz_Controller extends Base_Controller
{
    public function behaviors(): array
    {
        return [
            'roles' => Caps::ROLES_ADMINS_SUPPORT_SUBSCRIBER,
            'allowed_methods' => [Request_Method::POST]
        ];
    }

    public function finish_quiz_action(Current_Request $current_request): void
    {
        $question = $current_request->get_body_arg('bpmj_eddcm_question');
        $inserted_quiz_id = $current_request->get_body_arg('bpmj_eddcm_add_test_inserted_quiz_id');
        $quiz_post_id = $current_request->get_body_arg('bpmj_eddcm_add_test_quiz_post_id');
        $files = $current_request->get_file('bpmj_eddcm_question') ?? [];


        $_POST['_points'] = WPI()->courses->finish_quiz($inserted_quiz_id,$quiz_post_id, $files, $question);
    }
}
