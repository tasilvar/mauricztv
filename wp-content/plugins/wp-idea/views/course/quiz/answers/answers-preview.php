<?php
/** @var bool $can_see_answers */
/** @var array $questions */
/** @var array $user_answers */
/** @var bool $also_show_correct_answers */

use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\View;

?>

<?php if (!$can_see_answers ) {
	return;
} ?>

<div class="niepoprawne_odp quizy_contenter">
	<div id="niepoprawne_odp_zamknij" class="close_answers_preview"><img src="<?php echo bpmj_eddcm_template_get_file( 'assets/img/x.png' ); ?>"></div>

	<div id="bpmj-eddcm-questions-carousel-answers" class="quizy_contenter_quizy carousel slide" data-ride="carousel" data-interval="false" data-wrap="false">
		<form>
			<div class="carousel-inner" role="listbox">
				<?php
				$question_id = 1;
				foreach ( $questions as $key => $question ) : ?>
					<div class="quizy_contenter_quiz item <?php echo 0 == $key ? 'active' : ''; ?>" style="width: 10%;">
						<div class="quizy_contenter_quiz_tytul">
							<span><?php echo $question_id; ?></span>
							<p><?php echo do_shortcode($question['title']); ?></p>
						</div>
						<div class="quizy_contenter_quiz_odp">
							<ul>
								<?php if ( 'single_radio' === $question['type'] || 'single_select' === $question['type']) : ?>

									<?= View::get('answer-preview-single-radio-or-select', [
										'question' => $question,
										'user_answers' => $user_answers,
										'also_show_correct_answers' => $also_show_correct_answers,
									]) ?>

								<?php elseif ( 'multiple' === $question['type'] ) : ?>

									<?= View::get('answer-preview-multiselect', [
                                        'question' => $question,
                                        'user_answers' => $user_answers,
                                        'also_show_correct_answers' => $also_show_correct_answers,
                                    ]) ?>

								<?php elseif ( 'text' === $question['type'] ) : ?>
									<li class="bpmj-eddcm-text-answer">
										<?php
                                        $show_assessed_by_moderator_info = false;
										$text = '';
										if ( isset( $user_answers[ $question['id'] ] ) )
											$text = trim($user_answers[ $question['id'] ]['answer']);
										?>
                                        <?php if (!empty($text)) : ?>
                                        <?php $show_assessed_by_moderator_info = true; ?>
								    		<input type="hidden" name="bpmj_eddcm_question[<?php echo $question['id']; ?>][question_id]" value="<?php echo $question['id']; ?>">
									    	<textarea id="bpmj-eddcm-question-<?php echo $question['id']; ?>-answer" name="bpmj_eddcm_question[<?php echo $question['id']; ?>][answer]" cols="30" rows="10" disabled><?php echo $text; ?></textarea>
                                        <?php else : ?>
                                            <?= Translator_Static_Helper::translate('quiz.answers_preview.empty_answer_to_open_question') ?>
                                        <?php endif; ?>
                                    </li>
                                    <?= View::get('question-comment', [
                                        'question' => $question,
                                        'also_show_correct_answers' => $also_show_correct_answers,
                                    ]);
                                    ?>
                                    <?php if ($show_assessed_by_moderator_info): ?>
                                        <div class="quiz-answers-separate-line"></div>
                                        <p class="assessed-by-moderator"><?= Translator_Static_Helper::translate('quiz.answers_preview.assessed_by_moderator'); ?></p>
                                    <?php endif; ?>
								<?php elseif ( 'file' === $question['type'] ) : ?>

									<li class="bpmj-eddcm-file-answer">
                                        <?php
                                        $show_assessed_by_moderator_info = false;
                                        if(!empty($user_answers[ $question['id'] ]['answer'])) {
	                                        $file = wp_get_attachment_url($user_answers[$question['id']]['answer']);

                                            if(!$file) {
	                                            echo Translator_Static_Helper::translate('quiz.answers_preview.file.no_file');
                                            } else {
                                                $show_assessed_by_moderator_info = true;
                                                $file_name = basename($file);
                                                $file_link = bpmj_eddpc_encrypt_link($file);
                                                $file_href = "<a href='$file_link' target='_blank'>$file_name</a>";
                                                echo Translator_Static_Helper::translate('quiz.answers_preview.file') . ' ' . $file_href;
                                            }
                                        } else {
                                            echo Translator_Static_Helper::translate('quiz.answers_preview.file.no_file');
                                        }
                                        ?>
									</li>
                                    <?= View::get('question-comment', [
                                        'question' => $question,
                                        'also_show_correct_answers' => $also_show_correct_answers,
                                    ]);
                                    ?>
                                    <?php if ($show_assessed_by_moderator_info): ?>
                                        <div class="quiz-answers-separate-line"></div>
                                        <p class="assessed-by-moderator"><?= Translator_Static_Helper::translate('quiz.answers_preview.assessed_by_moderator'); ?></p>
                                    <?php endif; ?>
								<?php endif; ?>
							</ul>
						</div>
					</div>
					<?php
					$question_id++;
				endforeach; ?>
			</div>
		</form>
	</div>
	<div class="quizy_contenter_paginacja">
		<div id="quizy_contenter_paginacja_prev" class="left" href="#bpmj-eddcm-questions-carousel-answers" role="button" data-slide="prev">
			<i class="fas fa-caret-left"></i> <?php _e( 'BACK', BPMJ_EDDCM_DOMAIN ); ?>
		</div>
		<div id="quizy_contenter_paginacja_next" class="right" href="#bpmj-eddcm-questions-carousel-answers" role="button" data-slide="next">
			<?php _e( 'NEXT', BPMJ_EDDCM_DOMAIN ); ?> <i class="fas fa-caret-right"></i>
		</div>
        <div id="quizy_contenter_paginacja_finish" class="right close_answers_preview" href="#bpmj-eddcm-questions-carousel" role="button" data-slide="next">
            <?php _e( 'FINISH', BPMJ_EDDCM_DOMAIN ); ?> <i class="fas fa-caret-right"></i>
        </div>
	</div>
</div>