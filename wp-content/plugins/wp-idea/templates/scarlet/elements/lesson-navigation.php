<?php
/* @var $navigation_section_position string */
/* @var $course_page_id int */
/* @var $lesson_page_id int */

use bpmj\wpidea\Course_Progress;

$modules = WPI()->courses->get_course_level1_modules_or_lessons( $course_page_id );
if ( ! empty( $modules ) ) :
$progress = new Course_Progress( $course_page_id );
?>
<h3><?php _e( 'Course Content', BPMJ_EDDCM_DOMAIN ); ?></h3>
	
<div class="row">

	<?php
	$lessons  = WPI()->courses->get_course_structure_flat( $course_page_id, false );
	$lessons_cnt = count( $lessons );
	$i        = 0;
	$parent = 0;
	if ( ! empty( $lessons ) ) {
		?>


			<?php
			foreach ( $lessons as $lesson ) {
				if( $i == 0 ) {
					echo '<div class="col-sm-6 etapy_kursu">
						<div class="etap_kursu">';
				}
				else if( $lessons_cnt > 3 && $i == (int)($lessons_cnt / 2) ) {
					echo '</ul></div></div><div class="col-sm-6 etapy_kursu">
						<div class="etap_kursu"><ul>';
				}
				if( $lesson->post_parent != $parent ) {
					if( $i != 0 ) echo '</ul>';
					if( $lesson->post_parent == $lesson_page_id ) {
						echo '<p></p><ul>';
					}
					else {
						echo '<p><i class="icon-module"></i> ' . get_the_title( $lesson->post_parent ) . '</p>
							<ul>';
					}
					$parent = $lesson->post_parent;
				}
				
				$class_active = '';
				if( $lesson_page_id == $lesson->ID ) {
					$class_active = ' active';
				}
				
				if ( $lesson->should_be_grayed_out() ) {
					echo '<li' . ( $progress->is_lesson_finished( $lesson->ID ) ? ' class="zakonczony"' : '' ) . '><div class="etap_kursu_kreska fa"></div><span>' . $lesson->post_title . '</span></li>';
				} else {
					echo '<li' . ( $progress->is_lesson_finished( $lesson->ID ) ? ' class="zakonczony' . $class_active . '"' : ' class="' . $class_active . '"' ) . '><div class="etap_kursu_kreska fa"></div><a href="' . $lesson->get_permalink() . '">' . $lesson->post_title . '</a></li>';
				}
				$i ++;
			}
			?>
		</ul>
	<?php } ?>
</div>
<?php endif; ?>
</div>
</div>
