<?php

namespace bpmj\wpidea\admin\pages\quiz_editor\core\fields;

use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\learning\quiz\Quiz_ID;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\View;

class Questions_Structure_Field extends Abstract_Setting_Field
{
    private Quiz_ID $quiz_id;
    private Interface_Translator $translator;

    public function __construct(
        string $name,
        Quiz_ID $quiz_id,
        Interface_Translator $translator
    ) {
        $this->quiz_id = $quiz_id;
        $this->translator = $translator;

        parent::__construct($name);
    }

    public function render_to_string(): string
    {
        ob_start();

	    $questions = get_post_meta( $this->quiz_id->to_int(), 'test_questions', true );
	    $pass_points = get_post_meta( $this->quiz_id->to_int(), 'test_questions_points_pass', true );
	    ?>
        <div class="bpmj-eddcm-cs-section-body">

            <form id='form-quiz-structure'>
            <div class="form-group" style="padding:10px;">
                <ul id="bpmj_eddcm_questions_list" class="modules">
				    <?php $i = 0; ?>
				    <?php if ( ! empty( $questions ) ) : ?>
					    <?php foreach ( $questions as $question ) : ?>
                            <li class="module question editor">
                                <div class="question-header">
                                    <img style="vertical-align: middle;" src="<?= BPMJ_EDDCM_URL .'/assets/imgs/settings/move-icon.svg' ?>" alt="">
                                    <input class="eddcm-test-question-id" type="hidden" name="bpmj_eddcm_test_questions[<?php echo $i; ?>][id]" value="<?php echo $question['id']; ?>">
                                    <input class="eddcm-test-question-title" type="text" name="bpmj_eddcm_test_questions[<?php echo $i; ?>][title]" value="<?php echo esc_html($question['title']); ?>">
                                    <select name="bpmj_eddcm_test_questions[<?php echo $i; ?>][type]" class="eddcm-test-question-type">
                                        <option><?php _e( 'Select question type', BPMJ_EDDCM_DOMAIN ); ?></option>
                                        <option value="single_radio" <?php echo 'single_radio' === $question['type'] ? 'selected' : ''; ?>><?php _e( 'Single choice question', BPMJ_EDDCM_DOMAIN ); ?>&nbsp;(radio)</option>
                                        <option value="single_select" <?php echo 'single_select' === $question['type'] ? 'selected' : ''; ?>><?php _e( 'Single choice question', BPMJ_EDDCM_DOMAIN ); ?>&nbsp;(select)</option>
                                        <option value="multiple" <?php echo 'multiple' === $question['type'] ? 'selected' : ''; ?>><?php _e( 'Multiple choice question', BPMJ_EDDCM_DOMAIN ); ?></option>
                                        <option value="text" <?php echo 'text' === $question['type'] ? 'selected' : ''; ?>><?php _e( 'Text question', BPMJ_EDDCM_DOMAIN ); ?></option>
                                        <option value="file" <?php echo 'file' === $question['type'] ? 'selected' : ''; ?>><?php _e( 'File question', BPMJ_EDDCM_DOMAIN ); ?></option>
                                    </select>
                                    <span class="dashicons dashicons-no-alt remove-module" data-action="remove-question"></span>
                                    <div>
                                        <?= View::get('views/show-question-comment-button', [
                                            'not_empty' => !empty($question['question_comment'])
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="question-body">
	                                <?= View::get('views/question-comment-field', [
                                        'question_id' => $i,
                                        'value' => $question['question_comment'] ?? null
                                    ]) ?>
                                    <div class="question-type-single-tab question-type-tab">
                                        <ul class="answers">
										    <?php if ( ( $question['type'] == 'single_radio' || $question['type'] === 'single_select' || $question['type'] === 'multiple' ) && ! empty( $question['answer'] ) ) : ?>
											    <?php $j = 0; ?>
											    <?php foreach ( $question['answer'] as $answer ) : ?>
                                                    <li class="answer">
                                                        <img style='vertical-align: middle;'
                                                             src="<?= BPMJ_EDDCM_URL . '/assets/imgs/settings/move-icon.svg' ?>"
                                                             alt=''>
                                                        <input class="eddcm-test-question-answer-id" type="hidden" name="bpmj_eddcm_test_questions[<?php echo $i; ?>][answer][<?php echo $j; ?>][id]" value="<?php echo $answer['id'] ?? 0; ?>">
                                                        <input class="eddcm-test-question-answer-title" type="text" name="bpmj_eddcm_test_questions[<?php echo $i; ?>][answer][<?php echo $j; ?>][title]" value="<?php echo esc_html( $answer['title'] ?? '' ); ?>">
                                                        <span class="dashicons dashicons-no-alt remove-module" data-action="remove-answer"></span>
                                                        <input class="eddcm-test-question-answer-points points-value" type="number" name="bpmj_eddcm_test_questions[<?php echo $i; ?>][answer][<?php echo $j; ?>][points]" value="<?php echo $answer['points'] ?? ''; ?>">&nbsp;<?php _e( 'Points', BPMJ_EDDCM_DOMAIN ); ?>
                                                    </li>
												    <?php $j++; ?>
											    <?php endforeach; ?>
										    <?php endif; ?>
                                            <li class="add-answer" data-action="add-answer" data-type="single">
                                                <span class="dashicons dashicons-plus"></span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="question-type-text-tab question-type-tab"></div>
                                    <div class="question-type-file-tab question-type-tab"></div>
                                </div>
                            </li>
						    <?php $i++; ?>
					    <?php endforeach; ?>
				    <?php endif; ?>
                </ul>
            </div>
            <input type='hidden' id='quiz_id' name='quiz_id' value="<?= $this->quiz_id->to_int() ?>"/>

            <div class='pass-condition'>
                <p>
                    <label for='pass-condition-points-input'><?php _e( 'Points for passing', BPMJ_EDDCM_DOMAIN ); ?></label>
                    <input id="pass-condition-points-input" type="number" min="0" max="0"
                           value="<?php echo empty( $pass_points ) ? '0' : $pass_points; ?>"
                           name="test_questions_points_pass"> / <span id="pass-condition-points">0</span>
                    <input id="pass-condition-points-input-all" type="hidden" name="test_questions_points_all">
                </p>
            </div>

            </form>

            <template id="bpmj_eddcm_new_test_question_single_answer_template">
                <li class="answer">
                    <img style='vertical-align: middle;'
                         src="<?= BPMJ_EDDCM_URL . '/assets/imgs/settings/move-icon.svg' ?>" alt=''>
                    <input class="eddcm-test-question-answer-id" type="hidden" name="bpmj_eddcm_test_questions[0][answer][0][id]" value="">
                    <input class="eddcm-test-question-answer-title" type="text" name="bpmj_eddcm_test_questions[0][answer][0][title]">
                    <span class="dashicons dashicons-no-alt remove-module" data-action="remove-answer"></span>
                    <input class="eddcm-test-question-answer-points points-value" type="number" name="bpmj_eddcm_test_questions[0][answer][0][points]" value="0">&nbsp;<?php _e( 'Points', BPMJ_EDDCM_DOMAIN ); ?>
                </li>
            </template>
            <template id="bpmj_eddcm_new_test_question_template">
                <li class="module question editor">
                    <div class="question-header">
                        <img style='vertical-align: middle;'
                             src="<?= BPMJ_EDDCM_URL . '/assets/imgs/settings/move-icon.svg' ?>" alt=''>
                        <input class="eddcm-test-question-id" type="hidden" name="bpmj_eddcm_test_questions[0][id]" value="<?php echo $question['id'] ?? ''; ?>">
                        <input class="eddcm-test-question-title" type="text" name="bpmj_eddcm_test_questions[0][title]" class="focus-me">
                        <select name="bpmj_eddcm_test_questions[0][type]" class="eddcm-test-question-type template">
                            <option><?php _e( 'Select question type', BPMJ_EDDCM_DOMAIN ); ?></option>
                            <option value="single_radio"><?php _e( 'Single choice question', BPMJ_EDDCM_DOMAIN ); ?>&nbsp;(radio)</option>
                            <option value="single_select"><?php _e( 'Single choice question', BPMJ_EDDCM_DOMAIN ); ?>&nbsp;(select)</option>
                            <option value="multiple"><?php _e( 'Multiple choice question', BPMJ_EDDCM_DOMAIN ); ?></option>
                            <option value="text"><?php _e( 'Text question', BPMJ_EDDCM_DOMAIN ); ?></option>
                            <option value="file"><?php _e( 'File question', BPMJ_EDDCM_DOMAIN ); ?></option>
                        </select>
                        <span class="dashicons dashicons-no-alt remove-module" data-action="remove-question"></span>
                        <div>
                            <?= View::get('views/show-question-comment-button') ?>
                        </div>
                    </div>
                    <div class="question-body">
	                    <?= View::get('views/question-comment-field', [
		                    'question_id' => 0
	                    ]) ?>
                        <div class="question-type-single-tab question-type-tab">
                            <ul class="answers">
                                <li class="add-answer" data-action="add-answer" data-type="single">
                                    <span class="dashicons dashicons-plus"></span>
                                </li>
                            </ul>
                        </div>
                        <div class="question-type-text-tab question-type-tab"></div>
                        <div class="question-type-file-tab question-type-tab"></div>
                    </div>
                </li>
            </template>

            <script>
                window.bpmj_eddcm_quiz_questions_structure_creator_init('new_quiz_editor');
            </script>

            <div class='save-fixed-bottom-box'>
                <button type='button' class='creator-buttons-add'
                        data-action='add-question'>
			        <?php _e( 'Add question', BPMJ_EDDCM_DOMAIN ); ?>
                </button>

                <button type="button" id="save-quiz-structure" class="creator-buttons-add">
			        <?= $this->translator->translate( 'settings.field.button.save' ) ?>
                </button>
                <br style="clear:both;">
            </div>
        </div>
	    <?php

        return ob_get_clean();
    }
}