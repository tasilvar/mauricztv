<?php WPI()->templates->header(); ?>
<!-- Sekcja pod paskiem z menu -->
<div id="content" class="<?php echo apply_filters( 'bpmj_eddcm_template_section_css_class', 'content' ); ?>">
    <?php include('template_parts/content-post.php'); ?>
</div>

<?php WPI()->templates->footer(); ?>
