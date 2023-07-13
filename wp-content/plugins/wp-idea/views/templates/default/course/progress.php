<?php
use bpmj\wpidea\Course_Progress;

/** @var int $course_page_id */
/** @var int $lesson_page_id */
/** @var string $nonce */

$course_progress_section     = function ($course_page_id, $lesson_page_id, $header_class = 'center' ) use ( $nonce ) {
    $user_progress = new Course_Progress( $course_page_id, $lesson_page_id );
    if ( $user_progress->is_tracking_enabled() ):
        ?>
        <div class="box">
            <h2 class="bg <?php echo $header_class; ?>"><?php _e( 'Course progress', BPMJ_EDDCM_DOMAIN ); ?></h2>
            <p id="course-progress" data-course-page-id="<?php echo $course_page_id; ?>"
               data-lesson-page-id="<?php echo $lesson_page_id; ?>" data-nonce="<?php echo $nonce; ?>"
               data-spinner="<?= WPI()->templates->get_spinner_url() ?>">
                <?php $user_progress->output_course_progress_widget(); ?>
            </p>
        </div>
    <?php
    endif;
};
?>

<?php $course_progress_section( $course_page_id, $lesson_page_id ); ?>