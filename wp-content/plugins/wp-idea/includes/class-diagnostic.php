<?php

namespace bpmj\wpidea;

use bpmj\wpidea\admin\settings\Settings_API;
use bpmj\wpidea\integrations\Interface_Invoice_Service_Status_Checker;

/**
 *
 * The class responsible for system diagnostic
 *
 */
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

class Diagnostic implements Interface_Invoice_Service_Status_Checker
{

	private $system_content;

	/**
	 * Diagnostic Constructor.
	 */
	public function __construct() {

		$this->includes();
		$this->hooks();
	}

	private function includes() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}

	private function hooks() {
		add_filter( 'bpmj_eddcm_payment_gates', array( $this, 'filter_settings_gates' ) );
		add_filter( 'bpmj_eddcm_invoices_settings', array( $this, 'filter_settings_invoices' ) );
		add_filter( 'bpmj_eddcm_mailers_settings', array( $this, 'filter_settings_mailers' ) );
	}

	private function get_gates() {
		return array(
			'edd-dotpay',
			'edd-payu',
			'edd-przelewy24',
			'edd-tpay',
            'edd-coinbase',
            'edd-stripe',
            'edd-paynow',
            'edd-przelewy',
		);
	}

	private function get_mailers() {
		return array(
			'edd-mailchimp',
			'edd-mailerlite',
			'edd-getresponse',
			'edd-salesmanago',
			'edd-freshmail',
			'edd-activecampaign',
			'edd-interspire',
			'edd-ipresso',
			'edd-convertkit',
		);
	}

	private function get_invoices() {
		return array(
			'wp-fakturownia',
			'wp-ifirma',
			'wp-wfirma',
			'wp-infakt',
			'wp-taxe',
		);
	}

	public function get_gateways_for_invoices()
	{
		return [
			'manual'		     => 1,
			'paypal'		     => 1,
			'tpay_gateway'	     => 1,
			'dotpay_gateway'	 => 1,
			'przelewy24_gateway' => 1,
			'payu'		         => 1,
			'coinbase'		     => 1,
			'stripe'		     => 1,
			'paynow_gateway'     => 1,
            'przelewy_gateway'     => 1
		];
	}

	/**
	 * Get array of required plugins
	 */
	public function get_required_plugins() {
		$plugins = array(
			'easy-digital-downloads' => array(
				'name'        => 'Easy Digital Downloads',
				'enabled'     => $this->is_edd_enabled(),
				'install-url' => 'https://wordpress.org/plugins/easy-digital-downloads'
			),
			'edd-paid-content'       => array(
				'name'        => 'Easy Digital Downloads - Paid Content',
				'enabled'     => $this->is_edd_paid_content_enabled(),
				'install-url' => 'http://upsell.pl/sklep/edd-paid-content/'
			),
			'edd-auto-register'      => array(
				'name'        => 'Easy Digital Downloads - Auto Register',
				'enabled'     => $this->is_edd_auto_register_enabled(),
				'install-url' => 'https://wordpress.org/plugins/edd-auto-register'
			),
			'payment_gates'          => array(
				'edd-tpay'       => array(
					'name'        => 'Easy Digital Downloads - Bramka Tpay.com',
					'enabled'     => $this->is_payment_gate_enabled( 'tpay' ),
					'install-url' => 'http://upsell.pl/sklep/edd-bramka-platnosci-tpay-com/'
				),
				'edd-przelewy24' => array(
					'name'        => 'Easy Digital Downloads - Bramka Przelewy24.pl',
					'enabled'     => $this->is_payment_gate_enabled( 'przelewy24' ),
					'install-url' => 'http://upsell.pl/sklep/edd-bramka-platnosci-przelewy24-pl/'
				),
				'edd-payu'       => array(
					'name'        => 'Easy Digital Downloads - Bramka PayU',
					'enabled'     => $this->is_payment_gate_enabled( 'payu' ),
					'install-url' => 'http://upsell.pl/sklep/edd-bramka-platnosci-payu/'
				),
				'edd-dotpay'     => array(
					'name'        => 'Easy Digital Downloads - Bramka Dotpay',
					'enabled'     => $this->is_payment_gate_enabled( 'dotpay' ),
					'install-url' => 'http://upsell.pl/sklep/bramka-dotpay-do-easy-digital-downloads/'
				),
                'edd-coinbase'   => array(
                    'name'        => 'Easy Digital Downloads - Bramka Coinbase',
                    'enabled'     => $this->is_payment_gate_enabled( 'coinbase' ),
                    // TODO Ten url pewnie do zmiany
                    'install-url' => 'http://upsell.pl/sklep/bramka-dotpay-do-easy-digital-downloads/'
                ),
                'edd-stripe'     => array(
                    'name'        => 'Easy Digital Downloads - Bramka Dotpay',
                    'enabled'     => $this->is_payment_gate_enabled( 'dotpay' ),
                    'install-url' => 'http://upsell.pl/sklep/bramka-stripe-do-easy-digital-downloads/'
                ),
                'edd-paynow'    => array(
                    'name'       => 'Easy Digital Downloads - Bramka Paynow',
                    'enabled'     => $this->is_payment_gate_enabled( 'paynow' ),
                ),
			)
		);

		return $plugins;
	}

	/**
	 * Check if required plugins
	 * are active
	 *
	 * If all enabled returned true
	 */
	public function check_required_plugins() {

		$plugins = $this->get_required_plugins();

		$enabled	 = true;
		$payment_gate	 = false;
		foreach ( $plugins as $slug => $plugin ) {

			// All plugins except payment gates
			if ( $slug != 'payment_gates' ) {
				if ( !$plugin[ 'enabled' ] ) {
					$required[ $slug ]	 = $plugin;
					$required[ 'string' ][]	 = $plugin[ 'name' ];
					$enabled		 = false;
				}

				// Payment gates
			} else {
				foreach ( $plugin as $gate ) {
					if ( $gate[ 'enabled' ] )
						$payment_gate = true;
				}
			}
		}

		// If at least one payment gate is activated
		if ( !$payment_gate ) {
			$required[ 'string' ][] = __( 'At least one of the Payment Gates', BPMJ_EDDCM_DOMAIN );
		}

		if ( isset( $required ) )
			return $required;

		return $enabled;
	}

	/**
	 * Check if EDD Paid Content is enabled
	 *
	 * @return bool true if EDD Paid Content is enabled, false otherwise
	 */
	public function is_edd_paid_content_enabled() {
		$is_enabled = false;

		if ( is_plugin_active( 'edd-paid-content/edd-paid-content.php' ) ) {
			$is_enabled = true;
		}

		return apply_filters( 'eddcm_is_edd_paid_content_enabled', $is_enabled );
	}

	/**
	 * Check if EDD Auto Register is enabled
	 *
	 * @return bool true if EDD Auto Register is enabled, false otherwise
	 */
	public function is_edd_auto_register_enabled() {
		$is_enabled = false;

		if ( is_plugin_active( 'edd-auto-register/edd-auto-register.php' ) ) {
			$is_enabled = true;
		}

		return apply_filters( 'is_edd_auto_register_enabled', $is_enabled );
	}

	/**
	 * Check if EDD Auto Register Custom is enabled
	 *
	 * @return bool true if EDD Auto Register Custom is enabled, false otherwise
	 */
	public function is_edd_auto_register_custom_enabled() {
		$is_enabled = false;

		if ( is_plugin_active( 'edd-auto-register-custom/edd-auto-register-custom.php' ) ) {
			$is_enabled = true;
		}

		return apply_filters( 'is_edd_auto_register_custom_enabled', $is_enabled );
	}

	/**
	 * Check if payment gate is enabled
	 *
	 * @return bool true if payment gate is enabled, false otherwise
	 */
	public function is_payment_gate_enabled( $name ) {
		$is_enabled	 = false;
		$settings	 = new Settings_API();

		if ( strpos( $name, 'edd-' ) === 0 ) {
			$name = substr( $name, 4 );
		}
		if ( $name !== 'payu' && $name !== 'coinbase' && $name !== 'stripe' && $name !== 'paypal' ) {
			$name .= '_gateway';
		}

		$mode = $settings->get_option( array( 'gateways', $name ), false, 'edd_settings' );

		if ( is_numeric( $mode ) ) {
			$is_enabled = true;
		}

		if ( ! $is_enabled &&
            ( 'payu' === $name && is_numeric( $settings->get_option( array( 'gateways', 'payu_gateway'), false, 'edd_settings' ) ) )
        ) {
			// We check for existence of PayU also under older key to upgrade
			$is_enabled = true;
		}

		return apply_filters( 'is_payment_gate_enabled', $is_enabled );
	}

	/**
	 * Check if integration is enabled
	 *
	 * @return bool true if integration is enabled, false otherwise
	 */
	public function is_integration_enabled(string $name): bool
    {
		$is_enabled	 = false;
		$settings	 = new Settings_API();
		$mode		 = $settings->get_option( array( 'integrations', $name ), false, 'wp_idea' );

		if ( is_numeric( $mode ) )
			$is_enabled = true;

		return apply_filters( 'is_integration_enabled', $is_enabled );
	}

	/**
	 * Check if module is enabled
	 *
	 * @return bool true if module is enabled, false otherwise
	 */
	public function is_module_enabled( $name ) {
		$is_enabled	 = false;
		$settings	 = new Settings_API();
		$mode		 = $settings->get_option( $name, false, 'wp_idea' );

		if ( $mode == 'on' )
			$is_enabled = true;

		return apply_filters( 'is_module_enabled', $is_enabled );
	}

	/**
	 * Check if any invoice integration is enabled
	 */
	public function invoice_integration() {
		if ( $this->is_integration_enabled( 'wp-fakturownia' ) ||
			$this->is_integration_enabled( 'wp-ifirma' ) ||
			$this->is_integration_enabled( 'wp-wfirma' ) ||
			$this->is_integration_enabled( 'wp-infakt' ) ||
			$this->is_integration_enabled( 'wp-taxe' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if any mailer integration is enabled
	 */
	public function mailer_integration() {
		if ( $this->is_integration_enabled( 'edd-mailchimp' ) ||
		     $this->is_integration_enabled( 'edd-mailerlite' ) ||
		     $this->is_integration_enabled( 'edd-getresponse' ) ||
		     $this->is_integration_enabled( 'edd-salesmanago' ) ||
		     $this->is_integration_enabled( 'edd-freshmail' ) ||
		     $this->is_integration_enabled( 'edd-activecampaign' ) ||
		     $this->is_integration_enabled( 'edd-interspire' ) ||
		     $this->is_integration_enabled( 'edd-ipresso' ) ||
		     $this->is_integration_enabled( 'edd-convertkit' )
		) {
			return true;
		}

		return false;
	}

	/**
	 * Get system content
	 *
	 * @param string $key
	 *
	 * @return array
	 */
	public function get_system_content( $key = null ) {
		if ( ! isset( $this->system_content ) ) {
			$this->system_content = array( 'gates' => array(), 'mailers' => array(), 'invoices' => array() );

			$gates = $this->get_gates();
			foreach ( $gates as $gate ) {
				if ( file_exists( BPMJ_EDDCM_DIR . 'dependencies/' . $gate ) ) {
					$this->system_content[ 'gates' ][] = $gate;
				}
			}

			$mailers = $this->get_mailers();
			foreach ( $mailers as $mailer ) {
				if ( file_exists( BPMJ_EDDCM_DIR . 'dependencies/' . $mailer ) ) {
					$this->system_content[ 'mailers' ][] = $mailer;
				}
			}

			$invoices = $this->get_invoices();
			foreach ( $invoices as $invoice ) {
				if ( file_exists( BPMJ_EDDCM_DIR . 'dependencies/' . $invoice ) ) {
					$this->system_content[ 'invoices' ][] = $invoice;
				}
			}
		}

		if ( $key ) {
			return empty( $this->system_content[ $key ] ) ? array() : $this->system_content[ $key ];
		}

		return $this->system_content;
	}

	/**
	 * Filter settings / gates
	 */
	public function filter_settings_gates( $settings ) {

		$system_gates = $this->get_system_content( 'gates' );

		if ( !empty( $system_gates ) ) {
			foreach ( $system_gates as $gate ) {
				$settings[ 'payment_gate' ][ 'options' ][ $gate ][ 'status' ] = 'on';
			}
		}

		return $settings;
	}

	/**
	 * Filter settings / invoices
	 */
	public function filter_settings_invoices( $settings ) {

		$system_invoices = $this->get_system_content( 'invoices' );

		if ( !empty( $system_invoices ) ) {
			foreach ( $system_invoices as $invoice ) {
				$settings[ 'invoice_methods' ][ 'options' ][ $invoice ][ 'status' ] = 'on';
			}
		}

		return $settings;
	}

	/**
	 * Filter settings / mailers
	 */
	public function filter_settings_mailers( $settings ) {

		$system_mailers = $this->get_system_content( 'mailers' );

		if ( !empty( $system_mailers ) ) {
			foreach ( $system_mailers as $mailer ) {
				$settings[ 'mailer_methods' ][ 'options' ][ $mailer ][ 'status' ] = 'on';
			}
		}

		return $settings;
	}

	/**
	 * Check if EDD Sale Price is enabled
	 *
	 * @return bool true if EDD Sale Price is enabled, false otherwise
	 */
	public function is_edd_sale_price_enabled() {
		$is_enabled = false;

		if ( is_plugin_active( 'edd-sale-price/edd-sale-price.php' ) ) {
			$is_enabled = true;
		}

		return apply_filters( 'is_edd_sale_price_enabled', $is_enabled );
	}

	/**
	 * "Neutralizes" the integration so that it's files may be loaded without having to be enabled
	 *
	 * @param string $integration
	 */
	public function remove_integration_hooks( $integration ) {
		switch ( $integration ) {
			case 'edd-ipresso':
				$ipresso         = \bpmj\wp\eddip\Plugin::instance();
				$ipresso_handler = $ipresso->get_ipresso_handler();
				remove_action( 'add_meta_boxes', array( $ipresso_handler, 'add_metabox' ) );
				remove_filter( 'edd_metabox_fields_save', array( $ipresso_handler, 'save_metabox' ) );
				remove_filter( 'edd_settings_extensions', array( $ipresso_handler, 'settings' ) );
				remove_action( 'edd_purchase_form_before_submit', array( $ipresso_handler, 'checkout_fields' ), 100 );
				remove_action( 'edd_checkout_before_gateway', array( $ipresso_handler, 'checkout_signup' ), 10 );
				remove_action( 'edd_complete_download_purchase', array(
					$ipresso_handler,
					'completed_download_purchase_signup'
				), 10 );
				remove_filter( 'edd_settings_sections_extensions', array( $ipresso_handler, 'subsection' ), 10 );
				remove_action( 'wp_head', array( $ipresso, 'hook_ipresso_tracking_code' ) );
				break;
			case 'edd-freshmail':
				$freshmail = \bpmj\wp\eddfm\Plugin::instance()->get_freshmail_handler();
				remove_action( 'add_meta_boxes', array( $freshmail, 'add_metabox' ) );
				remove_filter( 'edd_metabox_fields_save', array( $freshmail, 'save_metabox' ) );
				remove_filter( 'edd_settings_extensions', array( $freshmail, 'settings' ) );
				remove_action( 'edd_purchase_form_before_submit', array( $freshmail, 'checkout_fields' ), 100 );
				remove_action( 'edd_checkout_before_gateway', array( $freshmail, 'checkout_signup' ), 10 );
				remove_action( 'edd_complete_download_purchase', array(
					$freshmail,
					'completed_download_purchase_signup'
				), 10 );
				remove_filter( 'edd_settings_sections_extensions', array( $freshmail, 'subsection' ), 10 );
				break;
			case 'edd-getresponse':
				$getresponse = \bpmj\wp\eddres\Plugin::instance()->get_getresponse_handler();
				remove_action( 'add_meta_boxes', array( $getresponse, 'add_metabox' ) );
				remove_filter( 'edd_metabox_fields_save', array( $getresponse, 'save_metabox' ) );
				remove_filter( 'edd_settings_extensions', array( $getresponse, 'settings' ) );
				remove_action( 'edd_purchase_form_before_submit', array( $getresponse, 'checkout_fields' ), 100 );
				remove_action( 'edd_checkout_before_gateway', array( $getresponse, 'checkout_signup' ), 10 );
				remove_action( 'edd_complete_download_purchase', array(
					$getresponse,
					'completed_download_purchase_signup'
				), 10 );
				remove_filter( 'edd_settings_sections_extensions', array( $getresponse, 'subsection' ), 10 );
				break;
			case 'edd-convertkit':
				/** @var EDD_ConvertKit $edd_convert_kit */
				global $edd_convert_kit;
				remove_filter( 'edd_settings_sections_extensions', array( $edd_convert_kit, 'subsection' ) );
				remove_action( 'add_meta_boxes', array( $edd_convert_kit, 'add_metabox' ) );
				remove_filter( 'edd_metabox_fields_save', array( $edd_convert_kit, 'save_metabox' ) );
				remove_filter( 'edd_settings_extensions', array( $edd_convert_kit, 'settings' ) );
				remove_action( 'edd_purchase_form_before_submit', array( $edd_convert_kit, 'checkout_fields' ), 100 );
				remove_action( 'edd_checkout_before_gateway', array( $edd_convert_kit, 'checkout_signup' ) );
				remove_action( 'edd_complete_download_purchase', array(
					$edd_convert_kit,
					'completed_download_purchase_signup'
				) );
				break;
			case 'edd-activecampaign':
			    $activecampaign = \bpmj\wp\eddact\Plugin::instance()->get_activecampaign_handler();
			    remove_action( 'add_meta_boxes', array( $activecampaign, 'add_metabox' ) );
			    remove_filter( 'edd_metabox_fields_save', array( $activecampaign, 'save_metabox' ) );
			    remove_filter( 'edd_settings_extensions', array( $activecampaign, 'settings' ) );
			    remove_action( 'edd_purchase_form_before_submit', array( $activecampaign, 'checkout_fields' ), 100 );
			    remove_action( 'edd_checkout_before_gateway', array( $activecampaign, 'checkout_signup' ) );
			    remove_action( 'edd_complete_download_purchase', array(
			        $activecampaign,
			        'completed_download_purchase_signup'
			    ) );
			    break;
		}
	}

	/**
	 * Removes hooks that are not needed by WP Idea
	 *
	 * @param string $integration
	 */
	public function remove_unnecessary_hooks( $integration ) {
		switch ( $integration ) {
			case 'wp-fakturownia':
//				remove_action( 'save_post', 'bpmj_wpfa_edd_download_save' );
				remove_action( 'edd_download_price_table_head', 'bpmj_wpfa_edd_variable_price_head' );
				remove_action( 'edd_download_price_table_row', 'bpmj_wpfa_edd_variable_price_row', 10 );
				remove_filter( 'edd_price_row_args', 'bpmj_wpfa_edd_price_row_args', 10 );
				remove_filter( 'sanitize_post_meta_edd_variable_prices', 'bpmj_wpfa_edd_sanitize_variable_prices', 20 );
				break;
		}
	}

	/**
	 * @return array
	 */
	public function get_compatibility_fixes() {
		$plugin_and_themes = array();
		foreach ( glob( BPMJ_EDDCM_DIR . 'includes/compatibility/*.php' ) as $file_path ) {
			$plugin_name         = substr( basename( $file_path ), 0, - 4 );
			$plugin_and_themes[] = $plugin_name;
		}

		return $plugin_and_themes;
	}

	/**
	 * @param $plugin_or_theme_name
	 *
	 * @return bool
	 */
	public function is_plugin_or_theme_active( $plugin_or_theme_name ) {
		switch ( $plugin_or_theme_name ) {
			case 'vendd':
				return 'vendd' === get_stylesheet();
			case 'elementor':
				return defined( 'ELEMENTOR_VERSION' );
            case 'twentyseventeen':
                return 'twentyseventeen' === wp_get_theme()->get_stylesheet();
            case 'twentytwentyone':
                return 'twentytwentyone' === wp_get_theme()->get_stylesheet();
            case 'ultimate-member':
                return is_plugin_active('ultimate-member/ultimate-member.php');
		}

		return false;
	}

    /**
     * Check if EDD Sell Discount is enabled
     *
     * @return bool true if EDD Sell Discount is enabled, false otherwise
     */
    public function is_edd_sell_discount_enabled() {
        $is_enabled = false;

        if ( is_plugin_active( 'edd-sell-discount/edd-sell-discount.php' ) ) {
            $is_enabled = true;
        }

        return apply_filters( 'is_edd_sell_discount_enabled', $is_enabled );
    }

}
