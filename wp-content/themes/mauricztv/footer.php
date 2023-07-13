<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WP_Bootstrap_Starter
 */

?>
<?php if(!is_page_template( 'blank-page.php' ) && !is_page_template( 'blank-page-with-container.php' )): ?>
			</div><!-- .row -->
		</div><!-- .container -->
	</div><!-- #content -->
	
    <?php get_template_part( 'footer-widget' ); ?>
	
	
	<div class="footer">
		<div class="container">
			<div class="row">
				<div class="col-lg-5">
				
					<?php if (is_front_page()){ ?>
						<img src="<?php echo get_template_directory_uri(); ?>/img/logo.svg" alt="Mauricz TV">
					<?php } else { ?>	
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
							<img src="<?php echo get_template_directory_uri(); ?>/img/logo.svg" alt="Mauricz TV">
						</a>
					<?php } ?>
					
					<h4>Zapisz się do newslettera</h4>
					
					<div class="nwsl">
						<?php echo do_shortcode("[contact-form-7 id='9' title='NWSL']"); ?>
					</div>
					
				</div>
				<div class="col-lg-7">
				
					<div class="row">
						<div class="col-sm-6 col-md-4">
							<h3>Menu</h3>
							
							<ul>
								<li><a href="#">Strona główna</a></li>
								<li><a href="#">O Mauricz.tv</a></li>
								<li><a href="/lista-produktow/">Kursy online</a></li> 
								<li><a href="#">FAQ</a></li> 
								<li><a href="#">Kontakt</a></li>
							</ul>
							
						</div>
						<div class="col-sm-6 col-md-4">
							<h3>Informacje</h3>
							
							<ul>
								<li><a href="#">Regulamin serwisu</a></li>
								<li><a href="#">Polityka prywatności</a></li> 
								<li><a href="#">Cookies</a></li>
								<li><a href="#">Regulamin newslettera</a></li>
							</ul>
							
						</div>
						<div class="col-md-4">
							<h3>Twoje konto</h3>
							
							<ul>
								<li><a href="#">Moje konto</a>
								<li><a href="#">Moje kursy</a>
								<li><a href="#">Historia płatności i faktury</a> 
								<li><a href="#">Moje dane</a> 
								<li><a href="#">Wyloguj się</a>
							</ul>
							
						</div>
					</div>
				
				</div>
			</div>
		</div>	
	</div>
	
	<div class="bottom-footer">
		<div class="container">
			<div class="row">
				<div class="col-md-7 first">
					&copy; <?php echo date('Y'); ?> Mauricz.tv. Wszelkie prawa zastrzeżone.
				</div>
				<div class="col-md-5 second">
					Projekt i wykonanie <a href="https://virtualpeople.pl">Virtual People</a>.
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>