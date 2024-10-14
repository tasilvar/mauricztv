<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\quizzes;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\pages\course_editor\Course_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\quiz_editor\Quiz_Editor_Page_Renderer;
use bpmj\wpidea\learning\quiz\Resolved_Quiz;

class Quiz_Table_Data_Parser_For_Display extends Quiz_Table_Data_Parser
{
    public function parse_models_to_plain_array(array $quizzes): array
    {
        $data = [];

        foreach ($quizzes as $quiz) {
            /** @var Resolved_Quiz $quiz */
            $data[] = [
                'id' => $quiz->get_id(),
                'title' => $quiz->get_title(),
                'date' => $quiz->get_completed_at()->format('Y-m-d H:i:s'),
                'points' => $quiz->get_points() . '/' . $quiz->get_points_total(),
                'result' => $quiz->get_result(),
                'result_label' => $this->get_result_label($quiz->get_result()),
                'course' => $quiz->get_course_title(),
                'user_full_name' => $quiz->get_user_full_name() ?? '',
                'user_email' => $quiz->get_user_email(),
                'details_url' => $this->get_details_url($quiz->get_id()),
                'quiz_edit_url' => $this->get_quiz_edit_url($quiz->get_quiz_id()),
                'course_edit_url' => $this->get_course_edit_url($quiz->get_course_id()),
                'student_profile_url' => $this->get_student_profile_url($quiz->get_user_email()),
            ];
        }

        return $data;
    }

    private function get_details_url(int $id): string
    {
        return $this->url_generator->generate_admin_page_url('post.php', [
            'post' => $id,
            'action' => 'edit'
        ]);
    }

    private function get_student_profile_url(string $email): string
    {
        $user = $this->user_repository->find_by_email($email);

        if(!$user) {
            return '';
        }

        return $this->url_generator->generate_admin_page_url('user-edit.php', [
            'user_id' => $user->get_id()->to_int()
        ]);
    }

	private function get_quiz_edit_url(int $quiz_id): string
	{
		return $this->url_generator->generate_admin_page_url('admin.php', [
			'page' => Admin_Menu_Item_Slug::EDITOR_QUIZ,
			Quiz_Editor_Page_Renderer::QUIZ_ID_QUERY_ARG_NAME => $quiz_id
		]);
	}

	private function get_course_edit_url(int $course_id): string
	{
		return $this->url_generator->generate_admin_page_url('admin.php', [
			'page' => Admin_Menu_Item_Slug::EDITOR_COURSE,
			Course_Editor_Page_Renderer::COURSE_ID_QUERY_ARG_NAME => $course_id
		]);
	}
}