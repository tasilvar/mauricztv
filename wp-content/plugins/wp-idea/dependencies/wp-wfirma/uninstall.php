<?php

/**
 * Deinstalacja Better Academy
 */
// Exit if accessed directly
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit;

// Wyczyść harmonogram zadań 
wp_clear_scheduled_hook( 'bpmj_wpwf_cron' );


