<?php
global $post;
?>
<span class="progress" style="display: inline-block; width: 75%;border: 2px solid #e0e4ec;line-height: 2px;">
	<span class="progress-bar"
		  style="display: inline-block; width: <?php echo $progress->get_progress_percent(); ?>%;height: 23px;background: #81c0c2;"></span>
</span>
<span class="progress-num" style="display: inline-block; width: 25%; float: right; text-align: right;">
	<?php echo $progress->get_finished_lesson_count(); ?>
	/
	<?php echo $progress->get_course_lesson_count(); ?>
</span>
<label style="clear: both;">
	<input type="checkbox"
		   style="vertical-align: middle;" <?php echo checked( $progress->is_lesson_finished() ); ?>/>
		   <?php _e( 'Mark this lesson as finished', BPMJ_EDDCM_DOMAIN ); ?>
</label>
<?php
if ( $progress->is_lesson_finished() ) {
	$next_lesson = WPI()->courses->get_next_sibling_of( $progress->get_course_page_id(), $progress->get_lesson_page_id() );
	if ( $next_lesson ) {
		$next_lesson_title	 = get_the_title( $next_lesson->ID );
		$button_label		 = WPI()->templates->get_lesson_nav_label( 'next' );
		?>
		<span class="progress-next-lesson" style="text-align: center; display: block">
			<a href="<?php echo $next_lesson->get_permalink(); ?>"
			   title="<?php echo esc_attr( $next_lesson_title ); ?>" class="button"><?php echo $button_label; ?>
				<span class="arrow">&rsaquo;</span></a>
		</span>
		<?php
	}
}
