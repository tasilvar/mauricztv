<?php

/**
 * Plugin Name: Converter for Media
 * Description: Speed up your website by using our WebP & AVIF Converter (formerly WebP Converter for Media). Serve WebP and AVIF images instead of standard formats JPEG, PNG and GIF now!
<<<<<<< HEAD
 * Version: 5.13.1
 * Author: matt plugins - Optimize images by convert WebP & AVIF
=======
 * Version: 5.12.5
 * Author: matt plugins
>>>>>>> ef700b4b391d00bdccb8f089fe79280fa6c1ef62
 * Author URI: https://url.mattplugins.com/converter-plugin-author-link
 * Text Domain: webp-converter-for-media
 * Network: true
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/vendor/autoload.php';

new WebpConverter\WebpConverter(
<<<<<<< HEAD
	new WebpConverter\PluginInfo( __FILE__, '5.13.1' )
=======
	new WebpConverter\PluginInfo( __FILE__, '5.12.5' )
>>>>>>> ef700b4b391d00bdccb8f089fe79280fa6c1ef62
);
