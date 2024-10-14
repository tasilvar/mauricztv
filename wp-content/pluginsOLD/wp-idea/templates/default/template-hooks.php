<?php

use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\admin\helpers\fonts\Fonts_Helper;
use bpmj\wpidea\assets\Assets;
use bpmj\wpidea\templates_system\admin\modules\Interface_Templates_System_Modules_Factory;
use bpmj\wpidea\templates_system\admin\modules\settings_handlers\Interface_Templates_Settings_Handler;
use bpmj\wpidea\View_Hooks;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'bpmj_eddcm_layout_template_settings_save', function ( $settings ) {
	if ( ! $settings || ! is_array( $settings ) ) {
		return;
	}

	$assets = new Assets( __DIR__ );

	$assets_src_dir  = $assets->get_source_dir();
	$assets_dest_dir = $assets->get_absolute_dir();

    /** @var Interface_Templates_Settings_Handler $templates_settings_handler */
    $templates_settings_handler = WPI()->container->get(Interface_Templates_System_Modules_Factory::class)->get_settings_handler();

	$active_group_settings = $templates_settings_handler->get_current_template_options() ?? [];
	$settings = array_merge($settings, $active_group_settings);

	$modify_css      = function ($source, $settings) use ( $assets ) {
		$regexes          = [];
		$replacements     = [];
		$colors_and_fonts = array_diff_key( $settings, array_flip( array(
            'login_bg_file',
            'bg_file',
			'section_bg_file',
			'main_font',
			'secondary_font',
		) ) );

		// we clear redundant bg colors so that the relevant layer "sees through"
        if(isset($colors_and_fonts['order_form_bg_color']) && isset($colors_and_fonts[ 'bg_color' ])) {
            if ($colors_and_fonts['order_form_bg_color'] === $colors_and_fonts['bg_color']) {
                $colors_and_fonts['order_form_bg_color'] = '';
            }
        }

		foreach ( $colors_and_fonts as $key => $color_or_font ) {
			$color_subpattern = '(\/\*\s*' . preg_quote( $key ) . '\s*\*\/)';
			// We search for things like color: #ff0000 /* text_color */;
			$regexes[] = '/(?:#[a-fA-F0-9]{3,6}|"[a-zA-Z0-9\s]+")\s*' . $color_subpattern . '|' . $color_subpattern . '\s*(?:#[a-fA-F0-9]{3,6}|"[a-zA-Z0-9\s]+")/';
			if ( 'font_family' === substr( $key, - 11 ) ) {
				$replacements[] = '"' . ( $color_or_font ?: 'inherit' ) . '" $1';
			} else {
				$replacements[] = ( $color_or_font ?: 'none' ) . ' $1';
			}
		}
		foreach (['bg_file', 'section_bg_file', 'login_bg_file'] as $bg_file_key ) {
            if ($settings[$bg_file_key] !== null) {
				$regexes[]      = '/\/\*\s*' . preg_quote( $bg_file_key, '/' ) . '\s*\*\//';
				$replacements[] = ' url(' . $settings[$bg_file_key] . ') $0';
			}
		}

		// replace variables
		foreach (['{TEMPLATE_ASSETS_DIR}'] as $variable_name ) {
			$regexes[]      = '/' . $variable_name . '/';
			$replacements[] = BPMJ_EDDCM_TEMPLATES_URL . 'default' . $assets->get_old_dir( true );
		}

        // search for fonts
        foreach (['main_font_family', 'secondary_font_family'] as $css_key ) {
            $settings_key = str_replace('_family', '', $css_key); //in settings there are no '_family' suffix
            $valid_font_name = str_replace( '-', ' ', $settings[$settings_key]); //change dashes to spaces
            if ($settings[$settings_key] !== null) {

                // We are replacing here things like:
                // font-family: "Hind" /* secondary_font_family */, sans-serif;
                // With:
                // font-family: "Font Name From Settings", sans-serif;
                // Definitely this whole mechanism should be refactored

                $subpattern = '(\/\*\s*' . preg_quote( $css_key, '/' ) . '\s*\*\/)';
                $regexes[] = '/(?:#[a-fA-F0-9]{3,6}|"[a-zA-Z0-9\s]+")\s*' . $subpattern . '|' . $subpattern . '\s*(?:#[a-fA-F0-9]{3,6}|"[a-zA-Z0-9\s]+")/';
                $replacements[] = '"' . $valid_font_name . '"'; //create css variable that holds font family
            }
        }

		return preg_replace( $regexes, $replacements, $source );
	};

	if (! empty( $settings['main_font'] ) && ! empty( $settings['secondary_font'] ) ) {
        /* Combine and prepare CSS files */
        $import_clause = '@import url(\'https://fonts.googleapis.com/css?family='
            . urlencode(Fonts_Helper::get_google_font_name_by_slug($settings['main_font'])) . ':400,700|'
            . urlencode(Fonts_Helper::get_google_font_name_by_slug($settings['secondary_font'])) . ':300,400,700'
            . '&subset=latin-ext\');';
    }

	$css_string    = $import_clause . "\n\n";
	foreach ( glob( $assets_src_dir . '/css/*.css' ) as $css_file ) {
		$css_source     = file_get_contents( $css_file );
		$css_source_new = preg_replace( '/^@charset.+?$/m', '', $modify_css( $css_source, $settings ) );
		$css_string     .= "\n/* " . basename( $css_file ) . " */\n" . $css_source_new;
	}

	file_put_contents( $assets_dest_dir . '/css/style.css', $css_string );

	/* Prepare SVGs copy them to destination location */
	foreach ( glob( $assets_src_dir . '/svg/*.svg' ) as $svg_file ) {
		$svg_source     = file_get_contents( $svg_file );
		$svg_source_new = $modify_css( $svg_source, $settings );
		file_put_contents( $assets_dest_dir . '/gfx/' . basename( $svg_file ), $svg_source_new );
	}

    WPI()->templates->reload_template_config();
	WPI()->templates->minify_css();
} );

add_filter( 'bpmj_eddcm_layout_filter_settings', function ( $sane_settings ) {
	$sane_settings['scripts_version'] = base_convert( time(), 10, 36 );

	return $sane_settings;
} );

add_filter( 'bpmj_eddcm_layout_script_version', function ( $script, $template_settings ) {
	if( 'assets/js/courses.js' == $script ) {
		return BPMJ_EDDCM_VERSION;
	}

	if ( isset( $template_settings['scripts_version'] ) && in_array( $script, array(
			'assets/css/style.css',
		) )
	) {
		return $template_settings['scripts_version'];
	}

	return false;
}, 10, 2 );

add_action( 'bpmj_eddcm_after_upgrade', function () {
	$assets = new Assets( __DIR__ );
	$assets->regenerate();
} );

add_filter( 'edd_downloads_query', function ( $query, $atts ) {
	if ( empty( $atts[ 'bpmj_eddcm_courses_tag' ] ) ) {
		// This filter applies only to [courses] tag
		return $query;
	}

	if ( ! in_array( (int) $atts[ 'columns' ], array( 2, 3 ) ) ) {
		// Currently we have special template for 2- or 3-columns course list only
		return $query;
	}

	if ( ! empty( $query[ 'post__in' ] ) ) {
		// We force the query to return no rows
		$query[ 'bpmj_eddcm_courses__post__in' ] = $query[ 'post__in' ];
		$query[ 'post__in' ]                     = array( - 1 );
	}

	return $query;
}, 10, 2 );

add_filter( 'downloads_shortcode', function ( $display, $atts, $buy_button, $columns, $ignore, $downloads, $excerpt, $full_content, $price, $thumbnails, $query ) {
	if ( empty( $atts[ 'bpmj_eddcm_courses_tag' ] ) ) {
		// This filter applies only to [courses] tag
		return $display;
	}

	if ( ! in_array( (int) $columns, array( 2, 3 ) ) ) {
		// Currently we have special template for 2- or 3-columns course list only
		return $display;
	}

	if ( ! empty( $query[ 'bpmj_eddcm_courses__post__in' ] ) ) {
		// Restore the post__in query arg
		$query[ 'post__in' ] = $query[ 'bpmj_eddcm_courses__post__in' ];
		unset( $query[ 'bpmj_eddcm_courses__post__in' ] );
	}

	$downloads          = new WP_Query( $query );
	if ( $downloads->have_posts() ) :
		$i = 1;
		$wrapper_class  = 'edd_download_columns_' . $atts[ 'columns' ];
		$download_class = 'box-wrapper';
		$user_id        = get_current_user_id();
		ob_start(); ?>
		<div
            <?php View_Hooks::run(View_Hooks::RENDER_INLINE_ELEMENTS_IN_PRODUCT_LIST_BY_DOWNLOADS, $downloads); ?>
			class="edd_downloads_list <?php echo apply_filters( 'edd_downloads_list_wrapper_class', $wrapper_class, $atts ); ?>">
			<?php while ( $downloads->have_posts() ) : $downloads->the_post();
				$product_id        = get_the_ID();
				$course            = WPI()->courses->get_course_by_product( $product_id );
				$show_open_padlock = false;
				if ( false !== $course ) {
					$course_page_id = get_post_meta( $course->ID, 'course_id', true );
					$restricted_to  = array( array( 'download' => $product_id ) );
					$access         = bpmj_eddpc_user_can_access( $user_id, $restricted_to, $course_page_id );
					if ( 'valid' === $access[ 'status' ] || 'waiting' === $access[ 'status' ] ) {
						$show_open_padlock = true;
					}
				}

				?>
                <?php View_Hooks::run(View_Hooks::BEFORE_PRODUCT_LIST_ITEM) ?>
				<div class="<?php echo apply_filters( 'edd_download_class', $download_class, get_the_ID(), $atts, $i ); ?>"
					id="edd_download_<?php echo get_the_ID(); ?>">
					<?php
					if ( $show_open_padlock ):
						?>
						<div class="label">
							<div class="label-content"></div>
						</div>
						<?php
					endif;

					do_action( 'edd_download_before' );

					if ( 'false' != $atts[ 'thumbnails' ] ) :
						?>
						<a <?php View_Hooks::run(View_Hooks::RENDER_INLINE_ELEMENTS_IN_HYPERLINK_PRODUCT, get_the_ID()); ?> href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="thumb">
							<?php
							if ( in_array( get_post_thumbnail_id( get_the_ID() ), array( '', - 1 ) ) ):
								?>
								<span class="course-thumbnail-default"></span>
							<?php else: ?>
								<?php echo get_the_post_thumbnail( get_the_ID(), 'post-thumbnail' ); ?>
							<?php endif; ?>
						</a>
						<?php
						do_action( 'edd_download_after_thumbnail' );
					endif;

					?>
					<div class="content">
						<?php
						edd_get_template_part( 'shortcode', 'content-title' );
						do_action( 'edd_download_after_title' );

						if ( $atts[ 'excerpt' ] == 'yes' && $atts[ 'full_content' ] != 'yes' ) {
							edd_get_template_part( 'shortcode', 'content-excerpt' );
							do_action( 'edd_download_after_content' );
						} else if ( $atts[ 'full_content' ] == 'yes' ) {
							edd_get_template_part( 'shortcode', 'content-full' );
							do_action( 'edd_download_after_content' );
						}

						?>
					</div>
					<div class="bottom">
						<?php

						if ( $atts[ 'price' ] == 'yes' ) {
							if ( ! edd_has_variable_prices( get_the_ID() ) ) : ?>
								<p class="price edd_price">
									<?php edd_price( get_the_ID() ); ?>
								</p>
							<?php endif;
							do_action( 'edd_download_after_price' );
						}

						if ( $atts[ 'buy_button' ] == 'yes' ) {
							echo edd_get_purchase_link( array(
								'download_id' => get_the_ID(),
								'class'       => 'button',
								'color'       => '',
								'style'       => '',
							) );
						}

						do_action( 'edd_download_after' );

						?>
					</div>
				</div>
				<?php $i ++; endwhile; ?>

			<div style="clear:both;"></div>

			<?php wp_reset_postdata(); ?>

			<?php if ( filter_var( $atts[ 'pagination' ], FILTER_VALIDATE_BOOLEAN ) ) : ?>

				<?php
				$pagination = false;

				if ( is_single() ) {
					$pagination = paginate_links( apply_filters( 'edd_download_pagination_args', array(
						'base'    => get_permalink() . '%#%',
						'format'  => '?paged=%#%',
						'current' => max( 1, $query[ 'paged' ] ),
						'total'   => $downloads->max_num_pages
					), $atts, $downloads, $query ) );
				} else {
					$big          = 999999;
					$search_for   = array( $big, '#038;' );
					$replace_with = array( '%#%', '&' );
					$pagination   = paginate_links( apply_filters( 'edd_download_pagination_args', array(
						'base'    => str_replace( $search_for, $replace_with, get_pagenum_link( $big ) ),
						'format'  => '?paged=%#%',
						'current' => max( 1, $query[ 'paged' ] ),
						'total'   => $downloads->max_num_pages
					), $atts, $downloads, $query ) );
				}
				?>

				<?php if ( ! empty( $pagination ) ) : ?>
					<div id="edd_download_pagination" class="navigation">
						<?php echo $pagination; ?>
					</div>
				<?php endif; ?>

			<?php endif; ?>

		</div>
		<?php
		$display = ob_get_clean();
	else:
		$display = sprintf( _x( 'No %s found', 'download post type name', 'easy-digital-downloads' ), edd_get_label_plural() );
	endif;

	return $display;
}, 999, 11 );

add_filter( 'bpmj_eddcm_template_section_css_class', function ( $css_class ) {
	if ( ! is_singular() ) {
		return $css_class;
	}
	$post            = get_post();
	$wpidea_settings = get_option( 'wp_idea' );
	$columns         = (int) ( isset( $wpidea_settings[ 'list_columns' ] ) ? $wpidea_settings[ 'list_columns' ] : 3 );
	if ( in_array( $columns, array( 2, 3 ) ) && ( has_shortcode( $post->post_content, 'courses' ) || has_shortcode( $post->post_content, 'products' ) ) ) {
		return 'content columns columns-' . $columns . ' bg';
	}

	return $css_class;
} );

add_action( 'login_header', function () {
	?>
	<div class="vertical_placer">
	<div class="inner_placer">
	<?php
} );

add_action( 'login_footer', function () {
	?>
	</div>
	</div>
	<?php
} );

add_action( 'wp_ajax_bpmj_eddcm_search_google_fonts', function () {
	global $bpmj_eddcm_template_default_get_fonts;

	$slugify = function ( $text ) {
		return preg_replace( '/[^a-z]/', '', strtolower( $text ) );
	};

	$fonts        = $bpmj_eddcm_template_default_get_fonts();
	$limit        = 10;
	$search       = ! empty( $_REQUEST[ 'term' ] ) ? $slugify( $_REQUEST[ 'term' ] ) : '';
	$search_split = str_split( $search );
	$search_regex = '/' . implode( '.*?', $search_split ) . '/';
	$result       = array();
	$found        = 0;
	foreach ( $fonts as $font_slug => $font ) {
		if ( ! $search || 1 === preg_match( $search_regex, $font_slug ) ) {
			$result[] = $font;
			++ $found;
		}
		if ( $found === $limit ) {
			break;
		}
	}

	wp_die( json_encode( $result ) );
} );

function display_course_categories() {
	global $post;

	$wpidea_settings    = get_option( 'wp_idea' );
	$display_categories = empty( $wpidea_settings[ 'display_categories' ] ) || $wpidea_settings[ 'display_categories' ] !== 'off';
	if ( ! $display_categories ) {
		return;
	}

	$course     = WPI()->courses->get_course_by_product( $post->ID );

	if ( empty( $course ) ) {
		return;
	}

	$course_id  = $course->ID;
	$categories = get_the_terms( $course_id, 'download_category' );

	if ( empty( $categories ) ) {
		return;
	}

	if ( count( $categories ) == 1 ) {
		$categories_title = Translator_Static_Helper::translate('product.item.category');
		$template_html    = '<p class="bpmj_edd_course_categories">' . $categories_title . ': <span>';
		$template_html    .= $categories[ 0 ]->name;
		$template_html    .= '</span></p>';
		echo $template_html;

		return;
	}

	if ( count( $categories ) > 1 ) {
		$categories_list = '';
		foreach ( $categories as $category ) {
			$categories_list .= $category->name . ', ';
		}

		$categories_list  = substr( $categories_list, 0, - 2 );
		$categories_title = Translator_Static_Helper::translate('product.item.categories');
		$template_html    = '<p class="bpmj_edd_course_categories">' . $categories_title . ': <span>';
		$template_html    .= $categories_list;
		$template_html    .= '</span></p>';
		echo $template_html;

		return;
	}
}

add_action( 'edd_download_after_title', 'display_course_categories', 10 );

function display_course_tags() {
	global $post;

	$wpidea_settings = get_option( 'wp_idea' );
	$display_tags    = empty( $wpidea_settings[ 'display_tags' ] ) || $wpidea_settings[ 'display_tags' ] !== 'off';
	if ( ! $display_tags ) {
		return;
	}

	$course    = WPI()->courses->get_course_by_product( $post->ID );

	if ( empty( $course ) ) {
		return;
	}

	$course_id = $course->ID;
	$tags      = get_the_terms( $course_id, 'download_tag' );

	if ( empty( $tags ) ) {
		return;
	}

	if ( count( $tags ) == 1 ) {
		$tags_title    = Translator_Static_Helper::translate('product.item.tag');
		$template_html = '<p class="bpmj_edd_course_tags">' . $tags_title . ': <span>';
		$template_html .= $tags[ 0 ]->name;
		$template_html .= '</span></p>';
		echo $template_html;

		return;
	}

	if ( count( $tags ) > 1 ) {
		$tags_list = '';
		foreach ( $tags as $tag ) {
			$tags_list .= $tag->name . ', ';
		}

		$tags_list     = substr( $tags_list, 0, - 2 );
		$tags_title    = Translator_Static_Helper::translate('product.item.tags');
		$template_html = '<p class="bpmj_edd_course_tags">' . $tags_title . ': <span>';
		$template_html .= $tags_list;
		$template_html .= '</span></p>';
		echo $template_html;

		return;
	}
}

add_action( 'edd_download_after_title', 'display_course_tags', 15 );


add_filter( 'bpmj_eddcm_template_section_css_class', function ($css_class) {
    if ( ! is_tax( 'download_category' ) ) {
        return $css_class;
    }

    $wpidea_settings = get_option('wp_idea');
    $columns = (int)(isset($wpidea_settings['list_columns']) ? $wpidea_settings['list_columns'] : 3);
    if (in_array($columns, array(2, 3))) {
        return 'content columns columns-' . $columns . ' bg';
    }

    return $css_class;
});

function bpmj_edd_download_category_add_body_class( $classes ) {
    if ( ! is_tax ( 'download_category' ) ) {
        return $classes;
    }

    $classes[] = 'courses';
    return $classes;
}

add_filter( 'body_class', 'bpmj_edd_download_category_add_body_class' );

function bpmj_edd_display_404_page() {
    if( ! is_admin() && is_404() )
        include( 'template_parts/404.php' );
}

add_action( 'loop_no_results', 'bpmj_edd_display_404_page' );

function bpmj_edd_products_body_class( $classes ) {
    $post = get_post();

    if(!$post) {
        return $classes;
    }

    if ( has_shortcode( $post->post_content, 'products' ) ) {
        $classes[] = 'courses';
    }

    return $classes;
}

add_filter( 'body_class', 'bpmj_edd_products_body_class' );

function bpmj_eddcm_default_order_settings( $order_settings ) {
    $order_settings[] = array(
        'name'  => 'scarlet_cart_additional_info_1_title',
        'label' => '',
        'desc'  => "<script>(function ($) { $( 'input[name=\"wp_idea[scarlet_cart_additional_info_1_title]\"]' ).closest( 'tr' ).css( 'display', 'none' ); }(jQuery))</script>",
        'type'  => 'text',
        'size'  => 'regular',
    );

    $order_settings[] = array(
        'name'  => 'scarlet_cart_additional_info_1_desc',
        'label' => '',
        'desc'  => "<script>(function ($) { $( 'input[name=\"wp_idea[scarlet_cart_additional_info_1_desc]\"]' ).closest( 'tr' ).css( 'display', 'none' ); }(jQuery))</script>",
        'type'    => 'text',
    );

    $order_settings[] = array(
        'name'  => 'scarlet_cart_additional_info_2_title',
        'label' => __( 'Cart additional information 2nd title', BPMJ_EDDCM_DOMAIN ),
        'desc'  => "<script>(function ($) { $( 'input[name=\"wp_idea[scarlet_cart_additional_info_2_title]\"]' ).closest( 'tr' ).css( 'display', 'none' ); }(jQuery))</script>",
        'type'  => 'text',
        'size'  => 'regular',
    );

    $order_settings[] = array(
        'name'  => 'scarlet_cart_additional_info_2_desc',
        'label' => __( 'Cart additional information 2nd description', BPMJ_EDDCM_DOMAIN ),
        'desc'  => "<script>(function ($) { $( 'input[name=\"wp_idea[scarlet_cart_additional_info_2_desc]\"]' ).closest( 'tr' ).css( 'display', 'none' ); }(jQuery))</script>",
        'type'    => 'text',
    );

    $order_settings[] = array(
        'name'  => 'scarlet_cart_secure_payments_cb',
        'label' => __( 'Show secure payments icons', BPMJ_EDDCM_DOMAIN ),
        'desc'  => "<script>(function ($) { $( 'input[name=\"wp_idea[scarlet_cart_secure_payments_cb]\"]' ).closest( 'tr' ).css( 'display', 'none' ); }(jQuery))</script>",
        'type'  => 'checkbox',
    );

    return $order_settings;
}
add_filter( 'bpmj_eddcm_order_settings', 'bpmj_eddcm_default_order_settings' );

// FIX: #451 - problem z liczbą produktów w kategorii / tagu
function bpmj_eddcm_posts_per_page($query) {
    if (is_archive() && (isset($query->query_vars['download_category']) || isset($query->query_vars['download_tag']))) {
        $query->set('posts_per_page', -1);
    }
}

add_action('pre_get_posts', 'bpmj_eddcm_posts_per_page', 999);
