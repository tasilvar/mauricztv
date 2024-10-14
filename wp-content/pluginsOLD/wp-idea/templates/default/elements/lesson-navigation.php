<?php
/* @var $navigation_section_position string */
/* @var $course_page_id int */
/* @var $lesson_page_id int */

use bpmj\wpidea\Course_Progress;

?>
<div class="box">
    <h2 class="bg <?php echo $navigation_section_position === 'side' ? 'center' : 'left'; ?>"><?php _e( 'Course navigation', BPMJ_EDDCM_DOMAIN ); ?></h2>
	<?php
	$tree = WPI()->courses->get_course_structure_tree( $course_page_id );
	?>
    <ul id="course_navigation">
	    <?php
	    $progress = new Course_Progress( $course_page_id );
	    foreach ( $tree as $level1_module_or_lesson ):
		    if ( $level1_module_or_lesson->get_is_hidden() ) {
			    continue;
		    }
		    ?>
		    <li class="<?php echo $level1_module_or_lesson->get_page_type(); ?> <?php echo $level1_module_or_lesson->has_child( $lesson_page_id ) ? 'active' : ''; ?> <?php echo $lesson_page_id == $level1_module_or_lesson->ID ? 'current' : ''; ?> <?php echo $progress->is_lesson_finished( $level1_module_or_lesson->ID ) ? 'finished' : ''; ?>">
			    <?php if ( $level1_module_or_lesson->is_lesson() ): ?>
				    <?php if ( $level1_module_or_lesson->should_be_grayed_out() ): ?>
					    <?php echo $level1_module_or_lesson->post_title; ?>
				    <?php else: ?>
					    <a href="<?php echo $level1_module_or_lesson->get_permalink(); ?>"><?php echo $level1_module_or_lesson->post_title; ?></a>
				    <?php endif; ?>
			    <?php else: ?>
				    <?php echo $level1_module_or_lesson->post_title; ?>
			    <?php endif; ?>
			    <?php if ( $level1_module_or_lesson->is_module() ): ?>
				    <ul>
					    <?php foreach ( $level1_module_or_lesson->get_children() as $level2_lesson ): ?>
						    <li class="<?php echo $lesson_page_id == $level2_lesson->ID ? 'current' : ''; ?> <?php echo $progress->is_lesson_finished( $level2_lesson->ID ) ? 'finished' : ''; ?>">
							    <?php if ( $level2_lesson->should_be_grayed_out() ): ?>
								    <?php echo $level2_lesson->post_title; ?>
							    <?php else: ?>
								    <a href="<?php echo $level2_lesson->get_permalink(); ?>"><?php echo $level2_lesson->post_title; ?></a>
							    <?php endif; ?>
						    </li>
					    <?php endforeach; ?>
				    </ul>
			    <?php endif; ?>
		    </li>
		    <?php
	    endforeach;
	    ?>
    </ul>
</div>
