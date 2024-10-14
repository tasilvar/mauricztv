<?php

WPI()->templates->header(); ?>

<?php
$download	     = new EDD_Download( get_the_ID() );
$download_id     = $download->id;
$start		     = get_post_meta( $download_id, '_bpmj_eddpc_access_start', true );
$start		     = preg_replace('/00:00$/', '', $start); // show only day if hour is set to 00:00

$course = WPI()->courses->get_course_by_product( $download_id );

$sale_price_to_date   = isset($course->ID) ? get_post_meta( $course->ID, 'sale_price_to_date', true ) : null;
$sale_price_to_hour   = isset($course->ID) ? get_post_meta( $course->ID, 'sale_price_to_hour', true ) : null;

$variable_sale_price_to_date   = get_post_meta( $download_id, 'variable_sale_price_to_date', true );
$variable_sale_price_to_hour   = get_post_meta( $download_id, 'variable_sale_price_to_hour', true );

$sale_price   = get_post_meta( $download_id, 'edd_sale_price', true );

$template_settings = get_option( WPI()->settings->get_layout_template_settings_slug() );
$disable_banners = ( 'on' === $template_settings['scarlet'][ 'disable_banners' ] );

$thumb = WPI()->templates->get_meta( 'banner' );
if( empty( $thumb ) ) {
	$thumb = bpmj_eddcm_template_get_file( 'assets/img/panelkursu.jpg' );
}

?>

<style type="text/css">
#strona_kursu_slider {
	background-image: url(<?php echo $thumb; ?>);
}
<?php if($disable_banners) : ?>
.breadcrumbs {
	margin-top: 30px;
}
<?php endif ?>
</style>

<div id="content" class="content <?php echo apply_filters( 'bpmj_eddcm_template_section_css_class', 'content' ); ?>">

	<?php if(edd_get_download() && !$disable_banners): ?>
	<div id="strona_kursu_slider" class="krotki_slider">
		<div class="contenter">
			<?php the_title( '<h1>', '</h1>' ); ?>
		</div>
	</div>
	<?php endif; ?>
	<div class="contenter">
		<?php if(!is_front_page()) WPI()->templates->breadcrumbs(); ?>
	</div>

	<div class="contenter contenter_tresci">
		<div class="row">
            <?php
            $hide_purche = get_post_meta( get_the_ID(), 'purchase_button_hidden', true );
            ?>

			<div <?php echo ( 'on' === $hide_purche ) ? 'class="col-md-12"' : 'class="col-md-7"'; ?>>

            <?php
			remove_action( 'edd_after_download_content', 'edd_append_purchase_link' );
			while ( have_posts() ) {
				the_post();
                the_title( '<h2>', '</h2>' );
				the_content();
			}
			?>
			</div>

            <?php
            if ( 'on' !== $hide_purche ) :
            ?>
			<div class="col-sm-5">

				<div class="strona_kursu_sidebar">
					<div class="strona_kursu_sidebar_tytul">
						<?php _e( 'Order', BPMJ_EDDCM_DOMAIN ) ?>
					</div>

					<?php
                    bpmj_render_available_quantities_information($download_id);

                    bpmj_eddcm_get_course_cats_and_tags();
                    ?>

                    <div class="strona_kursu_sidebar_box">
						<?php if ( !empty( $start ) ) : ?>
						<p>
							<?php _e( 'Start date', BPMJ_EDDCM_DOMAIN ) ?>: <span><?= $start ?></span>
						</p>
						<?php endif ?>
                        <p>
                            <?php
                            $sale_string = '';

                            if ( !empty($sale_price_to_date) && WPI()->courses->is_sheduled_sale_active( $course->ID ) ) {
                                $sale_string .= __( 'Promotion to: ', BPMJ_EDDCM_DOMAIN ) . ' <span>' . $sale_price_to_date;

                                if ( !empty( $sale_price_to_hour ) )
                                    $sale_string .=  ' ' . $sale_price_to_hour . ':00';

                                $sale_string .= '</span><br>';
                            }

                            if ( !empty($variable_sale_price_to_date) && WPI()->courses->is_sheduled_sale_active( $course->ID ) ) {
                                $sale_string .= __( 'Promotion to: ', BPMJ_EDDCM_DOMAIN ) . ' <span>' . $variable_sale_price_to_date;

                                if ( !empty( $variable_sale_price_to_hour ) )
                                    $sale_string .=  ' ' . $variable_sale_price_to_hour . ':00';

                                $sale_string .= '</span><br>';
                            }

                            echo $sale_string;
                            ?>
                        </p>
                    </div>
					<?php
						bpmj_eddcm_get_course_page_prices( $download );
					?>
				</div>
			</div>
            <?php endif; ?>

		</div>
	</div>


	<?php if ( comments_open() || get_comments_number() ) : ?>
	<!-- Sekcja z komentarzami -->

	<?php comments_template(); ?>

	<!-- Koniec sekcji z komentarzami -->
	<?php endif; ?>

</div>

<?php WPI()->templates->footer(); ?>

<script>
	var offset;

	jQuery( document ).ready(function( $ ) {
		offset = $('.strona_kursu_sidebar').offset();

		$(window).on('scroll', function () {
			Wscroll = $(this).scrollTop();
			contenter_tresci = $('.contenter_tresci').height();

			if (Wscroll > offset.top - 82 && Wscroll < contenter_tresci) {
				t = Wscroll - offset.top + 82;
				$('.strona_kursu_sidebar').addClass('fixed');
				$('.strona_kursu_sidebar').css('top', t + 'px');
			} else if (Wscroll > contenter_tresci) {
				$('.strona_kursu_sidebar').addClass('fixed');
			} else {
				$('.strona_kursu_sidebar').removeClass('fixed');
			}
		});
	});
</script>
