<?php

use bpmj\wpidea\Info_Message;
?>
<?php
if(WPI()->page->has_template()):
    WPI()->page->render_template();

    return;
endif;

WPI()->templates->header(); ?>
<?php
/*
 * Load the content
 */
?>

<!-- Sekcja pod paskiem z menu -->
<section id="content" class="content <?php echo apply_filters( 'bpmj_eddcm_template_section_css_class', 'content' ); ?>">
	<div class="contenter contenter_tresci">
		<div class="wrapper">
			<?php the_title( '<h2 class="bg left">', '</h2>' ); ?>
			<?php
			$previous_lesson = null;
			while ( have_posts() ) {
				the_post();
				$post_id            = get_the_ID();
				$access             = bpmj_eddpc_user_can_access( get_current_user_id(), bpmj_eddpc_is_restricted( $post_id ), $post_id );
				$course_id          = get_post_meta( $post_id, '_bpmj_eddcm', true );
				$course_page_id     = get_post_meta( $course_id, 'course_id', true );
				$lessons            = WPI()->courses->get_course_structure_flat( $course_page_id );
				$lesson_keys        = array_keys( $lessons );
				$current_lesson_key = array_search( $post_id, $lesson_keys );
				if ( false !== $current_lesson_key && $current_lesson_key > 0 ) {
					for ( $i = $current_lesson_key; $i > 0; -- $i ) {
						$previous_lesson = $lessons[ $lesson_keys[ $i ] ];
						if ( ! $previous_lesson->should_be_grayed_out() && $previous_lesson->get_can_access_lesson() ) {
							break;
						}
					}
				}

				if ( $previous_lesson ) {
					$previous_lesson_link = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', $previous_lesson->get_permalink(), esc_attr( $previous_lesson->post_title ), esc_html( $previous_lesson->post_title ) );
					$go_back_to_previous_lesson = sprintf( __( 'Go back to %s', BPMJ_EDDCM_DOMAIN ), $previous_lesson_link );
				}

				if ( 'valid' !== $access[ 'status' ] ) {
					$message = $access[ 'message' ];
				} else {
					$message = __( 'This content is not yet available.', BPMJ_EDDCM_DOMAIN );
				}

				$info_message = new Info_Message( $message, $go_back_to_previous_lesson ?? null );
				$info_message->render();
			}
			?>
		</div>
	</div>
</section>
<!-- Koniec sekcji pod paskiem z menu -->

<?php WPI()->templates->footer(); ?>
