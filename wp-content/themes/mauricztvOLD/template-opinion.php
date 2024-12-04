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



<div class="post hentry ivycat-post col-md-4">

	<div class="inner">
		<?php the_post_thumbnail(); ?>
		<h6><?php the_title(); ?></h6>
		<p><?php the_content(); ?></p>
	</div>

</div>

