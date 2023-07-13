<?php
$show_lesson_level = WPI()->templates->get_meta( 'level_mode' ) !== 'off';
$show_lesson_duration = WPI()->templates->get_meta( 'duration_mode' ) !== 'off';
$show_lesson_short_desc = WPI()->templates->get_meta( 'shortdesc_mode' ) !== 'off';
?>

<?php if ($show_lesson_level || $show_lesson_duration || $show_lesson_short_desc ) { ?>
    <!-- Pole z informacjami o lekcji -->
    <div class="box">
        <h2 class="bg center"><?php _e( 'Lesson details', BPMJ_EDDCM_DOMAIN ); ?></h2>

        <?php if ( $show_lesson_level ) { ?>
            <p><strong><?php _e( 'Difficulty level', BPMJ_EDDCM_DOMAIN ); ?>
                    :</strong> <?php WPI()->templates->the_meta( 'level' ); ?></p>
        <?php } ?>

        <?php if ( $show_lesson_duration ) { ?>
            <p><strong><?php _e( 'Duration time', BPMJ_EDDCM_DOMAIN ); ?>
                    :</strong> <?php WPI()->templates->the_meta( 'duration' ); ?></p>
        <?php } ?>

        <?php if ( $show_lesson_short_desc ) { ?>
            <p><?php WPI()->templates->the_meta( 'shortdesc' ); ?></p>
        <?php } ?>
    </div>
    <!-- Koniec pola z informacjami o lekcji -->
<?php } ?>