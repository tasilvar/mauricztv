<?php
global $post;
?>

<div class="lekcja_top_postep">
	 <i class="fas fa-tasks"></i>
	<span><?php _e( 'Course progress', BPMJ_EDDCM_DOMAIN ); ?>:</span>
	<div class="postep_contenter">
		<div class="postep_contenter_postep" style="width: <?php echo (int)($progress->get_progress_percent() * 145 / 100) ?>px;">
			<div class="postep_liczba"><?php echo $progress->get_progress_percent(); ?>%</div>
		</div>
	</div>
    &nbsp;
    <?php if( !$progress->is_no_content_access_mode() ) echo do_shortcode('[wpi_continue_anchor wpi_icon=true]' ); ?>
</div>
<?php
// empty($page_type) added for ajax requests (we can make such assertion because Done button should be accessible only for lessons)
if ( (empty($page_type) || 'lesson' === $page_type) && !$progress->is_no_content_access_mode() ) : ?>
<div class="ukonczony_material">
	<input type="checkbox" class="fa" name="ukonczony_material_check" id="ukonczony_material_check" <?php echo checked( $progress->is_lesson_finished() ); ?>>
	<label for="ukonczony_material_check"><?php _e( 'Done', BPMJ_EDDCM_DOMAIN ); ?></label>
</div>
<?php endif; ?>

