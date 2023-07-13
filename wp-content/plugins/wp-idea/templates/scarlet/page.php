<?php

use bpmj\wpidea\templates_system\templates\repository\Repository as TemplatesRepository;
use bpmj\wpidea\View_Hooks;

if(WPI()->page->has_template()):
    WPI()->page->render_template();

    return;
endif;

if( is_singular( TemplatesRepository::TEMPLATES_POST_TYPE ) ){
	include( 'template_parts/page-wpi_page_templates.php' );

	return;
}

if ( is_tax( 'download_category' ) || is_tax( 'download_tag' ) ) {
	WPI()->templates->header();

	include( 'template_parts/archive-download_category.php' );

	return;
}

if(edd_get_download()){

    add_action( 'wp_head', function (){
        View_Hooks::run(View_Hooks::RENDER_HEAD_ELEMENTS_IN_PRODUCT_PAGE, get_the_ID());
    }, 900 );

	include_once('template_parts/page-course.php');

	return;
}

if( is_single() ){
	include_once('single.php');

	return;
}

if( is_home() ){
	include_once('blog.php');

	return;
}

$page_template = get_post_meta( get_the_ID(), '_wp_page_template', true );
$constants       = array(
    '\Elementor\Modules\PageTemplates\Module::TEMPLATE_CANVAS',
    '\Elementor\Core\Settings\Page\Manager::TEMPLATE_CANVAS',
);
$template_canvas = '';
foreach ( $constants as $constant ) {
    if ( defined( $constant ) ) {
        $template_canvas = constant( $constant );
        break;
    }
}

if ( $template_canvas && $template_canvas === $page_template ) {
    include_once('template_parts/page-default_empty.php');
} else {
    $wpidea_settings = get_option( 'wp_idea', array() );
    if ($wpidea_settings['certificates_page'] == get_the_ID()) {
        include_once('template_parts/page-my-certificates.php');

        return;
    }

    include_once('template_parts/page-default.php');
}

?>

