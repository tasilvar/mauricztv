<?php

use bpmj\wpidea\templates_system\admin\modules\settings_handlers\New_Templates_System_Settings_Handler;
use bpmj\wpidea\templates_system\templates\scarlet\Search_Page_Template;
use bpmj\wpidea\modules\app_view\api\App_View_API_Static_Helper;

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php bloginfo('description'); ?>">
	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php endif; ?>
	<?php wp_head(); ?>
</head>

<body <?php body_class( array( WPI()->templates->get_body_class() ) ); ?>>
<?php do_action( 'bpmj_eddc_after_body_open_tag' ); ?>

<?= App_View_API_Static_Helper::render_top_bar_navigation() ?>

<div id="page">
    <div id="header">
        <div class="contenter">
            <div class="row">
                <div class="col-sm-4" id="logo-cell">
					<?php echo WPI()->templates->get_logo(); ?>
                </div>
                <div class="col-sm-8">
                    <div id="menu_mobile"><i class="fas fa-bars"></i></div>
                    <div class="menu_glowne">
						<?php echo WPI()->templates->get_main_menu( array(
							'id' => 'menu',
						) ); ?>
                    </div>
                    <?php if ( $this->override_all && $this->templates_settings_handler instanceof New_Templates_System_Settings_Handler ): ?>
                        <div class="search navbar-search">
                            <a href="<?= get_home_url() . '?' . Search_Page_Template::SEARCH_PHRASE_QUERY_PARAM_NAME . '=' . Search_Page_Template::SEARCH_PHRASE_PLACEHOLDER ?>"> <i class="icon-search"></i></a>
                        </div>
                    <?php endif; ?>
                    <div class="koszyk">
                        <a href="<?php echo edd_get_checkout_uri(); ?>"><i class="icon-cart"></i></a>
                        <span class="dymek edd-cart-quantity"><?php echo edd_get_cart_quantity(); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
