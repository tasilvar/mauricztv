<?php
/* @var bool $not_empty */

use bpmj\wpidea\helpers\Translator_Static_Helper;

$not_empty = $not_empty ?? false;
?>

<button type='button' class='question__show_comment_field_button <?= $not_empty ? 'question__show_comment_field_button--not-empty' : '' ?>'>
    <?= Translator_Static_Helper::translate(
            $not_empty
                ? 'quiz_editor.structure.show_question_comment_field.not_empty'
	            : 'quiz_editor.structure.show_question_comment_field'
    ) ?>
</button>