<?php

use bpmj\wpidea\Course_Progress;
use bpmj\wpidea\View;

if(WPI()->page->has_template()):
    WPI()->page->render_template();

    return;
endif;
?>

<?php WPI()->templates->header();

if ( is_tax( 'download_category' ) || is_tax( 'download_tag' ) ) {
	include( 'template_parts/archive-download_category.php' );

	return;
}

$thumb = WPI()->templates->get_meta( 'banner' );
if( empty( $thumb ) ) {
	$thumb = bpmj_eddcm_template_get_file( 'assets/img/panelkursu.jpg' );
}

$course_page_id = WPI()->courses->get_course_top_page( get_the_ID() );
$progress = new Course_Progress( $course_page_id );
?>

<style type="text/css">
#panel_kursu_slider {
   background-image: url(<?php echo $thumb; ?>);
}
.postep_contenter_postep {
	width: <?php echo (int)($progress->get_progress_percent() * 145 / 100) ?>px;
}
</style>

<div id="content">

<?php WPI()->templates->lesson_top_bar(); ?>

<?php
$template_settings = get_option( WPI()->settings->get_layout_template_settings_slug() );
if ( 'on' === $template_settings['scarlet'][ 'disable_banners' ] ):
?>
    <div id="panel_kursu_slider_placeholder"></div>
<?php else: ?>
    <div id="panel_kursu_slider" class="krotki_slider">
        <div class="contenter">
            <div><?php the_title(); ?></div>
        </div>
    </div>
<?php endif; ?>
	
<div class="contenter">
<?php WPI()->templates->breadcrumbs(); ?>
</div>
	
<div class="contenter contenter_tresci">
	
<?php

if ( WPI()->templates->get_meta( 'first_section' ) != 'off' ) { ?>
    <div>
		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				the_content();
			}
		}
		?>
	</div>
<?php } ?>

    <?php echo View::get('/course-panel/drip-counting-script'); ?>

    <?php
    $modules = WPI()->courses->get_course_level1_modules_or_lessons( $course_page_id );
    if ( ! empty( $modules ) ) :
    ?>
	<div class="row panel_kursu_moduly">

		<?php

			foreach ( $modules as $id => $module ) {
				?>
                <div class="col-sm-4">
                    <div class="modul_lekcja<?php if ( $module->should_be_grayed_out() ) echo ' lek_niedostepna'; ?>">
						<?php if ( $module->should_be_grayed_out() ): ?>
                            <div class="modul_lekcja_zdjecie">
								<?php $drip = $module->get_calculated_drip();
								if( !empty( $drip ) ) { 
									
								?>
								<div class="lekcja_niedostepna" data-drip="<?php echo $drip ?>">
									<p><?php if( $module->is_lesson() ) { _e( 'The lesson will be available in', BPMJ_EDDCM_DOMAIN ); } else {
										_e( 'The module will be available in', BPMJ_EDDCM_DOMAIN ); } ?>:</p>
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
								<img src="<?php if ( $module->get_thumbnail() ) { echo $module->get_thumbnail(); } else { echo bpmj_eddcm_template_get_file( 'assets/img/box1.jpg' ); } ?>" />
							</div>
							<div class="modul_lekcja_tytul">
								<?php echo $module->post_title; ?>
                            </div>
							<?php if ( $module->get_subtitle() ) { ?>
                                <?php echo '<div class="modul_lekcja_opis">' . $module->get_subtitle() . '</div>'; ?><?php } ?>
						<?php else: ?>
                            <a href="<?php echo get_permalink( $module->unwrap() ); ?>">
                                <div class="modul_lekcja_zdjecie">
									<img src="<?php if ( $module->get_thumbnail() ) { echo $module->get_thumbnail(); } else { echo bpmj_eddcm_template_get_file( 'assets/img/box1.jpg' ); } ?>" />
								</div>
                            </a>
                            <div class="modul_lekcja_tytul">
                                <a href="<?php echo get_permalink( $module->unwrap() ); ?>"><?php echo $module->post_title; ?></a>
                            </div>
							<?php if ( $module->get_subtitle() ) { ?><?php echo '<div class="modul_lekcja_opis">' . $module->get_subtitle() . '</div>'; ?>
                                <?php } ?>
						<?php endif; ?>
                    </div>
                </div>
				<?php
			}
			?>
	</div>

    <?php endif; ?>

<?php if ( WPI()->templates->get_meta( 'last_section' ) == 'on' ) { ?>
<?php WPI()->templates->html_navigation_section( null, $course_page_id, null ); ?>
<?php } ?>
	
</div>
</div>	
	
<?php WPI()->templates->footer(); ?>