<?php
/** @var bool|bpmj\wpidea\Course_Page $first_lesson */
/** @var bool $show_video */
/** @var string|null $video_url */
?>
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

            <?php if ( $show_video ) { ?>
                <!-- Pole z video -->
                <div class="box right">
                    <?php echo WPI()->templates->wrap_embed_oembed_html( wp_oembed_get( $video_url ), '0', true ); ?>
                </div>
                <!-- Koniec pola z video -->
            <?php } ?>
        </div>
    </div>
</section>