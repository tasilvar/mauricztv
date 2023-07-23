<?php
use bpmj\wpidea\modules\app_view\api\App_View_API_Static_Helper;
use bpmj\wpidea\Course_Progress;
use bpmj\wpidea\View;
?>

<?php
if (WPI()->page->has_template()):
    WPI()->page->render_template();

    return;
endif;
?>

<?php WPI()->templates->header(); ?>
<?php
$files_section_position      = WPI()->templates->get_download_section_position();
$files                       = WPI()->templates->get_meta( 'files' );
$navigation_section_position = WPI()->templates->get_navigation_section_position();
$progress_section_position   = WPI()->templates->get_progress_section_position();
$lesson_page_id              = get_the_ID();
$course_page_id              = WPI()->courses->get_course_top_page( $lesson_page_id );
$course_progress_section     = function ( $course_page_id, $lesson_page_id, $header_class = 'center' ) {
	$user_progress = new Course_Progress( $course_page_id, $lesson_page_id );
	if ( $user_progress->is_tracking_enabled() ):
		?>
		<div class="box">
			<h2 class="bg <?php echo $header_class; ?>"><?php _e( 'Course progress', BPMJ_EDDCM_DOMAIN ); ?></h2>
			<p id="course-progress" data-course-page-id="<?php echo $course_page_id; ?>"
			   data-lesson-page-id="<?php echo $lesson_page_id; ?>"
               data-spinner="<?= WPI()->templates->get_spinner_url() ?>">
				<?php $user_progress->output_course_progress_widget(); ?>
			</p>
		</div>
		<?php
	endif;
};
?>


	<!-- Sekcja pod paskiem z menu (opis lekcji) -->
	<section class="description">
		<div class="wrapper">

			<h2 class="bg center"><?php the_title(); ?></h2>


			<?php
			if ( WPI()->templates->get_meta( 'subtitle_mode' ) != 'off' ) {
				echo '<p>' . WPI()->templates->get_meta( 'subtitle' ) . '</p>';
			}
			?>


			<!-- Breadcrumbs -->
			<?php WPI()->templates->breadcrumbs(); ?>
			<!-- Koniec breadcrumbs -->

		</div>
	</section>
	<!-- Koniec sekcji pod paskiem z menu -->

	<!-- Nawigacja (poprzednia i następna lekcja) -->
	<div class="navigation">
		<div class="wrapper">
			<p class="prev"><?php echo WPI()->templates->get_previous_lesson_nav( '<span class="arrow">&lsaquo;</span>' ); ?></p>
			<p class="next"><?php echo WPI()->templates->get_next_lesson_nav( '<span class="arrow">&rsaquo;</span>' ); ?></p>
		</div>
	</div>
	<!-- Koniec nawigacji -->

	<!-- Sekcja z treścią lekcji i materiałami -->
	<section class="content">
		<div class="wrapper">
			<div class="right">

				<?php
				if ( 'side' === $progress_section_position ):
					$course_progress_section( $course_page_id, $lesson_page_id );
				endif;
				?>

				<?php if ( WPI()->templates->get_meta( 'level_mode' ) != 'off' || WPI()->templates->get_meta( 'duration_mode' ) != 'off' || WPI()->templates->get_meta( 'shortdesc_mode' ) != 'off' ) { ?>
					<!-- Pole z informacjami o lekcji -->
					<div class="box">
						<h2 class="bg center"><?php _e( 'Lesson details', BPMJ_EDDCM_DOMAIN ); ?></h2>

						<?php if ( WPI()->templates->get_meta( 'level_mode' ) != 'off' ) { ?>
							<p><strong><?php _e( 'Difficulty level', BPMJ_EDDCM_DOMAIN ); ?>
									:</strong> <?php WPI()->templates->the_meta( 'level' ); ?></p>
						<?php } ?>

						<?php if ( WPI()->templates->get_meta( 'duration_mode' ) != 'off' ) { ?>
							<p><strong><?php _e( 'Duration time', BPMJ_EDDCM_DOMAIN ); ?>
									:</strong> <?php WPI()->templates->the_meta( 'duration' ); ?></p>
						<?php } ?>

						<?php if ( WPI()->templates->get_meta( 'shortdesc_mode' ) != 'off' ) { ?>
							<p><?php WPI()->templates->the_meta( 'shortdesc' ); ?></p>
						<?php } ?>
					</div>
					<!-- Koniec pola z informacjami o lekcji -->
				<?php } ?>

				<!-- Pole z materiałami do pobrania -->
				<?php if ( 'side' === $files_section_position && is_array( $files ) ): ?>
					<div class="box">
						<h2 class="bg center"><?php _e( 'Files for download', BPMJ_EDDCM_DOMAIN ); ?></h2>
						<?php
						foreach ( $files as $fileID => $file ):
							?>
							<div class="download <?php WPI()->templates->the_file_icon( $fileID ); ?>">
								<h3>
									<a href="<?php echo bpmj_eddpc_encrypt_link( wp_get_attachment_url( $fileID ), $lesson_page_id ); ?>"
                                        <?php if(!App_View_API_Static_Helper::is_active()){ ?> target="_blank" <?php } ?>><?php echo get_the_title( $fileID ); ?></a></h3>
								<p><?php echo $file[ 'desc' ]; ?></p>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<!-- Koniec pola z materiałami do pobrania -->

				<?php if ( 'side' === $navigation_section_position ): ?>
					<div id="course-navigation-section">
						<?php
						WPI()->templates->html_navigation_section( $navigation_section_position, $course_page_id, $lesson_page_id );
						?>
					</div>
				<?php endif; ?>
			</div>

			<!-- Pole z treścią lekcji -->
			<div class="left">
				<div class="content">
					<?php
					if ( have_posts() ) {
						while ( have_posts() ) {
							the_post();
							the_content();
						}
					}
					?>
					<?php if ( 'below' === $files_section_position && is_array( $files ) ): ?>
						<div class="box-wrapper">
							<div class="box">
								<h2 class="bg left"><?php _e( 'Files for download', BPMJ_EDDCM_DOMAIN ); ?></h2>
								<?php
								foreach ( $files as $fileID => $file ):
									?>
									<div class="download <?php WPI()->templates->the_file_icon( $fileID ); ?>">
										<h3>
											<a href="<?php echo bpmj_eddpc_encrypt_link( wp_get_attachment_url( $fileID ), $lesson_page_id ); ?>"
											   target="_blank"><?php echo get_the_title( $fileID ); ?></a></h3>
										<p><?php echo $file[ 'desc' ]; ?></p>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>

					<?php
					if ( 'below' === $navigation_section_position ) {
						?>
						<div class="box-wrapper" id="course-navigation-section">
							5
							<?php
							WPI()->templates->html_navigation_section( $navigation_section_position, $course_page_id, $lesson_page_id );
							?>
						</div>
						<?php
					}

					if ( 'below' === $progress_section_position ) {
						?>
						<div class="box-wrapper">
							<?php
							$course_progress_section( $course_page_id, $lesson_page_id, 'left' );
							?>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<!-- Koniec pola z treścią lekcji -->
		</div>
	</section>
	<!-- Koniec sekcji z treścią lekcji i materiałami -->


<?php if ( comments_open() || get_comments_number() ) : ?>
	<!-- Sekcja z komentarzami -->

	<?php comments_template(); ?>

	<!-- Koniec sekcji z komentarzami -->
<?php endif; ?>

<?= View::get('/scripts/check-lesson-as-undone'); ?>

<?php
WPI()->templates->footer( 'alt' );
