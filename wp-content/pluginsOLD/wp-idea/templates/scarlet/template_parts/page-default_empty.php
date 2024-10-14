<?php WPI()->templates->header(); ?>
<?php
/*
 * Load the content
 */
?>

<!-- Sekcja pod paskiem z menu -->
<div id="content" class="<?php echo apply_filters( 'bpmj_eddcm_template_section_css_class', 'content' ); ?>">
    <div class="contenter">
        <?php
		while ( have_posts() ) {
			the_post();
			the_content();
		}
		?>
    </div>
<!-- Koniec sekcji pod paskiem z menu -->
</div>

<?php WPI()->templates->footer(); ?>