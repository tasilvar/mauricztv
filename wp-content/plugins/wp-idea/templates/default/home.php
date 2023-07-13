<?php

use bpmj\wpidea\Course_Progress;

if (WPI()->page->has_template()):
    WPI()->page->render_template();

    return;
endif;

WPI()->templates->header();

if ( is_tax( 'download_category' ) ) {
	include( 'template_parts/archive-download_category.php' );

	return;
}

$course_page_id = WPI()->courses->get_course_top_page( get_the_ID() );

if ( WPI()->templates->get_meta( 'first_section' ) != 'off' ) { ?>
    <!-- Sekcja pod paskiem z menu (opis kursu) -->
    <section class="description">
        <div class="wrapper">
            <div class="description-inner">
                <div class="box left">
                    <h2 class="bg left"><?php the_title(); ?></h2>
					<?php
					if ( have_posts() ) {
						while ( have_posts() ) {
							the_post();
							the_content();
						}
					}
					$first_lesson = WPI()->courses->get_first_lesson( $course_page_id );
					if ( false !== $first_lesson ) {
						?>
                        <a href="<?php echo get_permalink( $first_lesson->unwrap() ); ?>">
                            <span class="button"><?php _e( 'Start here', BPMJ_EDDCM_DOMAIN ); ?></span>						
                        </a>
						<?php
					}
					?>
                </div>
                <!-- Koniec pola z tekstem -->

				<?php if ( WPI()->templates->get_meta( 'video_mode' ) == 'on' ) { ?>
                    <!-- Pole z video -->
                    <div class="box right">
						<?php echo WPI()->templates->wrap_embed_oembed_html( wp_oembed_get( WPI()->templates->get_meta( 'video' ) ), '0', true ); ?>
                    </div>
                    <!-- Koniec pola z video -->
				<?php } ?>
            </div>
        </div>
    </section>
    <!-- Koniec sekcji pod paskiem z menu -->
<?php } ?>

<?php if ( WPI()->templates->get_meta( 'second_section' ) == 'on' ) { ?>
    <!-- Sekcja z wyśrodkowanym tekstem -->
    <section class="text">
        <div class="wrapper">
			<?php if ( WPI()->templates->get_meta( 'second_section_title_mode' ) == 'on' ) { ?><h2
                    class="bg center"><?php WPI()->templates->the_meta( 'second_section_title' ); ?></h2><?php } ?>
			<?php echo apply_filters( 'the_content', ( WPI()->templates->get_meta( 'second_section_content' ) ) ); ?>
        </div>
    </section>
    <!-- Koniec sekcji z wyśrodkowanym tekstem -->
<?php } ?>

    <?php
    $modules = WPI()->courses->get_course_level1_modules_or_lessons( $course_page_id );

    if ( ! empty( $modules ) ) :
    ?>
        <!-- Sekcja z miniaturami modułów -->
        <section class="modules bg">
            <div class="wrapper">
                <h2 class="bg center"><?php _e( 'Course Panel', BPMJ_EDDCM_DOMAIN ); ?></h2>
                <?php

                foreach ( $modules as $id => $module ) {
                    ?>
                    <div class="box-wrapper">
                        <div class="box">
                            <?php if ( $module->should_be_grayed_out() ): ?>
                                <div class="thumb note blurred"
                                     <?php if ( $module->get_thumbnail() ) { ?>style="background-image: url(<?php echo $module->get_thumbnail(); ?>); background-repeat: no-repeat; background-size: cover; background-position: center center;" <?php } ?>></div>
                                <h3>
                                    <?php echo $module->post_title; ?>
                                </h3>
                                <?php if ( $module->get_subtitle() ) { ?>
                                    <p><?php echo $module->get_subtitle(); ?></p><?php } ?>
                            <?php else: ?>
                                <a href="<?php echo get_permalink( $module->unwrap() ); ?>">
                                    <div class="thumb note"
                                         <?php if ( $module->get_thumbnail() ) { ?>style="background-image: url(<?php echo $module->get_thumbnail(); ?>); background-repeat: no-repeat; background-size: cover; background-position: center center;" <?php } ?>></div>
                                </a>
                                <h3>
                                    <a href="<?php echo get_permalink( $module->unwrap() ); ?>"><?php echo $module->post_title; ?></a>
                                </h3>
                                <?php if ( $module->get_subtitle() ) { ?><p><?php echo $module->get_subtitle(); ?> <a
                                        href="<?php echo $module->get_permalink(); ?>"><span
                                            class="arrow">&rsaquo;</span></a>
                                    </p><?php } ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </section>
        <!-- Koniec sekcji z miniaturami modułów -->


        <?php if ( WPI()->templates->get_meta( 'last_section' ) == 'on' ) { ?>
            <!-- Sekcja ze spisem lekcji -->
            <section class="lessons">
                <div class="wrapper">
                    <h2 class="bg center"><?php _e( 'Course Content', BPMJ_EDDCM_DOMAIN ); ?></h2>
                    <?php
                    $lessons  = WPI()->courses->get_course_structure_flat( $course_page_id, false );
                    $i        = 1;
                    $progress = new Course_Progress( $course_page_id );
                    if ( ! empty( $lessons ) ) {
                        ?>
                        <ul>
                            <?php
                            foreach ( $lessons as $lesson ) {
                                if ( $lesson->should_be_grayed_out() ) {
                                    echo '<li' . ( $progress->is_lesson_finished( $lesson->ID ) ? ' class="finished"' : '' ) . '>' . sprintf( __( 'Lesson %s:', BPMJ_EDDCM_DOMAIN ), $i ) . ' ' . $lesson->post_title . '</li>';
                                } else {
                                    echo '<li' . ( $progress->is_lesson_finished( $lesson->ID ) ? ' class="finished"' : '' ) . '><a href="' . $lesson->get_permalink() . '">' . sprintf( __( 'Lesson %s:', BPMJ_EDDCM_DOMAIN ), $i ) . '</a> ' . $lesson->post_title . '</li>';
                                }
                                $i ++;
                            }
                            ?>
                        </ul>
                    <?php } ?>
                </div>
            </section>
            <!-- Koniec sekcji ze spisem lekcji -->
        <?php } ?>
    <?php endif; ?>

<?php WPI()->templates->footer(); ?>