<?php

function bpmj_eddcm_vendd_revert_hooks() {
	if ( ! has_action( 'edd_after_download_content', 'edd_append_purchase_link' ) ) {
		add_action( 'edd_after_download_content', 'edd_append_purchase_link' );
	}

	remove_action( 'edd_download_before', 'vendd_downloads_shortcode_wrap_open' );
	remove_action( 'edd_download_after', 'vendd_downloads_shortcode_wrap_close' );
	remove_filter( 'post_class', 'vendd_edd_shortcodes_classes' );
	remove_filter( 'edd_empty_cart_message', 'vendd_empty_cart_content' );
}

add_action( 'bpmj_eddcm_prepare_custom_template', 'bpmj_eddcm_vendd_revert_hooks' );