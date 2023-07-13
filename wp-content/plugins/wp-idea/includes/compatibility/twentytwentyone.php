<?php

function wpi_remove_theme_attachment_image_attributes( $attr, $attachment, $size ) {
    unset( $attr['style'] );
    return $attr;
}

add_filter( 'wp_get_attachment_image_attributes', 'wpi_remove_theme_attachment_image_attributes', 20, 3 );
