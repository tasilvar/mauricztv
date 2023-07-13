<?php
/*
 * Tworzy metabox dla typu posta download
 * 
 * Umożliwia wpisanie innej stawki VAT dla produktu niż 23%
 */

add_action( 'add_meta_boxes', 'bpmj_wptaxe_metabox_download' );

function bpmj_wptaxe_metabox_download() {

	add_meta_box( 'bpmj_wptaxe_download', 'Taxe.pl', 'bpmj_wptaxe_download_body', 'download', 'side', 'high' );
}

// Funcja budująca szablon metaboxa.
function bpmj_wptaxe_download_body( $post ) {
	global $bpmj_wptaxe_settings;

	// Ustawienie nonce do weryfikacji przy zapisie
	wp_nonce_field( 'bpmj_wptaxe_download_check', 'bpmj_wptaxe_download_check_nonce' );


	$default_vat = isset( $bpmj_wptaxe_settings[ 'default_vat' ] ) && ! empty( $bpmj_wptaxe_settings[ 'default_vat' ] ) ? $bpmj_wptaxe_settings[ 'default_vat' ] : 23;

	$vat_value = get_post_meta( $post->ID, 'bpmj_wptaxe_vat', true );

	$VAT = isset( $vat_value ) && ! empty( $vat_value ) ? $vat_value : '';


	?>

    <div class="metabox">

        <div>
            <p><strong><?php _e( 'Stawka VAT', 'bpmj_wptaxe' ) ?></strong></p>
            <label for="bpmj_wptaxe_vat">
                <input class="small-text" name="bpmj_wptaxe_vat" type="text" value="<?php echo $VAT ?>"/>%
				<?php echo __( 'Pozostaw puste by zastosować stawkę domyślną ', 'bpmj_wptaxe' ) . $default_vat . '%.' ?>
            </label>
        </div>

    </div>

	<?php
}


/*
 * Funckja zapisująca dane
 */
add_action( 'save_post', 'bpmj_wptaxe_download_save' );

/**
 * @param int $post_id
 *
 * @return int
 */
function bpmj_wptaxe_download_save( $post_id ) {

	// Sprawdza klucz nonce
	if ( ! isset( $_POST[ 'bpmj_wptaxe_download_check_nonce' ] ) ) {
		return $post_id;
	}

	$nonce = $_POST[ 'bpmj_wptaxe_download_check_nonce' ];

	if ( ! wp_verify_nonce( $nonce, 'bpmj_wptaxe_download_check' ) ) {
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
	update_post_meta( $post_id, 'bpmj_wptaxe_vat', sanitize_text_field( $_POST[ 'bpmj_wptaxe_vat' ] ) );
    return $post_id;
}
