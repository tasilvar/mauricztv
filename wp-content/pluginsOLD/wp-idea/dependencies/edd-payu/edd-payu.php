<?php
/*
 * Plugin Name: EDD PayU
 * Plugin URI: http://upsell.pl/sklep/edd-payu/
 * Description: Integracja EDD z PayU
 * Version: 2.0.8.1
 * Author: upSell.pl & Better Profits
 * Author URI: http://upsell.pl
 */
/**
 * @package WP EDD PayU
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

defined( 'BPMJ_EDDPAYU_DOMAIN' ) OR define( 'BPMJ_EDDPAYU_DOMAIN', 'edd-payu-rest' );
defined( 'BPMJ_EDDPAYU_DIR' ) OR define( 'BPMJ_EDDPAYU_DIR', dirname( __FILE__ ) );
defined( 'BPMJ_EDDPAYU_URL' ) OR define( 'BPMJ_EDDPAYU_URL', plugin_dir_url( __FILE__ ) );
defined( 'BPMJ_EDDPAYU_FILE' ) OR define( 'BPMJ_EDDPAYU_FILE', __FILE__ );
defined( 'BPMJ_EDDPAYU_NAME' ) OR define( 'BPMJ_EDDPAYU_NAME', 'EDD Bramka płatności PayU' );
defined( 'BPMJ_EDDPAYU_ID' ) OR define( 'BPMJ_EDDPAYU_ID', 229 ); // currently not used
defined( 'BPMJ_EDDPAYU_VERSION' ) OR define( 'BPMJ_EDDPAYU_VERSION', '2.0.8.1' );
defined( 'BPMJ_UPSELL_STORE_URL' ) OR define( 'BPMJ_UPSELL_STORE_URL', 'https://upsell.pl' );
defined( 'BPMJ_UPSELL_LABEL' ) OR define( 'BPMJ_UPSELL_LABEL', 'upSell & Better Profits' );

if ( !class_exists( 'Payu' ) ) {
    include_once('includes/class_payu.php');
}

if ( version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
	/*
	 * If PHP version is lower than 5.3.0 then we need to stop here as we
	 * cannot use namespaces
	 */
	$lang_dir = basename( BPMJ_EDDPAYU_DIR ) . '/languages';
	load_plugin_textdomain( BPMJ_EDDPAYU_DOMAIN, false, $lang_dir );

	function bpmj_eddpayu_version_notice() {
		?>
        <div class="error">
            <p>
				<?php
				printf( __( 'WP EDD PayU: You need PHP version at least 5.3 to run this plugin. You are currently using PHP version %s.', BPMJ_EDDPAYU_DOMAIN ), PHP_VERSION );
				?>
            </p>
        </div>
		<?php
	}

	add_action( 'admin_notices', 'bpmj_eddpayu_version_notice' );
} else {
	/*
	 * We include another file, which is exclusively PHP 5.3+, so we can
	 * keep this (current) script simple to prevent provoking fatal syntax
	 * errors
	 */
	include_once BPMJ_EDDPAYU_DIR . '/includes/bootstrap.php';
}
