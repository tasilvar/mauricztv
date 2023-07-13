<?php

use bpmj\wpidea\View_Hooks;

function bpmj_courses_panel_shortcode($attr, $content ) {
	global $post;

	$attr = shortcode_atts( array(
	    'id'	 => '',
	    'mode'	 => '',
	    'time'	 => '',
	    'img'	 => '',
	    'txt'	 => ''
		), $attr );

	$top = $post->ID;

	$childpages = new WP_Query( array(
	    'post_type'	 => 'page',
	    'post_parent'	 => $top,
	    'posts_per_page' => 100,
	    'order'		 => 'ASC',
	    'orderby'	 => 'menu_order'
		) );

	ob_start();
	?>

	<div style="width: 968px; margin: 0 auto;">

		<?php
		while ( $childpages->have_posts() ) : $childpages->the_post();
			?>


			<figure id="<?php echo $top . '_' . $post->ID; ?>" style="width: 300px" class="wp-caption alignleft">
				<a href="<?php echo esc_url( get_permalink() ); ?>">
					<?php
					$thumb = get_the_post_thumbnail();
					if ( !empty( $thumb ) ) {
						echo $thumb;
					} else {
						echo '<img width="300" height="169" src="' . plugins_url( 'assets/imgs/tn-style-4-video-4.jpg', dirname( __FILE__ ) ) . '" class="attachment-post-thumbnail size-post-thumbnail wp-post-image" alt="' . get_the_title() . '">';
					}
					?>
				</a>
				<figcaption class="wp-caption-text"><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></figcaption></figure>


			<?php
		endwhile;
		wp_reset_query();
		?>

	</div>

	<?php
	return ob_get_clean();
}

add_shortcode( 'bpmj_courses_panel', 'bpmj_courses_panel_shortcode' );

function bpmj_template_content_button( $attr, $content ) {
	$attr = shortcode_atts( array(
	    'href'	 => '',
	    'title'	 => ''
		), $attr );

	return '<a href="' . $attr[ 'href' ] . '"><span class="button">' . $attr[ 'title' ] . '</span></a>';
}

add_shortcode( 'wpi_btn', 'bpmj_template_content_button' );

function bpmj_eddcm_courses_list( $shortcode_atts, $content = null ) {
	global $wpdb;

	$wpidea_settings = get_option( 'wp_idea' );
	$list_options    = array( 'ids' => null );
	if ( !empty( $wpidea_settings ) ) {
		foreach ( $wpidea_settings as $setting => $value ) {
			if ( substr( $setting, 0, 5 ) === 'list_' ) {
				$list_options[ substr( $setting, 5 ) ] = $value;
			}
		}
	}
	$attr                             = shortcode_atts( $list_options, $shortcode_atts );
	$attr[ 'bpmj_eddcm_courses_tag' ] = true;

	$query = "
        SELECT p.ID   
          FROM {$wpdb->posts} p 
         WHERE p.post_type = 'download'
               AND p.post_status = 'publish'
               AND (
                    -- select all published bundles
                    EXISTS (SELECT 1 
                              FROM {$wpdb->postmeta} pm 
                             WHERE pm.post_id = p.ID 
                                   AND pm.meta_key = '_edd_product_type' 
                                   AND pm.meta_value = 'bundle') OR
                    -- select all published courses
                    EXISTS (SELECT 1 
                              FROM {$wpdb->posts} p_course, {$wpdb->postmeta} pm_course 
                             WHERE p_course.ID = pm_course.post_id 
                                   AND pm_course.meta_key = 'product_id' 
                                   AND pm_course.meta_value = p.ID 
                                   AND p_course.post_Type = 'courses' 
                                   AND p_course.post_status = 'publish')
               )
               -- select only if the product should be visible on a list
               AND NOT EXISTS (SELECT 1 
                                 FROM {$wpdb->postmeta} pm 
                                WHERE pm.post_id = p.ID 
                                      AND pm.meta_key = 'hide_from_lists'
                                      AND pm.meta_value = 'on')
	";

	$all_product_ids = wp_list_pluck( $wpdb->get_results( $query ), 'ID' );
	$output          = '<p class="no-results">' . __( 'There are no courses in the catalog at this time', BPMJ_EDDCM_DOMAIN ) . '</p>';
	if ( ! empty( $all_product_ids ) ) {
		
		if( empty ( $attr[ 'ids' ] ) ) {
			$attr[ 'ids' ] = implode( ',', array_unique( $all_product_ids ) );
		}
		else {
			$ids_arr = explode( ',', $attr[ 'ids' ] );
			foreach( $ids_arr as $k => $v ) {
				$ids_arr[ $k ] = WPI()->courses->get_product_by_course( $v );
			}
			$attr[ 'ids' ] = implode( ',', array_unique( $ids_arr ) );
		}

		/**
		 * The below is not pretty, but it's the only sure way to check if @see edd_downloads_query() returned
		 * any products
		 */
		$did_action_before = did_action( 'edd_download_before' );
		$output_query      = edd_downloads_query( $attr, $content );
		$has_any_download  = did_action( 'edd_download_before' ) > $did_action_before;
		if ( $has_any_download ) {
			$output = $output_query;
		}
	}

	return $output;
}

add_shortcode( 'courses', 'bpmj_eddcm_courses_list' );

function bpmj_eddcm_purchase_shortcode( $attr ) {
    global $edd_receipt_args;

    if( empty( $edd_receipt_args ) )
        return '';

    $payment = get_post( $edd_receipt_args[ 'id' ] );
    if( empty( $payment ) )
        return '';

    $attr = shortcode_atts( array(
        'param'	 => 'id',
    ), $attr );

    $return_value = '';

    if( $attr[ 'param' ] == 'id' ) {
        $return_value = $payment->ID;
    } else if( $attr[ 'param' ] == 'total' ) {
        $return_value = edd_get_payment_amount( $payment->ID );
    }

    return $return_value;
}

add_shortcode( 'wpi_purchase', 'bpmj_eddcm_purchase_shortcode' );

function bpmj_eddcm_products_shortcode( $shortcode_atts, $content = null ) {
    global $wpdb;

    $wpidea_settings = get_option( 'wp_idea' );
    $list_options    = array( 'ids' => null );
    if ( !empty( $wpidea_settings ) ) {
        foreach ( $wpidea_settings as $setting => $value ) {
            if ( substr( $setting, 0, 5 ) === 'list_' ) {
                $list_options[ substr( $setting, 5 ) ] = $value;
            }
        }
    }
    $attr                             = shortcode_atts( $list_options, $shortcode_atts );
    $attr[ 'bpmj_eddcm_courses_tag' ] = true;

    $query = "
        SELECT p.ID   
          FROM {$wpdb->posts} p 
         WHERE p.post_type = 'download'
               AND p.post_status = 'publish'
               -- select only if the product should be visible on a list
               AND NOT EXISTS (SELECT 1 
                                 FROM {$wpdb->postmeta} pm 
                                WHERE pm.post_id = p.ID 
                                      AND pm.meta_key = 'hide_from_lists'
                                      AND pm.meta_value = 'on')
	";

    $all_product_ids = wp_list_pluck( $wpdb->get_results( $query ), 'ID' );
    $products_to_display = array();
    foreach( $all_product_ids as $download_id ) {
        $args = array(
            'post_type' => 'courses',
            'post_status' => array('any'),
            'meta_query' => array(
                array(
                    'key' => 'product_id',
                    'value' => $download_id
                )
            )
        );

        $course_query = new WP_Query( $args );

        if( $course_query->have_posts() ) {
            while( $course_query->have_posts() ) {
                $post = $course_query->the_post();
                $course_id = get_the_ID();
                $status = get_post_status($course_id );
                if( $status == 'publish' ) {
                    $products_to_display[] = $download_id;
                }
            }
        } else {
            $products_to_display[] = $download_id;
        }
    }

    $output          = __( 'There are no products in the catalog at this time', BPMJ_EDDCM_DOMAIN );
    if ( ! empty( $products_to_display ) ) {

        if( empty ( $attr[ 'ids' ] ) ) {
            $attr[ 'ids' ] = implode( ',', array_unique( $products_to_display ) );
        }
        else {
            $ids_arr = explode( ',', $attr[ 'ids' ] );
            $attr[ 'ids' ] = implode( ',', array_unique( $ids_arr ) );
        }

        /**
         * The below is not pretty, but it's the only sure way to check if @see edd_downloads_query() returned
         * any products
         */
        $did_action_before = did_action( 'edd_download_before' );
        $output_query      = edd_downloads_query( $attr, $content );
        $has_any_download  = did_action( 'edd_download_before' ) > $did_action_before;
        if ( $has_any_download ) {
            $output = $output_query;
        }
    }

    return $output;
}

add_shortcode( 'products', 'bpmj_eddcm_products_shortcode' );

function bpmj_eddcm_downloads_query( $atts, $content = null ) {
	global $wpdb;
	
	$atts = shortcode_atts( array(
		'category'         => '',
		'exclude_category' => '',
		'tags'             => '',
		'exclude_tags'     => '',
		'relation'         => 'OR',
		'number'           => 9,
		'price'            => 'no',
		'excerpt'          => 'yes',
		'full_content'     => 'no',
		'buy_button'       => 'yes',
		'columns'          => 3,
		'thumbnails'       => 'true',
		'orderby'          => 'post_date',
		'order'            => 'DESC',
		'ids'              => '',
		'pagination'       => 'true'
	), $atts, 'downloads' );

	$query = array(
		'post_type'      => 'download',
		'orderby'        => $atts['orderby'],
		'order'          => $atts['order'],
		'meta_query'	 => array(
			'relation'	 => 'OR',
			array (
				'key'     => 'hide_from_lists',
				'value'   => 'on',
				'compare' => '!='
			),
			array(
				'key' => 'hide_from_lists',
				'compare' => 'NOT EXISTS'
			),
		)
	);

	if ( filter_var( $atts['pagination'], FILTER_VALIDATE_BOOLEAN ) || ( ! filter_var( $atts['pagination'], FILTER_VALIDATE_BOOLEAN ) && $atts[ 'number' ] ) ) {

		$query['posts_per_page'] = (int) $atts['number'];

		if ( $query['posts_per_page'] < 0 ) {
			$query['posts_per_page'] = abs( $query['posts_per_page'] );
		}
	} else {
		$query['nopaging'] = true;
	}

	switch ( $atts['orderby'] ) {
		case 'price':
			$atts['orderby']   = 'meta_value';
			$query['meta_key'] = 'edd_price';
			$query['orderby']  = 'meta_value_num';
		break;

		case 'title':
			$query['orderby'] = 'title';
		break;

		case 'id':
			$query['orderby'] = 'ID';
		break;

		case 'random':
			$query['orderby'] = 'rand';
		break;

		default:
			$query['orderby'] = 'post_date';
		break;
	}

	if ( $atts['tags'] || $atts['category'] || $atts['exclude_category'] || $atts['exclude_tags'] ) {

		$query['tax_query'] = array(
			'relation' => $atts['relation']
		);

		if ( $atts['tags'] ) {

			$tag_list = explode( ',', $atts['tags'] );

			foreach( $tag_list as $tag ) {

				if( is_numeric( $tag ) ) {

					$term_id = $tag;

				} else {

					$term = get_term_by( 'slug', $tag, 'download_tag' );

					if( ! $term ) {
						continue;
					}

					$term_id = $term->term_id;
				}

				$query['tax_query'][] = array(
					'taxonomy' => 'download_tag',
					'field'    => 'term_id',
					'terms'    => $term_id
				);
			}

		}

		if ( $atts['category'] ) {

			$categories = explode( ',', $atts['category'] );

			foreach( $categories as $category ) {

				if( is_numeric( $category ) ) {

					$term_id = $category;

				} else {

					$term = get_term_by( 'slug', $category, 'download_category' );

					if( ! $term ) {
						continue;
					}

					$term_id = $term->term_id;

				}

				$query['tax_query'][] = array(
					'taxonomy' => 'download_category',
					'field'    => 'term_id',
					'terms'    => $term_id,
				);

			}

		}

		if ( $atts['exclude_category'] ) {

			$categories = explode( ',', $atts['exclude_category'] );

			foreach( $categories as $category ) {

				if( is_numeric( $category ) ) {

					$term_id = $category;

				} else {

					$term = get_term_by( 'slug', $category, 'download_category' );

					if( ! $term ) {
						continue;
					}

					$term_id = $term->term_id;
				}

				$query['tax_query'][] = array(
					'taxonomy' => 'download_category',
					'field'    => 'term_id',
					'terms'    => $term_id,
					'operator' => 'NOT IN'
				);
			}

		}

		if ( $atts['exclude_tags'] ) {

			$tag_list = explode( ',', $atts['exclude_tags'] );

			foreach( $tag_list as $tag ) {

				if( is_numeric( $tag ) ) {

					$term_id = $tag;

				} else {

					$term = get_term_by( 'slug', $tag, 'download_tag' );

					if( ! $term ) {
						continue;
					}

					$term_id = $term->term_id;
				}

				$query['tax_query'][] = array(
					'taxonomy' => 'download_tag',
					'field'    => 'term_id',
					'terms'    => $term_id,
					'operator' => 'NOT IN'
				);

			}

		}
	}

	if ( $atts['exclude_tags'] || $atts['exclude_category'] ) {
		$query['tax_query']['relation'] = 'AND';
	}

	if( ! empty( $atts['ids'] ) )
		$query['post__in'] = explode( ',', $atts['ids'] );

	if ( get_query_var( 'paged' ) )
		$query['paged'] = get_query_var('paged');
	else if ( get_query_var( 'page' ) )
		$query['paged'] = get_query_var( 'page' );
	else
		$query['paged'] = 1;

	// Allow the query to be manipulated by other plugins
	$query = apply_filters( 'edd_downloads_query', $query, $atts );

	$downloads = new WP_Query( $query );
	if ( $downloads->have_posts() ) :
		$i = 1;
		$wrapper_class = 'edd_download_columns_' . $atts['columns'];
		ob_start(); ?>
		<div class="edd_downloads_list <?php echo apply_filters( 'edd_downloads_list_wrapper_class', $wrapper_class, $atts ); ?>">
			<?php while ( $downloads->have_posts() ) : $downloads->the_post(); ?>
				<?php View_Hooks::run(View_Hooks::BEFORE_PRODUCT_LIST_ITEM) ?>
                class="<?php echo apply_filters( 'edd_download_class', 'edd_download', get_the_ID(), $atts, $i ); ?>" id="edd_download_<?php echo get_the_ID(); ?>">
					<div class="edd_download_inner">
						<?php

						do_action( 'edd_download_before' );

						if ( 'false' != $atts['thumbnails'] ) :
							edd_get_template_part( 'shortcode', 'content-image' );
							do_action( 'edd_download_after_thumbnail' );
						endif;

						edd_get_template_part( 'shortcode', 'content-title' );
						do_action( 'edd_download_after_title' );

						if ( $atts['excerpt'] == 'yes' && $atts['full_content'] != 'yes' ) {
							edd_get_template_part( 'shortcode', 'content-excerpt' );
							do_action( 'edd_download_after_content' );
						} else if ( $atts['full_content'] == 'yes' ) {
							edd_get_template_part( 'shortcode', 'content-full' );
							do_action( 'edd_download_after_content' );
						}

						if ( $atts['price'] == 'yes' ) {
							edd_get_template_part( 'shortcode', 'content-price' );
							do_action( 'edd_download_after_price' );
						}

						if ( $atts['buy_button'] == 'yes' )
							edd_get_template_part( 'shortcode', 'content-cart-button' );

						do_action( 'edd_download_after' );

						?>
					</div>
				</div>
				<?php if ( $atts['columns'] != 0 && $i % $atts['columns'] == 0 ) { ?><div style="clear:both;"></div><?php } ?>
			<?php $i++; endwhile; ?>

			<div style="clear:both;"></div>

			<?php wp_reset_postdata(); ?>

			<?php if ( filter_var( $atts['pagination'], FILTER_VALIDATE_BOOLEAN ) ) : ?>

			<?php
				$pagination = false;

				if ( is_single() ) {
					$pagination = paginate_links( apply_filters( 'edd_download_pagination_args', array(
						'base'    => get_permalink() . '%#%',
						'format'  => '?paged=%#%',
						'current' => max( 1, $query['paged'] ),
						'total'   => $downloads->max_num_pages
					), $atts, $downloads, $query ) );
				} else {
					$big = 999999;
					$search_for   = array( $big, '#038;' );
					$replace_with = array( '%#%', '&' );
					$pagination = paginate_links( apply_filters( 'edd_download_pagination_args', array(
						'base'    => str_replace( $search_for, $replace_with, get_pagenum_link( $big ) ),
						'format'  => '?paged=%#%',
						'current' => max( 1, $query['paged'] ),
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

	return apply_filters( 'downloads_shortcode', $display, $atts, $atts['buy_button'], $atts['columns'], '', $downloads, $atts['excerpt'], $atts['full_content'], $atts['price'], $atts['thumbnails'], $query );
}
add_shortcode( 'downloads_wpi', 'bpmj_eddcm_downloads_query' );

/**
 * Displays "go to the next undone lesson" button
 * 
 * @param type $atts
 * @return string
 * 
 * @note $atts['course_id'] can contains course id (cpt) or top page id (course panel page)
 */

function bpmj_eddcm_wpi_continue_shortcode( $atts ) {
	// needed for last lesson in course
	$course_id_from_post = array_key_exists('course_page_id', $_POST) ? $_POST['course_page_id'] : null;

    $atts = shortcode_atts( array(
        'course_id' => null,
        'text'      => null,
        'wpi_icon'  => null,
    ), $atts );

	if( empty( $atts['course_id'] ) ) {
        $atts['course_id'] = $course_id_from_post;
    }

	if( empty( $atts['course_id'] ) ) {
        $atts['course_id'] = WPI()->courses->get_course_top_page(get_the_ID());
    }
	    
	// try to check if we have id of top page or course cpt
	$type = get_post_type( $atts['course_id'] );
	if ( 'page' !== $type ) {
        $atts['course_id'] = WPI()->courses->get_page_id_by_course_id( $atts['course_id'] );
    }

	$course = WPI()->courses->get_course_by_page( $atts['course_id'] );

	if( empty( $course ) ) {
		return;
	}
	
	if ( ! WPI()->courses->course_has_content( $course->ID) )
        return '';

    if ( ! is_null( $atts['wpi_icon'] ) ) {
        $atts['text'] .= '<i class="fas fa-angle-double-right"></i>';
    }

    return '<a href="' . get_the_permalink( $atts['course_id'] ) . '?continue_course=' . $atts['course_id'] . '" title="' . __( 'Continue course', BPMJ_EDDCM_DOMAIN ) . '">' . $atts['text'] . '</a>';
}

add_shortcode( 'wpi_continue_anchor', 'bpmj_eddcm_wpi_continue_shortcode' );