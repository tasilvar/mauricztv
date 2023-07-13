<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WP_Bootstrap_Starter
 */

get_header(); ?>

	<?php if (in_category('dieta') || in_category('suplementacja') || in_category('trening')){ ?>
	
		<?php $backgroundImg = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );?>

		<div class="single-top" style="background-image:url('<?php echo $backgroundImg[0]; ?>');">
		<div class="inner">
		
			<div class="date">
				<?php echo date('d.m.Y'); ?>
			</div>
			
			<h1><?php the_title(); ?></h1>
			
			<h6>Autor: <strong><?php the_post(); echo get_the_author(); rewind_posts(); ?></strong></h6>
			
			<div class="links">
				<?php echo do_shortcode("[SSB]"); ?>
			</div>
			
		</div>
		</div>

	<?php } ?>

	
	<?/*
	<div class="post-thumbnail">
		<?php the_post_thumbnail(); ?>
	</div>
	*/?>
	
	

	<section id="primary" class="content-area col-sm-12 col-lg-8">
	
		<div id="main" class="site-main" role="main">

		<?php
		while ( have_posts() ) : the_post();

			get_template_part( 'template-parts/content', get_post_format() );

			    the_post_navigation();

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>

		</div><!-- #main -->
	</section><!-- #primary -->
	
	<div class="col-sm-12 col-lg-4 single-sidebar">
	
		<h3>Podobne artykuły</h3>
		
		<?php echo do_shortcode("[ic_add_posts showposts='3' template='template-single.php']"); ?>
		
	
	</div>
	

<?php
//get_sidebar();
get_footer();
