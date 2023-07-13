<?php WPI()->templates->header(); ?>
<?php
/*
 * Load the content
 */
?>
<!-- Sekcja pod paskiem z menu -->
<div id="content" class="<?php echo apply_filters( 'bpmj_eddcm_template_section_css_class', 'content' ); ?> posts-loop">
    <div class="contenter">
		<h1><?php wp_title( ' ' ); ?></h1>
                
        <div class="row">
		<?php
		if ( have_posts() ) :	

			while ( have_posts() ) :
                the_post();
                    
                include( 'template_parts/content-post-loop.php' );
			endwhile; // End of the loop.

            ?>

            <div class="col-md-12">
                <?php the_posts_navigation(array('screen_reader_text' => ' ')); ?>
            </div>

            <?php
        else :
            
            include( 'template_parts/content-none.php' );

		endif;
        ?>
        </div>

    </div>
    <!-- Koniec sekcji pod paskiem z menu -->
  
</div>


<?php WPI()->templates->footer(); ?>