<?php
global $post;
global $wpidea_settings;

use bpmj\wpidea\admin\settings\core\configuration\Design_Settings_Group;
use bpmj\wpidea\Course_Page;
use bpmj\wpidea\Course_Progress;

$files					 = WPI()->templates->get_meta( 'files' );
$course_page             = new Course_Page($post);
$lesson_page_id			 = get_the_ID();
$course_page_id			 = WPI()->courses->get_course_top_page( $lesson_page_id );
$user_progress           = new Course_Progress( $course_page_id, $lesson_page_id );

$course_progress_section = function ( $course_page_id, $lesson_page_id, $header_class = 'center' ) {
	$user_progress = new Course_Progress( $course_page_id, $lesson_page_id );
	if ( $user_progress->is_tracking_enabled() ):
		?>
		<div id="course-progress" data-course-page-id="<?php echo $course_page_id; ?>"
               data-lesson-page-id="<?php echo $lesson_page_id; ?>"
               data-spinner="<?= WPI()->templates->get_spinner_url() ?>">
			<?php $user_progress->output_course_progress_widget(); ?>
		</div>
		<?php
	endif;
};

$duration          = WPI()->templates->get_meta('duration');
$show_duration = (WPI()->templates->get_meta( 'duration_mode' ) !== 'off' && $duration)
                 || ($course_page->is_test() && !empty($duration));
$show_progress = $user_progress->is_tracking_enabled();
$level         = WPI()->templates->get_meta('level');
$show_level    = (WPI()->templates->get_meta( 'level_mode' ) !== 'off' && $level)
                 || ($course_page->is_test() && !empty($level));
$show_files    = is_array( $files );

$show_bar = $course_page->is_module() || ( $show_duration || $show_level || $show_files || $show_progress );

$author_meta_display_name = get_the_author_meta( 'display_name', $post->post_author );
$show_author         = $author_meta_display_name
                       && $course_page->is_module()
                       && ($wpidea_settings[ Design_Settings_Group::DISPLAY_AUTHOR_INFO] ?? 'on') === 'on';
?>

<?php if($show_bar): ?>
    <?php $user_progress = new Course_Progress( $course_page_id, $lesson_page_id ); ?>
    <div class="lekcja_top lekcja_top--low-padding lekcja_top--sticky" <?php if ( $user_progress->is_tracking_enabled() ) echo 'data-progress="enabled"' ?>>
        <div class="contenter flexbox">
            <div class="lekcja_top__left">

                <?php if($show_author): ?>
                <div class="lekcja_top_opis">
                    <i class="far fa-envelope-open"></i>
                    <span><?php _e( 'Author', BPMJ_EDDCM_DOMAIN ) ?>:</span>
                    <span><?php echo $author_meta_display_name ?></span>
                </div>
                <?php endif; ?>

                <?php if ( $show_duration ) { ?>
                <div class="lekcja_top_opis <?= $user_progress->is_tracking_enabled() ? 'hide-on-tiny' : '' ?>">
                    <i class="far fa-clock"></i>
                    <span><?php _e( 'Time', BPMJ_EDDCM_DOMAIN ); ?>:</span>
                    <span class="lekcja_top_opis__label--short" title="<?= $duration ?>"><?= $duration ?></span>
                </div>
                <?php } ?>
                <?php if ( $show_level ) { ?>
                <div class="lekcja_top_opis <?= $user_progress->is_tracking_enabled() ? 'hide-on-mobile' : '' ?> hide-on-tiny">
                    <i class="fas fa-award"></i>
                    <span><?php _e( 'Level', BPMJ_EDDCM_DOMAIN ); ?>:</span>
                    <span class="lekcja_top_opis__label--short" title="<?= $level ?>"><?= $level ?></span>
                </div>
                <?php } ?>
                <?php if ( $show_files ): ?>
                <div class="lekcja_top_opis <?= $user_progress->is_tracking_enabled() ? 'hide-on-mobile' : '' ?> hide-on-tiny">
                    <i class="far fa-file-alt"></i>
                    <span><?php _e( 'Files', BPMJ_EDDCM_DOMAIN ); ?>:</span>
                    <span><a href="#files"><?php _e( 'Download', BPMJ_EDDCM_DOMAIN ); ?></a></span>
                </div>
                <?php endif ?>
            </div>

            <div class="lekcja_top__right">
                <?php $course_progress_section( $course_page_id, $lesson_page_id ); ?>

                <div class="lekcja_top_nav <?= !$user_progress->is_tracking_enabled() ? 'lekcja_top_nav--right-aligned' : '' ?>">
                    <!-- PLACE FOR NAV ITEMS INJECTED WITH JS -->
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
