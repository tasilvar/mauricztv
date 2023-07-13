<?php
namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\modules\app_view\api\App_View_API_Static_Helper;

class Products_Slider_Block extends Block
{
    const BLOCK_NAME = 'wpi/curses-slider';

    public function __construct() {
        parent::__construct();
        
        $this->title = Translator_Static_Helper::translate('blocks.products_slider.title');
    }

    public function get_content_to_render($atts)
    {
        //@todo: przenieść kod html do widoków, a wczytywanie danych do api
        
        $content = '';
        $query = $this->get_wp_query();

        if ( $query->have_posts() ):
        ob_start();
        ?>
            <div class="slider">
                    <div id="home-page-slider" class="carousel slide" data-ride="carousel">
                        <!-- Indicators -->
                        <div class="paginacja">
                            <ul class="carousel-indicators">
                                <?php
                                $i = 0;
                                while ( $query->have_posts() ) :
                                    $query->the_post(); ?>
                                    <li data-target="#home-page-slider" data-slide-to="<?php echo $i; ?>"<?php echo $i === 0 ? ' class="active"' : ''; ?>></li>
                                    <?php $i++; ?>
                                <?php endwhile; ?>
                            </ul>
                        </div>
    
                        <!-- Wrapper for slides -->
                        <div class="carousel-inner" role="listbox">
                            <?php
                            $i = 0;
                            while ( $query->have_posts() ) :
                                $query->the_post();
    
                                $image = get_post_meta(get_the_ID(), 'banner', true);
                                if (! $image)
                                    $image = WPI()->templates->get_template_url() . '/assets/img/baner_glowna.png';
    
                                $product_id = get_the_ID();
                                ?>
                                <div class="item<?php echo $i === 0 ? ' active' : ''; ?>"<?php echo $image ? ' style="background-image: url(' . $image . ');"' : ''; ?>>
                                    <div class="contenter">
                                        <p class="czerwony_tekst_slider"><?php echo Translator_Static_Helper::translate('blocks.products_slider.front.title') ?></p>
                                        <a href="<?php the_permalink( $product_id ); ?>" class="duzy_tekst_slider"><?php the_title(); ?></a>
                                        <?php
                                        bpmj_render_available_quantities_information($product_id);
                                        ?>
                                        <div class="zwykly_tekst_slider">
                                            <?php $excerpt_length = apply_filters( 'wp_idea_excerpt_length', 30 ); ?>
                                            <?php if ( has_excerpt() ) : ?>
                                                <?php echo apply_filters( 'edd_downloads_excerpt', wp_trim_words( get_post_field( 'post_excerpt', get_the_ID() ), $excerpt_length ) ); ?>
                                            <?php elseif ( get_the_content() ) : ?>
                                                <?php echo apply_filters( 'edd_downloads_excerpt', wp_trim_words( get_post_field( 'post_content', get_the_ID() ), $excerpt_length ) ); ?>
                                            <?php endif; ?>
                                        </div>
                                        <?php
                                        bpmj_eddcm_get_course_page_prices( new \EDD_Download( (int)$product_id ), true );
                                        ?>
                                    </div>
                                </div>
                            <?php
                            $i++;
                            endwhile; ?>
                        </div>
                    </div>
            </div>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
        endif;

        return $content;
    }

    private function get_wp_query(): \WP_Query
    {
        if (App_View_API_Static_Helper::is_active()) {
            return new \WP_Query();
        }

        return new \WP_Query(array(
            'post_status' => 'publish',
            'post_type' => ['download'],
            'meta_query' => array(
                array(
                    'key' => 'promote_curse',
                    'value' => 'on',
                ),
            ),
        ));
    }
}