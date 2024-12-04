<?php
/**
 * Template for displaying a post in the [ic_add_posts] shortcode.
 *
 * @package   Posts_in_Page
 * @author    Eric Amundson <eric@ivycat.com>
 * @copyright Copyright (c) 2019, IvyCat, Inc.
 * @link      https://ivycat.com
 * @since     1.0.0
 * @license   GPL-2.0-or-later
 */

?>



<div class="post hentry ivycat-post">

	<div class="post-thumbnail">
		<a href="<?php echo get_permalink(); ?>">
		
			<div class="date-sidebar">
				<?php echo date('d.m.Y'); ?>
			</div>
			
			<?php the_post_thumbnail(); ?>
			
		</a>
	</div>

	<div class="entry-summary">
	
		<a href="<?php the_permalink(); ?>" class="post-link"><?php the_title(); ?></a>
	
		<?php the_excerpt(); ?>
		
		<a href="<?php echo get_permalink(); ?>" class="read-more">Czytaj więcej</a>
		
	</div>
	
	

</div>

