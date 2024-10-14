<?php

use bpmj\wpidea\View_Hooks;

$wpidea_settings = get_option( 'wp_idea', []);
if ($wpidea_settings['certificates_page'] == get_the_ID()) {
    include_once('template_parts/page-my-certificates.php');

    return;
}

if(WPI()->page->has_template()):
    WPI()->page->render_template();

    return;
endif;

if(edd_get_download()){
    add_action( 'wp_head', function (){
        View_Hooks::run(View_Hooks::RENDER_HEAD_ELEMENTS_IN_PRODUCT_PAGE, get_the_ID());
    }, 900 );
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
		while ( have_posts() ) {
			the_post();
			the_content();
		}
		?>
    </div>
</section>
<!-- Koniec sekcji pod paskiem z menu -->

<?php if ( comments_open() || get_comments_number() ) : ?>
    <!-- Sekcja z komentarzami -->

	<?php comments_template(); ?>

    <!-- Koniec sekcji z komentarzami -->
<?php endif; ?>

<?php WPI()->templates->footer(); ?>
