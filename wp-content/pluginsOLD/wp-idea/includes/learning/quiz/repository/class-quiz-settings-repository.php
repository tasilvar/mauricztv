<?php

declare(strict_types=1);

namespace bpmj\wpidea\learning\quiz\repository;

use bpmj\wpidea\learning\quiz\model\Quiz;
use bpmj\wpidea\learning\quiz\model\Quiz_File;
use bpmj\wpidea\learning\quiz\model\Quiz_File_Collection;
use bpmj\wpidea\learning\quiz\Quiz_ID;
use bpmj\wpidea\learning\quiz\value_object\Quiz_Answers_Preview_Settings;
use bpmj\wpidea\learning\quiz\value_object\Quiz_Attempts_Limit_Settings;
use bpmj\wpidea\learning\quiz\value_object\Quiz_Randomization_Settings;
use bpmj\wpidea\learning\quiz\value_object\Quiz_Time_Limit_Settings;

class Quiz_Settings_Repository implements Interface_Quiz_Settings_Repository
{
    private const OFF = 'off';
    private const ON = 'on';
    private const FILES = 'files';
    private const TEST_QUESTIONS = 'test_questions';
    private const RANDOMIZE_QUESTION_ORDER = 'randomize_question_order';
    private const RANDOMIZE_ANSWER_ORDER = 'randomize_answer_order';
    private const CAN_SEE_ANSWERS_MODE = 'can_see_answers_mode';
    private const ALSO_SHOW_CORRECT_ANSWERS = 'also_show_correct_answers';

    public function find_by_id(Quiz_ID $id_quiz): ?Quiz
    {
        $id = $id_quiz->to_int();

        $post = get_post($id);

        if (!$post) {
            return null;
        }

        $featured_image = $this->get_thumbnail_url_from_post_id($id);
        $subtitle = $this->get_post_meta($id, 'subtitle');
        $level = $this->get_post_meta($id, 'level');
        $duration = $this->get_post_meta($id, 'duration');
        $time_mode = $this->get_post_meta($id, 'time_mode');
        $time = $this->get_post_meta($id, 'time');
		$time_limit_settings = new Quiz_Time_Limit_Settings(
			$time_mode === self::ON,
			(int)($time ?: Quiz_Time_Limit_Settings::DEFAULT_TIME)
		);
        $attempts_mode = $this->get_post_meta($id, 'number_test_attempts_mode');
        $attempts_mode = $attempts_mode === self::ON;
        $attempts_number = $this->get_post_meta($id, 'number_test_attempts');
		$attempts_number = $attempts_number ? (int)$attempts_number : Quiz_Attempts_Limit_Settings::DEFAULT_LIMIT;
		$attempts_limit_settings = new Quiz_Attempts_Limit_Settings($attempts_mode, $attempts_number);
        $evaluated_by_admin_mode = $this->get_post_meta($id, 'evaluated_by_admin_mode');
        $evaluated_by_admin_mode = $evaluated_by_admin_mode === self::ON;
        $randomize_question_order = $this->get_post_meta($id, 'randomize_question_order');
        $randomize_answer_order = $this->get_post_meta($id, 'randomize_answer_order');
		$randomization_settings = new Quiz_Randomization_Settings(
			$randomize_question_order === self::ON,
			$randomize_answer_order === self::ON
		);
	    $questions = $this->get_post_meta($id, 'test_questions');
	    $questions = !empty($questions) ? $questions : [];
	    $points_to_pass = (int)$this->get_post_meta($id, 'test_questions_points_pass');
	    $points_max = (int)$this->get_post_meta($id, 'test_questions_points_all');
        $can_see_answers_mode = $this->get_post_meta($id, 'can_see_answers_mode');
        $can_see_answers_mode = $can_see_answers_mode === self::ON;
        $also_show_correct_answers = $this->get_post_meta($id, 'also_show_correct_answers');
        $also_show_correct_answers = $also_show_correct_answers === self::ON;
		$answers_preview_settings = new Quiz_Answers_Preview_Settings($can_see_answers_mode, $also_show_correct_answers);

        return new Quiz(
            new Quiz_ID((int)$post->ID),
            $post->post_title,
            $post->post_content,
            $post->post_name,
            $subtitle,
            $level,
            $duration,
			$time_limit_settings,
			$attempts_limit_settings,
	        $evaluated_by_admin_mode,
            $randomization_settings,
            $featured_image,
            $this->get_files(new Quiz_ID((int)$post->ID)),
	        $questions,
	        $points_to_pass,
	        $points_max,
            $answers_preview_settings
        );
    }

    public function save(Quiz $quiz): void
    {
        $args = [
            'ID' => $quiz->get_id()->to_int(),
            'post_title' => $quiz->get_name(),
            'post_content' => $quiz->get_description(),
            'post_name' => $quiz->get_slug(),
            'meta_input' => [
                'subtitle' => $quiz->get_subtitle(),
                'level' => $quiz->get_level(),
                'duration' => $quiz->get_duration(),
                'time_mode' => $quiz->get_time_limit_settings()->is_enabled() ? self::ON : self::OFF,
                'time' => $quiz->get_time_limit_settings()->get_time(),
	            'number_test_attempts_mode' => $quiz->get_attempts_limit_settings()->is_enabled() ? self::ON : self::OFF,
	            'number_test_attempts' => $quiz->get_attempts_limit_settings()->get_limit(),
                'evaluated_by_admin_mode' => $quiz->get_evaluated_by_admin() ? self::ON : self::OFF,
                'randomize_question_order' => $quiz->get_randomization_settings()->randomize_questions_order() ? self::ON : self::OFF,
                'randomize_answer_order' => $quiz->get_randomization_settings()->randomize_answers_order() ? self::ON : self::OFF,
                '_thumbnail_id' => $this->get_thumbnail_id_for_quiz($quiz),
                'files' => $this->prepare_files_for_save($quiz->get_files()),
	            'test_questions' => $this->prepare_questions_to_save($quiz),
	            'test_questions_points_pass' => $quiz->get_points_to_pass(),
	            'test_questions_points_all' => $quiz->get_points_max(),
                'can_see_answers_mode' => $quiz->get_answers_preview_settings()->allow_user_to_see_his_answers_for_completed_quiz() ? self::ON : self::OFF,
                'also_show_correct_answers' => $quiz->get_answers_preview_settings()->reveal_correct_answers() ? self::ON : self::OFF,
            ]
        ];

        wp_update_post($args);
    }

    private function get_post_meta(int $id, string $name)
    {
        return get_post_meta($id, $name, true);
    }

    private function get_thumbnail_url_from_post_id(int $post_id): ?string
    {
        $url = get_the_post_thumbnail_url($post_id);

        return $url ?: null;
    }

    private function get_thumbnail_id_for_quiz(Quiz $quiz): ?int
    {
        if (empty($quiz->get_featured_image())) {
            return null;
        }

        $attachment_id = attachment_url_to_postid($quiz->get_featured_image());

        return $attachment_id ?: null;
    }

    private function get_files(Quiz_ID $id): Quiz_File_Collection
    {
        $files = $this->get_post_meta($id->to_int(), self::FILES);
        $files = !empty($files) ? $files : [];

        $collection = Quiz_File_Collection::create();

        foreach ($files as $file_id => $file) {
            $collection->add(Quiz_File::create(
                $file_id,
                $file['desc'],
                $file['url'] ?? wp_get_attachment_url($file_id)
            ));
        }

        return $collection;
    }

    private function prepare_files_for_save(Quiz_File_Collection $quiz_file_collection): array
    {
        $result = [];
        foreach ($quiz_file_collection as $file) {
            /** @var Quiz_File $file */

            $file_id = $file->get_id() ?? $this->get_attachment_id_from_url($file->get_url());

            if (is_null($file_id)) {
                continue;
            }

            $result[$file_id] = [
                'desc' => $file->get_name(),
                'url' => $file->get_url(),
            ];
        }

        return $result;
    }

    private function get_attachment_id_from_url(string $url): ?int
    {
        $attachment_id = attachment_url_to_postid($url);

        return $attachment_id ?: null;
    }

    public function get_questions_by_quiz_id(Quiz_ID $quiz_id): array
    {
        $test_questions = get_post_meta($quiz_id->to_int(), self::TEST_QUESTIONS, true);

        return !empty($test_questions) ? $test_questions : [];
    }

    public function is_randomize_question_order_enabled(Quiz_ID $id): bool
    {
        return $this->get_post_meta($id->to_int(), self::RANDOMIZE_QUESTION_ORDER) === self::ON;
    }

    public function is_randomize_answer_order_enabled(Quiz_ID $id): bool
    {
        return $this->get_post_meta($id->to_int(), self::RANDOMIZE_ANSWER_ORDER) === self::ON;
    }

    public function is_can_see_answers_enabled(Quiz_ID $id): bool
    {
        return $this->get_post_meta($id->to_int(), self::CAN_SEE_ANSWERS_MODE) === self::ON;
    }

    public function is_also_show_correct_answers_enabled(Quiz_ID $id): bool
    {
        return $this->get_post_meta($id->to_int(), self::ALSO_SHOW_CORRECT_ANSWERS) === self::ON;
    }

    private function prepare_questions_to_save(Quiz $quiz): array
    {
        $questions = $quiz->get_questions();

        foreach ($questions as $question_key => $question) {
            if (isset($question['question_comment'])) {
                $questions[$question_key]['question_comment'] = trim($question['question_comment']);
            }
        }

        return $questions;
    }
}