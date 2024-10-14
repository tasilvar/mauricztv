<?php

namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\learning\quiz\api\Quiz_Api_Static_Helper;
use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\View;

class Course_Quiz_Content_Block extends Block
{
    const BLOCK_NAME = 'wpi/course-quiz-content';

    private const BPMJ_WPI_QUIZ_SETTINGS_I18N = 'BPMJ_WPI_QUIZ_SETTINGS_I18N';

    public function __construct() {
        parent::__construct();
        $this->title = __('Course Quiz Content Block', BPMJ_EDDCM_DOMAIN);
    }

    public function get_content_to_render($atts)
    {
        $quiz_post_id = get_the_ID();
        $user_email = wp_get_current_user()->user_email;

        $query = new \WP_Query( array(
            'post_status' => 'draft',
            'post_type' => 'tests',
            'meta_query' => array(
                array(
                    'key'    => 'quiz_id',
                    'value'  => $quiz_post_id,
                ),
                array(
                    'key'   => 'user_email',
                    'value' => $user_email,
                ),
            ),
        ) );

        $is_ongoing_quiz = false;
        $is_time_quiz = false;
        $inserted_quiz_id = null;

        $is_solved_quiz = false;
        $evaluated_by_admin = get_post_meta( $quiz_post_id, 'evaluated_by_admin_mode', true );
        $can_see_answers = Quiz_Api_Static_Helper::is_can_see_answers_enabled($quiz_post_id);
        $user_answers = [];
        $points = [];
        $is_passed = false;
        $pass_points = 0;
        $all_points = 0;
        $all_points_string = '';

        if ( $query->post_count > 0 ) {
            $is_ongoing_quiz = true;

            $inserted_quiz_id = $query->post->ID;

            $time_mode = get_post_meta( $quiz_post_id, 'time_mode', true );
            if ($time_mode == 'on') {

                $time_end = get_post_meta($inserted_quiz_id, 'time_end', true);

                if (!empty($time_end)) {
                    $is_time_quiz = true;
                    $time = $time_end - time();
                    $time *= 0.016667;
                }
            }

            $questions = Quiz_Api_Static_Helper::get_configuration_of_questions($inserted_quiz_id);
            $time_is_up = Quiz_Api_Static_Helper::get_time_is_up($inserted_quiz_id);

        } else {
            $query = new \WP_Query( array(
                'post_status' => 'publish',
                'post_type' => 'tests',
                'meta_query' => array(
                    array(
                        'key'    => 'quiz_id',
                        'value'  => $quiz_post_id,
                    ),
                    array(
                        'key'   => 'user_email',
                        'value' => $user_email,
                    ),
                ),
            ) );

            if ( $query->post_count > 0 ) {
                $is_solved_quiz = true;
                $user_answers = get_post_meta( $query->post->ID, 'user_answers', true );
                $points = get_post_meta( $query->post->ID, 'points', true );
                $is_passed = get_post_meta($query->post->ID, 'is_passed', true);
                $pass_points = get_post_meta( $quiz_post_id, 'test_questions_points_pass', true );
                $all_points = get_post_meta( $quiz_post_id, 'test_questions_points_all', true );
                $questions = Quiz_Api_Static_Helper::get_configuration_of_questions($query->post->ID);
                $time_is_up = Quiz_Api_Static_Helper::get_time_is_up($query->post->ID);
                $all_points_string = '/' . $all_points;

                if ( (int) $all_points === 0)
                    $all_points_string = '';
            }
        }
        $course_id = get_post_meta( get_the_ID(), '_bpmj_eddcm', true );

        $content = get_post_field('post_content', $quiz_post_id);

        $this->register_scripts();

        $number_test_attempts = $this->get_number_test_attempts($quiz_post_id);
        $user_solving_quiz_attempts_count = Quiz_Api_Static_Helper::get_user_solving_quiz_attempts_count($user_email, $quiz_post_id);

        return View::get('/course/quiz/content', [
            'is_ongoing_quiz' => $is_ongoing_quiz,
            'is_time_quiz' => $is_time_quiz,
            'inserted_quiz_id' => $inserted_quiz_id,
            'questions' => !empty($questions) ? $questions : Quiz_Api_Static_Helper::get_questions_for_single_test($quiz_post_id),
            'is_solved_quiz' => $is_solved_quiz,
            'evaluated_by_admin' => !empty($evaluated_by_admin) ? $evaluated_by_admin : 'off',
            'points' => $points,
            'is_passed' => $is_passed,
            'can_see_answers' => $can_see_answers,
            'also_show_correct_answers' => Quiz_Api_Static_Helper::is_also_show_correct_answers_enabled($quiz_post_id),
            'user_answers' => $user_answers,
            'pass_points' => $pass_points,
            'all_points' => $all_points,
            'all_points_string' => $all_points_string,
            'time' => $time ?? null,
            'quiz_post_id' => $quiz_post_id,
            'course_id' => $course_id,
            'course_page_id' => get_post_meta( $course_id, 'course_id', true ),
            'content' => nl2br($content),
            'number_of_attempts_to_solve_the_quiz_was_used' => $this->number_of_attempts_to_solve_the_quiz_was_used($number_test_attempts, $user_solving_quiz_attempts_count),
            'attempts_left' => $number_test_attempts ? ($number_test_attempts - $user_solving_quiz_attempts_count) : 0,
            'time_is_up' => $time_is_up ?? false,
        ]);
    }

    public function register_scripts(): void
    {
        add_action(Action_Name::PRINT_FOOTER_SCRIPT, [$this, 'print_translation_strings_as_js_variable']);
    }

    public function print_translation_strings_as_js_variable(): void
    {
        echo "<script>let " . self::BPMJ_WPI_QUIZ_SETTINGS_I18N . "=" . $this->get_json_translations() . "</script>";
    }

    private function get_json_translations(): string
    {
        return json_encode($this->get_settings_as_variable_in_array()) ?: '[]';
    }

    private function get_settings_as_variable_in_array(): array
    {
        return [
            'right_click' => LMS_Settings::get_option(Settings_Const::RIGHT_CLICK_BLOCKING_QUIZ) ?? false
        ];
    }

    private function get_number_test_attempts(int $quiz_id): ?int
    {
		return Quiz_Api_Static_Helper::get_user_quiz_allowed_attempts_number($quiz_id);
    }

    private function number_of_attempts_to_solve_the_quiz_was_used(?int $number_test_attempts, int $user_solving_quiz_attempts_count): bool
    {
        if (!$number_test_attempts) {
            return false;
        }

        if ($user_solving_quiz_attempts_count < $number_test_attempts) {
            return false;
        }

        return true;
    }
}
