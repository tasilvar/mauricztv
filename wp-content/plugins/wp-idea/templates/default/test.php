<?php

use bpmj\wpidea\View;
use bpmj\wpidea\Info_Message;

$wpidea_settings = get_option( 'wp_idea', array() );
if ($wpidea_settings['certificates_page'] == get_the_ID()) {
    include_once('template_parts/page-my-certificates.php');

    return;
}
?>

<?php WPI()->templates->header(); ?>
<?php
/*
 * Load the content
 */
?>

<!-- Sekcja pod paskiem z menu -->
<section class="<?php echo apply_filters( 'bpmj_eddcm_template_section_css_class', 'content' ); ?>">
    <div class="wrapper">
        <?php the_title( '<h2 class="bg center">', '</h2>' ); ?>
        <?php
            $info_message = new Info_Message( __('Quizzes are not supported in the default WP Idea template', BPMJ_EDDCM_DOMAIN ) );
            $info_message->render();
        ?>
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

<?php if ( comments_open() || get_comments_number() ) : ?>
    <!-- Sekcja z komentarzami -->

	<?php comments_template(); ?>

    <!-- Koniec sekcji z komentarzami -->
<?php endif; ?>

<?= View::get('/scripts/check-lesson-as-undone'); ?>

<?php WPI()->templates->footer(); ?>
