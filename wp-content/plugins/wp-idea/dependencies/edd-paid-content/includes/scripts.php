<?php

function bpmj_eddpc_admin_scripts() {
	global $post;

	wp_enqueue_script( 'jquery-datetimepicker', BPMJ_EDD_PC_URL . '/assets/js/jquery.datetimepicker.full.min.js', array( 'jquery' ), BPMJ_EDD_PC_VERSION );
	wp_enqueue_script( 'bpmj-eddpc-admin-script', BPMJ_EDD_PC_URL . '/assets/js/admin.js', array( 'jquery' ), BPMJ_EDD_PC_VERSION );

	wp_localize_script( 'bpmj-eddpc-admin-script', 'bpmj_eddpc', array(
		'ajax'                   => admin_url( 'admin-ajax.php' ),
		'delete_renewal_confirm' => __( 'Are you sure you want to remove access to this product?', 'edd-paid-content' ),
	) );

	wp_enqueue_style( 'jquery-datetimepicker-style', BPMJ_EDD_PC_URL . '/assets/css/jquery.datetimepicker.min.css', BPMJ_EDD_PC_VERSION );
	wp_enqueue_style( 'bpmj_eddpc-admin-style', BPMJ_EDD_PC_URL . '/assets/css/admin-style.css', BPMJ_EDD_PC_VERSION );
}

add_action( 'admin_enqueue_scripts', 'bpmj_eddpc_admin_scripts' );
?>
