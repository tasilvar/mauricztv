<?php

namespace bpmj\wpidea\admin\settings;

use bpmj\wpidea\assets\Assets;

class Field_Sanitizer {
    /**
     * Returns file url relative to the css files directory
     *
     * @param string $file_url
     * @param string $template_dir Template directory
     * @return string
     */
    public static function sanitize_image_field( $file_url, $template_dir )
    {
        $assets = new Assets( $template_dir );
        
        if ( strpos( $file_url, WP_CONTENT_URL ) === 0 ) {
            $css_url_rel     = ltrim( str_replace( WP_CONTENT_URL, '', WP_CONTENT_URL . '/' . $assets->get_assets_dir_path() . '/css' ), '/' );
            $file_url_rel	 = ltrim( str_replace( WP_CONTENT_URL, '', $file_url ), '/' );
            $from_path_parts = explode( '/', $css_url_rel );
            $to_path_parts	 = explode( '/', $file_url_rel );
            $i		 = 0;
            while ( isset( $from_path_parts[ $i ] ) && isset( $to_path_parts[ $i ] ) && $from_path_parts[ $i ] === $to_path_parts[ $i ] ) {
                ++$i;
            }
            $relative_path = str_repeat( '../', count( $from_path_parts ) - $i );
            $relative_path .= implode( '/', array_slice( $to_path_parts, $i ) );
            return $relative_path;
        }

        return $file_url;
    }
}