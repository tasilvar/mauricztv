<?php

/** @var string $is_time_quiz */
/** @var string $time */
/** @var string $quiz_post_id */
/** @var string $inserted_quiz_id */
/** @var string $questions */

use bpmj\wpidea\Courses;
use bpmj\wpidea\nonce\Nonce_Handler;

?>

<div class="quizy_contenter_postep">
    <div class="lecja_top_postep">
        <i class="fas fa-chart-line"></i>
        <span><?php _e( 'Progress', BPMJ_EDDCM_DOMAIN ); ?>:</span>
        <div class="postep_contenter">
            <div class="postep_contenter_postep">
                <div class="postep_liczba"></div>
            </div>
        </div>
    </div>
    <?php
    if ( $is_time_quiz ) : ?>
        <div class="czas_lekcji quiz-time-left
" style="display: inline-block;">
            <i class="far fa-clock"></i>
            <strong>
                                    <span>
                                        <?php _e( 'Time for answers', BPMJ_EDDCM_DOMAIN ); ?>
                                    </span>:
                <span id="bpmj-eddcm-tfa" data-time="<?php echo intval($time * 60000)   ; ?>"></span>
            </strong>
        </div>
    <?php endif; ?>
</div>
<div id="bpmj-eddcm-questions-carousel" class="quizy_contenter_quizy carousel slide" data-ride="carousel" data-interval="false" data-wrap="false">
    <form id="bpmj-eddcm-questions-form" method="post" action="?wpi_route=quiz/finish_quiz" enctype="multipart/form-data">
        <?= Nonce_Handler::get_field(); ?>
        <input type="hidden" name="bpmj_eddcm_add_test">
        <input type="hidden" name="bpmj_eddcm_add_test_quiz_post_id" value="<?php echo $quiz_post_id; ?>">
        <input type="hidden" name="bpmj_eddcm_add_test_inserted_quiz_id" value="<?php echo $inserted_quiz_id; ?>">
        <div class="carousel-inner" role="listbox">
            <?php
            $user_answers = get_post_meta( $inserted_quiz_id, 'user_answers', true );
            $question_id = 1;
            foreach ( $questions as $key => $question ) : ?>
                <div class="quizy_contenter_quiz item <?php echo 0 == $key ? 'active' : ''; ?>" style="width: 10%;">
                    <div class="quizy_contenter_quiz_tytul">
                        <span><?php echo $question_id; ?></span>
                        <p><?php echo do_shortcode(strip_tags($question['title'])); ?></p>
                    </div>
                    <div class="quizy_contenter_quiz_odp">
                        <ul>
                            <?php $answer_char = 'A'; ?>
                            <?php if ( 'single_radio' === $question['type'] ) : ?>
                                <?php foreach ( $question['answer'] as $answer ) : ?>
                                    <li>
                                        <?php
                                        $checked = '';
                                        if ( isset( $user_answers[ $question['id'] ] ) && $user_answers[ $question['id'] ]['answer'] === $answer['id'] )
                                            $checked = ' checked="checked"';
                                        ?>
                                        <input type="hidden" name="bpmj_eddcm_question[<?php echo $question['id']; ?>][question_id]" value="<?php echo $question['id']; ?>">
                                        <input id="bpmj-eddcm-question-<?php echo $question['id']; ?>-answer-<?php echo $answer['id']; ?>" type="radio"<?php echo $checked; ?> class="fa" name="bpmj_eddcm_question[<?php echo $question['id']; ?>][answer]" value="<?php echo $answer['id']; ?>">
                                        <label for="bpmj-eddcm-question-<?php echo $question['id']; ?>-answer-<?php echo $answer['id']; ?>">
                                            <strong><?php echo $answer_char; ?>.</strong>
                                            <?php echo do_shortcode( strip_tags($answer['title']) ); ?>
                                        </label>
                                    </li>
                                    <?php $answer_char++; ?>
                                <?php endforeach; ?>
                            <?php elseif ( 'single_select' === $question['type'] ) : ?>
                                <li>
                                    <input type="hidden" name="bpmj_eddcm_question[<?php echo $question['id']; ?>][question_id]" value="<?php echo $question['id']; ?>">
                                    <select id="bpmj-eddcm-question-<?php echo $question['id']; ?>-answer" name="bpmj_eddcm_question[<?php echo $question['id']; ?>][answer]" class="bpmj-eddcm-single-select-question">
                                        <option value=""><?php _e( 'Select answer', BPMJ_EDDCM_DOMAIN ); ?></option>
                                        <?php foreach ( $question['answer'] as $answer ) : ?>
                                            <?php
                                            $checked = '';
                                            if ( isset( $user_answers[ $question['id'] ] ) && $user_answers[ $question['id'] ]['answer'] === $answer['id'] )
                                                $checked = ' selected';
                                            ?>
                                            <option<?php echo $checked; ?> value="<?php echo $answer['id']; ?>"><?php echo strip_tags($answer['title']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </li>
                            <?php elseif ( 'multiple' === $question['type'] ) : ?>
                                <?php
                                $value = !empty( $user_answers ) ? $user_answers[ $question['id'] ]['answer'] : '';
                                ?>
                                <input id="bpmj-eddcm-question-<?php echo $question['id']; ?>-answer" type="hidden" name="bpmj_eddcm_question[<?php echo $question['id']; ?>][answer]" value="<?= $value ?>">

                                <?php foreach ( $question['answer'] as $answer ) : ?>
                                    <li>
                                        <?php
                                        $checked = '';
                                        if ( isset( $user_answers[ $question['id'] ] ) ) {
                                            if ( in_array( $answer['id'], explode( ',', $user_answers[ $question['id'] ]['answer'] ) ) )
                                                $checked = ' checked="checked"';
                                        }
                                        ?>
                                        <input type="hidden" name="bpmj_eddcm_question[<?php echo $question['id']; ?>][question_id]" value="<?php echo $question['id']; ?>">
                                        <input id="bpmj-eddcm-question-<?php echo $question['id']; ?>-answer-<?php echo $answer['id']; ?>" data-to-id="#bpmj-eddcm-question-<?php echo $question['id']; ?>-answer" type="checkbox"<?php echo $checked; ?> class="fa" name="bpmj_eddcm_question_fake[<?php echo $question['id']; ?>][answer]" value="<?php echo $answer['id']; ?>">
                                        <label for="bpmj-eddcm-question-<?php echo $question['id']; ?>-answer-<?php echo $answer['id']; ?>">
                                            <strong><?php echo $answer_char; ?>.</strong>
                                            <?php echo do_shortcode( strip_tags($answer['title']) ); ?>
                                        </label>
                                    </li>
                                    <?php $answer_char++; ?>
                                <?php endforeach; ?>
                            <?php elseif ( 'text' === $question['type'] ) : ?>
                                <li>
                                    <?php
                                    $text = '';
                                    if ( isset( $user_answers[ $question['id'] ] ) )
                                        $text = $user_answers[ $question['id'] ]['answer'];
                                    ?>
                                    <input type="hidden" name="bpmj_eddcm_question[<?php echo $question['id']; ?>][question_id]" value="<?php echo $question['id']; ?>">
                                    <textarea id="bpmj-eddcm-question-<?php echo $question['id']; ?>-answer" name="bpmj_eddcm_question[<?php echo $question['id']; ?>][answer]" cols="30" rows="10"><?php echo $text; ?></textarea>
                                </li>
                            <?php elseif ( 'file' === $question['type'] ) : ?>
                                <li class="bpmj-eddcm-file-answer">
                                    <input type="hidden" name="bpmj_eddcm_question[<?php echo $question['id']; ?>][question_id]" value="<?php echo $question['id']; ?>">
                                    <input type="file" name="bpmj_eddcm_question[<?php echo $question['id']; ?>]" class="bpmj-eddcm-file-input" data-types="<?php echo implode( ',', Courses::$allowed_quiz_file_types ); ?>">
                                    <br>
                                    <p>
                                        <small>
                                            <?php echo sprintf( __( 'Allowed file types: %s', BPMJ_EDDCM_DOMAIN ), implode( ', ', array_keys( Courses::$allowed_quiz_file_types ) ) ); ?>
                                        </small>
                                    </p>
                                </li>
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
    <div id="quizy_contenter_paginacja_prev" class="left" href="#bpmj-eddcm-questions-carousel" role="button" data-slide="prev">
        <i class="fas fa-caret-left"></i> <?php _e( 'BACK', BPMJ_EDDCM_DOMAIN ); ?>
    </div>
    <div id="quizy_contenter_paginacja_next" class="right" href="#bpmj-eddcm-questions-carousel" role="button" data-slide="next">
        <?php _e( 'NEXT', BPMJ_EDDCM_DOMAIN ); ?> <i class="fas fa-caret-right"></i>
    </div>
    <div id="quizy_contenter_paginacja_finish" class="right" href="#bpmj-eddcm-questions-carousel" role="button" data-slide="next">
        <?php _e( 'FINISH', BPMJ_EDDCM_DOMAIN ); ?> <i class="fas fa-caret-right"></i>
    </div>
</div>
