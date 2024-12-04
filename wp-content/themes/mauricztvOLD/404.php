<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WP_Bootstrap_Starter
 */

get_header(); ?>

	<section id="primary" class="content-area col-sm-12 col-lg-12">
		<div id="main" class="site-main" role="main">

			<section class="error-404 not-found">
				<header>
					<h2 class="page-title">
					Nie możemy odnaleźć strony o podanym adresie internetowym. Zapraszamy do zapoznania się z informacjami zamieszczonymi na stronie głównej oraz w innych zakładkach.
					<?php //_e( 'Oops! Something went wrong here.', '_tk' ); ?></h2>
				</header><!-- .page-header -->

				<div class="page-content">
					<p><?php //_e( 'Nothing could be found at this location. Maybe try a search?', '_tk' ); ?></p>

					<?php //get_search_form(); ?>

				</div><!-- .page-content -->
			</section><!-- .error-404 -->

		</div><!-- #main -->
	</section><!-- #primary -->

<?php
//get_sidebar();
get_footer();
