<?php

use bpmj\wpidea\wolverine\product\Discount;

add_action( 'shutdown', function () {
	global $wpdb;
	$log_stack = true;
    
    $log = '';

	foreach ( $wpdb->queries as $query ) {
        
        if( false === strpos( $query[0], '_bpmj_eddpc_access' ) ) {
        //    continue;
        }
        
		$log .= trim($query[0]) . " - ($query[1] s)";
		if ( $log_stack ) {
			$log .= PHP_EOL . "[Stack]: $query[2]" . PHP_EOL . PHP_EOL;
		} else {
			$log .= PHP_EOL . PHP_EOL;
		}
	}
    
    if( empty( $log ) ) {
        return;
    }
    
    $filename = date('Y_m_d_H') . '.log';
	$log_file = fopen( ABSPATH . '/wp-content/logs/' . $filename, 'a' );

	fwrite( $log_file, PHP_EOL . PHP_EOL . "############################################################" . PHP_EOL . PHP_EOL . date( "F j, Y, g:i:s a" ) . PHP_EOL );
    fwrite( $log_file, $log );
    
	fclose( $log_file );
} );

add_action( 'admin_init', function () {
} );