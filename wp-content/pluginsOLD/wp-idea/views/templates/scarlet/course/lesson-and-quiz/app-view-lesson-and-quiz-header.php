<?php
global $post;
global $wpidea_settings;

use bpmj\wpidea\Course_Page;
use bpmj\wpidea\Course_Progress;

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

$level         = WPI()->templates->get_meta('level');
$show_level    = (WPI()->templates->get_meta( 'level_mode' ) !== 'off' && $level)
                 || ($course_page->is_test() && !empty($level));

?>

<?php $user_progress = new Course_Progress( $course_page_id, $lesson_page_id ); ?>
<div class="app-view-header">
    <div class="lekcja_top lekcja_top--sticky lekcja_top--app-view" <?php
    if ($user_progress->is_tracking_enabled()) echo 'data-progress="enabled"' ?>>
        <div class="contenter flexbox">
            <?php
            $course_progress_section($course_page_id, $lesson_page_id); ?>

            <div class="lekcja_top__nav">
                <?= WPI()->templates->get_previous_lesson_nav(
                    '<i class="fas fa-caret-left"></i> ',
                    'lekcja_nast_pop lekcja_pop lekcja_top_nav__item'
                ); ?>
                <?= WPI()->templates->get_next_lesson_nav(
                    ' <i class="fas fa-caret-right"></i>',
                    'lekcja_nast_pop lekcja_nas lekcja_top_nav__item'
                ); ?>
            </div>
        </div>
    </div>

    <?php $show_bar = ( $show_duration || $show_level ); ?>
    <?php if($show_bar): ?>
    <div class="app-view-header__meta">
        <?php if ( $show_duration ) { ?>
            <div class="lekcja_top_opis">
                <i class="far fa-clock"></i>
                <span><?php _e( 'Time', BPMJ_EDDCM_DOMAIN ); ?>:</span>
                <span class="lekcja_top_opis__label--short" title="<?= $duration ?>"><?= $duration ?></span>
            </div>
        <?php } ?>
        <?php if ( $show_level ) { ?>
            <div class="lekcja_top_opis">
                <i class="fas fa-award"></i>
                <span><?php _e( 'Level', BPMJ_EDDCM_DOMAIN ); ?>:</span>
                <span class="lekcja_top_opis__label--short" title="<?= $level ?>"><?= $level ?></span>
            </div>
        <?php } ?>
    </div>
    <?php endif; ?>

    <div class="app-view-header__title">
        <h1 class="wpi-page-title"><?= apply_filters('bpmj_eddcm_page_title_block_title', get_the_title()) ?></h1>
    </div>
</div>