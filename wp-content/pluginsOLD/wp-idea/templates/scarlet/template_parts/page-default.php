<?php WPI()->templates->header(); ?>
<?php
/*
 * Load the content
 */
$is_slider = false;
?>
<?php if ( is_front_page() ) : ?>
    <?php
    $query = new WP_Query( array(
        'post_status' => 'publish',
        'post_type'   => 'courses',
        'meta_query'  => array(
            array(
                'key'   => 'promote_curse',
                'value' => 'on',
            ),
        ),
    ) );
    ?>
    <?php if ( $query->have_posts() ):
        $is_slider = true;
        ?>
        <div class="slider">
                <div id="home-page-slider" class="carousel slide" data-ride="carousel">
                    <!-- Indicators -->
                    <div class="paginacja">
                        <ul class="carousel-indicators">
                            <?php
                            $i = 0;
                            while ( $query->have_posts() ) :
                                $query->the_post(); ?>
                                <li data-target="#home-page-slider" data-slide-to="<?php echo $i; ?>"<?php echo $i === 0 ? ' class="active"' : ''; ?>></li>
                                <?php $i++; ?>
                            <?php endwhile; ?>
                        </ul>
                    </div>

                    <!-- Wrapper for slides -->
                    <div class="carousel-inner" role="listbox">
                        <?php
                        $i = 0;
                        while ( $query->have_posts() ) :
                            $query->the_post();

                            $image = get_post_meta(get_the_ID(), 'banner', true);
                            if (! $image)
                                $image = WPI()->templates->get_template_url() . '/assets/img/baner_glowna.png';

                            $product_id = get_post_meta( get_the_ID(), 'product_id', true );
                            ?>
                            <div class="item<?php echo $i === 0 ? ' active' : ''; ?>"<?php echo $image ? ' style="background-image: url(' . $image . ');"' : ''; ?>>
                                <div class="contenter">
                                    <p class="czerwony_tekst_slider"><?php _e( 'Recommended course', BPMJ_EDDCM_DOMAIN ); ?></p>
                                    <a href="<?php the_permalink( $product_id ); ?>" class="duzy_tekst_slider"><?php the_title(); ?></a>
                                    <div class="zwykly_tekst_slider">
                                        <?php $excerpt_length = apply_filters( 'wp_idea_excerpt_length', 30 ); ?>
                                        <?php if ( has_excerpt() ) : ?>
                                            <?php echo apply_filters( 'edd_downloads_excerpt', wp_trim_words( get_post_field( 'post_excerpt', get_the_ID() ), $excerpt_length ) ); ?>
                                        <?php elseif ( get_the_content() ) : ?>
                                            <?php echo apply_filters( 'edd_downloads_excerpt', wp_trim_words( get_post_field( 'post_content', get_the_ID() ), $excerpt_length ) ); ?>
                                        <?php endif; ?>
                                    </div>
                                    <?php
                                    bpmj_eddcm_get_course_page_prices( new EDD_Download( (int)$product_id ), true );
                                    ?>
                                </div>
                            </div>
                        <?php
                        $i++;
                        endwhile; ?>
                    </div>
                </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Sekcja pod paskiem z menu -->
<div id="content" class="<?php echo apply_filters( 'bpmj_eddcm_template_section_css_class', 'content' ); echo $is_slider ? ' with-slider' : ''; ?>">
    <div class="contenter">
		<?php if(is_404()) echo '<h1>404</h1>'; 
		else if(!is_front_page()) the_title( '<h1>', '</h1>' ); ?>
        <?php if(!is_front_page()) WPI()->templates->breadcrumbs(); ?>
		<?php
            while ( have_posts() ) {
                the_post();
                the_content();
            }
		?>
    </div>
<!-- Koniec sekcji pod paskiem z menu -->

<?php if ( !is_404() && (comments_open() || get_comments_number()) ) : ?>
    <!-- Sekcja z komentarzami -->

	<?php comments_template(); ?>

    <!-- Koniec sekcji z komentarzami -->
<?php endif; ?>

</div>

<?php WPI()->templates->footer(); ?>