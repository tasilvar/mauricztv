<?php
/*
 * Plugin Name: EDD GetResponse
 * Plugin URI: http://upsell.pl/sklep/edd-getresponse/
 * Description: Integracja EDD z GetResponse
 * Version: 2.0.0
 * Author: upSell.pl & Better Profits
 * Author URI: http://upsell.pl
 */
/**
 * @package WP EDD GetResponse
 */
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

defined( 'BPMJ_EDDRES_DOMAIN' ) OR define( 'BPMJ_EDDRES_DOMAIN', 'wp-eddfm' );
defined( 'BPMJ_EDDRES_DIR' ) OR define( 'BPMJ_EDDRES_DIR', dirname( __FILE__ ) );
defined( 'BPMJ_EDDRES_FILE' ) OR define( 'BPMJ_EDDRES_FILE', __FILE__ );
defined( 'BPMJ_EDDRES_NAME' ) OR define( 'BPMJ_EDDRES_NAME', 'EDD GetResponse' );
defined( 'BPMJ_EDDRES_VERSION' ) OR define( 'BPMJ_EDDRES_VERSION', '2.0.1' );
defined( 'BPMJ_UPSELL_STORE_URL' ) OR define( 'BPMJ_UPSELL_STORE_URL', 'http://upsell.pl' );

if ( version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
	/*
	 * If PHP version is lower than 5.3.0 then we need to stop here as we
	 * cannot use namespaces
	 */
	$lang_dir = basename( BPMJ_EDDRES_DIR ) . '/languages';
	load_plugin_textdomain( BPMJ_EDDRES_DOMAIN, false, $lang_dir );

	function bpmj_eddres_version_notice() {
		?>
		<div class="error">
			<p>
				<?php
				printf( __( 'WP EDD GetResponse: You need PHP version at least 5.3 to run this plugin. You are currently using PHP version %s.', BPMJ_EDDRES_DOMAIN ), PHP_VERSION );
				?>
			</p>
		</div>
		<?php
	}

	add_action( 'admin_notices', 'bpmj_eddres_version_notice' );
} else {
	/*
	 * We include another file, which is exclusively PHP 5.3+, so we can
	 * keep this (current) script simple to prevent provoking fatal syntax
	 * errors
	 */
	include_once BPMJ_EDDRES_DIR . '/includes/bootstrap.php';
}