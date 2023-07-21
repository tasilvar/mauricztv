<?php
if(WPI()->page->has_template()):
    WPI()->page->render_template();
    return;
endif;

use bpmj\wpidea\Courses;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\View;
?>
<?php WPI()->templates->header(); ?>
<?php
$files					 = WPI()->templates->get_meta( 'files' );
$lesson_page_id			 = get_the_ID();
$course_page_id			 = WPI()->courses->get_course_top_page( $lesson_page_id );

$quiz_post_id = get_the_ID();

$query = new WP_Query( array(
    'post_status' => 'draft',
    'post_type' => 'tests',
    'meta_query' => array(
        array(
            'key'    => 'quiz_id',
            'value'  => $quiz_post_id,
        ),
        array(
            'key'   => 'user_email',
            'value' => wp_get_current_user()->user_email,
        ),
    ),
) );

$questions = get_post_meta( $quiz_post_id, 'test_questions', true );
$evaluated_by_admin = get_post_meta( $quiz_post_id, 'evaluated_by_admin_mode', true );
$can_see_answers = get_post_meta( $quiz_post_id, 'can_see_answers_mode', true );

// TODO (Usunięte tymczasowo)
$can_see_answers = 'off';

$is_ongoing_quiz = false;
$is_solved_quiz = false;
if ( $query->post_count > 0 ) {
    $is_ongoing_quiz = true;

    $inserted_quiz_id = $query->post->ID;

    $is_time_quiz = false;

    $time_mode = get_post_meta( $quiz_post_id, 'time_mode', true );
    if ( $time_mode == 'on' ) {
        $is_time_quiz = true;

        $time_end = get_post_meta( $inserted_quiz_id, 'time_end', true );
        $time = $time_end - time();
        $time *= 0.016667;
    }
} else {
    $query = new WP_Query( array(
        'post_status' => 'publish',
        'post_type' => 'tests',
        'meta_query' => array(
            array(
                'key'    => 'quiz_id',
                'value'  => $quiz_post_id,
            ),
            array(
                'key'   => 'user_email',
                'value' => wp_get_current_user()->user_email,
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

        $all_points_string = '/' . $all_points;

        if ( (int) $all_points === 0)
            $all_points_string = '';
    }
}
?>

    <div id="content">

        <?php WPI()->templates->lesson_top_bar(); ?>

        <div class="contenter contenter_lekcji">
            <div class="row lekcje_tytul">
                <div class="col-sm-7">
                    <h1><?php the_title(); ?></h1>
                </div>
                <div class="col-sm-5 lekcje_paginacja">
                    <?php
                    echo WPI()->templates->get_previous_lesson_nav( '<i class="fas fa-caret-left"></i> ', 'lekcja_nast_pop lekcja_pop' );
                    echo WPI()->templates->get_next_lesson_nav( ' <i class="fas fa-caret-right"></i>', 'lekcja_nast_pop lekcja_nas', true );
                    ?>
                </div>
            </div>
            <?php WPI()->templates->breadcrumbs(); ?>
        </div>

        <div class="contenter content_lekcji">
            <?php
            if ( have_posts() ) {
                while ( have_posts() ) {
                    the_post();
                    the_content();
                }
            }
            ?>

            <?php if ( $is_ongoing_quiz ) : ?>
                <div class="quizy_contenter">
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
                            <div class="czas_lekcji" style="display: inline-block;">
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
                        <form id="bpmj-eddcm-questions-form" method="post"  action="?wpi_route=quiz/finish_quiz" enctype="multipart/form-data">

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
                                            <p><?php echo do_shortcode($question['title']); ?></p>
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
                                                                <?php echo do_shortcode( $answer['title'] ); ?>
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
                                                                <option<?php echo $checked; ?> value="<?php echo $answer['id']; ?>"><?php echo $answer['title']; ?></option>
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
                                                                <?php echo do_shortcode( $answer['title'] ); ?>
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
                </div>
            <?php elseif ( ! $is_solved_quiz ) : ?>
                <div class="quizy_contenter">

                    <a id="bpmj-eddcm-start-quiz-button" href="#" class="quiz_podsumowanie_nastepna_lekcja" data-quiz="<?php echo get_the_ID(); ?>" data-course="<?php echo $course_page_id; ?>"><?php _e( 'Start quiz', BPMJ_EDDCM_DOMAIN ); ?> <i class="fas fa-caret-right"></i></a>
                </div>
            <?php endif; ?>

            <?php if ( $is_solved_quiz ) : ?>
                <?php if ( 'on' === $evaluated_by_admin ) : ?>
                    <?php if ( empty( $is_passed ) ) : ?>
                        <div class="quiz_podsumowanie czekanie">
                            <div class="quiz_podsumowanie_zdjecie">
                                <div class="quiz_podsumowanie_zdjecie_ramka">
                                    <img src="<?php echo bpmj_eddcm_template_get_file( 'assets/img/czekanie.png' ); ?>">
                                </div>
                            </div>
                            <div class="quiz_podsumowanie_tekst">
                                <?php _e( 'Waiting for administrator approval', BPMJ_EDDCM_DOMAIN ); ?>
                            </div>
                        </div>
                    <?php elseif ( 'no' === $is_passed ) : ?>
                        <div class="quiz_podsumowanie">
                            <div class="quiz_podsumowanie_tytul"><?php _e( 'Unfortunately', BPMJ_EDDCM_DOMAIN ); ?></div>
                            <div class="quiz_podsumowanie_zdjecie">
                                <div class="quiz_podsumowanie_zdjecie_ramka">
                                    <img src="<?php echo bpmj_eddcm_template_get_file( 'assets/img/zero.png' ); ?>">
                                </div>
                            </div>
                            <div class="quiz_podsumowanie_wynik"><?php _e( 'Your result', BPMJ_EDDCM_DOMAIN ); ?>:</div>
                            <div class="quiz_podsumowanie_punkty zero"><?php echo ( ( $points < 0 ) ? 0 : $points ) . $all_points_string; ?></div>
                            <div class="quiz_podsumowanie_tekst">
                                <?php if ( $points == 0) : ?>
                                    <?php _e( 'Unfortunately, you answered all the questions wrong. Do not give up and try again, you will definitely go better!', BPMJ_EDDCM_DOMAIN ); ?>
                                <?php else : ?>
                                    <?php _e( 'Unfortunately, you scored too few points. Do not give up and try again, you will definitely go better!', BPMJ_EDDCM_DOMAIN ); ?>
                                <?php endif; ?>
                            </div>
                            <div href="#" class="quiz_podsumowanie_nastepna_lekcja prev bpmj-eddcm-quiz-again" data-quiz="<?php echo get_the_ID(); ?>" data-course="<?php echo $course_page_id; ?>">
                                <i class="fas fa-caret-left"></i>
                                <?php _e( 'AGAIN', BPMJ_EDDCM_DOMAIN ); ?>
                            </div>
                            <?php
                            $course_id      = get_post_meta( get_the_ID(), '_bpmj_eddcm', true );
                            $course_page_id = get_post_meta( $course_id, 'course_id', true );
                            $course_page = WPI()->courses->get_next_sibling_of( $course_page_id, get_the_ID() );
                            if (  ! is_null( $course_page ) && $course_page->should_be_grayed_out() ) : ?>
                                <div class="nastepna_lekcja_off">
                                    <?php _e( 'NEXT LESSON', BPMJ_EDDCM_DOMAIN ); ?>
                                    <i class="fas fa-caret-right"></i>
                                </div>
                            <?php elseif ( ! is_null( $course_page ) ) : ?>
                                <?php echo WPI()->templates->get_next_lesson_nav( ' <i class="fas fa-caret-right"></i>', 'lekcja_nast_pop lekcja_nas' ); ?>
                            <?php endif; ?>
                        </div>
                    <?php elseif ( 'yes' === $is_passed ) : ?>
                        <div class="quiz_podsumowanie">
                            <div class="quiz_podsumowanie_tytul"><?php _e( 'Congratulations!', BPMJ_EDDCM_DOMAIN ); ?></div>
                            <div class="quiz_podsumowanie_zdjecie">
                                <div class="quiz_podsumowanie_zdjecie_ramka">
                                    <img src="<?php echo bpmj_eddcm_template_get_file( 'assets/img/puchar.png' ); ?>">
                                </div>
                            </div>
                            <div class="quiz_podsumowanie_wynik"><?php _e( 'Your result', BPMJ_EDDCM_DOMAIN ); ?>:</div>
                            <div class="quiz_podsumowanie_punkty"><?php echo ( ( $points < 0 ) ? 0 : $points ) . $all_points_string; ?></div>
                            <div class="quiz_podsumowanie_tekst"></div>
                            <div href="#" class="quiz_podsumowanie_nastepna_lekcja prev bpmj-eddcm-quiz-again" data-quiz="<?php echo get_the_ID(); ?>" data-course="<?php echo $course_page_id; ?>">
                                <i class="fas fa-caret-left"></i>
                                <?php _e( 'AGAIN', BPMJ_EDDCM_DOMAIN ); ?>
                            </div>
                            <?php echo WPI()->templates->get_next_lesson_nav( ' <i class="fas fa-caret-right"></i>', 'lekcja_nast_pop lekcja_nas' ); ?>
                        </div>
                    <?php endif; ?>
                <?php elseif ( $points >= $all_points && 'off' === $evaluated_by_admin ) : ?>
                    <div class="quiz_podsumowanie">
                        <div class="quiz_podsumowanie_tytul"><?php _e( 'Congratulations!', BPMJ_EDDCM_DOMAIN ); ?></div>
                        <div class="quiz_podsumowanie_zdjecie">
                            <div class="quiz_podsumowanie_zdjecie_ramka">
                                <img src="<?php echo bpmj_eddcm_template_get_file( 'assets/img/puchar.png' ); ?>">
                            </div>
                        </div>
                        <div class="quiz_podsumowanie_wynik"><?php _e( 'Your result', BPMJ_EDDCM_DOMAIN ); ?>:</div>
                        <div class="quiz_podsumowanie_punkty"><?php echo ( ( $points < 0 ) ? 0 : $points ) . $all_points_string; ?></div>
                        <div class="quiz_podsumowanie_tekst"></div>
                        <div href="#" class="quiz_podsumowanie_nastepna_lekcja prev bpmj-eddcm-quiz-again" data-quiz="<?php echo get_the_ID(); ?>" data-course="<?php echo $course_page_id; ?>">
                            <i class="fas fa-caret-left"></i>
                            <?php _e( 'AGAIN', BPMJ_EDDCM_DOMAIN ); ?>
                        </div>
                        <?php echo WPI()->templates->get_next_lesson_nav( ' <i class="fas fa-caret-right"></i>', 'lekcja_nast_pop lekcja_nas' ); ?>
                    </div>
                <?php elseif ( $points >= $pass_points && $points < $all_points && 'off' === $evaluated_by_admin ) : ?>
                    <div class="quiz_podsumowanie">
                        <div class="quiz_podsumowanie_tytul"><?php _e( 'Not bad!', BPMJ_EDDCM_DOMAIN ); ?></div>
                        <div class="quiz_podsumowanie_zdjecie">
                            <div class="quiz_podsumowanie_zdjecie_ramka">
                                <img src="<?php echo bpmj_eddcm_template_get_file( 'assets/img/ok.png' ); ?>">
                            </div>
                        </div>
                        <div class="quiz_podsumowanie_wynik"><?php _e( 'Your result', BPMJ_EDDCM_DOMAIN ); ?>:</div>
                        <div class="quiz_podsumowanie_punkty"><?php echo ( ( $points < 0 ) ? 0 : $points ) . $all_points_string; ?></div>
                        <?php if ( 'on' === $can_see_answers ) : ?>
                            <div class="quiz_podsumowanie_tekst">
                                <a class="niepoprawne" href="#"><?php _e( 'See incorrect answers', BPMJ_EDDCM_DOMAIN ); ?></a>
                            </div>
                        <?php endif; ?>
                        <a href="#" class="quiz_podsumowanie_nastepna_lekcja prev bpmj-eddcm-quiz-again" data-quiz="<?php echo get_the_ID(); ?>" data-course="<?php echo $course_page_id; ?>">
                            <i class="fas fa-caret-left"></i>
                            <?php _e( 'Again', BPMJ_EDDCM_DOMAIN ); ?>
                        </a>
                        <?php
                        $course_id      = get_post_meta( get_the_ID(), '_bpmj_eddcm', true );
                        $course_page_id = get_post_meta( $course_id, 'course_id', true );
                        $course_page = WPI()->courses->get_next_sibling_of( $course_page_id, get_the_ID() );
                        if ( ! is_null( $course_page ) && $course_page->should_be_grayed_out() ) : ?>
                            <div class="nastepna_lekcja_off">
                                <?php _e( 'NEXT LESSON', BPMJ_EDDCM_DOMAIN ); ?>
                                <i class="fas fa-caret-right"></i>
                            </div>
                        <?php elseif ( ! is_null( $course_page ) ) : ?>
                            <?php echo WPI()->templates->get_next_lesson_nav( ' <i class="fas fa-caret-right"></i>', 'lekcja_nast_pop lekcja_nas' ); ?>
                        <?php endif; ?>
                        <?php if ( 'on' === $can_see_answers ) : ?>
                            <div class="niepoprawne_odp quizy_contenter">
                                <div id="niepoprawne_odp_zamknij"><img src="<?php echo bpmj_eddcm_template_get_file( 'assets/img/x.png' ); ?>"></div>
                                <div class="quizy_contenter_postep">
                                    <div class="lecja_top_postep">
                                        <i class="fas fa-chart-line"></i>
                                        <span><?php _e( 'Progress', BPMJ_EDDCM_DOMAIN ); ?>:</span>
                                        <div class="postep_contenter">
                                            <div class="postep_contenter_postep" style="width: 100%;">
                                                <div class="postep_liczba">100%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="bpmj-eddcm-questions-carousel-answers" class="quizy_contenter_quizy carousel slide" data-ride="carousel" data-interval="false" data-wrap="false">
                                    <form id="bpmj-eddcm-questions-form" method="post" action="?wpi_route=quiz/finish_quiz" enctype="multipart/form-data">
                                        <div class="carousel-inner" role="listbox">
                                            <?php
                                            $user_answers = get_post_meta( $query->post->ID, 'user_answers', true );
                                            $question_id = 1;
                                            foreach ( $questions as $key => $question ) : ?>
                                                <div class="quizy_contenter_quiz item <?php echo 0 == $key ? 'active' : ''; ?>" style="width: 10%;">
                                                    <div class="quizy_contenter_quiz_tytul">
                                                        <span><?php echo $question_id; ?></span>
                                                        <p><?php echo do_shortcode($question['title']); ?></p>
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

                                                                        $answer_type = '';
                                                                        if ( (int) $answer['points'] > 0 ) {
                                                                            if ( empty( $checked ) )
                                                                                $answer_type = 'bad';
                                                                            else
                                                                                $answer_type = 'good';
                                                                        } else {
                                                                            if ( ! empty( $checked ) )
                                                                                $answer_type = 'bad';
                                                                        }
                                                                        ?>
                                                                        <input id="bpmj-eddcm-question-<?php echo $question['id']; ?>-answer-<?php echo $answer['id']; ?>" type="radio"<?php echo $checked; ?> class="fa <?php echo $answer_type; ?>" name="bpmj_eddcm_question[<?php echo $question['id']; ?>][answer]" value="<?php echo $answer['id']; ?>" disabled>
                                                                        <label for="bpmj-eddcm-question-<?php echo $question['id']; ?>-answer-<?php echo $answer['id']; ?>" class="<?php echo $answer_type; ?>">
                                                                            <strong><?php echo $answer_char; ?>.</strong>
                                                                            <?php echo do_shortcode( $answer['title'] ); ?>
                                                                        </label>
                                                                    </li>
                                                                    <?php $answer_char++; ?>
                                                                <?php endforeach; ?>
                                                            <?php elseif ( 'single_select' === $question['type'] ) :
                                                                $answer_type = '';
                                                                if ( (int) $question['answer'][ $user_answers[ $question['id'] ]['answer'] ]['points'] > 0 ) {
                                                                    $answer_type = 'good';
                                                                } else {
                                                                    $answer_type = 'bad';
                                                                }

                                                                $correct_answer = null;
                                                                ?>
                                                                <li class="answer-<?php echo $answer_type; ?>">
                                                                    <input type="hidden" name="bpmj_eddcm_question[<?php echo $question['id']; ?>][question_id]" value="<?php echo $question['id']; ?>">
                                                                    <select id="bpmj-eddcm-question-<?php echo $question['id']; ?>-answer" name="bpmj_eddcm_question[<?php echo $question['id']; ?>][answer]" class="bpmj-eddcm-single-select-question">
                                                                        <option value=""><?php _e( 'Select answer', BPMJ_EDDCM_DOMAIN ); ?></option>
                                                                        <?php foreach ( $question['answer'] as $answer ) : ?>
                                                                            <?php
                                                                            if ( (int) $answer['points'] > 0 )
                                                                                $correct_answer = $answer;

                                                                            $checked = '';
                                                                            if ( isset( $user_answers[ $question['id'] ] ) && $user_answers[ $question['id'] ]['answer'] === $answer['id'] )
                                                                                $checked = ' selected';
                                                                            ?>
                                                                            <option<?php echo $checked; ?> value="<?php echo $answer['id']; ?>" disabled><?php echo $answer['title']; ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                    <?php if ( $answer_type === 'bad' ) :
                                                                        ?>
                                                                        <p><?php _e( 'Correct answer', BPMJ_EDDCM_DOMAIN ); ?>: <?php echo $correct_answer['title']; ?></p>
                                                                    <?php endif; ?>
                                                                </li>
                                                            <?php elseif ( 'multiple' === $question['type'] ) :
                                                                $user_answer = $user_answers[ $question['id'] ][ 'answer' ]; ?>
                                                                <?php foreach ( $question['answer'] as $answer ) : ?>
                                                                    <li>
                                                                        <?php
                                                                        $checked = '';
                                                                        if ( isset( $user_answers[ $question['id'] ] ) ) {
                                                                            if ( in_array( $answer['id'], explode( ',', $user_answers[ $question['id'] ]['answer'] ) ) )
                                                                                $checked = ' checked="checked"';
                                                                        }

                                                                        $answer_type = '';
                                                                        if ( (int) $answer['points'] > 0 ) {
                                                                            if ( empty( $checked ) )
                                                                                $answer_type = 'bad';
                                                                            else
                                                                                $answer_type = 'good';
                                                                        } else {
                                                                            if ( ! empty( $checked ) )
                                                                                $answer_type = 'bad';
                                                                        }
                                                                        ?>
                                                                        <input id="bpmj-eddcm-question-<?php echo $question['id']; ?>-answer-<?php echo $answer['id']; ?>" data-to-id="#bpmj-eddcm-question-<?php echo $question['id']; ?>-answer" type="checkbox"<?php echo $checked; ?> class="fa <?php echo $answer_type; ?>" name="bpmj_eddcm_question_fake[<?php echo $question['id']; ?>][answer]" value="<?php echo $answer['id']; ?>" disabled>
                                                                        <label for="bpmj-eddcm-question-<?php echo $question['id']; ?>-answer-<?php echo $answer['id']; ?>" class="<?php echo $answer_type; ?>">
                                                                            <strong><?php echo $answer_char; ?>.</strong>
                                                                            <?php echo do_shortcode( $answer['title'] ); ?>
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
                                    <div id="quizy_contenter_paginacja_prev" class="left" href="#bpmj-eddcm-questions-carousel-answers" role="button" data-slide="prev">
                                        <i class="fas fa-caret-left"></i> <?php _e( 'BACK', BPMJ_EDDCM_DOMAIN ); ?>
                                    </div>
                                    <div id="quizy_contenter_paginacja_next" class="right" href="#bpmj-eddcm-questions-carousel-answers" role="button" data-slide="next">
                                        <?php _e( 'NEXT', BPMJ_EDDCM_DOMAIN ); ?> <i class="fas fa-caret-right"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="niepoprawne_odp_tlo"></div>
                        <?php endif; ?>
                    </div>
                <?php elseif ( $points < $pass_points && 'off' === $evaluated_by_admin ) : ?>
                    <div class="quiz_podsumowanie">
                        <div class="quiz_podsumowanie_tytul"><?php _e( 'Unfortunately', BPMJ_EDDCM_DOMAIN ); ?></div>
                        <div class="quiz_podsumowanie_zdjecie">
                            <div class="quiz_podsumowanie_zdjecie_ramka">
                                <img src="<?php echo bpmj_eddcm_template_get_file( 'assets/img/zero.png' ); ?>">
                            </div>
                        </div>
                        <div class="quiz_podsumowanie_wynik"><?php _e( 'Your result', BPMJ_EDDCM_DOMAIN ); ?>:</div>
                        <div class="quiz_podsumowanie_punkty zero"><?php echo ( ( $points < 0 ) ? 0 : $points ). $all_points_string; ?></div>
                        <div class="quiz_podsumowanie_tekst">
                            <?php if ( $points == 0) : ?>
                                <?php _e( 'Unfortunately, you answered all the questions wrong. Do not give up and try again, you will definitely go better!', BPMJ_EDDCM_DOMAIN ); ?>
                            <?php else : ?>
                                <?php _e( 'Unfortunately, you scored too few points. Do not give up and try again, you will definitely go better!', BPMJ_EDDCM_DOMAIN ); ?>
                            <?php endif; ?>
                        </div>
                        <div href="#" class="quiz_podsumowanie_nastepna_lekcja prev bpmj-eddcm-quiz-again" data-quiz="<?php echo get_the_ID(); ?>" data-course="<?php echo $course_page_id; ?>">
                            <i class="fas fa-caret-left"></i>
                            <?php _e( 'AGAIN', BPMJ_EDDCM_DOMAIN ); ?>
                        </div>
                        <?php
                        $course_id      = get_post_meta( get_the_ID(), '_bpmj_eddcm', true );
                        $course_page_id = get_post_meta( $course_id, 'course_id', true );
                        $course_page = WPI()->courses->get_next_sibling_of( $course_page_id, get_the_ID() );
                        if (  ! is_null( $course_page ) && $course_page->should_be_grayed_out() ) : ?>
                            <div class="nastepna_lekcja_off">
                                <?php _e( 'NEXT LESSON', BPMJ_EDDCM_DOMAIN ); ?>
                                <i class="fas fa-caret-right"></i>
                            </div>
                        <?php elseif ( ! is_null( $course_page ) ) : ?>
                            <?php echo WPI()->templates->get_next_lesson_nav( ' <i class="fas fa-caret-right"></i>', 'lekcja_nast_pop lekcja_nas' ); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Pole z materiałami do pobrania -->
            <?php if ( is_array( $files ) ): ?>
                <a name="files"></a>
                <h3><?php _e( 'Files for download', BPMJ_EDDCM_DOMAIN ); ?></h3>
                <div class="pliki_do_pobrania">
                    <div class="row">
                        <div class="col-sm-6">
                            <ul>
                                <?php
                                $files_cnt	 = count( $files );
                                $i			 = 0;
                                foreach ( $files as $fileID => $file ):
                                ?>
                                <li><a href="<?php echo bpmj_eddpc_encrypt_link( wp_get_attachment_url( $fileID ), $lesson_page_id ); ?>" target="_blank"><i class="far fa-file-<?php WPI()->templates->the_file_icon( $fileID ); ?>"></i><?php echo get_the_title( $fileID ); ?></a></li>
                                <?php if ( $i == (int) ($files_cnt / 2) ) : ?>
                            </ul>
                        </div>
                        <div class="col-sm-6">
                            <ul>
                                <?php endif;
                                $i++; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <!-- Koniec pola z materiałami do pobrania -->

            <div id="course-navigation-section">
                2
                <?php WPI()->templates->html_navigation_section( null, $course_page_id, $lesson_page_id ); ?>
            </div>

            <?php if ( comments_open() || get_comments_number() ) : ?>
                <!-- Sekcja z komentarzami -->

                <div class="tabs">
                    <div class="tab">
                        <ul>
                            <li id="tab_komentarze" class="active"><p>Komentarze</p></li>
                            <!--<li id="tab_notatki" class=""><p>Notatki</p></li-->
                        </ul>
                    </div>
                    <div id="tab_cont_komentarze" class="tab_cont tab_komentarze" style="display: block;">
                        <?php comments_template(); ?>
                    </div>
                </div>

                <!-- Koniec sekcji z komentarzami -->
            <?php endif; ?>

        </div>

    </div>

<?= View::get('/scripts/check-lesson-as-undone'); ?>

<?php
WPI()->templates->footer( 'alt' );
