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

$sanitize_image = function ($file_url) {
	return Field_Sanitizer::sanitize_image_field( $file_url, __DIR__ );
};

global $bpmj_eddcm_template_default_get_fonts;
$bpmj_eddcm_template_default_get_fonts = function () {
	return Fonts_Helper::get_google_fonts_from_remote();
};

global $bpmj_eddcm_colors_settings;


$general_settings_colors_header = sprintf($this->translator->translate('color_settings.scarlet.header.general_settings'),'<p>','</p>');

$general_settings_colors =[
    array(
        'name'              => 'bg_color',
        'label'             => $general_settings_colors_header.$this->translator->translate('color_settings.scarlet.bg_color'),
        'type'              => 'color',
        'default'           => '#fbfbfd',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'generic_white_color',
        'label'             => sprintf($this->translator->translate('color_settings.scarlet.generic_white_color'),'<br>'),
        'type'              => 'color',
        'default'           => '#ffffff',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'default_img_bg_color',
        'label'             => sprintf($this->translator->translate('color_settings.scarlet.default_img_bg_color'),'<br>'),
        'type'              => 'color',
        'default'           => '#272434',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'content_header_color',
        'label'             => $this->translator->translate('color_settings.scarlet.content_header_color'),
        'type'              => 'color',
        'default'           => '#5d5e79',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'main_color',
        'label'             => sprintf($this->translator->translate('color_settings.scarlet.main_color'),'<br>'),
        'type'              => 'color',
        'default'           => '#f67d77',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'main_color_hover',
        'label'             => $this->translator->translate('color_settings.scarlet.main_color_hover'),
        'type'              => 'color',
        'default'           => '#e56b65',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'inactive_border_color',
        'label'             => $this->translator->translate('color_settings.scarlet.inactive_border_color'),
        'type'              => 'color',
        'default'           => '#cecece',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'breadcrumbs_color',
        'label'             => $this->translator->translate('color_settings.scarlet.breadcrumbs_color'),
        'type'              => 'color',
        'default'           => '#bcbdc9',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'tab_alt_text_color',
        'label'             => sprintf($this->translator->translate('color_settings.scarlet.tab_alt_text_color'),'<br>'),
        'type'              => 'color',
        'default'           => '#9badbe',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'price_bg_color',
        'label'             => $this->translator->translate('color_settings.scarlet.price_bg_color'),
        'type'              => 'color',
        'default'           => '#77b7bf',
        'sanitize_callback' => $sanitize_color,
    )
];

$the_menu_bar_colors_header = sprintf($this->translator->translate('color_settings.scarlet.header.the_menu_bar'),'<p>','</p>');

$the_menu_bar_colors = [
    array(
        'name'              => 'content_header_bg_color',
        'label'             => $the_menu_bar_colors_header.$this->translator->translate('color_settings.scarlet.content_header_bg_color'),
        'type'              => 'color',
        'default'           => '#151726',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'link_color',
        'label'             => $this->translator->translate('color_settings.scarlet.link_color'),
        'type'              => 'color',
        'default'           => '#fbfbfb',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'menu_link_bg_color',
        'label'             => $this->translator->translate('color_settings.scarlet.menu_link_bg_color'),
        'type'              => 'color',
        'default'           => '#f0f1f3',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'menu_bg_color',
        'label'             => $this->translator->translate('color_settings.scarlet.menu_bg_color'),
        'type'              => 'color',
        'default'           => '#1d1e2f',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'menu_link_hover_color',
        'label'             => $this->translator->translate('color_settings.scarlet.menu_link_hover_color'),
        'type'              => 'color',
        'default'           => '#e7e7e7',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'menu_link_border_color',
        'label'             => $this->translator->translate('color_settings.scarlet.menu_link_border_color'),
        'type'              => 'color',
        'default'           => '#d5d8dd',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'menu_border_color',
        'label'             => $this->translator->translate('color_settings.scarlet.menu_border_color'),
        'type'              => 'color',
        'default'           => '#2e2f3f',
        'sanitize_callback' => $sanitize_color,
    )
];

$footer_colors_header = sprintf($this->translator->translate('color_settings.scarlet.header.footer'),'<p>','</p>');

$footer_colors = [
    array(
        'name'              => 'footer_bg_color',
        'label'             => $footer_colors_header.$this->translator->translate('color_settings.scarlet.footer_bg_color'),
        'type'              => 'color',
        'default'           => '#1a1a29',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'footer_text_color',
        'label'             => $this->translator->translate('color_settings.scarlet.footer_text_color'),
        'type'              => 'color',
        'default'           => '#ffffff',
        'sanitize_callback' => $sanitize_color,
    )
];

$forms_order_page_colors_header = sprintf($this->translator->translate('color_settings.scarlet.header.forms_order_page'),'<p>','</p>');

$forms_order_page_colors = [
    array(
        'name'              => 'main_text_color',
        'label'             => $forms_order_page_colors_header.$this->translator->translate('color_settings.scarlet.main_text_color'),
        'type'              => 'color',
        'default'           => '#6c7f90',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'main_border_color',
        'label'             => $this->translator->translate('color_settings.scarlet.main_border_color'),
        'type'              => 'color',
        'default'           => '#d0e4e4',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'placeholder_color',
        'label'             => $this->translator->translate('color_settings.scarlet.placeholder_color'),
        'type'              => 'color',
        'default'           => '#bcbdc9',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'form_text_color',
        'label'             => $this->translator->translate('color_settings.scarlet.form_text_color'),
        'type'              => 'color',
        'default'           => '#bcbdc9',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'cart_border_color',
        'label'             => $this->translator->translate('color_settings.scarlet.cart_border_color'),
        'type'              => 'color',
        'default'           => '#d8dde5',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'cart_promotion_price_color',
        'label'             => $this->translator->translate('color_settings.scarlet.cart_promotion_price_color'),
        'type'              => 'color',
        'default'           => '#babac3',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'cart_summary_price_color',
        'label'             => $this->translator->translate('color_settings.scarlet.cart_summary_price_color'),
        'type'              => 'color',
        'default'           => '#91cbbf',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'discount_code_color',
        'label'             => $this->translator->translate('color_settings.scarlet.discount_code_color'),
        'type'              => 'color',
        'default'           => '#91cbbf',
        'sanitize_callback' => $sanitize_color,
    )
];

$login_page_colors_header = sprintf($this->translator->translate('color_settings.scarlet.header.login_page'),'<p>','</p>');

$login_page_colors = [
    array(
        'name'              => 'login_input_placeholder',
        'label'             => $login_page_colors_header.$this->translator->translate('color_settings.scarlet.login_input_placeholder'),
        'type'              => 'color',
        'default'           => '#d7d7de',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'login_label_color',
        'label'             => $this->translator->translate('color_settings.scarlet.login_label_color'),
        'type'              => 'color',
        'default'           => '#99a5b0',
        'sanitize_callback' => $sanitize_color,
    )
];

$list_of_products_colors_header = sprintf($this->translator->translate('color_settings.scarlet.header.list_of_products'),'<p>','</p>');

$list_of_products_colors = [
    array(
        'name'              => 'main_box_border_color',
        'label'             => $list_of_products_colors_header.$this->translator->translate('color_settings.scarlet.main_box_border_color'),
        'type'              => 'color',
        'default'           => '#eaf0f3',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'default_img_color',
        'label'             => $this->translator->translate('color_settings.scarlet.default_img_color'),
        'type'              => 'color',
        'default'           => '#474252',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'price_available_color',
        'label'             => $this->translator->translate('color_settings.scarlet.price_available_color'),
        'type'              => 'color',
        'default'           => '#90cbbf',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'promotion_price_color',
        'label'             => $this->translator->translate('color_settings.scarlet.promotion_price_color'),
        'type'              => 'color',
        'default'           => '#d3d4e1',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'category_link_color',
        'label'             => $this->translator->translate('color_settings.scarlet.category_link_color'),
        'type'              => 'color',
        'default'           => '#77b7bf',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'display_mode_color',
        'label'             => $this->translator->translate('color_settings.scarlet.display_mode_color'),
        'type'              => 'color',
        'default'           => '#5f6368',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'display_mode_icon_color',
        'label'             => $this->translator->translate('color_settings.scarlet.display_mode_icon_color'),
        'type'              => 'color',
        'default'           => '#e2e1e1',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'display_mode_active_icon_color',
        'label'             => $this->translator->translate('color_settings.scarlet.display_mode_active_icon_color'),
        'type'              => 'color',
        'default'           => '#f9988e',
        'sanitize_callback' => $sanitize_color,
    )
];

$course_pages_colors_header = sprintf($this->translator->translate('color_settings.scarlet.header.course_pages'),'<p>','</p>');

$course_pages_colors = [
    array(
        'name'              => 'stage_text_color',
        'label'             => $course_pages_colors_header.$this->translator->translate('color_settings.scarlet.stage_text_color'),
        'type'              => 'color',
        'default'           => '#ed7c77',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'lesson_completed_link_color',
        'label'             => $this->translator->translate('color_settings.scarlet.lesson_completed_link_color'),
        'type'              => 'color',
        'default'           => '#b6b6c3',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'stage_bg_color',
        'label'             => $this->translator->translate('color_settings.scarlet.stage_bg_color'),
        'type'              => 'color',
        'default'           => '#f5f5f8',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'box_border_color',
        'label'             => $this->translator->translate('color_settings.scarlet.box_border_color'),
        'type'              => 'color',
        'default'           => '#eaeff3',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'stage_text_border_color',
        'label'             => $this->translator->translate('color_settings.scarlet.stage_text_border_color'),
        'type'              => 'color',
        'default'           => '#eaeff4',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'course_stage_line_color',
        'label'             => $this->translator->translate('color_settings.scarlet.course_stage_line_color'),
        'type'              => 'color',
        'default'           => '#d9e2ea',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'lesson_text_color',
        'label'             => $this->translator->translate('color_settings.scarlet.lesson_text_color'),
        'type'              => 'color',
        'default'           => '#6c7f91',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'completed_lesson_input_border',
        'label'             => $this->translator->translate('color_settings.scarlet.completed_lesson_input_border'),
        'type'              => 'color',
        'default'           => '#d8d8dd',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'lesson_top_bg_color',
        'label'             => $this->translator->translate('color_settings.scarlet.lesson_top_bg_color'),
        'type'              => 'color',
        'default'           => '#e8e8ee',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'lesson_top_text_color',
        'label'             => $this->translator->translate('color_settings.scarlet.lesson_top_text_color'),
        'type'              => 'color',
        'default'           => '#5d5e79',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'lesson_icon_color',
        'label'             => $this->translator->translate('color_settings.scarlet.lesson_icon_color'),
        'type'              => 'color',
        'default'           => '#f1a2a0',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'quiz_summary_img_bg_color',
        'label'             => $this->translator->translate('color_settings.scarlet.quiz_summary_img_bg_color'),
        'type'              => 'color',
        'default'           => '#fefefe',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'quiz_summary_img_frame_color',
        'label'             => $this->translator->translate('color_settings.scarlet.quiz_summary_img_frame_color'),
        'type'              => 'color',
        'default'           => '#ebf0f4',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'quiz_summary_img_border_color',
        'label'             => $this->translator->translate('color_settings.scarlet.quiz_summary_img_border_color'),
        'type'              => 'color',
        'default'           => '#f5f8f9',
        'sanitize_callback' => $sanitize_color,
    )
];

$comments_colors_header = sprintf($this->translator->translate('color_settings.scarlet.header.comments'),'<p>','</p>');

$comments_colors = [
    array(
        'name'              => 'comments_third_color',
        'label'             => $comments_colors_header.$this->translator->translate('color_settings.scarlet.comments_third_color'),
        'type'              => 'color',
        'default'           => '#97a3af',
        'sanitize_callback' => $sanitize_color,
    )
];

/**
 * Kolory których zastosowanie nie zostało znalezione w szablonie

$unused_colors = [
    array(
        'name'              => 'alt_text_color',
        'label'             => __( 'Alt text color', BPMJ_EDDCM_DOMAIN ),
        'type'              => 'color',
        'default'           => '#5d5e79',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'user_profile_color',
        'label'             => __( 'User profile color', BPMJ_EDDCM_DOMAIN ),
        'type'              => 'color',
        'default'           => '#2d3942',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'menu_active_link_color',
        'label'             => __( 'Menu active link color', BPMJ_EDDCM_DOMAIN ),
        'type'              => 'color',
        'default'           => '#0a0b15',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'wrong_answer_color',
        'label'             => __( 'Wrong answer color', BPMJ_EDDCM_DOMAIN ),
        'type'              => 'color',
        'default'           => '#91cbbf',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'menu_bg_color_inverted',
        'label'             => __( 'Menu background color inverted', BPMJ_EDDCM_DOMAIN ),
        'type'              => 'hidden',
        'default'           => '#bbb890',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'tab_border_color',
        'label'             => __( 'Tab border color', BPMJ_EDDCM_DOMAIN ),
        'type'              => 'color',
        'default'           => '#eaeaf5',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'inactive_bg_color',
        'label'             => __( 'Inactive background color', BPMJ_EDDCM_DOMAIN ),
        'type'              => 'color',
        'default'           => '#dfdfdf',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'price_border_color',
        'label'             => __( 'Price border color', BPMJ_EDDCM_DOMAIN ),
        'type'              => 'color',
        'default'           => '#368791',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'pagination_bg_color',
        'label'             => __( 'Pagination background color', BPMJ_EDDCM_DOMAIN ),
        'type'              => 'color',
        'default'           => '#a1a4aa',
        'sanitize_callback' => $sanitize_color,
    ),
    array(
        'name'              => 'tab_bg_color',
        'label'             => __( 'Tab background color', BPMJ_EDDCM_DOMAIN ),
        'type'              => 'color',
        'default'           => '#ffffff',
        'sanitize_callback' => $sanitize_color,
    )
];
*/

$bpmj_eddcm_colors_settings = array_merge($general_settings_colors, $the_menu_bar_colors, $footer_colors, $forms_order_page_colors, $login_page_colors, $list_of_products_colors, $course_pages_colors, $comments_colors);

$template_settings     = WPI()->settings->get_layout_template_settings_array( $template );
$main_font_id          = isset( $template_settings[ 'main_font' ] ) ? $template_settings[ 'main_font' ] : 'open-sans';
$main_font_family      = isset( $template_settings[ 'main_font_family' ] ) ? $template_settings[ 'main_font_family' ] : 'Open Sans';
$secondary_font_id     = isset( $template_settings[ 'secondary_font' ] ) ? $template_settings[ 'secondary_font' ] : 'montserrat';
$secondary_font_family = isset( $template_settings[ 'secondary_font_family' ] ) ? $template_settings[ 'secondary_font_family' ] : 'Montserrat';

$main_settings = array(
	array(
		'name'    => 'color_preset',
		'label'   => __( 'Color preset', BPMJ_EDDCM_DOMAIN ),
		'type'    => 'select2',
		'style'   => 'width: 160px;',
		'default' => 'default',
		'class'   => 'color-preset',
		'options' => include __DIR__ . '/color-presets.php',
	),
	array(
		'name'    => 'main_font',
		'label'   => __( 'Main font', BPMJ_EDDCM_DOMAIN ),
		'type'    => 'select2',
		'style'   => 'width: 260px;',
		'class'   => 'font',
		'options' => array(
			$main_font_id => $main_font_family,
		),
	),
	array(
		'name'    => 'secondary_font',
		'label'   => __( 'Secondary font', BPMJ_EDDCM_DOMAIN ),
		'type'    => 'select2',
		'style'   => 'width: 260px;',
		'class'   => 'font',
		'options' => array(
			$secondary_font_id => $secondary_font_family,
		),
	),
	array(
		'name'              => 'login_bg_file',
		'label'             => __( 'Login page background image', BPMJ_EDDCM_DOMAIN ),
		'type'              => 'file',
		'sanitize_callback' => $sanitize_image,
	),
	array(
		'name'              => 'bg_file',
		'label'             => __( 'Background image', BPMJ_EDDCM_DOMAIN ),
		'type'              => 'file',
		'sanitize_callback' => $sanitize_image,
	),
	array(
		'name'              => 'section_bg_file',
		'label'             => __( 'Section background image', BPMJ_EDDCM_DOMAIN ),
		'type'              => 'file',
		'sanitize_callback' => $sanitize_image,
	),
	array(
		'name'              => 'disable_banners',
		'label'             => __( 'Disable banners', BPMJ_EDDCM_DOMAIN ),
		'desc'              => __( 'Check to disable banners', BPMJ_EDDCM_DOMAIN ),
		'type'              => 'checkbox',
	),
    array(
        'name'              => 'css',
        'label'             => __( 'Custom CSS', BPMJ_EDDCM_DOMAIN ),
        'type'              => 'textarea',
    ),
);

$assets = new Assets( __DIR__ );

return array(
    'styles'      => array(
        'https://use.fontawesome.com/releases/v5.9.0/css/all.css',
        'assets/'. Assets::WP_IDEA_MIN_CSS_FILE_PATH ,
        $assets->get_relative_file_path(Assets::WP_IDEA_MIN_DYNAMIC_CSS_FILE_PATH). '?v=' . filemtime( $assets->get_absolute_file_path( Assets::WP_IDEA_MIN_DYNAMIC_CSS_FILE_PATH)  )
    ),
    'styles_for_minification' => [
        $assets->get_absolute_dir() . '/' . Assets::WP_IDEA_MIN_DYNAMIC_CSS_FILE_PATH => [
            $assets->get_absolute_file_path('css/style.css'),
            $assets->get_absolute_file_path('css/colors.css'),
        ],
        __DIR__.'/assets/' . Assets::WP_IDEA_MIN_CSS_FILE_PATH => [
            __DIR__.'/../../assets/css/select2.min.css',
            __DIR__.'/assets/css/wpidea-scarlet.css',
            __DIR__.'/assets/css/bootstrap.min.css',
            __DIR__.'/assets/css/bootstrap-theme.min.css',
        ]
    ],
    'javascripts' => array(
        //'https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js', //added as dependency for all custom scripts
        'assets/'. Assets::WP_IDEA_MIN_JS_FILE_PATH. '?v=' . filemtime( $assets->get_absolute_file_path( Assets::WP_IDEA_MIN_JS_FILE_PATH)  ),
        '../../assets/js/data-layer.js',
        '../../templates/default/assets/js/notice.js',
    ),
    'javascripts_for_minification' => [
        __DIR__.'/assets/' . Assets::WP_IDEA_MIN_JS_FILE_PATH=> [
            __DIR__.'/../../assets/js/select2.min.js',
            __DIR__.'/assets/js/jquery.countdown.min.js',
            __DIR__.'/assets/js/bootstrap.min.js',
            __DIR__.'/assets/js/courses.js',
            __DIR__.'/assets/js/lesson-notes.js',
            __DIR__.'/assets/js/opinions-form.js',
        ]
    ],
    'features'    => array(
        Templates::FEATURE_COURSE_WELCOME_BANNER,
        Templates::FEATURE_LESSON_SUBTITLE,
    ),
    'settings'    => $main_settings
);

