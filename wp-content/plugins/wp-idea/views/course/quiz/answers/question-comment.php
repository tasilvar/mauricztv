<?php
/** @var array $question */
/** @var bool $also_show_correct_answers */

use bpmj\wpidea\helpers\Translator_Static_Helper;

?>

<?php if ($also_show_correct_answers && !empty($question['question_comment'])) : ?>
    <div class="quiz-answers-separate-line"></div>
    <div class="question-feedback">
        <strong><?= Translator_Static_Helper::translate('quiz.answers_preview.question_comment'); ?></strong>
        <br>
        <?= nl2br($question['question_comment']); ?>
    </div>
<?php endif; ?>