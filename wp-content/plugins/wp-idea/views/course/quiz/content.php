<?php

use bpmj\wpidea\View;

/** @var boolean $is_ongoing_quiz */
/** @var boolean $is_time_quiz */
/** @var boolean $inserted_quiz_id */
/** @var boolean $points */
/** @var boolean $all_points */
/** @var boolean $all_points_string */
/** @var string $time */
/** @var int $quiz_post_id */
/** @var array $questions */
/** @var string $content */
/** @var bool $can_see_answers */
/** @var bool $also_show_correct_answers */
/** @var boolean $is_solved_quiz */
/** @var string $evaluated_by_admin */
/** @var boolean $is_passed */
/** @var array $user_answers */
/** @var int $pass_points */
/** @var int $course_page_id */
/** @var bool $number_of_attempts_to_solve_the_quiz_was_used */
/** @var int $attempts_left */
/** @var bool $time_is_up */


?>
<?php if ( $is_ongoing_quiz ) : ?>

    <div class="quizy_contenter">
        <?= View::get('/course/quiz/container', [
            'is_time_quiz' => $is_time_quiz,
            'time' => $time,
            'quiz_post_id' => $quiz_post_id,
            'inserted_quiz_id' => $inserted_quiz_id,
            'questions' => $questions,
        ]) ?>
    </div>
<?php elseif ( ! $is_solved_quiz ) : ?>
    <div class="quizy_contenter">
        <?php if (!empty($content)) : ?>
            <div class="quiz-content">
                <?= $content; ?>
            </div>
        <?php endif; ?>
        <a id="bpmj-eddcm-start-quiz-button" href="#" class="quiz_podsumowanie_nastepna_lekcja <?= !empty($content) ? 'center-start-quiz-button' : ''?>" data-quiz="<?php echo get_the_ID(); ?>" data-course="<?php echo $course_page_id; ?>"><?php _e( 'Start quiz', BPMJ_EDDCM_DOMAIN ); ?> <i class="fas fa-caret-right"></i></a>
    </div>
<?php endif; ?>

<?php if ( $is_solved_quiz ) : ?>
   <?= View::get('solved/solved-quiz', [
		'can_see_answers' => $can_see_answers,
		'evaluated_by_admin' => $evaluated_by_admin,
        'is_passed' => $is_passed,
        'points' => $points,
        'all_points' => $all_points,
        'all_points_string' => $all_points_string,
        'pass_points' => $pass_points,
        'number_of_attempts_to_solve_the_quiz_was_used' => $number_of_attempts_to_solve_the_quiz_was_used,
        'attempts_left' => $attempts_left,
        'course_page_id' => $course_page_id,
		'questions' => $questions,
		'user_answers' => $user_answers,
		'also_show_correct_answers' => $also_show_correct_answers,
        'time_is_up' => $time_is_up,

	]) ?>
<?php endif; ?>

<?= View::get('/scripts/check-lesson-as-undone'); ?>
