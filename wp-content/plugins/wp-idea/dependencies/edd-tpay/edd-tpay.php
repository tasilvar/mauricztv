<?php

/*
  Plugin Name: Easy Digital Downloads - Bramka Tpay.com
  Plugin URI: http://upsell.pl/sklep/edd-bramka-platnosci-tpay-com/
  Description: Bramka płatności Tpay.com do Easy Digital Downloads
  Version: 2.0.7
  Author: upSell.pl & Better Profits
  Author URI: http://upsell.pl
 */

if ( ! defined( 'BPMJ_UPSELL_STORE_URL' ) ) {
	define( 'BPMJ_UPSELL_STORE_URL', 'https://upsell.pl' );
}
define( 'BPMJ_TRA_EDD_NAME', 'EDD Bramka płatności tpay.com' );
define( 'BPMJ_TRA_EDD_ID', 219 ); // currently not used

// Definiowanie stałych
define( 'BPMJ_TRA_EDD_FILE', __FILE__ );
define( 'BPMJ_TRA_EDD_DIR', dirname( __FILE__ ) ); // Główny katalog wtyczki
define( 'BPMJ_TRA_EDD_URL', plugins_url( '/', __FILE__ ) ); // URL do katalogu głównego
define( 'BPMJ_TRA_EDD_INC', plugins_url( 'includes', __FILE__ ) ); // URL do katalogu incudes
define( 'BPMJ_TRA_EDD_GATEWAY_ID', 'tpay_gateway' );

// Licencja / Autoaklualizacja
if ( ! defined( 'BPMJ_TRA_EDD_VERSION' ) ) {
	define( 'BPMJ_TRA_EDD_VERSION', '2.0.7' );
}


if ( !class_exists( 'Tpay' ) ) {
    include_once('includes/class_tpay.php');
}

if ( version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
    /*
     * If PHP version is lower than 5.3.0 then we stop here
     */
    $lang_dir = basename(BPMJ_TRA_EDD_DIR) . '/languages';
    load_plugin_textdomain('edd-tpay', false, $lang_dir);

    function bpmjd_tra_version_notice()
    {
        ?>
        <div class="error">
            <p>
                <?php
                printf(__('EDD Tpay: You need PHP version at least 5.3 to run this plugin. You are currently using PHP version %s.', 'edd-tpay'), PHP_VERSION);
                ?>
            </p>
        </div>
        <?php
    }

    add_action('admin_notices', 'bpmjd_tra_version_notice');
} else {
    include_once BPMJ_TRA_EDD_DIR . '/includes/plugin.php';
}
