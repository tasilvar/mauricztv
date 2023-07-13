<?php
/** @var string $evaluated_by_admin */
/** @var boolean $is_passed */
/** @var bool $can_see_answers */
/** @var boolean $points */
/** @var boolean $all_points */
/** @var boolean $all_points_string */
/** @var int $course_page_id */
/** @var bool $number_of_attempts_to_solve_the_quiz_was_used */
/** @var int $attempts_left */
/** @var int $pass_points */
/** @var array $questions */
/** @var array $user_answers */
/** @var bool $also_show_correct_answers */
/** @var bool $time_is_up */

?>

<?php use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\View;

if ('on' === $evaluated_by_admin ) : ?>
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
            <?php if ($time_is_up): ?>
                <div class="quiz_time_is_up_info"><?= Translator_Static_Helper::translate('quiz.end_view.time_is_up') ?></div>
            <?php endif; ?>
			<?php if ($can_see_answers) : ?>
				<a class="quiz-big-button niepoprawne"
				   href="#"><?= Translator_Static_Helper::translate('quiz.answers_preview.see_answers') ?></a>
			<?php endif; ?>
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

            <?php if ($time_is_up): ?>
                <div class="quiz_time_is_up_info"><?= Translator_Static_Helper::translate('quiz.end_view.time_is_up') ?></div>
            <?php endif; ?>

			<?php
			if (!$number_of_attempts_to_solve_the_quiz_was_used){
				?>
				<div class="quiz_podsumowanie_tekst">
					<?php if ( $points == 0) : ?>
						<?php _e( 'Unfortunately, you answered all the questions wrong. Do not give up and try again, you will definitely go better!', BPMJ_EDDCM_DOMAIN ); ?>
					<?php else : ?>
						<?php _e( 'Unfortunately, you scored too few points. Do not give up and try again, you will definitely go better!', BPMJ_EDDCM_DOMAIN ); ?>
					<?php endif; ?>
				</div>
				<?php if (!empty($attempts_left)) : ?>
					<div class="quiz-attempts-left-info"><?= sprintf(Translator_Static_Helper::translate('course_editor.sections.structure.quiz.number_test_attempts.attempts_left_info'), $attempts_left); ?></div>
				<?php endif; ?>
				<?php if ($can_see_answers) : ?>
					<a class="quiz-big-button niepoprawne"
					   href="#"><?= Translator_Static_Helper::translate('quiz.answers_preview.see_answers') ?></a>
				<?php endif; ?>
				<div href="#" class="quiz_podsumowanie_nastepna_lekcja prev bpmj-eddcm-quiz-again" data-quiz="<?php echo get_the_ID(); ?>" data-course="<?php echo $course_page_id; ?>">
					<i class="fas fa-caret-left"></i>
					<?= Translator_Static_Helper::translate('quiz.end_view.try_again') ?>
				</div>

				<?php
			}else{
				echo'<div class="quiz_podsumowanie_tekst">';
				if ($points == 0) {
					echo Translator_Static_Helper::translate('course_editor.sections.structure.quiz.number_test_attempts.limit.wrong');
				} else {
					echo Translator_Static_Helper::translate('course_editor.sections.structure.quiz.number_test_attempts.limit.few_points');
				}
				echo'</div><br>';

				if ($can_see_answers) {
					echo '<a class="quiz-big-button niepoprawne" href="#">'
					     . Translator_Static_Helper::translate('quiz.answers_preview.see_answers')
					     . '</a>';
				}
			}

			$course_id      = get_post_meta( get_the_ID(), '_bpmj_eddcm', true );
			$course_page_id = get_post_meta( $course_id, 'course_id', true );
			$course_page = WPI()->courses->get_next_sibling_of( $course_page_id, get_the_ID() );

			if (  ! is_null( $course_page ) && $course_page->should_be_grayed_out() ) : ?>
				<div class="nastepna_lekcja_off">
					<?php _e( 'NEXT LESSON', BPMJ_EDDCM_DOMAIN ); ?>
					<i class="fas fa-caret-right"></i>
				</div>
			<?php elseif ( ! is_null( $course_page ) ) : ?>
				<?php echo WPI()->templates->get_next_lesson_nav( ' <i class="fas fa-caret-right"></i>', 'lekcja_nast_pop lekcja_nas quiz-big-button' ); ?>
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
            <?php if ($time_is_up): ?>
                <div class="quiz_time_is_up_info"><?= Translator_Static_Helper::translate('quiz.end_view.time_is_up') ?></div>
            <?php endif; ?>

			<?php
			if (!$number_of_attempts_to_solve_the_quiz_was_used){
				?>
				<?php if (!empty($attempts_left)) : ?>
					<div class="quiz-attempts-left-info"><?= sprintf(Translator_Static_Helper::translate('course_editor.sections.structure.quiz.number_test_attempts.attempts_left_info'), $attempts_left); ?></div>
				<?php endif; ?>
				<?php if ($can_see_answers) : ?>
					<a class="quiz-big-button niepoprawne"
					   href="#"><?= Translator_Static_Helper::translate('quiz.answers_preview.see_answers') ?></a>
				<?php endif; ?>
				<div href="#" class="quiz_podsumowanie_nastepna_lekcja prev bpmj-eddcm-quiz-again" data-quiz="<?php echo get_the_ID(); ?>" data-course="<?php echo $course_page_id; ?>">
					<i class="fas fa-caret-left"></i>
					<?= Translator_Static_Helper::translate('quiz.end_view.try_again') ?>
				</div>
				<?php
			}else{
				echo'<div class="quiz_podsumowanie_tekst">
                        ' . Translator_Static_Helper::translate('course_editor.sections.structure.quiz.number_test_attempts.limit') . '
                    </div><br>';
				if ($can_see_answers) {
					echo '<a class="quiz-big-button niepoprawne" href="#">'
					     . Translator_Static_Helper::translate('quiz.answers_preview.see_answers')
					     . '</a>';
				}
			}

			echo WPI()->templates->get_next_lesson_nav( ' <i class="fas fa-caret-right"></i>', 'lekcja_nast_pop lekcja_nas quiz-big-button' ); ?>
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
        <?php if ($time_is_up): ?>
            <div class="quiz_time_is_up_info"><?= Translator_Static_Helper::translate('quiz.end_view.time_is_up') ?></div>
        <?php endif; ?>
		<?php
		if (!$number_of_attempts_to_solve_the_quiz_was_used){
			?>
			<?php if (!empty($attempts_left)) : ?>
				<div class="quiz-attempts-left-info"><?= sprintf(Translator_Static_Helper::translate('course_editor.sections.structure.quiz.number_test_attempts.attempts_left_info'), $attempts_left); ?></div>
			<?php endif; ?>
			<?php if ($can_see_answers) : ?>
				<a class="quiz-big-button niepoprawne"
				   href="#"><?= Translator_Static_Helper::translate('quiz.answers_preview.see_answers') ?></a>
			<?php endif; ?>
			<div href="#" class="quiz_podsumowanie_nastepna_lekcja prev bpmj-eddcm-quiz-again" data-quiz="<?php echo get_the_ID(); ?>" data-course="<?php echo $course_page_id; ?>">
				<i class="fas fa-caret-left"></i>
				<?= Translator_Static_Helper::translate('quiz.end_view.try_again') ?>
			</div>
			<?php
		}else{
			echo'<div class="quiz_podsumowanie_tekst">
                        ' . Translator_Static_Helper::translate('course_editor.sections.structure.quiz.number_test_attempts.limit') . '
                    </div><br>';
			if ($can_see_answers) {
				echo '<a class="quiz-big-button niepoprawne" href="#">'
				     . Translator_Static_Helper::translate('quiz.answers_preview.see_answers')
				     . '</a>';
			}
		}
		echo WPI()->templates->get_next_lesson_nav( ' <i class="fas fa-caret-right"></i>', 'lekcja_nast_pop lekcja_nas quiz-big-button' ); ?>
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
        <?php if ($time_is_up): ?>
            <div class="quiz_time_is_up_info"><?= Translator_Static_Helper::translate('quiz.end_view.time_is_up') ?></div>
        <?php endif; ?>
		<?php
		if (!$number_of_attempts_to_solve_the_quiz_was_used){
			?>
			<?php if (!empty($attempts_left)) : ?>
				<div class="quiz-attempts-left-info"><?= sprintf(Translator_Static_Helper::translate('course_editor.sections.structure.quiz.number_test_attempts.attempts_left_info'), $attempts_left); ?></div>
			<?php endif; ?>
			<?php if ($can_see_answers) : ?>
				<a class="quiz-big-button niepoprawne"
				   href="#"><?= Translator_Static_Helper::translate('quiz.answers_preview.see_answers') ?></a>
			<?php endif; ?>
			<a href="#" class="quiz_podsumowanie_nastepna_lekcja prev bpmj-eddcm-quiz-again"
			   data-quiz="<?php echo get_the_ID(); ?>" data-course="<?php echo $course_page_id; ?>">
				<i class="fas fa-caret-left"></i>
				<?= Translator_Static_Helper::translate('quiz.end_view.try_again') ?>
			</a>
			<?php
		}else{
			echo'<div class="quiz_podsumowanie_tekst">
                        ' . Translator_Static_Helper::translate('course_editor.sections.structure.quiz.number_test_attempts.limit') . '
                    </div><br>';

			if ($can_see_answers) {
				echo '<a class="quiz-big-button niepoprawne" href="#">'
				     . Translator_Static_Helper::translate('quiz.answers_preview.see_answers')
				     . '</a>';
			}
		}

		$course_id      = get_post_meta( get_the_ID(), '_bpmj_eddcm', true );
		$course_page_id = get_post_meta( $course_id, 'course_id', true );
		$course_page = WPI()->courses->get_next_sibling_of( $course_page_id, get_the_ID() );
		if ( ! is_null( $course_page ) && $course_page->should_be_grayed_out() ) : ?>
			<div class="nastepna_lekcja_off">
				<?php _e( 'NEXT LESSON', BPMJ_EDDCM_DOMAIN ); ?>
				<i class="fas fa-caret-right"></i>
			</div>
		<?php elseif ( ! is_null( $course_page ) ) : ?>
			<?php echo WPI()->templates->get_next_lesson_nav( ' <i class="fas fa-caret-right"></i>', 'lekcja_nast_pop lekcja_nas quiz-big-button' ); ?>
		<?php endif; ?>

	</div>
<?php elseif ( $points < $pass_points && 'off' === $evaluated_by_admin ) : ?>
	<?php
	$course_id      = get_post_meta( get_the_ID(), '_bpmj_eddcm', true );
	$course_page_id = get_post_meta( $course_id, 'course_id', true );
	?>
	<div class="quiz_podsumowanie">
		<div class="quiz_podsumowanie_tytul"><?php _e( 'Unfortunately', BPMJ_EDDCM_DOMAIN ); ?></div>
		<div class="quiz_podsumowanie_zdjecie">
			<div class="quiz_podsumowanie_zdjecie_ramka">
				<img src="<?php echo bpmj_eddcm_template_get_file( 'assets/img/zero.png' ); ?>">
			</div>
		</div>
		<div class="quiz_podsumowanie_wynik"><?php _e( 'Your result', BPMJ_EDDCM_DOMAIN ); ?>:</div>
		<div class="quiz_podsumowanie_punkty zero"><?php echo ( ( $points < 0 ) ? 0 : $points ). $all_points_string; ?></div>
        <?php if ($time_is_up): ?>
            <div class="quiz_time_is_up_info"><?= Translator_Static_Helper::translate('quiz.end_view.time_is_up') ?></div>
        <?php endif; ?>
		<?php
		if (!$number_of_attempts_to_solve_the_quiz_was_used){
			?>
			<div class="quiz_podsumowanie_tekst">
				<?php
				if ( $points == 0) : ?>
					<?php _e( 'Unfortunately, you answered all the questions wrong. Do not give up and try again, you will definitely go better!', BPMJ_EDDCM_DOMAIN ); ?>
				<?php else : ?>
					<?php _e( 'Unfortunately, you scored too few points. Do not give up and try again, you will definitely go better!', BPMJ_EDDCM_DOMAIN ); ?>
				<?php endif; ?>
				<?php if (!empty($attempts_left)) : ?>
					<div class="quiz-attempts-left-info"><?= sprintf(Translator_Static_Helper::translate('course_editor.sections.structure.quiz.number_test_attempts.attempts_left_info'), $attempts_left); ?></div>
				<?php endif; ?>
			</div>
			<?php if ($can_see_answers ) : ?>
				<a class="quiz-big-button niepoprawne" href="#"><?= Translator_Static_Helper::translate('quiz.answers_preview.see_answers') ?></a>
			<?php endif; ?>
			<div href="#" class="quiz_podsumowanie_nastepna_lekcja prev bpmj-eddcm-quiz-again" data-quiz="<?php echo get_the_ID(); ?>" data-course="<?php echo $course_page_id; ?>">
				<i class="fas fa-caret-left"></i>
				<?= Translator_Static_Helper::translate('quiz.end_view.try_again') ?>
			</div>
			<?php
		}else{
			echo'<div class="quiz_podsumowanie_tekst">';
			if ($points == 0) {
				echo Translator_Static_Helper::translate('course_editor.sections.structure.quiz.number_test_attempts.limit.wrong');
			} else {
				echo Translator_Static_Helper::translate('course_editor.sections.structure.quiz.number_test_attempts.limit.few_points');
			}
			echo'</div><br>';
			if ($can_see_answers ) : ?>
				<a class="quiz-big-button niepoprawne" href="#"><?= Translator_Static_Helper::translate('quiz.answers_preview.see_answers') ?></a>
			<?php endif;
		}
		?>

		<?php
		$course_page = WPI()->courses->get_next_sibling_of( $course_page_id, get_the_ID() );
		if (  ! is_null( $course_page ) && $course_page->should_be_grayed_out() ) : ?>
			<div class="nastepna_lekcja_off">
				<?php _e( 'NEXT LESSON', BPMJ_EDDCM_DOMAIN ); ?>
				<i class="fas fa-caret-right"></i>
			</div>
		<?php elseif ( ! is_null( $course_page ) ) : ?>
			<?php echo WPI()->templates->get_next_lesson_nav( ' <i class="fas fa-caret-right"></i>', 'lekcja_nast_pop lekcja_nas quiz-big-button' ); ?>
		<?php endif; ?>
	</div>
<?php endif; ?>

<?= View::get('/course/quiz/answers/answers-preview', [
	'can_see_answers' => $can_see_answers,
	'questions' => $questions,
	'user_answers' => $user_answers,
	'also_show_correct_answers' => $also_show_correct_answers,
]) ?>