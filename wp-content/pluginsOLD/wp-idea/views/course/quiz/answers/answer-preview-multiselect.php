<?php
/** @var array $question */
/** @var array $user_answers */
/** @var bool $also_show_correct_answers */
/** @var bool $show_question_comment */


use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\View;

$user_answer = $user_answers[$question['id']]['answer'];
$has_at_least_one_unchecked_correct_answer = false;
$has_at_least_one_checked_correct_answer = false;
$answer_char = 'A';
$has_no_answer = !isset($user_answers[$question['id']]['answer']) || $user_answers[$question['id']]['answer'] === '';
?>

<?php foreach ($question['answer'] as $answer) : ?>
	<li>
		<div class='single-answer-preview'>
			<div class='single-answer-preview__answer <?= ($has_no_answer && !$also_show_correct_answers) ? 'no-border' : '' ?>'>
				<?php
				$checked = '';
				if ( isset( $user_answers[ $question['id'] ] ) ) {
					if ( in_array( $answer['id'], explode( ',', $user_answers[ $question['id'] ]['answer'] ) ) )
						$checked = ' checked="checked"';
				}

				$answer_type = '';
				$show_answer_correctness = false;
				if ((int)$answer['points'] > 0) {
					if (!empty($checked)) {
						$answer_type = 'good';
						$has_at_least_one_checked_correct_answer = true;
						$show_answer_correctness = true;
					}

					if ($also_show_correct_answers) {
						$show_answer_correctness = true;
					}

					if (empty($checked)) {
						$has_at_least_one_unchecked_correct_answer = true;
					}

				} else {
					if (!empty($checked)){
						$answer_type = 'bad';
						$show_answer_correctness = true;
					}
				}
				?>
				<input id="bpmj-eddcm-question-<?php echo $question['id']; ?>-answer-<?php echo $answer['id']; ?>" data-to-id="#bpmj-eddcm-question-<?php echo $question['id']; ?>-answer" type="checkbox"<?php echo $checked; ?> class="fa <?php echo (!empty($checked) ? 'checked' : ''); ?>" name="bpmj_eddcm_question_fake[<?php echo $question['id']; ?>][answer]" value="<?php echo $answer['id']; ?>" disabled>
				<label for="bpmj-eddcm-question-<?php echo $question['id']; ?>-answer-<?php echo $answer['id']; ?>" class="<?php echo (!empty($checked) ? 'checked' : ''); ?>">
					<?php echo $answer_char; ?>.
					<?php echo do_shortcode( $answer['title'] ); ?>
				</label>
			</div>
			<div class='single-answer-preview__info'>
				<?php if($show_answer_correctness) :
					$is_correct_answer = (int) $answer['points'] > 0;
					$dashicon_type = $is_correct_answer ? 'saved' : 'no';
					$answer_correctness_type_text = Translator_Static_Helper::translate('quiz.answers_preview.' . ($is_correct_answer ? 'correct_answer' : 'incorrect_answer'));
					?>
					<span class="<?= !empty($answer_type) ? $answer_type : 'correct-unchecked' ?>">
                                                     <i class="dashicons dashicons-<?= $dashicon_type ?>"></i>
                                                     <?= $answer_correctness_type_text ?>
                                                </span>
				<?php endif; ?>
			</div>
	</li>
	<?php $answer_char++; ?>
<?php endforeach; ?>
<?php
$show_more_correct_answers_info = $has_at_least_one_unchecked_correct_answer && $has_at_least_one_checked_correct_answer && !$also_show_correct_answers;
?>
<?= View::get('question-comment', [
    'question' => $question,
    'also_show_correct_answers' => $also_show_correct_answers,
]);
?>
<?php if ($has_no_answer || $show_more_correct_answers_info) : ?>
    <div class="quiz-answers-separate-line"></div>
<?php endif; ?>
<?php if ($has_no_answer) : ?>
    <p class="quiz-empty-answer-info"><?= Translator_Static_Helper::translate('quiz.answers_preview.empty_answer') ?></p>
<?php endif; ?>
<?php if ($show_more_correct_answers_info) : ?>
    <p class="more-correct-answers-info"><?= Translator_Static_Helper::translate('quiz.answers_preview.more_correct_answers_info') ?></p>
<?php endif; ?>