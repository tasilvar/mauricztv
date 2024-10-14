<?php

/**
 * @var Templates $this
 * @var string $template
 */

use bpmj\wpidea\admin\helpers\fonts\Fonts_Helper;
use bpmj\wpidea\admin\settings\Field_Sanitizer;
use bpmj\wpidea\assets\Assets;
use bpmj\wpidea\Templates;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$sanitize_color = function ( $color ) {
	$color = strtolower( trim( $color ) );
	if ( preg_match( '/^#[a-f0-9]{3,6}$/', $color ) === 1 ) {
		return $color;
	}

	return '';
};

$sanitize_image = function ( $file_url ) {
	return Field_Sanitizer::sanitize_image_field( $file_url, __DIR__ );
};

global $bpmj_eddcm_template_default_get_fonts;
$bpmj_eddcm_template_default_get_fonts = function () {
	return Fonts_Helper::get_google_fonts_from_remote();
};

$template_settings     = WPI()->settings->get_layout_template_settings_array( $template );
$main_font_id          = isset( $template_settings[ 'main_font' ] ) ? $template_settings[ 'main_font' ] : 'open-sans';
$main_font_family      = isset( $template_settings[ 'main_font_family' ] ) ? $template_settings[ 'main_font_family' ] : 'Open Sans';
$secondary_font_id     = isset( $template_settings[ 'secondary_font' ] ) ? $template_settings[ 'secondary_font' ] : 'hind';
$secondary_font_family = isset( $template_settings[ 'secondary_font_family' ] ) ? $template_settings[ 'secondary_font_family' ] : 'Hind';

$assets = new Assets( __DIR__ );


return [
    'styles'      => [
        $assets->get_relative_file_path(Assets::WP_IDEA_MIN_DYNAMIC_CSS_FILE_PATH). '?v=' . filemtime( $assets->get_absolute_file_path( Assets::WP_IDEA_MIN_DYNAMIC_CSS_FILE_PATH)  ),
        'assets/genericons/genericons.css',
    ],
    'styles_for_minification' => [
        $assets->get_absolute_dir() . '/' . Assets::WP_IDEA_MIN_DYNAMIC_CSS_FILE_PATH => [
            $assets->get_absolute_file_path('css/style.css'),
        ],
    ],
    'javascripts' => [
        'assets/'.Assets::WP_IDEA_MIN_JS_FILE_PATH . '?v=' . filemtime( $assets->get_absolute_file_path( Assets::WP_IDEA_MIN_JS_FILE_PATH)  ),
        'assets/js/top_on_scroll.js',
        'assets/js/menu.js',
        'assets/js/notice.js',
        '../../assets/js/data-layer.js',
    ],
    'javascripts_for_minification' => [
        __DIR__.'/assets/'.Assets::WP_IDEA_MIN_JS_FILE_PATH=> [
            __DIR__.'/assets/js/courses.js'
        ]
    ],
	'features'    => [
		Templates::FEATURE_COURSE_WELCOME_VIDEO,
		Templates::FEATURE_COURSE_SECOND_SECTION,
		Templates::FEATURE_LESSON_NAVIGATION_POSITION,
		Templates::FEATURE_LESSON_PROGRESS_POSITION,
		Templates::FEATURE_LESSON_FILES_POSITION,
		Templates::FEATURE_LESSON_SUBTITLE,
		Templates::FEATURE_LESSON_SHORT_DESCRIPTION,
	],
	'settings'    =>
		[
			[
				'name'    => 'color_preset',
				'label'   => __( 'Color preset', BPMJ_EDDCM_DOMAIN ),
				'type'    => 'select2',
				'style'   => 'width: 160px;',
				'default' => 'default',
				'class'   => 'color-preset',
				'options' => include __DIR__ . '/color-presets.php',
            ],
			[
				'name'    => 'main_font',
				'label'   => __( 'Main font', BPMJ_EDDCM_DOMAIN ),
				'type'    => 'select2',
				'style'   => 'width: 260px;',
				'class'   => 'font',
				'options' => [
					$main_font_id => $main_font_family,
                ],
            ],
			[
				'name'    => 'secondary_font',
				'label'   => __( 'Secondary font', BPMJ_EDDCM_DOMAIN ),
				'type'    => 'select2',
				'style'   => 'width: 260px;',
				'class'   => 'font',
				'options' => [
					$secondary_font_id => $secondary_font_family,
                ],
            ],
			[
				'name'              => 'bg_file',
				'label'             => __( 'Background image', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'file',
				'sanitize_callback' => $sanitize_image,
            ],
			[
				'name'              => 'section_bg_file',
				'label'             => __( 'Section background image', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'file',
				'sanitize_callback' => $sanitize_image,
            ],
			[
				'name'              => 'login_bg_file',
				'label'             => __( 'Login page background image', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'file',
				'sanitize_callback' => $sanitize_image,
            ],
			/* BACKGROUND COLORS */
			[
				'name'              => 'bg_color',
				'label'             => __( 'Background color', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#ffffff',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'alternative_bg_color',
				'label'             => __( 'Contrast background color', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#f1f1f8',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'box_bg_color',
				'label'             => __( 'Box section', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#f1f1f8',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'header_bg_color',
				'label'             => __( 'Header section', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#f1f1f8',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'order_form_bg_color',
				'label'             => __( 'Checkout section', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#ffffff',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'section_bg_color',
				'label'             => __( 'Content section (e.g. comments)', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#f1f1f8',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'button_bg_color',
				'label'             => __( 'Buttons', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#445878',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'input_bg_color',
				'label'             => __( 'Inputs', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#f1f1f8',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'form_button_bg_color',
				'label'             => __( 'Form buttons', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#e0e4ec',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'thumb_bg_color',
				'label'             => __( 'Thumbnail', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#e0e4ec',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'footer_bg_color',
				'label'             => __( 'Footer', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#344868',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'footer_alt_bg_color',
				'label'             => __( 'Footer alternative', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#344868',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'footer_alt_text_color',
				'label'             => __( 'Footer alternative', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#ffffff',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'primary_color',
				'label'             => __( 'Primary color', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#92cdcf',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'links_color',
				'label'             => __( 'Links', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#81c0c2',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'links_color_alt',
				'label'             => __( 'Alternative links', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#81c0c2',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'footer_links_color',
				'label'             => __( 'Footer links', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#81c0c2',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'footer_links_hover_color',
				'label'             => __( 'Footer links hover', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#81c0c2',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'text_color',
				'label'             => __( 'Text color', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#1c1d21',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'box_text_color',
				'label'             => __( 'Box text', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#1c1d21',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'order_form_text_color',
				'label'             => __( 'Checkout text', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#1c1d21',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'input_text_color',
				'label'             => __( 'Input text', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#1c1d21',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'section_text_color',
				'label'             => __( 'Content section', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#1c1d21',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'highlight_color',
				'label'             => __( 'Highlight', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#ffffff',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'placeholder_text_color',
				'label'             => __( 'Input placeholder text', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#bdc3ce',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'order_form_button_text_color',
				'label'             => __( 'Order form button text', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#1c1d21',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'border_color',
				'label'             => __( 'Border color', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#e0e4ec',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'menu_icon_color',
				'label'             => __( 'Menu icon', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#e0e4ec',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'box_border_color',
				'label'             => __( 'Box', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#e0e4ec',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'button_border_color',
				'label'             => __( 'Buttons', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#344868',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'form_button_border_color',
				'label'             => __( 'Form buttons', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#344868',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'order_form_button_border_color',
				'label'             => __( 'Order/contact form buttons', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#ced4e0',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'thumb_border_color',
				'label'             => __( 'Thumbnail', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#ced4e0',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'input_border_color',
				'label'             => __( 'Form input', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#e0e4ec',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'contrast_text_color',
				'label'             => __( 'Contrast text', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#ffffff',
				'sanitize_callback' => $sanitize_color,
            ],
			[
				'name'              => 'footer_text_color',
				'label'             => __( 'Footer text', BPMJ_EDDCM_DOMAIN ),
				'type'              => 'color',
				'default'           => '#ffffff',
				'sanitize_callback' => $sanitize_color,
            ],
            [
                'name'              => 'css',
                'label'             => __( 'Custom CSS', BPMJ_EDDCM_DOMAIN ),
                'type'              => 'textarea',
            ],
        ],
];
