<?php

use bpmj\wpidea\Course_Progress;
use bpmj\wpidea\View;

/** @var array $lessons */
/** @var Course_Progress $progress */
/** @var int $lesson_page_id */
/** @var bool $compact_mode_on */
/** @var bool $show_title */
/** @var View $view */

$mode = $compact_mode_on ? 'compact' : 'default';
?>

<section class="lessons lessons--mode-<?= $mode ?>">
    <div class="wrapper">
        <?php if($show_title): ?>
            <h2 class="bg center"><?php _e( 'Course Content', BPMJ_EDDCM_DOMAIN ); ?></h2>
        <?php endif; ?>

        <?php
        $i        = 1;
        if ( ! empty( $lessons ) ) {
            ?>
            <ul>
                <?php
                foreach ( $lessons as $lesson ) {
                    if ( $lesson->should_be_grayed_out() ) {
                        echo '<li' . ( $progress->is_lesson_finished( $lesson->ID ) ? ' class="finished"' : '' ) . '>' . sprintf( __( 'Lesson %s:', BPMJ_EDDCM_DOMAIN ), $i ) . ' ' . $lesson->post_title . '</li>';
                    } else {
                        echo '<li' . ( $progress->is_lesson_finished( $lesson->ID ) ? ' class="finished"' : '' ) . '><a href="' . $lesson->get_permalink() . '">' . sprintf( __( 'Lesson %s:', BPMJ_EDDCM_DOMAIN ), $i ) . '</a> ' . $lesson->post_title . '</li>';
                    }
                    $i ++;
                }
                ?>
            </ul>
        <?php } ?>
    </div>
</section>