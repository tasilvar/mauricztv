<?php
/*
 * Plugin Name: EDD iPresso
 * Plugin URI: http://upsell.pl/sklep/edd-ipresso/
 * Description: Integracja EDD z iPresso
 * Version: 1.0.1
 * Author: upSell.pl & Better Profits
 * Author URI: http://upsell.pl
 */
/**
 * @package WP EDD iPresso
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

defined( 'BPMJ_EDDIP_DOMAIN' ) OR define( 'BPMJ_EDDIP_DOMAIN', 'wp-eddip' );
defined( 'BPMJ_EDDIP_DIR' ) OR define( 'BPMJ_EDDIP_DIR', dirname( __FILE__ ) );
defined( 'BPMJ_EDDIP_URL' ) OR define( 'BPMJ_EDDIP_URL', plugin_dir_url( __FILE__ ) );
defined( 'BPMJ_EDDIP_FILE' ) OR define( 'BPMJ_EDDIP_FILE', __FILE__ );
defined( 'BPMJ_EDDIP_NAME' ) OR define( 'BPMJ_EDDIP_NAME', 'EDD iPresso' );
defined( 'BPMJ_EDDIP_VERSION' ) OR define( 'BPMJ_EDDIP_VERSION', '1.0.1' );
defined( 'BPMJ_UPSELL_STORE_URL' ) OR define( 'BPMJ_UPSELL_STORE_URL', 'http://upsell.pl' );

if ( version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
	/*
	 * If PHP version is lower than 5.3.0 then we need to stop here as we
	 * cannot use namespaces
	 */
	$lang_dir = basename( BPMJ_EDDIP_DIR ) . '/languages';
	load_plugin_textdomain( BPMJ_EDDIP_DOMAIN, false, $lang_dir );

	function bpmj_eddip_version_notice() {
		?>
        <div class="error">
            <p>
				<?php
				printf( __( 'WP EDD iPresso: You need PHP version at least 5.3 to run this plugin. You are currently using PHP version %s.', BPMJ_EDDIP_DOMAIN ), PHP_VERSION );
				?>
            </p>
        </div>
		<?php
	}

	add_action( 'admin_notices', 'bpmj_eddip_version_notice' );
} else {
	/*
	 * We include another file, which is exclusively PHP 5.3+, so we can
	 * keep this (current) script simple to prevent provoking fatal syntax
	 * errors
	 */
	include_once BPMJ_EDDIP_DIR . '/includes/bootstrap.php';
}