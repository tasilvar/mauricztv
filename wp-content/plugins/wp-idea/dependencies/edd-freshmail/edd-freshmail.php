<?php
/*
 * Plugin Name: EDD FreshMail
 * Plugin URI: http://upsell.pl/sklep/edd-freshmail/
 * Description: Integracja EDD z FreshMail
 * Version: 2.0.1
 * Author: upSell.pl & Better Profits
 * Author URI: http://upsell.pl
 */
/**
 * @package WP EDD Freshmail
 */
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

defined( 'BPMJ_EDDFM_DOMAIN' ) OR define( 'BPMJ_EDDFM_DOMAIN', 'wp-eddfm' );
defined( 'BPMJ_EDDFM_DIR' ) OR define( 'BPMJ_EDDFM_DIR', dirname( __FILE__ ) );
defined( 'BPMJ_EDDFM_FILE' ) OR define( 'BPMJ_EDDFM_FILE', __FILE__ );
defined( 'BPMJ_EDDFM_NAME' ) OR define( 'BPMJ_EDDFM_NAME', 'EDD FreshMail' );
defined( 'BPMJ_EDDFM_VERSION' ) OR define( 'BPMJ_EDDFM_VERSION', '2.0.1' );
defined( 'BPMJ_UPSELL_STORE_URL' ) OR define( 'BPMJ_UPSELL_STORE_URL', 'http://upsell.pl' );

if ( version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
	/*
	 * If PHP version is lower than 5.3.0 then we need to stop here as we
	 * cannot use namespaces
	 */
	$lang_dir = basename( BPMJ_EDDFM_DIR ) . '/languages';
	load_plugin_textdomain( BPMJ_EDDFM_DOMAIN, false, $lang_dir );

	function bpmj_eddfm_version_notice() {
		?>
        <div class="error">
            <p>
				<?php
				printf( __( 'WP EDD Freshmail: You need PHP version at least 5.3 to run this plugin. You are currently using PHP version %s.', BPMJ_EDDFM_DOMAIN ), PHP_VERSION );
				?>
            </p>
        </div>
		<?php
	}

	add_action( 'admin_notices', 'bpmj_eddfm_version_notice' );
} else {
	/*
	 * We include another file, which is exclusively PHP 5.3+, so we can
	 * keep this (current) script simple to prevent provoking fatal syntax
	 * errors
	 */
	include_once BPMJ_EDDFM_DIR . '/includes/bootstrap.php';
}