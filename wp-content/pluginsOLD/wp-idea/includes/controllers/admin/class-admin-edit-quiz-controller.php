<?php

namespace bpmj\wpidea\controllers\admin;

use bpmj\wpidea\admin\support\diagnostics\items\Max_Input_Vars;
use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Ajax_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\exceptions\No_Required_Variables_Exception;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\learning\quiz\Quiz_ID;
use bpmj\wpidea\learning\quiz\repository\Interface_Quiz_Settings_Repository;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;
use Exception;


class Admin_Edit_Quiz_Controller extends Ajax_Controller
{
    private Max_Input_Vars $max_input_vars;
	private Interface_Quiz_Settings_Repository $quiz_settings_repository;

	public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Max_Input_Vars $max_input_vars,
		Interface_Quiz_Settings_Repository $quiz_settings_repository
    ) {
        $this->max_input_vars = $max_input_vars;
		$this->quiz_settings_repository = $quiz_settings_repository;

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

	public function save_quiz_structure_action(Current_Request $current_request): string
    {
        $quiz_id = $current_request->get_request_arg('quiz_id');

        $questions = $current_request->get_body_arg('bpmj_eddcm_test_questions');
        $points_to_pass = $current_request->get_body_arg('test_questions_points_pass');
        $points_max = $current_request->get_body_arg('test_questions_points_all');

        if (!$quiz_id) {
            throw new No_Required_Variables_Exception($this->translator);
        }

        if (!$questions) {
            return $this->success_with_warning_message([
                'errorMessage' => $this->translator->translate('quiz_editor.sections.structure.validation.empty_structure')
            ]);
        }

         if(count($questions, COUNT_RECURSIVE) >= $this->max_input_vars->get_current_value()) {
             return $this->success_with_warning_message([
                 'errorMessage' => $this->translator->translate('edit_courses.max_input_vars.error')
             ]);
         }

        try {
			 $quiz = $this->quiz_settings_repository->find_by_id(new Quiz_ID((int)$quiz_id));

			 if(!$quiz) {
				 throw new Exception('No quiz found!');
			 }

			 $quiz->change_questions($questions);
			 $quiz->change_points_to_pass((int)$points_to_pass);
			 $quiz->change_points_max((int)$points_max);

			 $this->quiz_settings_repository->save($quiz);
        } catch (Exception $e) {
            return $this->success_with_warning_message([
                'errorMessage' => $this->translator->translate('quiz_editor.sections.structure.validation.error_message')
            ]);
        }

        return $this->success([
            'successMessage' => $this->translator->translate('quiz_editor.sections.structure.success_message'),
            'quizId' => $quiz_id
        ]);
    }
}
