<?php

function bpmj_eddcm_remove_themes_filters() {
    remove_filter( 'wp_calculate_image_sizes', 'twentyseventeen_content_image_sizes_attr', 10 );
}

add_action( 'after_setup_theme', 'bpmj_eddcm_remove_themes_filters' );
