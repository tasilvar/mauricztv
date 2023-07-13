<?php
namespace bpmj\wpidea\templates_system\admin\blocks;

class Course_Offer_Page_Details_Block extends Block
{
    const BLOCK_NAME = 'wpi/course-offer-page-details';

    public function __construct() {
        parent::__construct();
        
        $this->title = __('Product Details', BPMJ_EDDCM_DOMAIN);
    }
    
    public function get_content_to_render($atts)
    {
		$download	     = new \EDD_Download( get_the_ID() );
		$download_id     = $download->id;

		if(empty($download_id)) return '';

		$start		     = get_post_meta( $download_id, '_bpmj_eddpc_access_start', true );
		$start		     = preg_replace('/00:00$/', '', $start); // show only day if hour is set to 00:00
		
		$course = WPI()->courses->get_course_by_product( $download_id );
		if(!$course) {
            $course = new \stdClass();
            $course->ID = $download_id;
        }
		
		$sale_price_to_date   = get_post_meta( $course->ID, 'sale_price_to_date', true );
		$sale_price_to_hour   = get_post_meta( $course->ID, 'sale_price_to_hour', true );

		$variable_sale_price_to_date   = get_post_meta( $download_id, 'variable_sale_price_to_date', true );
		$variable_sale_price_to_hour   = get_post_meta( $download_id, 'variable_sale_price_to_hour', true );
		
		$sale_price   = get_post_meta( $download_id, 'edd_sale_price', true );

		ob_start();

		$purchase_button_hidden = get_post_meta( $course->ID, 'purchase_button_hidden', true );

		if ( 'on' !== $purchase_button_hidden ) :
		?>
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
		<?php
        endif;

		$content = ob_get_contents();
		ob_end_clean();
        return $content;
    }
}