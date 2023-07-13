<?php

use bpmj\wpidea\Course_Progress;
use bpmj\wpidea\View;

if(WPI()->page->has_template()):
    WPI()->page->render_template();

    return;
endif;
?>
<?php WPI()->templates->header();

$course_page_id = WPI()->courses->get_course_top_page( get_the_ID() );
$progress = new Course_Progress( $course_page_id );
?>

<style type="text/css">
.postep_contenter_postep {
	width: <?php echo (int)($progress->get_progress_percent() * 145 / 100) ?>px;
}
</style>

<div id="content" class="modul">

<?php WPI()->templates->lesson_top_bar(); ?>
	
<div class="contenter">
<h1><?php the_title(); ?></h1>
<?php WPI()->templates->breadcrumbs(); ?>
</div>

	<div class="contenter contenter_tresci">
	
<?php
if ( have_posts() ) {
?>
    <div>
		<?php
			while ( have_posts() ) {
				the_post();
				the_content();
			}
		?>
	</div>
<?php } ?>

        <?php echo View::get('/course-panel/drip-counting-script'); ?>
		
	<div class="row modul_lista_lekcji">
		<?php
			$module = WPI()->courses->get_course_structure_module( get_post() );
			if ( $module ) {
				foreach ( $module->get_children() as $lesson ) {
				?>
                <div class="col-sm-4">
                    <div class="modul_lekcja<?php if ( $lesson->should_be_grayed_out() ) echo ' lek_niedostepna'; ?>">
						<?php if ( $lesson->should_be_grayed_out() ): ?>
                            <div class="modul_lekcja_zdjecie">
								<?php $drip = $lesson->get_calculated_drip();
								if( !empty( $drip ) ) { 
									
								?>
								<div class="lekcja_niedostepna" data-drip="<?php echo $drip ?>">
									<p><?php _e( 'The lesson will be available in', BPMJ_EDDCM_DOMAIN ) ?>:</p>
									<div>
										<div class="lekcja_niedostepna_zegar"><i class="fas fa-clock"></i></div>
										<div class="lekcja_niedostepna_time">
											<div class="drip_time_d"><?php echo bpmj_eddcm_seconds_to_time( $drip, 'a' ) ?></div>
											<div><?php _e( 'days', BPMJ_EDDCM_DOMAIN ) ?></div>
										</div>
										<div class="lekcja_niedostepna_time">:</div>
										<div class="lekcja_niedostepna_time">
											<div class="drip_time_h"><?php echo bpmj_eddcm_seconds_to_time( $drip, 'h' ) ?></div>
											<div><?php _e( 'hr', BPMJ_EDDCM_DOMAIN ) ?></div>
										</div>
										<div class="lekcja_niedostepna_time">:</div>
										<div class="lekcja_niedostepna_time">
											<div class="drip_time_m"><?php echo bpmj_eddcm_seconds_to_time( $drip, 'i' ) ?></div>
											<div><?php _e( 'min', BPMJ_EDDCM_DOMAIN ) ?></div>
										</div>
										<div class="lekcja_niedostepna_time">:</div>
										<div class="lekcja_niedostepna_time">
											<div class="drip_time_s"><?php echo bpmj_eddcm_seconds_to_time( $drip, 's' ) ?></div>
											<div><?php _e( 'sec', BPMJ_EDDCM_DOMAIN ) ?></div>
										</div>
									</div>
								</div>
								<?php } ?>
								<img src="<?php if ( $lesson->get_thumbnail() ) { echo $lesson->get_thumbnail(); } else { echo bpmj_eddcm_template_get_file( 'assets/img/box1.jpg' ); } ?>" />
							</div>
							<div class="modul_lekcja_tytul">
								<?php echo $lesson->post_title; ?>
                            </div>
							<?php if ( $lesson->get_subtitle() ) { ?>
								<div class="modul_lekcja_opis"><?= $lesson->get_subtitle(); ?></div>
							<?php } ?>
						<?php else: ?>
                            <a href="<?php echo get_permalink( $lesson->unwrap() ); ?>">
                                <div class="modul_lekcja_zdjecie">
									<img src="<?php if ( $lesson->get_thumbnail() ) { echo $lesson->get_thumbnail(); } else { echo bpmj_eddcm_template_get_file( 'assets/img/box1.jpg' ); } ?>" />
								</div>
                            </a>
                            <div class="modul_lekcja_tytul">
                                <a href="<?php echo get_permalink( $lesson->unwrap() ); ?>"><?php echo $lesson->post_title; ?></a>
                            </div>
							<?php if ( $lesson->get_subtitle() ) { ?><?php echo '<div class="modul_lekcja_opis">' . $lesson->get_subtitle() . '</div>'; ?><?php } ?>
						<?php endif; ?>
                    </div>
                </div>
				<?php
				}
			}
			?>
	</div>
    <?php if ( comments_open() || get_comments_number() ) : ?>
        <!-- Sekcja z komentarzami -->

       <?php comments_template(); ?>

       <!-- Koniec sekcji z komentarzami -->
    <?php endif; ?>


</div>	
</div>


	
<?php WPI()->templates->footer(); ?>