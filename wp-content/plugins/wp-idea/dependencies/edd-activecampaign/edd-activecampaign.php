<?php
/*
 * Plugin Name: EDD ActiveCampaign
 * Plugin URI: http://upsell.pl/sklep/edd-activecampaign/
 * Description: Integracja EDD z ActiveCampaign
 * Version: 2.0.0
 * Author: upSell.pl & Better Profits
 * Author URI: http://upsell.pl
 */
/**
 * @package WP EDD ActiveCampaign
 */
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

defined( 'BPMJ_EDDACT_DOMAIN' ) OR define( 'BPMJ_EDDACT_DOMAIN', 'wp-eddact' );
defined( 'BPMJ_EDDACT_DIR' ) OR define( 'BPMJ_EDDACT_DIR', dirname( __FILE__ ) );
defined( 'BPMJ_EDDACT_URL' ) OR define( 'BPMJ_EDDACT_URL', plugin_dir_url( __FILE__ ) );
defined( 'BPMJ_EDDACT_FILE' ) OR define( 'BPMJ_EDDACT_FILE', __FILE__ );
defined( 'BPMJ_EDDACT_NAME' ) OR define( 'BPMJ_EDDACT_NAME', 'EDD ActiveCampaign' );
defined( 'BPMJ_EDDACT_VERSION' ) OR define( 'BPMJ_EDDACT_VERSION', '2.0.1' );
defined( 'BPMJ_UPSELL_STORE_URL' ) OR define( 'BPMJ_UPSELL_STORE_URL', 'http://upsell.pl' );

if ( version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
    /*
     * If PHP version is lower than 5.3.0 then we need to stop here as we
     * cannot use namespaces
     */
    $lang_dir = basename( BPMJ_EDDACT_DIR ) . '/languages';
    load_plugin_textdomain( BPMJ_EDDACT_DOMAIN, false, $lang_dir );

    function bpmj_eddact_version_notice() {
        ?>
        <div class="error">
            <p>
                <?php
                printf( __( 'WP EDD ActiveCampaign: You need PHP version at least 5.3 to run this plugin. You are currently using PHP version %s.', BPMJ_EDDACT_DOMAIN ), PHP_VERSION );
                ?>
            </p>
        </div>
        <?php
    }

    add_action( 'admin_notices', 'bpmj_eddact_version_notice' );
} else {
    /*
     * We include another file, which is exclusively PHP 5.3+, so we can
     * keep this (current) script simple to prevent provoking fatal syntax
     * errors
     */
    include_once BPMJ_EDDACT_DIR . '/includes/bootstrap.php';
}