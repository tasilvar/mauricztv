<?php
/*
 * Tworzy metabox dla typu posta download
 * 
 * Umożliwia wpisanie innej stawki VAT dla produktu niż 23%
 */

add_action( 'add_meta_boxes', 'bpmj_wpinfakt_metabox_download' );

function bpmj_wpinfakt_metabox_download() {

	add_meta_box( 'bpmj_wpinfakt_download', 'Infakt.pl', 'bpmj_wpinfakt_download_body', 'download', 'side', 'high' );
}

// Funcja budująca szablon metaboxa.
function bpmj_wpinfakt_download_body( $post ) {
	global $bpmj_wpinfakt_settings;

	// Ustawienie nonce do weryfikacji przy zapisie
	wp_nonce_field( 'bpmj_wpinfakt_download_check', 'bpmj_wpinfakt_download_check_nonce' );


	$default_vat = isset( $bpmj_wpinfakt_settings[ 'default_vat' ] ) && ! empty( $bpmj_wpinfakt_settings[ 'default_vat' ] ) ? $bpmj_wpinfakt_settings[ 'default_vat' ] : 23;

	$vat_value = get_post_meta( $post->ID, 'bpmj_wpinfakt_vat', true );

	$VAT = isset( $vat_value ) && ! empty( $vat_value ) ? $vat_value : '';


	?>

    <div class="metabox">

        <div>
            <p><strong><?php _e( 'Stawka VAT', 'bpmj_wpinfakt' ) ?></strong></p>
            <label for="bpmj_wpinfakt_vat">
                <input class="small-text" name="bpmj_wpinfakt_vat" type="text" value="<?php echo $VAT ?>"/>%
				<?php echo __( 'Pozostaw puste by zastosować stawkę domyślną ', 'bpmj_wpinfakt' ) . $default_vat . '%.' ?>
            </label>
        </div>

    </div>

	<?php
}


/*
 * Funckja zapisująca dane
 */
add_action( 'save_post', 'bpmj_wpinfakt_download_save' );

/**
 * @param int $post_id
 *
 * @return int
 */
function bpmj_wpinfakt_download_save( $post_id ) {

	// Sprawdza klucz nonce
	if ( ! isset( $_POST[ 'bpmj_wpinfakt_download_check_nonce' ] ) ) {
		return $post_id;
	}

	$nonce = $_POST[ 'bpmj_wpinfakt_download_check_nonce' ];

	if ( ! wp_verify_nonce( $nonce, 'bpmj_wpinfakt_download_check' ) ) {
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
	update_post_meta( $post_id, 'bpmj_wpinfakt_vat', sanitize_text_field( $_POST[ 'bpmj_wpinfakt_vat' ] ) );
    return $post_id;
}
