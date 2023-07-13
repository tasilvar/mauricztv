<?php
if(WPI()->page->has_template()):
    WPI()->page->render_template();
    return;
endif;

use bpmj\wpidea\modules\app_view\api\App_View_API_Static_Helper;
use bpmj\wpidea\View;
?>
<?php WPI()->templates->header(); ?>

<?php
$files					 = WPI()->templates->get_meta( 'files' );
$lesson_page_id			 = get_the_ID();
$course_page_id			 = WPI()->courses->get_course_top_page( $lesson_page_id );
?>

<div id="content">
	<?php WPI()->templates->lesson_top_bar(); ?>
	<div class="contenter contenter_lekcji">
		<div class="row lekcje_tytul">
			<div class="col-sm-7">
				<h1><?php the_title(); ?></h1>
			</div>
			<div class="col-sm-5 lekcje_paginacja">
				<?php echo WPI()->templates->get_previous_lesson_nav( '<i class="fas fa-caret-left"></i> ', 'lekcja_nast_pop lekcja_pop' );
				echo WPI()->templates->get_next_lesson_nav( ' <i class="fas fa-caret-right"></i>', 'lekcja_nast_pop lekcja_nas' );
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
								<li><a href="<?php echo bpmj_eddpc_encrypt_link( wp_get_attachment_url( $fileID ), $lesson_page_id ); ?>" <?php if(!App_View_API_Static_Helper::is_active()){ ?> target="_blank" <?php } ?>><i class="far fa-file-<?php WPI()->templates->the_file_icon( $fileID ); ?>"></i><?php echo get_the_title( $fileID ); ?></a></li>
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
		<?php WPI()->templates->html_navigation_section( null, $course_page_id, $lesson_page_id ); ?>
	</div>

<?php if ( comments_open() || get_comments_number() ) : ?>
	<!-- Sekcja z komentarzami -->
	<?php comments_template(); ?>
	<!-- Koniec sekcji z komentarzami -->
<?php endif; ?>

	</div>

</div>

<?php
echo View::get('/scripts/check-lesson-as-undone');
?>

<?php
WPI()->templates->footer( 'alt' );
