<?php

if ( ! function_exists( 'UM' ) ) {
    return;
}

$um = UM();
$um_access_object = $um->access();
remove_filter( 'get_pages', [ &$um_access_object, 'filter_protected_posts' ], 99 );
remove_action( 'pre_get_posts', [ &$um_access_object, 'exclude_posts' ], 99 );
