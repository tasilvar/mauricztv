<?php
/*
 * Tworzy metabox dla typu posta download
 * 
 * Umożliwia wpisanie innej stawki VAT dla produktu niż 23%
 */

add_action( 'add_meta_boxes', 'bpmj_wpfa_edd_metabox_download' );

function bpmj_wpfa_edd_metabox_download() {

	add_meta_box( 'bpmj_wpfa_download', 'Fakturownia.pl', 'bpmj_wpfa_edd_download_body', 'download', 'side', 'high' );
}

// Funcja budująca szablon metaboxa.
function bpmj_wpfa_edd_download_body( $post ) {
	global $bpmj_wpfa_settings;

	// Ustawienie nonce do weryfikacji przy zapisie
	wp_nonce_field( 'bpmj_wpfa_download_check', 'bpmj_wpfa_download_check_nonce' );


	$default_vat = isset( $bpmj_wpfa_settings[ 'default_vat' ] ) && ! empty( $bpmj_wpfa_settings[ 'default_vat' ] ) ? $bpmj_wpfa_settings[ 'default_vat' ] : 23;

	$vat_value                = get_post_meta( $post->ID, 'bpmj_wpfa_vat', true );
	$add_info_value           = get_post_meta( $post->ID, 'bpmj_wpfa_add_info', true );
	$bpmj_wpfa_product_id     = get_post_meta( $post->ID, '_bpmj_wpfa_product_id', true );
	$bpmj_wpfa_update_product = get_post_meta( $post->ID, '_bpmj_wpfa_update_product', true );

	$VAT      = isset( $vat_value ) && ! empty( $vat_value ) ? $vat_value : '';
	$add_info = isset( $add_info_value ) && ! empty( $add_info_value ) ? $add_info_value : '';

	$options        = bpmj_wpfa_get_products_as_options();
	$title_prepared = bpmj_wpfa_normalize_string_for_comparison( get_the_title( $post->ID ) );
	?>

    <div class="metabox">

        <div>
            <p><strong><?php _e( 'Stawka VAT', 'bpmj_wpfa' ) ?></strong></p>
            <label for="bpmj_wpfa_vat">
                <input class="small-text" name="bpmj_wpfa_vat" type="text" value="<?php echo $VAT ?>"/>%
				<?php echo __( 'Pozostaw puste by zastosować stawkę domyślną ', 'bpmj_wpfa' ) . $default_vat . '%.' ?>
            </label>
        </div>

        <div>
            <p><strong><?php _e( 'Dodatkowa informacja (np. PKWiU)', 'bpmj_wpfa' ) ?></strong></p>
            <label for="bpmj_wpfa_add_info">
                <input class="medium-text" name="bpmj_wpfa_add_info" type="text" value="<?php echo $add_info ?>"/>
            </label>
        </div>

        <div>
            <p><strong><?php _e( 'Produkt w serwisie fakturownia.pl', 'bpmj_wpfa' ) ?></strong></p>
            <label>
                <select id="_bpmj_wpfa_product_id" name="_bpmj_wpfa_product_id" style="width: 100%;">
	                <?php foreach ( $options as $key => $value ): ?>
		                <option value="<?php echo esc_attr( $key ); ?>" <?php if ( $bpmj_wpfa_product_id ) {
			                selected( $key, $bpmj_wpfa_product_id );
		                } else {
			                selected( bpmj_wpfa_normalize_string_for_comparison( $value ) === $title_prepared );
		                } ?>><?php echo esc_html( $value ); ?></option>
	                <?php endforeach; ?>
                </select>
            </label>
        </div>

        <div>
            <p><strong><?php _e( 'Aktualizuj dane produktu', 'bpmj_wpfa' ) ?></strong></p>
            <label>
                <input type="checkbox" name="_bpmj_wpfa_update_product"
                       value="yes" <?php checked( 'yes', $bpmj_wpfa_update_product ); ?> />
				<?php _e( 'Zaznacz, jeśli chcesz by dane produktu (nazwa, kod produktu, cena, podatek) były automatycznie aktualizowane w serwisie fakturownia.pl po zapisie produktu.' ); ?>
            </label>
        </div>

    </div>

	<?php
}

/*
 * Funckja zapisująca dane
 */
add_action( 'save_post', 'bpmj_wpfa_edd_download_save' );

function bpmj_wpfa_edd_download_save( $post_id ) {

	// Sprawdza klucz nonce
	if ( ! isset( $_POST[ 'bpmj_wpfa_download_check_nonce' ] ) ) {
		return $post_id;
	}

	$nonce = $_POST[ 'bpmj_wpfa_download_check_nonce' ];

	if ( ! wp_verify_nonce( $nonce, 'bpmj_wpfa_download_check' ) ) {
		return $post_id;
	}


	// Zlikwidowanie konfliktu z autozapisem
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	// Sprawdza prawa użytkownika i typ postu
	if ( 'download' == $_POST[ 'post_type' ] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}
	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
	}

	/* Wszytstko się zgadza. Można zapisac dane. */

	update_post_meta( $post_id, 'bpmj_wpfa_vat', sanitize_text_field( $_POST[ 'bpmj_wpfa_vat' ] ) );
	update_post_meta( $post_id, 'bpmj_wpfa_add_info', sanitize_text_field( $_POST[ 'bpmj_wpfa_add_info' ] ) );

	$tax_rate             = bpmj_wpfa_edd_get_tax_rate( $post_id );
	$update_product_input = isset( $_POST[ '_bpmj_wpfa_update_product' ] ) ? $_POST[ '_bpmj_wpfa_update_product' ] : false;
	$product_id_input     = isset( $_POST[ '_bpmj_wpfa_product_id' ] ) ? $_POST[ '_bpmj_wpfa_product_id' ] : false;
	$price                = bpmj_wpfa_edd_get_price( $post_id );

	bpmj_wpfa_save_product_meta( $post_id, $update_product_input, $product_id_input, $price, $tax_rate );
	return $post_id;
}

add_action( 'edd_download_price_table_head', 'bpmj_wpfa_edd_variable_price_head' );

function bpmj_wpfa_edd_variable_price_head() {
	?>
    <th><?php _e( 'Produkt fakturownia.pl', 'bpmj_wpfa' ); ?></th>
	<?php
}

add_action( 'edd_download_price_table_row', 'bpmj_wpfa_edd_variable_price_row', 10, 3 );

function bpmj_wpfa_edd_variable_price_row( $post_id, $key, $args ) {
	$wpfa_product_options = bpmj_wpfa_get_products_as_options();
	$variable_price_name_prepared = bpmj_wpfa_normalize_string_for_comparison($args['name']);
	?>
    <td>
        <select name="edd_variable_prices[<?php echo $key; ?>][_bpmj_wpfa_product_id]" style="width: 100%;">
	        <?php foreach ( $wpfa_product_options as $product_id => $product_label ): ?>
		        <option value="<?php echo esc_attr( $product_id ); ?>" <?php if ( $args[ '_bpmj_wpfa_product_id' ] ) {
			        selected( $args[ '_bpmj_wpfa_product_id' ], $product_id );
		        } else {
			        selected( bpmj_wpfa_normalize_string_for_comparison( $product_label ) === $variable_price_name_prepared );
		        } ?>><?php echo esc_html( $product_label ); ?></option>
	        <?php endforeach; ?>
        </select>
    </td>
	<?php
}

add_filter( 'edd_price_row_args', 'bpmj_wpfa_edd_price_row_args', 10, 2 );

function bpmj_wpfa_edd_price_row_args( $args, $value ) {
	$args[ '_bpmj_wpfa_product_id' ] = isset( $value[ '_bpmj_wpfa_product_id' ] ) ? $value[ '_bpmj_wpfa_product_id' ] : '';

	return $args;
}

add_filter( 'sanitize_post_meta_edd_variable_prices', 'bpmj_wpfa_edd_sanitize_variable_prices', 20 );

function bpmj_wpfa_edd_sanitize_variable_prices( $prices ) {
	$download = get_post();
	if ( ! $download && isset( $_POST[ 'product_id' ] ) ) {
		$download = get_post( $_POST[ 'product_id' ] );
	}
	if ( ! $download instanceof WP_Post ) {
		return $prices;
	}
	$update_product_input = isset( $_POST[ '_bpmj_wpfa_update_product' ] ) ? $_POST[ '_bpmj_wpfa_update_product' ] : false;
	$update_product       = false;
	if ( false !== $update_product_input ) {
		$update_product = 'yes' === $update_product_input;
	}
	$tax_rate = bpmj_wpfa_edd_get_tax_rate( $download->ID );

	foreach ( $prices as $id => $price ) {
		if ( empty( $prices[ $id ][ '_bpmj_wpfa_product_id' ] ) ) {
			$product_id_input = isset( $prices[ $id ][ '_bpmj_wpfa_product_id' ] ) ? $prices[ $id ][ '_bpmj_wpfa_product_id' ] : false;
			if ( false !== $product_id_input ) {
				$fakturownia_product_id = $product_id_input;
				if ( ! $fakturownia_product_id || $update_product ) {
					$fakturownia                              = new BPMJ_WP_Fakturownia();
					$prices[ $id ][ '_bpmj_wpfa_product_id' ] = $fakturownia->create_modify_product( $download->post_title . ' - ' . $prices[ $id ][ 'name' ], '', $prices[ $id ][ 'amount' ], $tax_rate, $fakturownia_product_id );
				}
			}
		}
	}

	return $prices;
}