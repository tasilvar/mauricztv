<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
			<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
		<?php endif; ?>
		<?php wp_head(); ?>
	</head>

	<body <?php body_class( array( WPI()->templates->get_body_class() ) ); ?>>

        <?php do_action( 'bpmj_eddc_after_body_open_tag' ); ?>

		<div id="top"></div>

		<!-- Górny pasek z logo i menu -->
		<header>
			<div class="nav-strip">
				<div>
					<div class="logo-wrapper">
						<div class="content">
							<div>
								<div>
									<?php echo WPI()->templates->get_logo(); ?>
								</div>
							</div>
						</div>
					</div>

					<div class="nav-wrapper">
						<nav id="cssmenu">
							<div id="head-mobile"></div>
							<div class="menu-button"></div>
							<?php echo WPI()->templates->get_main_menu(); ?>
						</nav>
					</div>
				</div>
			</div>
		</header>
		<!-- Koniec górnego paska -->


		<div class="clearfix"></div>

		<div class="container">

            <div class="main">
