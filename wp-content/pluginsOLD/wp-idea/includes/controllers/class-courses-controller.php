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

class Courses_Controller extends Ajax_Controller
{
    public function __construct(Access_Control $access_control, Interface_Translator $translator, Interface_Redirector $redirector)
    {
        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'roles' => Caps::ROLES_ADMINS_SUPPORT_SUBSCRIBER,
            'caps'  => [Caps::CAP_MANAGE_PRODUCTS],
            'allowed_methods' => [Request_Method::POST],
            'rules' => [
                'finish_quiz_action' => [
                    'roles' => Caps::ROLES_ADMINS_SUPPORT_SUBSCRIBER,
                    'allowed_methods' => [Request_Method::POST]
                ]
            ]
        ];
    }

    public function update_course_progress_action(Current_Request $current_request): string
    {
        $finished =  $current_request->get_body_arg('finished') ? true : false;
        $course_page_id = (int)$current_request->get_body_arg('course_page_id');
        $lesson_page_id = (int)$current_request->get_body_arg('lesson_page_id');

        $this->throw_exception_if_current_user_has_no_access_to_course($course_page_id);

        $data = WPI()->courses->update_course_progress($finished, $course_page_id, $lesson_page_id);

        return $this->return_as_json(self::STATUS_SUCCESS, $data);
    }

    public function get_quiz_action(Current_Request $current_request): string
    {
        $quiz_post_id = (int)$current_request->get_body_arg('quiz_post_id');
        $course_page_id =  (int)$current_request->get_body_arg('course_post_id');

        $this->throw_exception_if_current_user_has_no_access_to_course($course_page_id);

        $data = WPI()->courses->get_quiz($quiz_post_id, $course_page_id);


        return $this->return_as_json(self::STATUS_SUCCESS, ['quiz' => $data]);
    }

    public function update_quiz_action(Current_Request $current_request)
    {
        if ($current_request->get_body_arg('bpmj_eddcm_add_test') === null) {
            throw new No_Required_Variables_Exception($this->translator);
        }

        $inserted_quiz_id = $current_request->get_body_arg('bpmj_eddcm_add_test_inserted_quiz_id');
        $user_answers = $current_request->get_body_arg('bpmj_eddcm_question');
        $course_page_id = get_post_meta( $inserted_quiz_id, 'course_id', true );

        if(!$course_page_id){
            throw new Quiz_Not_Exist_Exception($this->translator);
        }

        $this->throw_exception_if_current_user_has_no_access_to_course($course_page_id);

        WPI()->courses->update_quiz($inserted_quiz_id, $user_answers);

        return $this->return_as_json(self::STATUS_SUCCESS, []);
    }

    public function finish_quiz_action(Current_Request $current_request): void
    {
        $question = $current_request->get_body_arg('bpmj_eddcm_question');
        $inserted_quiz_id = $current_request->get_body_arg('bpmj_eddcm_add_test_inserted_quiz_id');
        $quiz_post_id = $current_request->get_body_arg('bpmj_eddcm_add_test_quiz_post_id');
        $files = $current_request->get_file('bpmj_eddcm_question') ?? [];


        $_POST['_points'] = WPI()->courses->finish_quiz($inserted_quiz_id,$quiz_post_id, $files, $question);
    }

    private function throw_exception_if_current_user_has_no_access_to_course(int $course_page_id): void
    {
        $product = WPI()->courses->get_product_by_page_id($course_page_id);
        if(!$product->currentUserHasNoContentAccess()){
            throw new No_Permission_For_Course_Exception($this->translator);
        }
    }
}
