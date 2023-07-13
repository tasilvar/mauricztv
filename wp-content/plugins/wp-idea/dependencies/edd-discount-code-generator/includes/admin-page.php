<?php

function edd_dcg_admin_messages() {
	if ( isset( $_GET['wp-idea-message'] ) && 'discounts_added' == $_GET['wp-idea-message'] && current_user_can( 'manage_shop_discounts' ) ) {
		 $message = $_GET['wp-idea-number'] .' '. __( 'codes generated', 'edd_dcg' );
		 add_settings_error( 'edd-dcg-notices', 'edd-discounts-added', $message, 'updated' );
		 settings_errors( 'edd-dcg-notices' );
	}
}
add_action( 'admin_notices', 'edd_dcg_admin_messages', 10 );