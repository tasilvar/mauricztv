<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *
 */
function bpmj_wpfa_on_woo_product_details() {
	global $thepostid, $post;
	$post_id = empty( $thepostid ) ? $post->ID : $thepostid;

	$options = bpmj_wpfa_get_products_as_options();

	echo '<div class="options_group show_if_simple">';

	$bpmj_wpfa_product_id = get_post_meta( $post_id, '_bpmj_wpfa_product_id', true );

	$select_params = array(
		'id'          => '_bpmj_wpfa_product_id',
		'label'       => 'Produkt w serwisie fakturownia.pl',
		'desc_tip'    => 'true',
		'description' => 'Wybierz produkt w serwisie fakturownia.pl, z którym chcesz powiązać ten produkt. Po dokonaniu sprzedaży stan magazynowy w serwisie fakturownia.pl zostanie automatycznie obniżony.',
		'options'     => $options,
	);
	if ( ! $bpmj_wpfa_product_id ) {
		$title_prepared = bpmj_wpfa_normalize_string_for_comparison( get_the_title( $post_id ) );
		foreach ( $options as $key => $option ) {
			if ( bpmj_wpfa_normalize_string_for_comparison( $option ) === $title_prepared ) {
				$select_params[ 'value' ] = $key;
				break;
			}
		}
	}
	woocommerce_wp_select( $select_params );

	woocommerce_wp_checkbox( array(
		'id'          => '_bpmj_wpfa_update_product',
		'label'       => 'Aktualizuj dane produktu',
		'desc_tip'    => 'true',
		'description' => 'Zaznacz, jeśli chcesz by dane produktu (nazwa, kod produktu, cena, podatek) były automatycznie aktualizowane w serwisie fakturownia.pl po zapisie produktu.',
	) );

	echo '</div>';
}

add_action( 'woocommerce_product_options_general_product_data', 'bpmj_wpfa_on_woo_product_details' );

/**
 * @param int $loop
 * @param array $variation_data
 * @param WP_Post $variation
 */
function wpfa_on_woo_product_details_variation( $loop, $variation_data, $variation ) {
	$options = bpmj_wpfa_get_products_as_options();

	$bpmj_wpfa_product_id     = get_post_meta( $variation->ID, '_bpmj_wpfa_product_id', true );
	$bpmj_wpfa_update_product = get_post_meta( $variation->ID, '_bpmj_wpfa_update_product', true );
	$variation_title_prepared = bpmj_wpfa_normalize_string_for_comparison( get_the_title( $variation->ID ) );

	?>
	<p class="form-row form-row-first">
		<label for="_bpmj_wpfa_product_id_variation-<?php echo $loop; ?>">Produkt w serwisie
			fakturownia.pl <?php echo wc_help_tip( 'Wybierz produkt w serwisie fakturownia.pl, z którym chcesz powiązać ten produkt. Po dokonaniu sprzedaży stan magazynowy w serwisie fakturownia.pl zostanie automatycznie obniżony.' ); ?></label>
		<select id="_bpmj_wpfa_product_id_variation-<?php echo $loop; ?>"
		        name="_bpmj_wpfa_product_id_variation[<?php echo $loop; ?>]">
			<?php foreach ( $options as $key => $value ): ?>
				<option
					value="<?php echo esc_attr( $key ); ?>" <?php if ( $bpmj_wpfa_product_id ) {
					selected( $key, $bpmj_wpfa_product_id );
				} else {
					selected( bpmj_wpfa_normalize_string_for_comparison( $value ) === $variation_title_prepared );
				} ?>><?php echo esc_html( $value ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p class="form-row form-row-last">
		<label for="_bpmj_wpfa_update_product_variation-<?php echo $loop; ?>">Aktualizuj dane
			produktu <?php echo wc_help_tip( 'Zaznacz, jeśli chcesz by dane produktu (nazwa, kod produktu, cena, podatek) były automatycznie aktualizowane w serwisie fakturownia.pl po zapisie produktu.' ); ?></label>
		<input type="checkbox" id="_bpmj_wpfa_update_product_variation-<?php echo $loop; ?>"
		       name="_bpmj_wpfa_update_product_variation[<?php echo $loop; ?>]"
		       value="yes" <?php checked( 'yes', $bpmj_wpfa_update_product ); ?>/>
	</p>
	<?php
}

add_action( 'woocommerce_product_after_variable_attributes', 'wpfa_on_woo_product_details_variation', 10, 3 );

/**
 * @param int $post_id
 */
function bpmj_wpfa_on_woo_save_metabox_simple( $post_id ) {
	$product              = new WC_Product( $post_id );
	$tax_rate             = bpmj_wpfa_woo_get_tax_rate( $product );
	$update_product_input = isset( $_POST[ '_bpmj_wpfa_update_product' ] ) ? $_POST[ '_bpmj_wpfa_update_product' ] : false;
	$product_id_input     = isset( $_POST[ '_bpmj_wpfa_product_id' ] ) ? $_POST[ '_bpmj_wpfa_product_id' ] : false;
	bpmj_wpfa_save_product_meta( $post_id, $update_product_input, $product_id_input, round( $product->get_price_including_tax(), 2 ), $tax_rate, $product->get_sku() );
}

add_action( 'woocommerce_process_product_meta_simple', 'bpmj_wpfa_on_woo_save_metabox_simple' );

/**
 * @param int $variation_id
 * @param int $i
 */
function bpmj_wpfa_on_woo_save_metabox_variation( $variation_id, $i ) {
	$product              = new WC_Product_Variation( $variation_id );
	$tax_rate             = bpmj_wpfa_woo_get_tax_rate( $product );
	$update_product_input = isset( $_POST[ '_bpmj_wpfa_update_product_variation' ] ) && isset( $_POST[ '_bpmj_wpfa_update_product_variation' ][ $i ] ) ? $_POST[ '_bpmj_wpfa_update_product_variation' ][ $i ] : false;
	$product_id_input     = isset( $_POST[ '_bpmj_wpfa_product_id_variation' ] ) && isset( $_POST[ '_bpmj_wpfa_product_id_variation' ][ $i ] ) ? $_POST[ '_bpmj_wpfa_product_id_variation' ][ $i ] : false;
	bpmj_wpfa_save_product_meta( $variation_id, $update_product_input, $product_id_input, round( $product->get_price_including_tax(), 2 ), $tax_rate, $product->get_sku() );
}

add_action( 'woocommerce_save_product_variation', 'bpmj_wpfa_on_woo_save_metabox_variation', 10, 2 );
