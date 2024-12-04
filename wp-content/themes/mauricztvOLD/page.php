<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */

get_header(); ?>


	<section id="primary" class="content-area col-sm-12 col-lg-8">
		<div id="main" class="site-main" role="main">
		<?php 
		// the_title();
		// echo $wpidea_settings['certificates_page'];
		if($wpidea_settings['certificates_page'] == get_the_ID()) {
			get_template_part( 'template-parts/content', 'my-certificates' );
		} else {
		?>
		
			<?php
			while ( have_posts() ) : the_post();

				get_template_part( 'template-parts/content', 'page' );

                // If comments are open or we have at least one comment, load up the comment template.
                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif;

			endwhile; // End of the loop.
			?>
	<?php 
		}
		?>
		</div><!-- #main -->
	</section><!-- #primary -->

<?php
get_sidebar();
get_footer();
