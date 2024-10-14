<?php
/* @var int $question_id */
/* @var ?string $value */

$value = $value ?? '';
?>

<div class="question-comment-field-wrapper" style="display: none">
<textarea
	name='bpmj_eddcm_test_questions[<?= $question_id ?>][question_comment]'
	rows='3'
	class='question-comment-field eddcm-test-question-question_comment'><?= $value ?></textarea>
</div>