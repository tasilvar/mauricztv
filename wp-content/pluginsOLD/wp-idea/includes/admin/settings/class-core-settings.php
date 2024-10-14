<?php
/**
 * Courses Manager Settings API data
 *
 * @package     WPTAO/Admin
 * @category    Admin
 */

namespace bpmj\wpidea\admin\settings;

use bpmj\wpidea\admin\helpers\html\Button;
use bpmj\wpidea\admin\helpers\html\Popup;
use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\admin\subscription\models\Subscription_Const;
use bpmj\wpidea\Analytics;
use bpmj\wpidea\Caps;
use bpmj\wpidea\certificates\Certificate_Template;
use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\Helper;
use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;
use bpmj\wpidea\notices\User_Notice_Service;
use bpmj\wpidea\Packages;
use bpmj\wpidea\sales\product\Flat_Rate_Tax_Symbol_Helper;
use bpmj\wpidea\sales\product\Invoice_Tax_Payer_Helper;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\Software_Variant;
use bpmj\wpidea\telemetry\Telemetry;
use bpmj\wpidea\templates_system\admin\modules\Interface_Templates_System_Modules_Factory;
use bpmj\wpidea\templates_system\Templates_System;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\View;
use bpmj\wpidea\wolverine\feature\Feature;
use bpmj\wpidea\wolverine\user\User;
use WP_Post;

if (!class_exists('bpmj\wpidea\admin\settings\Core_Settings')) {

    class Core_Settings
    {
        /*
         * @var string
         * Unique settings slug
         */

        private $setting_slug = BPMJ_EDDCM_SETTINGS_SLUG;

        /**
         * Slug for layout template settings
         * @var string
         */
        private $layout_template_settings_slug = '';

        /**
         * @var Subscription
         */
        private $subscription;

        /*
         * Settings API object
         * @var Settings_API
         */
        public $settings_api;

        /*
         * @var WP_Settings_Handler
         */
        public $wp_settings_handler;

        private $templates_settings_handler;

        private $templates_guide;

        private $user_notice;

        private $templates_system;

        private $events;

        private $translator;

        private Interface_Readable_Course_Repository $courses_repository;
        private Interface_Settings $settings;

        public function __construct(
            Subscription $subscription,
            Interface_Templates_System_Modules_Factory $templates_system_modules_factory,
            Interface_Events $events,
            User_Notice_Service $user_notice,
            Templates_System $templates_system,
            Interface_Translator $translator,
            Interface_Readable_Course_Repository $courses_repository,
            Interface_Settings $settings
        ) {
            $this->subscription = $subscription;
            $this->templates_settings_handler = $templates_system_modules_factory->get_settings_handler();
            $this->templates_guide = $templates_system_modules_factory->get_templates_guide();
            $this->user_notice = $user_notice;
            $this->templates_system = $templates_system;
            $this->events = $events;
            $this->translator = $translator;
            $this->courses_repository = $courses_repository;
            $this->settings = $settings;

            global $courses_manager_settings;

            // Set global variable with settings
            $settings = get_option($this->setting_slug);
            if (!isset($settings) || empty($settings)) {
                $courses_manager_settings = array();
            } else {
                $courses_manager_settings = $settings;
            }

            $this->settings_api = new Settings_API($this->setting_slug, false, true);
            $this->layout_template_settings_slug = $this->setting_slug . '-layout-template-settings';
            $this->wp_settings_handler = new WP_Settings_Handler();

            add_action('admin_init', array($this, 'settings_init'));
//			add_action( 'admin_init', array( $this, 'settings_save' ) );
            add_action('admin_init', array($this, 'hook_init_layout_settings'));

            // Remove actions from EDD PC
            remove_action('admin_init', 'bpmj_eddpc_save_renewal');
            remove_action('admin_init', 'bpmj_eddpc_delete_renewal');

            // Add new actions
            add_action('admin_init', array($this, 'save_renewal'));
            add_action('admin_init', array($this, 'delete_renewal'));

            add_action('admin_init', array($this, 'hook_reload_api_cache'));

            // remove Amazon gateway
            add_filter('edd_register_amazon_gateway', array($this, 'remove_amazon'));

            add_filter('pre_update_option_wp_idea', array($this, 'settings_prepare_pre_update'), 10, 2);
            add_action('update_option_wp_idea', array($this, 'settings_save'));
            add_action('update_option_edd_settings', array($this, 'edd_settings_save'), 10, 2);
        }

        public function remove_amazon($default_amazon_info)
        {
            return [];
        }

        /*
         * Set sections and fields
         */

        public function settings_init()
        {
            $is_options_page = false;
            if (isset($_GET['page']) && 'wp-idea-settings' === $_GET['page']) {
                $is_options_page = true;
            }
            if (!$is_options_page && isset($_POST['option_page']) && 'wp_idea' === $_POST['option_page']) {
                $is_options_page = true;
            }
            if (!$is_options_page) {
                return;
            }

            //Set the settings
            $this->settings_api->set_sections($this->settings_sections());
            $this->settings_api->set_fields($this->settings_fields());

            //Initialize settings
            $this->settings_api->settings_init();
        }

        /*
         * Set settings sections
         *
         * @return array settings sections
         */

        public function settings_sections()
        {
            $courses_functionality_enabled = $this->settings->get(Settings_Const::COURSES_ENABLED) ?? true;
            $courses_layout_subsections = [
                    [
                        'id' => 'courses_list',
                        'title' => __('List settings', BPMJ_EDDCM_DOMAIN)
                    ],

            ];

            if($courses_functionality_enabled){
                $courses_layout_subsections = array_merge($courses_layout_subsections, [
                    [
                        'id' => 'courses_view',
                        'title' => __('Course view settings', BPMJ_EDDCM_DOMAIN)
                    ]
                ]);
            }

            $sections = array(
                array(
                    'id' => 'courses_general',
                    'title' => __('Main', BPMJ_EDDCM_DOMAIN)
                ),
                array(
                    'id' => 'courses_payment_gates',
                    'title' => $this->translator->translate('settings.main.payment_methods')
                ),
                'courses_invoices' => array(
                    'id' => 'courses_invoices',
                    'title' => __('Invoices', BPMJ_EDDCM_DOMAIN)
                ),
                array(
                    'id' => 'courses_emails',
                    'title' => __('Emails', BPMJ_EDDCM_DOMAIN)
                ),
                array(
                    'id' => 'courses_mailers',
                    'title' => __('Mailers', BPMJ_EDDCM_DOMAIN)
                ),
                array(
                    'id' => 'courses_subscriptions',
                    'title' => __('Subscriptions', BPMJ_EDDCM_DOMAIN)
                ),
                array(
                    'id' => 'courses_layout',
                    'title' => __('Layout', BPMJ_EDDCM_DOMAIN),
                    'subsections' => $courses_layout_subsections,
                ),
                array(
                    'id' => 'courses_order',
                    'title' => __('Order form', BPMJ_EDDCM_DOMAIN),
                ),
                array(
                    'id' => 'courses_gift',
                    'title' => __('Gift order settings', BPMJ_EDDCM_DOMAIN),
                ),
                array(
                    'id' => 'courses_certificates',
                    'title' => __('Certificates', BPMJ_EDDCM_DOMAIN),
                ),
                array(
                    'id' => 'analytics_and_scripts',
                    'title' => __('Analytics and scripts', BPMJ_EDDCM_DOMAIN),
                ),
                array(
                    'id' => 'courses_advanced',
                    'title' => __('Advanced', BPMJ_EDDCM_DOMAIN),
                ),
                /*
                array(
                    'id'    => 'salesrun',
                    'title' => __( 'Increase sales', BPMJ_EDDCM_DOMAIN ),
                ),
                 */
            );

            if(!$courses_functionality_enabled) {
                // unset certificates tab
                $sections = array_filter($sections, static function ($section) {
                    return $section['id'] !== 'courses_certificates';
                });
            }

            if (Software_Variant::is_international()) {
                foreach ($this->payments_allowed_only_in_poland() as $payment_id) {
                    unset($sections['courses_invoices']);
                }
            }

            return apply_filters('bpmj_eddcm_settings_sections', $sections);
        }

        /**
         * Create settings fields
         *
         * @return array settings fields
         */
        function settings_fields()
        {

            $settings_fields = $this->get_settings_fields();

            if (Software_Variant::is_international()) {
                foreach ($this->payments_allowed_only_in_poland() as $payment_id) {
                    unset($settings_fields['courses_payment_gates']['payment_gate']['options'][$payment_id]);
                }
            }

            return apply_filters('bpmj_eddpc_settings', $settings_fields);
        }

        public function get_settings_fields()
        {
            $all_pages = get_pages(array('hierarchical' => false, 'post_type' => 'page'));
            $all_pages_new = array();
            /* @var $page WP_Post */
            foreach ($all_pages as $page) {
                if ('publish' === $page->post_status) {
                    $all_pages_new[$page->ID] = $page->post_title;
                } else {
                    $post_status_obj = get_post_status_object($page->post_status);
                    $all_pages_new[$page->ID] = $page->post_title . ' (' . $post_status_obj['label'] . ')';
                }
            }
            $all_pages_with_turnoff = $all_pages_new;
            $all_pages_with_turnoff[''] = __('-- Turn Off --', BPMJ_EDDCM_DOMAIN);

            $gateways = array();
            if (function_exists('edd_get_payment_gateways')) {
                $gateways = edd_get_payment_gateways();
            }

            return array(
                /**
                 * Ustawienia ogólne
                 */
                'courses_general' => apply_filters('bpmj_eddcm_general_settings', $this->get_general_settings($all_pages_new, $all_pages_with_turnoff)),
                'courses_list' => apply_filters('bpmj_eddcm_list_settings', $this->get_list_settings()),
                'courses_view' => apply_filters('bpmj_eddcm_view_settings', $this->get_view_settings()),
                /**
                 * Bramki Płatności
                 */
                'courses_payment_gates' => apply_filters('bpmj_eddcm_payment_gates', array(
                    array(
                        'name' => 'test_mode',
                        'label' => __('Test Mode', BPMJ_EDDCM_DOMAIN),
                        'desc' => __('While in test mode, no live transactions are processed. To fully use the test mode, you must have a sandbox (test) account for the payment gateway you are testing.', BPMJ_EDDCM_DOMAIN),
                        'type' => 'checkbox',
                        'save_to' => 'edd_settings'
                    ),
                    array(
                        'name' => 'default_gateway',
                        'label' => __('Default payment method', BPMJ_EDDCM_DOMAIN),
                        'desc' => __('This payment gate will be used as the default.', BPMJ_EDDCM_DOMAIN),
                        'type' => 'gateways',
                        'options' => $gateways,
                        'save_to' => 'edd_settings'
                    ),
                    array(
                        'name' => 'display_payment_methods_as_icons',
                        'label' => __('Display payment methods as icons on checkout', BPMJ_EDDCM_DOMAIN),
                        'desc' => __('When you select this option, small logos of payment methods will be displayed instead of radio buttons during checkout.', BPMJ_EDDCM_DOMAIN),
                        'type' => 'checkbox',
                    ),
                    'payment_gate' => array(
                        'name' => 'payment_gate',
                        'label' => __('Payment Methods', BPMJ_EDDCM_DOMAIN),
                        'type' => 'bpmj_groups',
                        'save_to' => 'edd_settings',
                        'groups_type' => 'gateways',
                        'options' => array(
                            'manual' => array(
                                'name' => __('Payment test', BPMJ_EDDCM_DOMAIN),
                                'save_to' => 'edd_settings',
                            ),
                            'edd-tpay' => array(
                                'name' => 'Tpay.com',
                                'settings' => array(
                                    'tpay_id' => array(
                                        'id' => 'tpay_id',
                                        'name' => __('Identyfikator tpay.com', 'edd-tpay'),
                                        'desc' => __('Wprowadź Twój identyfikator z serwisu tpay.com', 'edd-tpay'),
                                        'type' => 'text',
                                        'size' => 'regular'
                                    ),
                                    'tpay_pin' => array(
                                        'id' => 'tpay_pin',
                                        'name' => __('Kod bezpieczeństwa tpay.com', 'edd-tpay'),
                                        'desc' => __('Wprowadź Twój kod bezpieczeństwa (potwierdzający)', 'edd-tpay'),
                                        'type' => 'text',
                                        'size' => 'regular'
                                    ),
                                    'tpay_cards_api_key' => array(
                                        'id' => 'tpay_cards_api_key',
                                        'name' => __('Klucz API dla kart płatniczych (opcjonalnie)', 'edd-tpay'),
                                        'desc' => __('Wprowadź Twój klucz API dla kart płatniczych. Wprowadzenie klucza i hasła umożliwia włączenie płatności cyklicznych (subskrypcyjnych)', 'edd-tpay'),
                                        'type' => 'text',
                                        'size' => 'regular'
                                    ),
                                    'tpay_cards_api_password' => array(
                                        'id' => 'tpay_cards_api_password',
                                        'name' => __('Hasło do API dla kart płatniczych (opcjonalnie)', 'edd-tpay'),
                                        'desc' => __('Wprowadź Twoje hasło do API dla kart płatniczych', 'edd-tpay'),
                                        'type' => 'text',
                                        'size' => 'regular'
                                    ),
                                    'tpay_cards_verification_code' => array(
                                        'id' => 'tpay_cards_verification_code',
                                        'name' => __('Kod weryfikacyjny dla API kart płatniczych', 'edd-tpay'),
                                        'desc' => __('Wprowadź Twój kod weryfikacyjny do API dla kart płatniczych', 'edd-tpay'),
                                        'type' => 'text',
                                        'size' => 'regular'
                                    ),
                                    'tpay_recurrence_allow_standard_payments' => array(
                                        'id' => 'tpay_recurrence_allow_standard_payments',
                                        'label' => __('Enable standard payment methods for recurrent orders', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('When enabled, customers will be able to choose non-card payment methods to pay for recurrent products. The system will automatically generate payments for consecutive periods, but the customer has to be informed and make the payment manually. Automatic charging is possible only with credit card payments.', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'checkbox'
                                    ),
                                ),
                                'save_to' => 'edd_settings',
                                'status' => 'off'
                            ),
                            'edd-przelewy24' => array(
                                'name' => 'Przelewy24',
                                'settings' => array(
                                    'przelewy24_id' => array(
                                        'label' => __('Przelewy24 ID', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Your Przelewy24 account ID', BPMJ_EDDCM_DOMAIN)
                                    ),
                                    'przelewy24_pin' => array(
                                        'label' => __('Przelewy24 CRC', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('You can find this CRC code on <a href="http://przelewy24.pl">Przelewy24.pl</a>: <b>Moje dane / Klucz do CRC</b>', BPMJ_EDDCM_DOMAIN),
                                    )
                                ),
                                'save_to' => 'edd_settings',
                                'status' => 'off'
                            ),
                            'edd-dotpay' => array(
                                'name' => 'Dotpay',
                                'settings' => array(
                                    'dotpay_id' => array(
                                        'label' => __('Dotpay ID', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Your Dotpay account ID', BPMJ_EDDCM_DOMAIN)
                                    ),
                                    'dotpay_pin' => array(
                                        'label' => __('Dotpay PIN', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __("It's a string that you have to prepare on <a href='http://www.dotpay.pl/'>Dotpay.pl</a>: <b>Ustawienia / parametry URLC</b>", BPMJ_EDDCM_DOMAIN),
                                    ),
                                    'dotpay_onlinetransfer' => array(
                                        'label' => __('Check if you accept only realtime payments', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'checkbox'
                                    ),
                                    'dotpay_message' => array(
                                        'type' => 'message',
                                        'text' => __("Before start using this payment gate, please go to <a href='http://www.dotpay.pl/'>Dotpay.pl</a> admin panel: <b>Ustawienia / Konfiguracja URLC / Edycja[JD5]</b> and uncheck the fields <b>Blokuj zewnętrzne urlc[JD6]</b> and <b>HTTPS verify</b>.<br>That will helps with making payments through this plugin.", BPMJ_EDDCM_DOMAIN)
                                    )
                                ),
                                'save_to' => 'edd_settings',
                                'status' => 'off'
                            ),
                            'edd-paynow' => array(
                                'name' => 'Paynow',
                                'settings' => array(
                                    'paynow_access_key' => array(
                                        'id' => 'paynow_access_key',
                                        'label' => __('Access key to API', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'text',
                                        'size' => 'regular',
                                    ),
                                    'paynow_signature_key' => array(
                                        'id' => 'paynow_signature_key',
                                        'label' => __('Signature key to API', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'text',
                                        'size' => 'regular',
                                    ),
                                    'paynow_environment' => array(
                                        'id' => 'paynow_environment',
                                        'name' => __('Paynow environment:', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Select Paynow environment', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'radio',
                                        'options' => array(
                                            'production' => __('Production', BPMJ_EDDCM_DOMAIN),
                                            'sandbox' => __('Sandbox (for testing)', BPMJ_EDDCM_DOMAIN),
                                        ),
                                        'default' => 'sandbox',
                                    ),
                                ),
                                'save_to' => 'edd_settings',
                                'status' => 'off'
                            ),
                            'edd-payu' => array(
                                'name' => 'PayU',
                                'settings' => array(
                                    'payu_pos_id' => array(
                                        'id' => 'payu_pos_id',
                                        'name' => __('PayU POS Id', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Enter your PayU POS Id', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'text',
                                        'size' => 'regular',
                                    ),
                                    'payu_pos_auth_key' => array(
                                        'id' => 'payu_pos_auth_key',
                                        'name' => __('PayU POS auth key', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Enter your POS PayU payment authorization key (pos_auth_key), 7 characters', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'text',
                                        'size' => 'regular'
                                    ),
                                    'payu_key1' => array(
                                        'id' => 'payu_key1',
                                        'name' => __('PayU key:', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Enter your POS first key (MD5)', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'text',
                                        'size' => 'regular',
                                    ),
                                    'payu_key2' => array(
                                        'id' => 'payu_key2',
                                        'name' => __('PayU second key:', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Enter your POS second key (MD5)', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'text',
                                        'size' => 'regular'
                                    ),
                                    'payu_api_type' => array(
                                        'id' => 'payu_api_type',
                                        'name' => __('PayU API type:', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Select your POS API type', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'radio',
                                        'options' => array(
                                            'rest' => __('REST (Checkout - Express Payment)', BPMJ_EDDCM_DOMAIN),
                                            'classic' => __('Classic (Express Payment)', BPMJ_EDDCM_DOMAIN),
                                        ),
                                        'default' => 'rest',
                                    ),
                                    'payu_api_environment' => array(
                                        'id' => 'payu_api_environment',
                                        'name' => __('PayU API environment:', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Select PayU API environment', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'radio',
                                        'options' => array(
                                            'secure' => __('Secure (default)', BPMJ_EDDCM_DOMAIN),
                                            'sandbox' => __('Sandbox (for testing)', BPMJ_EDDCM_DOMAIN),
                                        ),
                                        'default' => 'secure',
                                    ),
                                    'payu_return_url_failure' => array(
                                        'id' => 'payu_return_url_failure',
                                        'name' => __('PayU return URL - failure:', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Copy and paste this URL in your POS settings in the PayU control panel', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'payu_return_url',
                                        'value' => $this->get_payu_failure_return_url(),
                                    ),
                                    'payu_return_url_success' => array(
                                        'id' => 'payu_return_url_success',
                                        'name' => __('PayU return URL - success:', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Copy and paste this URL in your POS settings in the PayU control panel', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'payu_return_url',
                                        'value' => $this->get_success_page_uri(),
                                    ),
                                    'payu_return_url_reports' => array(
                                        'id' => 'payu_return_url_reports',
                                        'name' => __('PayU reports URL:', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Copy and paste this URL in your POS settings in the PayU control panel', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'payu_return_url',
                                        'value' => home_url('/'),
                                    ),
                                    'payu_recurrence_allow_standard_payments' => array(
                                        'id' => 'payu_recurrence_allow_standard_payments',
                                        'label' => __('Enable standard payment methods for recurrent orders', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('When enabled, customers will be able to choose non-card payment methods to pay for recurrent products. The system will automatically generate payments for consecutive periods, but the customer has to be informed and make the payment manually. Automatic charging is possible only with credit card payments.', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'checkbox',
                                    ),
                                    'payu_enable_debug' => array(
                                        'id' => 'payu_enable_debug',
                                        'label' => __('Enable debug', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('When enabled, the system will store additional diagnostic information during the payment process.', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'checkbox'
                                    ),
                                ),
                                'save_to' => 'edd_settings',
                                'status' => 'off',
                            ),
                            'paypal' => array(
                                'name' => 'PayPal',
                                'settings' => array(
                                    'paypal_email' => array(
                                        'label' => 'PayPal Email',
                                        'desc' => __("Enter the email to your PayPal account", BPMJ_EDDCM_DOMAIN),
                                    ),
                                    'paypal_page_style' => array(
                                        'label' => __('PayPal Page Style', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Enter the name of the page style to use or leave blank for default.', BPMJ_EDDCM_DOMAIN),
                                    ),
                                    'disable_paypal_verification' => array(
                                        'label' => __('Disable PayPal IPN Verification', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Check this box if the order status does not change to Finished. This forces the site to use a slightly less secure method of verifying purchases.', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'checkbox',
                                    )
                                ),
                                'save_to' => 'edd_settings'
                            ),
                            'edd-coinbase' => array(
                                'name' => 'Coinbase',
                                'settings' => array(
                                    'edd_coinbase_api_key' => array(
                                        'id' => 'edd_coinbase_api_key',
                                        'name' => __('API Key', 'edd-coinbase', BPMJ_EDDCM_DOMAIN),
                                        'label' => __('API Key', 'edd-coinbase', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Enter your Coinbase API key', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'text'
                                    ),
                                    'coinbase_webhook_description' => array(
                                        'type' => 'message',
                                        'text' => sprintf(__('In order for Coinbase to function completely, you must configure your webhooks. Visit your <a href="%s" target="_blank">account dashboard</a> to configure them. Please add a webhook endpoint for the URL below.', BPMJ_EDDCM_DOMAIN), 'https://commerce.coinbase.com/dashboard/settings')
                                            . '<br>' . sprintf(__('Webhook URL: %s', BPMJ_EDDCM_DOMAIN), home_url('index.php?edd-listener=coinbase'))
                                            . '<br>' . sprintf(__('See our <a href="%s">documentation</a> for more information.', BPMJ_EDDCM_DOMAIN), 'https://docs.easydigitaldownloads.com/article/314-coinbase-payment-gateway-setup-documentation'),
                                    ),
                                ),
                                'save_to' => 'edd_settings',
                                'status' => 'off',
                            ),
                            'edd-stripe' => array(
                                'name' => 'Stripe',
                                'settings' => array(
                                    'test_secret_key' => array(
                                        'id' => 'test_secret_key',
                                        'label' => __('Test Secret Key', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Enter your test secret key, found in your Stripe Account Settings', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'text',
                                        'size' => 'regular',
                                        'class' => 'edd-hidden edds-api-key-row',
                                    ),
                                    'test_publishable_key' => array(
                                        'id' => 'test_publishable_key',
                                        'label' => __('Test Publishable Key', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Enter your test publishable key, found in your Stripe Account Settings', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'text',
                                        'size' => 'regular',
                                        'class' => 'edd-hidden edds-api-key-row',
                                    ),
                                    'live_secret_key' => array(
                                        'id' => 'live_secret_key',
                                        'label' => __('Live Secret Key', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Enter your live secret key, found in your Stripe Account Settings', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'text',
                                        'size' => 'regular',
                                        'class' => 'edd-hidden edds-api-key-row',
                                    ),
                                    'live_publishable_key' => array(
                                        'id' => 'live_publishable_key',
                                        'label' => __('Live Publishable Key', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Enter your live publishable key, found in your Stripe Account Settings', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'text',
                                        'size' => 'regular',
                                        'class' => 'edd-hidden edds-api-key-row'
                                    ),
                                ),
                                'save_to' => 'edd_settings',
                                'status' => 'off',
                            ),
                            'edd-przelewy' => array(
                                'name' => $this->translator->translate('settings.main.payment_methods.traditional_transfer'),
                                'settings' => array(
                                    'edd_przelewy_name' => array(
                                        'id' => 'edd_prz_name',
                                        'label' => $this->translator->translate('settings.main.payment_methods.traditional_transfer.name'),
                                        'type' => 'text',
                                        'desc' => ''
                                    ),
                                    'edd_przelewy_address' => array(
                                        'id' => 'edd_prz_address',
                                        'label' => $this->translator->translate('settings.main.payment_methods.traditional_transfer.address'),
                                        'type' => 'text',
                                        'desc' => 'ul. Testowa 123, 11-123 Warszawa'
                                    ),
                                    'edd_przelewy_account_number' => array(
                                        'id' => 'edd_prz_account_number',
                                        'label' => $this->translator->translate('settings.main.payment_methods.traditional_transfer.account_number'),
                                        'type' => 'text',
                                        'desc' => ''
                                    )
                                ),
                                'save_to' => 'edd_settings'
                            )
                        )
                    )
                )),
                /**
                 * Faktury
                 */
                'courses_invoices' => apply_filters('bpmj_eddcm_invoices_settings', array(
                    array(
                        'name' => 'invoices',
                        'label' => __('Turn On/Off Invoices', BPMJ_EDDCM_DOMAIN),
                        'desc' => __('When you select this option, customers will be able to enter invoice data.', BPMJ_EDDCM_DOMAIN),
                        'type' => 'checkbox',
                    ),
                    [
                        'name' => Invoice_Tax_Payer_Helper::ENABLE_OPTION_NAME,
                        'label' => $this->translator->translate('invoices.vat_rate.is_vat_payer'),
                        'type' => 'select',
                        'options' => [
                            'yes' => $this->translator->translate('invoices.vat_rate.is_vat_payer.yes'),
                            'no' => $this->translator->translate('invoices.vat_rate.is_vat_payer.no'),
                        ],
                    ],
                    [
                        'name' => 'invoices_default_vat_rate',
                        'label' => $this->translator->translate('invoices.vat_rate.default_vat_rate'),
                        'type' => 'text',
                        'desc' => $this->translator->translate('invoices.vat_rate.default_vat_rate.desc'),
                        'default' => '23',
                    ],
                    array(
                        'name' => 'edd_id_force',
                        'label' => __('Require invoice', BPMJ_EDDCM_DOMAIN),
                        'desc' => __('When you select this option, it will be mandatory to enter the invoice data.', BPMJ_EDDCM_DOMAIN),
                        'type' => 'checkbox',
                        'save_to' => 'edd_settings',
                    ),
                    array(
                        'name' => 'edd_id_person',
                        'label' => __('Invoices for individuals', BPMJ_EDDCM_DOMAIN),
                        'desc' => __('When you select this option, individuals can also give invoice data (name and address)', BPMJ_EDDCM_DOMAIN),
                        'type' => 'checkbox',
                        'save_to' => 'edd_settings',
                    ),
                    array(
                        'name' => 'edd_id_disable_taxid_verification',
                        'label' => __('Disable Tax ID verification', 'bpmj-edd-invoice-data'),
                        'desc' => __('When you check this option, the Tax ID verification on the checkout will be disabled', 'bpmj-edd-invoice-data'),
                        'type' => 'checkbox',
                        'save_to' => 'edd_settings'
                    ),
                    'edd_id_enable_receiver' => Feature::isEnabled(Feature::INVOICE_RECEIVER) ? [
                        'name' => 'edd_id_enable_receiver',
                        'label' => __('Enable collecting receiver\'s information', BPMJ_EDDCM_DOMAIN),
                        'desc' => __('When you check this option, the buyer will be able to optionally specify receiver\'s data', BPMJ_EDDCM_DOMAIN),
                        'type' => 'checkbox',
                        'save_to' => 'edd_settings'
                    ] : null,
                    array(
                        'name' => 'nip_for_receipts',
                        'label' => __('NIP for receipts', BPMJ_EDDCM_DOMAIN),
                        'desc' => __('When you select this option, a field will appear on cart page to enter NIP for receipts.', BPMJ_EDDCM_DOMAIN),
                        'type' => 'checkbox'
                    ),
                    'edd_id_enable_vat_moss' => array(
                        'name' => 'edd_id_enable_vat_moss',
                        'label' => __('Enable simplified VAT MOSS (beta)', BPMJ_EDDCM_DOMAIN),
                        'desc' => __('When you check this option, the simplified VAT MOSS rules will be applied', BPMJ_EDDCM_DOMAIN) .
                            (WPI()->packages->no_access_to_feature(Packages::FEAT_VAT_MOSS)
                                ? '<br /><span class="text-danger">' . WPI()->packages->feature_not_available_message(Packages::FEAT_VAT_MOSS, __('In order to use VAT MOSS, you need to upgrade your license to level: "%s"', BPMJ_EDDCM_DOMAIN)) . '</span>'
                                : ''),
                        'type' => 'checkbox',
                        'disabled' => WPI()->packages->no_access_to_feature(Packages::FEAT_VAT_MOSS),
                        'save_to' => 'edd_settings'
                    ),
                    array(
                        'name' => Flat_Rate_Tax_Symbol_Helper::ENABLE_CHECKBOX_NAME,
                        'label' => $this->translator->translate('settings.sections.accounting.flat_rate_tax_symbol'),
                        'desc' => $this->translator->translate('settings.sections.accounting.enable_flat_rate_tax_symbol'),
                        'type' => 'checkbox',
                        'default' => '',
                    ),
                    'invoice_methods' => array(
                        'name' => 'invoice_methods',
                        'label' => __('Invoice Methods', BPMJ_EDDCM_DOMAIN),
                        'type' => 'bpmj_groups',
                        'groups_type' => 'invoices',
                        'options' => array(
                            'wp-fakturownia' => array(
                                'name' => 'Fakturownia',
                                'settings' => array(
                                    'apikey' => array(
                                        'label' => 'Klucz API / Nazwa konta',
                                        'desc'	 => __( 'Fakturownia.pl -> Ustawienia konta -> Integracja -> Kod autoryzacyjny API / Fakturownia.pl -> Ustawienia konta -> Nazwa konta', 'bpmj_wpfa' ),
                                        'validation_regex' => '(.*)/(.*)+'
                                    ),
                                    'departments_id' => array(
                                        'label' => 'ID Firmy',
                                        'desc' => 'W Fakturownia.pl -> Ustawienia -> Dane firmy należy kliknąć na firmę / dział i ID działu pojawi się w URL. Jeśli to pole pozostanie puste, wtedy będą wstawione domyślne dane Twojej firmy',
                                        'type' => 'number',
                                    ),
                                    'auto_sent' => array(
                                        'label' => 'Automatyczna wysyłka faktur',
                                        'desc' => 'Zaznacz, jeżeli faktury maja być wysyłane automatycznie e-mailem do klienta. Wymagana pełna aktywacja systemu Fakturownia.pl',
                                        'type' => 'checkbox',
                                    ),
                                    'auto_sent_receipt' => array(
                                        'label' => 'Automatyczna wysyłka paragonów',
                                        'desc' => 'Zaznacz, jeżeli paragony maja być wysyłane automatycznie e-mailem do klienta. Wymagana pełna aktywacja systemu Fakturownia.pl',
                                        'type' => 'checkbox',
                                    ),
                                    'receipt' => array(
                                        'label' => 'Wystawiaj też paragony',
                                        'type' => 'checkbox',
                                    ),
                                    'bill_note' => array(
                                        'label' => __('Text to be inserted in the invoice (invoice without VAT)', 'bpmj_wpfa'),
                                        'type' => 'textarea',
                                        'default' => __('SPRZEDAWCA ZWOLNIONY PODMIOTOWO Z PODATKU OD TOWARU I USŁUG (dostawa towarów lub świadczenie usług zwolnione na podstawie art. 113 ust 1 (albo ust. 9) ustawy z dnia 11 marca 2004 r. o podatku od towarów i usług (Dz. U. z 2011 r Nr 177, poz. 1054 z późn. zm.)).', 'bpmj_wpfa'),
                                        'sanitize_callback' => array($this, 'sanitize_for_post_allowed_html_tags')
                                    ),
                                ),
                                'save_to' => 'bpmj_wpfa_settings',
                                'status' => 'off'
                            ),
                            'wp-ifirma' => array(
                                'name' => 'iFirma',
                                'settings' => array(
                                    'ifirma_email' => array(
                                        'label' => 'Email z systemu iFirma',
                                        'desc' => 'Podaj email, za pomocą którego logujesz się do panelu systemu iFirma',
                                        'type' => 'text',
                                    ),
                                    'ifirma_invoice_key' => array(
                                        'label' => 'Klucz API faktura',
                                        'desc' => 'iFirma.pl -> Narzędzia -> API',
                                    ),
                                    'ifirma_subscriber_key' => array(
                                        'label' => 'Klucz API abonent',
                                        'desc' => 'iFirma.pl -> Narzędzia -> API',
                                    ),
                                    'vat_exemption' => [
                                        'label' => 'Podstawa zwolnienia z VAT',
                                        'type' => 'text',
                                        'default' => 'Art. 113 ust. 1',
                                    ],
                                    'auto_sent' => array(
                                        'label' => 'Automatyczna wysyłka',
                                        'desc' => 'Zaznacz, jeżeli dokumenty sprzedaży maja być wysyłane automatycznie e-mailem do klienta',
                                        'type' => 'checkbox',
                                    ),
                                ),
                                'save_to' => 'bpmj_wpifirma_settings',
                                'status' => 'off'
                            ),
                            'wp-wfirma' => array(
                                'name' => 'wFirma',
                                'settings' => array(
                                    'wf_login' => array(
                                        'label' => 'Login',
                                        'desc' => 'Login (adres email) do systemu wfirma.pl',
                                    ),
                                    'wf_pass' => array(
                                        'label' => 'Hasło',
                                        'desc' => 'Hasło do systemu wfirma.pl',
                                        'type' => 'password',
                                    ),
                                    'wf_company_id' => array(
                                        'label' => 'ID Firmy',
                                        'desc' => 'Pozostaw pole puste, gdy posiadasz jedną firmę w wFirma',
                                    ),
                                    'receipt' => array(
                                        'label' => 'Wystawiaj też paragony',
                                        'type' => 'checkbox'
                                    ),
                                    'auto_sent' => array(
                                        'label' => 'Automatyczna wysyłka faktur',
                                        'desc' => 'Zaznacz, jeżeli faktury i rachunki maja być wysyłane automatycznie e-mailem do klienta',
                                        'type' => 'checkbox'
                                    ),
                                    'auto_sent_receipt' => array(
                                        'label' => 'Automatyczna wysyłka paragonów',
                                        'desc' => 'Zaznacz, jeżeli paragony maja być wysyłane automatycznie e-mailem do klienta',
                                        'type' => 'checkbox',
                                    ),
                                ),
                                'save_to' => 'bpmj_wpwf_settings',
                                'status' => 'off'
                            ),
                            'wp-infakt' => array(
                                'name' => 'Infakt',
                                'settings' => array(
                                    'infakt_api_key' => array(
                                        'label' => 'Klucz API',
                                        'desc' => 'Ustawienia -> Inne opcje -> API',
                                    ),
                                    'auto_sent' => array(
                                        'label' => 'Automatyczna wysyłka faktur',
                                        'desc' => 'Zaznacz, jeżeli dokumenty sprzedaży maja być wysyłane automatycznie e-mailem do klienta',
                                        'type' => 'checkbox',
                                    ),
                                ),
                                'save_to' => 'bpmj_wpinfakt_settings',
                                'status' => 'off',
                            ),
                            'wp-taxe' => array(
                                'name' => 'Taxe',
                                'settings' => array(
                                    'taxe_login' => array(
                                        'label' => 'Login',
                                    ),
                                    'taxe_api_key' => array(
                                        'label' => 'Klucz API',
                                        'desc' => 'CRM -> Usługi API',
                                    ),
                                    'receipt' => array(
                                        'label' => 'Wystawiaj też paragony',
                                        'type' => 'checkbox',
                                    ),
                                    'auto_sent' => array(
                                        'label' => 'Automatyczna wysyłka faktur',
                                        'desc' => 'Zaznacz, jeżeli faktury i rachunki maja być wysyłane automatycznie e-mailem do klienta. <br /><strong>Uwaga:</strong> upewnij się, że na stronie <a href="https://panel.taxe.pl/email-szablony/">Szablony wiadomości e-mail</a> masz ustawiony szablon domyślny dla czynności &quot;Wysyłka dokumentu w wiadomości e-mail&quot;.',
                                        'type' => 'checkbox',
                                    ),
                                    'auto_sent_receipt' => array(
                                        'label' => 'Automatyczna wysyłka paragonów',
                                        'desc' => 'Zaznacz, jeżeli paragony maja być wysyłane automatycznie e-mailem do klienta. <br /><strong>Uwaga:</strong> upewnij się, że na stronie <a href="https://panel.taxe.pl/email-szablony/">Szablony wiadomości e-mail</a> masz ustawiony szablon domyślny dla czynności &quot;Wysyłka dokumentu w wiadomości e-mail&quot;.',
                                        'type' => 'checkbox',
                                    ),
                                ),
                                'save_to' => 'bpmj_wptaxe_settings',
                                'status' => 'off',
                            ),
                        ),
                    ),
                )),
                /**
                 * Emaile
                 */
                'courses_emails' => apply_filters('bpmj_eddcm_emails_settings', array(
                    array(
                        'name' => 'from_name',
                        'label' => __('From Name', BPMJ_EDDCM_DOMAIN),
                        'desc' => __('This should probably be your site or shop name.', BPMJ_EDDCM_DOMAIN),
                        'type' => 'text',
                        'save_to' => 'edd_settings',
                    ),
                    array(
                        'name' => 'from_email',
                        'label' => __('From Email', BPMJ_EDDCM_DOMAIN),
                        'desc' => __('This will act as the "from" and "reply-to" address.', BPMJ_EDDCM_DOMAIN),
                        'type' => 'text',
                        'save_to' => 'edd_settings',
                    ),
                    array(
                        'name' => 'purchase_subject',
                        'label' => __('After Purchase Email Subject', BPMJ_EDDCM_DOMAIN),
                        'desc' => $this->translator->translate('settings.messages.purchase_subject'),
                        'type' => 'text',
                        'save_to' => 'edd_settings',
                    ),
                    array(
                        'name' => 'purchase_heading',
                        'label' => __('After Purchase Email Heading', BPMJ_EDDCM_DOMAIN),
                        'desc' => $this->translator->translate('settings.messages.purchase_heading'),
                        'type' => 'text',
                        'save_to' => 'edd_settings',
                    ),
                    array(
                        'name' => 'purchase_receipt',
                        'label' => __('After Purchase Email Content', BPMJ_EDDCM_DOMAIN),
                        'desc' => __("Enter the text that is sent as purchase receipt email to users after completion of a successful purchase. HTML is accepted. Available template tags:<br><code>{download_list}</code> - A list of download links for each download purchased<br><code>{file_urls}</code> - A plain-text list of download URLs for each download purchased<br><code>{name}</code> - The buyer's first name<br><code>{fullname}</code> - The buyer's full name, first and last<br><code>{username}</code> - The buyer's user name on the site, if they registered an account<br><code>{user_email}</code> - The buyer's email address<br><code>{billing_address}</code> - The buyer's billing address<br><code>{date}</code> - The date of the purchase<br><code>{subtotal}</code> - The price of the purchase before taxes<br><code>{tax}</code> - The taxed amount of the purchase<br><code>{price}</code> - The total price of the purchase<br><code>{payment_id}</code> - The unique ID number for this purchase<br><code>{receipt_id}</code> - The unique ID number for this purchase receipt<br><code>{payment_method}</code> - The method of payment used for this purchase<br><code>{sitename}</code> - Your site name<br><code>{receipt_link}</code> - Adds a link so users can view their receipt directly on your website if they are unable to view it in the browser correctly.<br><code>{discount_codes}</code> - Adds a list of any discount codes applied to this purchase<br><code>{ip_address}</code> - The buyer's IP Address<br><code>{generated_discount_codes_details}</code> - Displays list of generated discount codes with details<br><code>{generated_discount_codes}</code> - Displays list of generated discount codes separated with comma<br>", BPMJ_EDDCM_DOMAIN),
                        'type' => 'wysiwyg',
                        'size' => '100%',
                        'save_to' => 'edd_settings',
                        'class' => 'hideable_setting'
                    ),
                    array(
                        'name' => 'bpmj_edd_arc_subject',
                        'label' => __('After User Registration Email Subject', BPMJ_EDDCM_DOMAIN),
                        'type' => 'text',
                        'save_to' => 'edd_settings',
                    ),
                    array(
                        'name' => 'bpmj_edd_arc_content',
                        'label' => __('After User Registration Email Content', BPMJ_EDDCM_DOMAIN),
                        'type' => 'wysiwyg',
                        'size' => '100%',
                        'save_to' => 'edd_settings',
                        'class' => 'hideable_setting'
                    ),
                    Settings_Const::PAYMENT_REMINDERS_ENABLED => [
                        'name' => Settings_Const::PAYMENT_REMINDERS_ENABLED,
                        'label' => $this->translator->translate('settings.messages.payment_reminders'),
                        'type' => 'checkbox_one_empty',
                        'desc' => ($this->subscription->get_plan() !== Subscription_Const::PLAN_PRO) ? $this->translator->translate('settings.messages.payment_reminders.notice') : $this->translator->translate('settings.messages.payment_reminders.desc'),
                        'class' => 'recovery_shopping_cart_checkbox',
                        'save_to' => Settings_Const::PAYMENT_REMINDERS,
                        'default' => false,
                        'disabled' => ($this->subscription->get_plan() !== Subscription_Const::PLAN_PRO) ? true : false
                    ],
                    Settings_Const::PAYMENT_REMINDERS_NUMBER_DAYS => [
                        'name' => Settings_Const::PAYMENT_REMINDERS_NUMBER_DAYS,
                        'label' => $this->translator->translate('settings.messages.payment_reminders.number_days'),
                        'type' => 'number',
                        'class' => 'recovery_shopping_cart_hideable_setting',
                        'save_to' => Settings_Const::PAYMENT_REMINDERS,
                        'default' => 7,
                        'min' => 1
                    ],
                    Settings_Const::PAYMENT_REMINDERS_MESSAGE_SUBJECT => [
                        'name' => Settings_Const::PAYMENT_REMINDERS_MESSAGE_SUBJECT,
                        'label' => $this->translator->translate('settings.messages.payment_reminders.message_subject'),
                        'type' => 'text',
                        'class' => 'recovery_shopping_cart_hideable_setting',
                        'save_to' => Settings_Const::PAYMENT_REMINDERS
                    ],
                    Settings_Const::PAYMENT_REMINDERS_MESSAGE_CONTENT => [
                        'name' => Settings_Const::PAYMENT_REMINDERS_MESSAGE_CONTENT,
                        'label' => $this->translator->translate('settings.messages.payment_reminders.message_content'),
                        'desc' => sprintf($this->translator->translate('settings.messages.payment_reminders.message_content.desc'), '<br>','<code>','</code>'),
                        'type' => 'wysiwyg',
                        'size' => '100%',
                        'class' => 'recovery_shopping_cart_hideable_setting',
                        'save_to' => Settings_Const::PAYMENT_REMINDERS

                    ],
                    array(
                        'name' => 'bpmj_eddcm_admin_notice_policy',
                        'label' => $this->translator->translate('settings.sections.general.new_sale_notifications'),
                        'type' => 'select',
                        'default' => 'disabled',
                        'options' => array(
                            'disabled' => __('Disabled', BPMJ_EDDCM_DOMAIN),
                            'comments' => __('Only orders with comments', BPMJ_EDDCM_DOMAIN),
                            'all' => __('All orders', BPMJ_EDDCM_DOMAIN),
                        ),
                    ),
                    'admin_notice_emails' => array(
                        'name' => 'admin_notice_emails',
                        'label' => __('E-mail addresses for sales notifications', BPMJ_EDDCM_DOMAIN),
                        'desc' => __('Enter the email address(es) that should receive a notification anytime a sale is made, one per line', BPMJ_EDDCM_DOMAIN),
                        'type' => 'textarea',
                        'default' => get_bloginfo('admin_email'),
                        'save_to' => 'edd_settings',
                        'sanitize_callback' => array($this, 'sanitize_for_post_allowed_html_tags')
                    ),
                )),
                /**
                 * Mailers
                 */
                'courses_mailers' => WPI()->packages->has_access_to_feature(Packages::FEAT_MAILERS) ? apply_filters('bpmj_eddcm_mailers_settings', array(
                    'mailer_methods' => array(
                        'name' => 'mailer_methods',
                        'label' => __('Mailers integrations', BPMJ_EDDCM_DOMAIN),
                        'type' => 'bpmj_groups',
                        'groups_type' => 'mailers',
                        'options' => array(
                            // Mailchimp
                            'edd-mailchimp' => array(
                                'name' => 'MailChimp',
                                'save_to' => 'edd_settings',
                                'settings' => array(
                                    'eddmc_api' => array(
                                        'label' => __('MailChimp API Key', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Enter your MailChimp API key', BPMJ_EDDCM_DOMAIN),
                                    ),
                                    'eddmc_list' => array(
                                        'label' => __('Choose a list', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Select the list you wish to subscribe buyers to', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'select',
                                        'options' => bpmj_wpid_get_mailer_data('edd-mailchimp'),
                                    ),
                                    'eddmc_show_checkout_signup' => array(
                                        'label' => __('Show Signup on Checkout', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Allow customers to sign up for the list selected below during checkout?', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'checkbox',
                                    ),
                                    'eddmc_label' => array(
                                        'label' => __('Checkout Label', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('This is the text shown next to the signup option', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'text',
                                        'size' => 'regular',
                                    ),
                                    'eddmc_double_opt_in' => array(
                                        'label' => __('Double Opt-In', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('When checked, users will be sent a confirmation email after signing up, and will only be added once they have confirmed the subscription.', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'checkbox',
                                    )
                                ),
                                'status' => 'off'
                            ),
                            // ActiveCampaign
                            'edd-activecampaign' => array(
                                'name' => 'ActiveCampaign',
                                'save_to' => 'edd_settings',
                                'settings' => bpmj_wpid_get_mailer_settings('edd-activecampaign'),
                                'status' => 'off'
                            ),
                            // GetResponse
                            'edd-getresponse' => array(
                                'name' => 'GetResponse',
                                'save_to' => 'edd_settings',
                                'settings' => bpmj_wpid_get_mailer_settings('edd-getresponse'),
                                'status' => 'off'
                            ),
                            // FreshMail
                            'edd-freshmail' => array(
                                'name' => 'FreshMail',
                                'save_to' => 'edd_settings',
                                'settings' => bpmj_wpid_get_mailer_settings('edd-freshmail'),
                                'status' => 'off'
                            ),
                            // iPresso
                            'edd-ipresso' => array(
                                'name' => 'iPresso',
                                'save_to' => 'edd_settings',
                                'settings' => bpmj_wpid_get_mailer_settings('edd-ipresso'),
                                'status' => 'off',
                            ),
                            // MailerLite
                            'edd-mailerlite' => array(
                                'name' => 'MailerLite',
                                'save_to' => 'edd_settings',
                                'settings' => array(
                                    'bpmj_edd_ml_api' => array(
                                        'label' => __('MailerLite API Key', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Enter your MailerLite API key', BPMJ_EDDCM_DOMAIN),
                                    ),
                                    'bpmj_edd_ml_group' => array(
                                        'label' => __('Choose a group', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Select the list you wish to subscribe buyers to', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'select',
                                        'options' => bpmj_wpid_get_mailer_data('edd-mailerlite')
                                    ),
                                    'bpmj_edd_ml_show_checkout_signup' => array(
                                        'label' => __('Show Signup on Checkout', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Allow customers to sign up for the list selected below during checkout?', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'checkbox',
                                    ),
                                    'bpmj_edd_ml_label' => array(
                                        'label' => __('Checkout Label', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('This is the text shown next to the signup option', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'text',
                                        'size' => 'regular',
                                    ),
                                    'bpmj_edd_ml_double_opt_in' => array(
                                        'label' => __('Double Opt-In', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('When checked, users will be sent a confirmation email after signing up, and will only be added once they have confirmed the subscription.', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'checkbox',
                                    )
                                ),
                                'status' => 'off'
                            ),
                            // SalesManago
                            'edd-salesmanago' => array(
                                'name' => 'SALESmanago',
                                'save_to' => 'edd_settings',
                                'settings' => array(
                                    'salesmanago_owner' => array(
                                        'label' => 'Adres email konta SALESmanago',
                                        'desc' => 'Adres email na który zarejestrowane jest Twoje konto SAELESmanago.',
                                    ),
                                    'salesmanago_endpoint' => array(
                                        'label' => 'Endpoint',
                                        'desc' => 'Indentyfikator Twojego serwera (endpoint) z panelu SALESmanago (Ustawienia->Integracja).',
                                    ),
                                    'salesmanago_client_id' => array(
                                        'label' => 'ID Klienta',
                                        'desc' => 'Twoje ID Klienta z panelu SALESmanago (Ustawienia->Integracja).',
                                    ),
                                    'salesmanago_api_secret' => array(
                                        'label' => 'API Secret',
                                        'desc' => 'Kod API Secret z panelu SALESmanago (Ustawienia->Integracja).',
                                    ),
                                    'salesmanago_tracking_code' => array(
                                        'label' => 'Kod śledzący',
                                        'desc' => 'Zaznacz aby umieścić kod śledzący.',
                                        'type' => 'checkbox',
                                    ),
                                    'salesmanago_checkout_mode' => array(
                                        'label' => 'Pole zapisu',
                                        'desc' => 'Zaznacz aby pole zapisu zostało pokazane.',
                                        'type' => 'checkbox',
                                    ),
                                    'salesmanago_checkout_label' => array(
                                        'label' => 'Opis pola zapisu',
                                        'desc' => 'Ten tekst wyświetli się obok opcji zapisu w podsumowaniu koszyka.',
                                    ),
                                    'bpmj_eddsm_salesmanago_tags' => array(
                                        'label' => 'Tagi dopisywane do użytkownika',
                                        'desc' => 'Wpisz tagi (oddzielając je przecinkiem), które mają być dodane do kontaktu w panelu SALESmanago po każdym zakupie.<br>Tagi te będą dodane tylko jeżeli będzie wyświetlone i zaznaczone pole zapisu w podsumowaniu koszyka.<br>Tagi produktów będą dodane niezależnie.',
                                        'type' => 'salesmanago-tags',
                                        'size' => '100%',
                                    )
                                ),
                                'status' => 'off'
                            ),

                            // Interspire
                            'edd-interspire' => array(
                                'name' => 'Interspire',
                                'save_to' => 'edd_settings',
                                'settings' => array(
                                    'bpmj_edd_in_username' => array(
                                        'label' => __('Interspire Username', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Enter your Interspire Username', BPMJ_EDDCM_DOMAIN),
                                    ),
                                    'bpmj_edd_in_token' => array(
                                        'label' => __('Interspire Token', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Enter Interspire Token', BPMJ_EDDCM_DOMAIN),
                                    ),
                                    'bpmj_edd_in_xmlEndpoint' => array(
                                        'label' => __('Interspire XML path', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Enter your full Interspire XML path', BPMJ_EDDCM_DOMAIN),
                                    ),
                                    'bpmj_edd_in_contact_list' => array(
                                        'label' => __('Choose Contact List', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Select the list you wish to subscribe buyers to', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'select',
                                        'options' => bpmj_wpid_get_mailer_data('edd-interspire'),
                                    ),
                                    'bpmj_edd_in_show_checkout_signup' => array(
                                        'label' => __('Show Signup on Checkout', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('Allow customers to signup for the list selected below during checkout?', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'checkbox',
                                    ),
                                    'bpmj_edd_in_label' => array(
                                        'label' => __('Checkout Label', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('This is the text shown next to the signup option', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'text',
                                        'size' => 'regular',
                                    ),
                                    'bpmj_edd_in_double_opt_in' => array(
                                        'label' => __('Double Opt-In', BPMJ_EDDCM_DOMAIN),
                                        'desc' => __('When checked, users will be sent a confirmation email after signing up, and will only be added once they have confirmed the subscription.', BPMJ_EDDCM_DOMAIN),
                                        'type' => 'checkbox',
                                    )
                                ),
                                'status' => 'off'
                            ),
                            // ConvertKit
                            'edd-convertkit' => array(
                                'name' => 'ConvertKit',
                                'save_to' => 'edd_settings',
                                'settings' => bpmj_wpid_get_mailer_settings('edd-convertkit'),
                                'status' => 'off',
                            ),
                        )
                    )
                )) : array(
                    array(
                        'name' => 'mailers_disabled_info',
                        'label' => __('Upgrade needed', BPMJ_EDDCM_DOMAIN),
                        'desc' => WPI()->packages->feature_not_available_message(Packages::FEAT_MAILERS, __('In order to use mailer systems integration, you need to upgrade your license to level: "%s"', BPMJ_EDDCM_DOMAIN)),
                        'type' => 'hidden',
                    )
                ),
                /**
                 * Subskrybcje
                 */
                'courses_subscriptions' => $this->get_course_subscriptions_settings(),
                /**
                 * Layout
                 */
                'courses_layout' => apply_filters('bpmj_eddcm_layout', [
                    [
                        'name' => 'template',
                        'label' => __('Custom template', BPMJ_EDDCM_DOMAIN),
                        'desc' => __('Choose which template you would like to use.', BPMJ_EDDCM_DOMAIN),
                        'default' => 'scarlet',
                        'type' => $this->templates_settings_handler->should_template_option_be_visible_on_the_settings_page('template') ? 'select' : 'hidden',
                        'options' => apply_filters('bpmj_eddcm_layout_options', array(
                            'default' => __('Classic', BPMJ_EDDCM_DOMAIN),
                            'off' => __('Turned Off', BPMJ_EDDCM_DOMAIN)
                        )),
                        'class' => $this->templates_settings_handler->should_template_option_be_visible_on_the_settings_page('template') ? '' : 'hidden'
                    ],
                    [
                        'name' => 'override_all',
                        'label' => __('Override mode', BPMJ_EDDCM_DOMAIN),
                        'desc' => __('Enable this mode if you want to convert your site into a "courses only" platform. Your chosen WordPress theme will be completely overridden. Don\'t enable this option if you want to use WP Idea courses as a side feature of your site.', BPMJ_EDDCM_DOMAIN),
                        'type' => $this->templates_settings_handler->should_template_option_be_visible_on_the_settings_page('override_all') ? 'override_all' : 'hidden',
                        'options' => $all_pages_new,
                        'class' => $this->templates_settings_handler->should_template_option_be_visible_on_the_settings_page('override_all') ? '' : 'hidden',
                    ],
                ]),
                'courses_order' => apply_filters('bpmj_eddcm_order_settings', $this->get_order_settings()),
                'courses_gift' => apply_filters('bpmj_eddcm_gift_settings', $this->get_gift_settings()),
                'courses_advanced' => apply_filters('bpmj_eddcm_advanced_settings', $this->get_advanced_settings()),
                'courses_certificates' => apply_filters('bpmj_eddcm_certificates_settings', $this->get_certificates_settings()),
                'analytics_and_scripts' => apply_filters('bpmj_eddcm_analytics_and_scripts_settings', $this->get_analytics_and_scripts_settings())
            );
        }

        public function payments_allowed_only_in_poland()
        {
            return array(
                'edd-tpay', 'edd-przelewy24', 'edd-dotpay', 'edd-payu', 'edd-paynow',
            );
        }

        function sanitize_license_key($license)
        {
            global $courses_manager_settings;

            $license = trim($license);

            if (!empty($courses_manager_settings['license_key']) && $courses_manager_settings['license_key'] === $license) {
                return $license;
            }

            $license_status = bpmj_eddcm_check_license($license, BPMJ_EDDCM_NAME, 'bpmj_eddcm_license_status');
            if ('valid' === $license_status) {
                /*
                 * We reset the package only if the user has entered another valid key
                 */
                delete_option('wpidea_package');

                update_option('bmpj_wpidea_vkey', $license);

                $this->events->emit(Event_Name::NEW_VALID_LICENSE_HAS_BEEN_ENTERED, $license);

            }

            if(false === $license_status) {
                update_option('bpmj_wpidea_license_connection_error', true);
            }
            else {
                delete_option( 'bpmj_wpidea_license_connection_error' );
            }

            return $license;
        }

        function sanitize_for_post_allowed_html_tags($content)
        {
            return wp_kses_post($content);
        }

        /**
         * Save WP Idea Settings
         */
        public function settings_save()
        {
            $this->reload_mailers();

            $template = !empty($_POST[$this->setting_slug]['template']) ? $_POST[$this->setting_slug]['template'] : '';
            $template_settings_key = $this->layout_template_settings_slug . '-' . $template;
            $template_settings = $_POST[$template_settings_key] ?? null;
            if (!empty($template_settings)) {
                $template_settings = $this->remove_slashes_from_css($template_settings);
                $settings_api = $this->get_layout_template_settings_api($template);
                $sane_data = $settings_api->sanitize_options($template_settings);
                $options = get_option($this->layout_template_settings_slug);
                if (false === $options) {
                    $options = array();
                    add_option($this->layout_template_settings_slug, array());
                }
                $options[$template] = apply_filters('bpmj_eddcm_layout_filter_settings', $sane_data);
                update_option($this->layout_template_settings_slug, $options);
                do_action('bpmj_eddcm_layout_template_settings_save', $sane_data);
                bpmj_eddcm_reload_layout_template_settings();
            }

            do_action('wpi_after_save_settings');
        }

        private function remove_slashes_from_css(array $template_settings): array
        {
            $template_settings['css'] = stripslashes($template_settings['css'] ?? '');

            return $template_settings;
        }

        /**
         * Save EDD Settings
         *
         * @param array $new_value
         * @param array $old_value
         */
        public function edd_settings_save($new_value, $old_value)
        {
            if ($this->settings_api->has_option_key_changed($new_value, $old_value, 'bpmj_eddact_api_token')) {
                $this->delete_activecampaign_transients();
            }
            if ($this->settings_api->has_option_key_changed($new_value, $old_value, 'edd_convertkit_api')) {
                $this->delete_convertkit_transients();
            }
            if ($this->settings_api->has_option_key_changed($new_value, $old_value, 'eddmc_api')) {
                $this->delete_mailchimp_transients();
            }
            if ($this->settings_api->has_option_key_changed($new_value, $old_value, 'bpmj_eddfm_api_key')) {
                $this->delete_freshmail_transients();
            }
            if ($this->settings_api->has_option_key_changed($new_value, $old_value, 'bpmj_eddres_token')) {
                $this->delete_getresponse_transients();
            }
            if ($this->settings_api->has_option_key_changed($new_value, $old_value, 'bpmj_edd_ml_api')) {
                $this->delete_mailerlite_transients();
            }
        }

        /**
         * Fired before saving the option to the DB
         */
        public function settings_prepare_pre_update($new_value, $old_value)
        {
            global $wp_settings_errors;

            if (!is_array($old_value)) {
                // first-time save
                $old_value = array();
            }
            if (!is_array($new_value)) {
                // this shouldn't be possible
                return;
            }
            if ($this->settings_api->has_option_key_changed($new_value, $old_value, 'override_all')) {
                $new_override_all = $new_value['override_all'];
                if ('off' === $new_override_all) {
                    $old_show_on_front = !empty($old_value['_old_show_on_front']) ? $old_value['_old_show_on_front'] : false;
                    if ($old_show_on_front) {
                        update_option('show_on_front', $old_show_on_front);
                        update_option('page_on_front', '');
                        unset($new_value['_old_show_on_front']);
                    }
                } else if ('on' === $new_override_all && $new_value['course_list_page']) {
                    $old_show_on_front = get_option('show_on_front');
                    if ('page' !== $old_show_on_front) {
                        $new_value['_old_show_on_front'] = $old_show_on_front;
                        update_option('show_on_front', 'page');
                        update_option('page_on_front', $new_value['course_list_page']);
                    }
                }
            }

            // zapisanie do EDD ustawoień włączających lub wyłączających faktury w zależności od ustawień WP Idea (w EDD jest to per bramnka)

            $invoices = $new_value['invoices'] ?? null;
            if ('on' === $invoices) {
                $options = get_option('edd_settings');
                $options['edd_id_gateways'] = WPI()->diagnostic->get_gateways_for_invoices();
                update_option('edd_settings', $options);
            } else if ('off' === $invoices) {
                $options = get_option('edd_settings');
                $options['edd_id_gateways'] = array();
                update_option('edd_settings', $options);
            }

            // usunięcie niepotrzebnego notice generowanego przez edd przy zapisywaniu opcji do edd_settings

            if (is_array($wp_settings_errors) && !empty($wp_settings_errors)) {
                foreach ($wp_settings_errors as $k => $wpse) {
                    if (is_array($wpse) && in_array('edd-notices', $wpse)) {
                        unset($wp_settings_errors[$k]);
                    }
                }
            }

            $favicon_url =  $new_value["favicon"] ?? null;
            $old_favicon_value = $old_value["favicon"] ?? null;

            if( $favicon_url !== $old_favicon_value ){
                $id_post_favicon = attachment_url_to_postid($favicon_url);
                $new_value[WP_Settings_Handler::WP_SETTING_SITE_ICON] = $id_post_favicon;
            }

            $this->wp_settings_handler->maybe_update_wordpress_settings($new_value, $old_value);

            return $new_value;
        }

        /**
         * Add and Edit fields
         */
        public function save_renewal()
        {

            if ($_POST && isset($_POST['bpmj_eddpc_action']) && WPI()->packages->has_access_to_feature(Packages::FEAT_SUBSCRIPTIONS)) {

                if (!is_admin()) {
                    return;
                }

                if (!current_user_can('manage_shop_settings')) {
                    wp_die(__('You do not have permission to add reminders', BPMJ_EDDCM_DOMAIN), __('Error', BPMJ_EDDCM_DOMAIN), array('response' => 401));
                }

                if (!wp_verify_nonce($_POST['bpmj_eddpc_renewal_nonce'], 'bpmj_eddpc_renewal_nonce')) {
                    wp_die(__('Invalid verification', BPMJ_EDDCM_DOMAIN), __('Error', BPMJ_EDDCM_DOMAIN), array('response' => 401));
                }


                $action = $_POST['bpmj_eddpc_action'];
                $renewal_options = get_option('bmpj_eddpc_renewal');

                if (empty($renewal_options))
                    $renewal_options = array();

                switch ($action) {

                    // Add new renewal
                    case 'add':

                        $subject = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : __('Access to protected content will expire soon', 'bmpj_eddpc');
                        $period = bpmj_eddpc_renewal_period_combine_inputs(1, 'months', '-');
                        $message = isset($_POST['message']) ? wp_kses(stripslashes($_POST['message']), wp_kses_allowed_html('post')) : false;
                        $type = isset($_POST['bpmj_eddpc_renewal_type']) ? $_POST['bpmj_eddpc_renewal_type'] : 'renewal';
                        $charge_mode = isset($_POST['charge-mode']) ? $_POST['charge-mode'] : array();

                        $renewal_options[] = array(
                            'subject' => $subject,
                            'message' => $message,
                            'send_period' => $period,
                            'type' => $type,
                            'charge_modes' => $charge_mode,
                        );

                        update_option('bmpj_eddpc_renewal', $renewal_options);
                        break;


                    // Edit renewal
                    case 'edit':

                        $id = isset($_POST['id']) ? $_POST['id'] : false;
                        if ($id !== false) {
                            $subject = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : __('Access to protected content will expire soon', 'bmpj_eddpc');
                            $period = bpmj_eddpc_renewal_period_combine_inputs(1, 'months', '-');
                            $message = isset($_POST['message']) ? wp_kses(stripslashes($_POST['message']), wp_kses_allowed_html('post')) : false;
                            $type = isset($_POST['bpmj_eddpc_renewal_type']) ? $_POST['bpmj_eddpc_renewal_type'] : 'renewal';
                            $charge_mode = isset($_POST['charge-mode']) ? $_POST['charge-mode'] : array();
                            $renewal_options[absint($id)] = array(
                                'subject' => $subject,
                                'message' => $message,
                                'send_period' => $period,
                                'type' => $type,
                                'charge_modes' => $charge_mode,
                            );
                            update_option('bmpj_eddpc_renewal', $renewal_options);
                        }
                        break;
                }

                wp_redirect(admin_url('admin.php?page=' . Admin_Menu_Item_Slug::SETTINGS . '&autofocus=messages'));
                exit;
            }
        }

        /**
         * Delete renewal
         */
        public function delete_renewal()
        {
            if (isset($_GET['wpid_action']) && $_GET['wpid_action'] == 'delete-renewal') {
                if (isset($_GET['renewal-id'])) {

                    $renewal_options = get_option('bmpj_eddpc_renewal');
                    $id = $_GET['renewal-id'];

                    unset($renewal_options[$id]);

                    update_option('bmpj_eddpc_renewal', $renewal_options);

                    wp_redirect(admin_url('admin.php?page=' . Admin_Menu_Item_Slug::SETTINGS . '&autofocus=messages'));
                    exit;
                }
            }
        }

        /**
         *
         * @param string $template
         * @return Settings_API
         */
        public function get_layout_template_settings_api($template)
        {
            $slug = $this->setting_slug . '-layout-template-settings';
            $template_options = $this->get_layout_template_settings_array($template);
            $template_settings_api = new Settings_API($slug . '-' . $template, true);
            $template_settings_api->set_detached_options($template_options);
            $template_root_dir = WPI()->templates->get_template_root_dir($template);
            if (file_exists($template_root_dir . '/template-config.php')) {
                $template_config = include $template_root_dir . '/template-config.php';
                if (!empty($template_config['settings'])) {
                    $template_settings_api->set_fields($template_config['settings']);
                }
            }
            $template_settings_api->settings_init();
            return $template_settings_api;
        }

        /**
         *
         * @param string $template
         * @return array
         */
        public function get_layout_template_settings_array($template)
        {
            $slug = $this->layout_template_settings_slug;
            $options = get_option($slug);
            if (false === $options) {
                $options = array();
                add_option($slug, array());
            }
            return isset($options[$template]) ? $options[$template] : array();
        }


        /**
         * Get custom css field from WP Idea template settings
         *
         * @param string $template_name Template name, eg. scarlet or default
         *
         * @return string
         */
        public function get_custom_css_field_value($template_name)
        {
            $layout_template_settings_api = $this->get_layout_template_settings_api($template_name);

            return $layout_template_settings_api->get_detached_option_value('css') ?: '';
        }

        public function hook_init_layout_settings()
        {
            add_action(sanitize_title($this->setting_slug) . '-form_top_courses_layout', array($this, 'display_before_layout_settings_info'));
            add_action(sanitize_title($this->setting_slug) . '-form_before_subsections_courses_layout', array($this, 'display_layout_settings_info'));
            add_action(sanitize_title($this->setting_slug) . '-form_before_subsections_courses_layout', array($this, 'hook_layout_settings_html'));
            add_action('wp_ajax_bpmj_eddcm_ajax_layout_settings', array($this, 'ajax_layout_settings'));
        }

        public function display_before_layout_settings_info(): void
        {
            $this->templates_guide->print_before_layout_settings_info();
        }

        public function display_layout_settings_info(): void
        {
            $this->templates_guide->print_layout_settings_info();
        }

        public function hook_layout_settings_html()
        {
            $display_colors_settings = $this->templates_settings_handler->should_color_settings_be_displayed();

            if (!$display_colors_settings) {
                return;
            }

            $ajax_action_nonce = wp_create_nonce('bpmj_eddcm_ajax_layout_settings');
            ?>
            <div id="courses_layout_additional_settings_div">
                <h2><?php _e('Template colors', BPMJ_EDDCM_DOMAIN) ?></h2>
                <?php
                    $this->templates_guide->print_color_settings_info();
                ?>

                <div class="settings"></div>
            </div>
            <script type="text/javascript">
                (function ($) {
                    $('#courses_layout *[name="wp_idea[template]"]').change(function () {
                        var template = $(this).val();
                        $('#courses_layout_additional_settings_div,#courses_layout_additional_settings_div *').off();
                        if ('off' === template) {
                            $('#courses_layout_additional_settings_div').hide();
                            $('*[id="wpuf-wp_idea[override_all]"]').closest('tr').hide();
                            return;
                        }
                        $('*[id="wpuf-wp_idea[override_all]"]').closest('tr').show();
                        $('#courses_layout_additional_settings_div .spinner').show();
                        $('#courses_layout_additional_settings_div .settings').html('');
                        $('#courses_layout_additional_settings_div').show();
                        $('#courses_layout_additional_settings_div .settings').load(ajaxurl, {
                            action: 'bpmj_eddcm_ajax_layout_settings',
                            token: '<?php echo $ajax_action_nonce ?>',
                            template: template
                        }, function (result) {
                            $('#courses_layout_additional_settings_div .spinner').hide();
                            if (result === '0') {
                                $('#courses_layout_additional_settings_div').hide();
                            }
                        });
                    });
                    $('#courses_layout *[name="wp_idea[template]"]').change();

                    $(document).ready(function () {
                        setup_lastname_checkboxes();
                    });

                    function setup_lastname_checkboxes() {
                        var hide_lname_chkbx = $('input[type="checkbox"][name="edd_settings[edd_id_hide_lname]"]');
                        var lname_required_chkbx = $('input[type="checkbox"][name="wp_idea[last_name_required]"]');

                        if (lname_required_chkbx.is(':checked')) {
                            disable_checkbox(hide_lname_chkbx);
                            return;
                        }

                        if (hide_lname_chkbx.is(':checked')) {
                            disable_checkbox(lname_required_chkbx);
                        }
                    }

                    $(document).on('change', 'input[type="checkbox"][name="wp_idea[last_name_required]"]', function () {
                        var hide_lname_chkbx = $('input[type="checkbox"][name="edd_settings[edd_id_hide_lname]"]');
                        if (this.checked) {
                            disable_checkbox(hide_lname_chkbx);
                        } else {
                            enable_checkbox(hide_lname_chkbx);
                        }
                    });

                    $(document).on('change', 'input[type="checkbox"][name="edd_settings[edd_id_hide_lname]"]', function () {
                        var lname_required_chkbx = $('input[type="checkbox"][name="wp_idea[last_name_required]"]');
                        if (this.checked) {
                            disable_checkbox(lname_required_chkbx);
                        } else {
                            enable_checkbox(lname_required_chkbx);
                        }
                    });

                    function disable_checkbox(checkbox) {
                        checkbox.prop('checked', false);
                        checkbox.attr('disabled', true);
                    }

                    function enable_checkbox(checkbox) {
                        checkbox.attr('disabled', false);
                    }
                }(jQuery));
            </script>
            <?php
        }

        public function ajax_layout_settings()
        {
            if (!check_ajax_referer('bpmj_eddcm_ajax_layout_settings', 'token')) {
                wp_die('0');
            }
            $template = basename($_REQUEST['template']);
            $file = WPI()->templates->get_template_root_dir($template) . '/template-widget.php';
            if (file_exists($file)) {
                include $file;
                wp_die();
            }
            wp_die('0');
        }

        public function get_view_settings()
        {
            $lesson_navigation_section_visible = $this->templates_settings_handler->should_template_option_be_visible_on_the_settings_page('lesson_navigation_section');
            $download_section_visible = $this->templates_settings_handler->should_template_option_be_visible_on_the_settings_page('download_section');
            $lesson_progress_section_visible = $this->templates_settings_handler->should_template_option_be_visible_on_the_settings_page('lesson_progress_section');

            return array(
                'download_section' => array(
                    'name' => 'download_section',
                    'label' => __('Default download section position', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('Where the download section of the lesson should be displayed by default. This setting can be overridden by lesson specific settings.', BPMJ_EDDCM_DOMAIN),
                    'type' => $download_section_visible ? 'select' : 'hidden',
                    'options' => $this->get_download_position_options(),
                    'default' => 'side',
                    'class' => $download_section_visible ? '' : 'hidden',
                ),
                'lesson_navigation_section' => array(
                    'name' => 'lesson_navigation_section',
                    'label' => __('Default lesson navigation section position', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('Where the lesson navigation section of the lesson should be displayed by default on a lesson page. This setting can be overridden by course-specific settings.', BPMJ_EDDCM_DOMAIN),
                    'type' => $lesson_navigation_section_visible ? 'select' : 'hidden',
                    'options' => $this->get_lesson_navigation_position_options(),
                    'default' => 'off',
                    'class' => $lesson_navigation_section_visible ? '' : 'hidden',
                ),
                'navigation_next_lesson_label' => array(
                    'name' => 'navigation_next_lesson_label',
                    'label' => __('Next lesson label', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('What the next lesson label should contain by default. This setting can be overridden by course-specific settings.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'radio_with_other',
                    'options' => $this->get_lesson_navigation_label_options(true),
                    'default' => 'lesson',
                ),
                'navigation_previous_lesson_label' => array(
                    'name' => 'navigation_previous_lesson_label',
                    'label' => __('Previous lesson label', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('What the previous lesson label should contain by default. This setting can be overridden by course-specific settings.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'radio_with_other',
                    'options' => $this->get_lesson_navigation_label_options(false),
                    'default' => 'lesson',
                ),
                'inaccessible_lesson_display' => array(
                    'name' => 'inaccessible_lesson_display',
                    'label' => __('Inaccessible lesson display', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('How modules and lessons that are not yet accessible should be displayed to the user in course panel. This setting can be overridden by course-specific settings.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'radio',
                    'options' => array(
                        'visible' => __('Always visible', BPMJ_EDDCM_DOMAIN),
                        'grayed' => __('Visible, grayed out', BPMJ_EDDCM_DOMAIN),
                        'hidden' => __('Hidden', BPMJ_EDDCM_DOMAIN),
                    ),
                    'default' => 'visible',
                ),
                'progress_tracking' => array(
                    'name' => 'progress_tracking',
                    'label' => __('Progress tracking', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('Check to enable course progress tracking. This setting can be overridden by course-specific settings.', BPMJ_EDDCM_DOMAIN) .
                        (WPI()->packages->no_access_to_feature(Packages::FEAT_PROGRESS_TRACKING)
                            ? '<br /><span class="text-danger">' . WPI()->packages->feature_not_available_message(Packages::FEAT_PROGRESS_TRACKING, __('In order to use course progress tracking, you need to upgrade your license to level: "%s"', BPMJ_EDDCM_DOMAIN)) . '</span>'
                            : ''),
                    'type' => 'checkbox',
                    'disabled' => WPI()->packages->no_access_to_feature(Packages::FEAT_PROGRESS_TRACKING),
                ),
                'progress_forced' => array(
                    'name' => 'progress_forced',
                    'label' => __('Linear progress', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('When enabled, the user needs to mark a lesson as finished to continue to the next. This setting can be overridden by course-specific settings.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'radio',
                    'options' => array(
                        'enabled' => __('Enabled', BPMJ_EDDCM_DOMAIN),
                        'disabled' => __('Disabled', BPMJ_EDDCM_DOMAIN),
                    ),
                    'default' => 'disabled',
                    'disabled' => WPI()->packages->no_access_to_feature(Packages::FEAT_PROGRESS_TRACKING)
                ),
                'auto_progress' => array(
                    'name' => 'auto_progress',
                    'label' => __('Autocheck progress', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('When enabled, lesson will automatically be marked as finished when user clicks on the "Next lesson" button.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox',
                    'disabled' => WPI()->packages->no_access_to_feature(Packages::FEAT_PROGRESS_TRACKING)
                ),
                'lesson_progress_section' => array(
                    'name' => 'lesson_progress_section',
                    'label' => __('Default progress section position', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('Where the progress section of the lesson should be displayed by default. This setting can be overridden by lesson specific settings.', BPMJ_EDDCM_DOMAIN),
                    'type' => $lesson_progress_section_visible ? 'select' : 'hidden',
                    'options' => $this->get_lesson_progress_position_options(),
                    'default' => 'side',
                    'class' => $lesson_progress_section_visible ? '' : 'hidden',
                ),
                array(
                    'name' => 'enable_responsive_videos',
                    'label' => __('Responsive videos', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('Check to enable responsive (fluid) video embeds on WP Idea pages.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox',
                ),
            );
        }

        public function get_download_position_options($add_default = false)
        {
            $options = array();
            if ($add_default) {
                $options['default'] = __('Use default settings', BPMJ_EDDCM_DOMAIN);
            }
            return array_merge($options, array(
                'side' => __('In a box on the side', BPMJ_EDDCM_DOMAIN),
                'below' => __('In the separate section below', BPMJ_EDDCM_DOMAIN),
            ));
        }

        public function get_lesson_progress_position_options($add_default = false)
        {
            $options = array();
            if ($add_default) {
                $options['default'] = __('Use default settings', BPMJ_EDDCM_DOMAIN);
            }
            return array_merge($options, array(
                'side' => __('In a box on the side', BPMJ_EDDCM_DOMAIN),
                'below' => __('In the separate section below', BPMJ_EDDCM_DOMAIN),
            ));
        }

        public function get_lesson_navigation_position_options($add_default = false)
        {
            $options = array();
            if ($add_default) {
                $options['default'] = __('Use default settings', BPMJ_EDDCM_DOMAIN);
            }
            return array_merge($options, array(
                'off' => __('Disable', BPMJ_EDDCM_DOMAIN),
                'side' => __('In a box on the side', BPMJ_EDDCM_DOMAIN),
                'below' => __('In the separate section below', BPMJ_EDDCM_DOMAIN),
            ));
        }

        public function get_lesson_navigation_label_options($next)
        {
            return array(
                'lesson' => $next ? __('Text &quot;Next lesson&quot;', BPMJ_EDDCM_DOMAIN) : __('Text &quot;Previous lesson&quot;', BPMJ_EDDCM_DOMAIN),
                'lesson_title' => $next ? __('Next lesson\'s title', BPMJ_EDDCM_DOMAIN) : __('Previous lesson\'s title', BPMJ_EDDCM_DOMAIN),
                'other' => __('Other', BPMJ_EDDCM_DOMAIN),
            );
        }

        public function get_list_settings()
        {
            return array(
                'list_number' => array(
                    'name' => 'list_number',
                    'label' => __('Items on page', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('How many items to display on a single page.', BPMJ_EDDCM_DOMAIN),
                    'type' => $this->templates_settings_handler->should_template_option_be_visible_on_the_settings_page('list_number') ? 'number' : 'hidden',
                    'default' => '9',
                    'class' => $this->templates_settings_handler->should_template_option_be_visible_on_the_settings_page('list_number') ? '' : 'hidden',
                ),
                'list_price' => array(
                    'name' => 'list_price',
                    'label' => __('Show the price', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox_yes_no',
                    'default' => 'no',
                ),
                'list_price_button' => array(
                    'name' => 'list_price_button',
                    'label' => __('Show the price in &quot;add to cart&quot; button', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox_yes_no',
                    'default' => 'yes',
                ),
                'list_excerpt' => array(
                    'name' => 'list_excerpt',
                    'label' => __('Show the abbreviated description', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox_yes_no',
                    'default' => 'yes',
                ),
                'list_full_content' => array(
                    'name' => 'list_full_content',
                    'label' => __('Show the full course description', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox_yes_no',
                    'default' => 'no',
                ),
                'list_buy_button' => array(
                    'name' => 'list_buy_button',
                    'label' => __('Show the buy button', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox_yes_no',
                    'default' => 'yes',
                ),
                'list_pagination' => array(
                    'name' => 'list_pagination',
                    'label' => __('Show pagination', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox_yes_no',
                    'default' => 'yes',
                ),
                'list_columns' => array(
                    'name' => 'list_columns',
                    'label' => __('Grid columns', BPMJ_EDDCM_DOMAIN),
                    'type' => 'select',
                    'options' => array_combine(range(1, 6), range(1, 6)),
                    'default' => '3',
                ),
                'list_thumbnails' => array(
                    'name' => 'list_thumbnails',
                    'label' => __('Show thumbnails', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox_true_false',
                    'default' => 'true',
                ),
                'list_details_button' => array(
                    'name' => 'list_details_button',
                    'label' => __('Show details', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox_true_false',
                    'default' => 'false',
                ),
                'list_orderby' => array(
                    'name' => 'list_orderby',
                    'label' => __('Sort by', BPMJ_EDDCM_DOMAIN),
                    'type' => 'select',
                    'default' => 'post_date',
                    'options' => array(
                        'post_date' => __('Course publish date', BPMJ_EDDCM_DOMAIN),
                        'id' => __('Course id', BPMJ_EDDCM_DOMAIN),
                        'title' => __('Course title', BPMJ_EDDCM_DOMAIN),
                        'price' => __('Course price', BPMJ_EDDCM_DOMAIN),
                        'random' => __('Random', BPMJ_EDDCM_DOMAIN),
                    ),
                ),
                'list_order' => array(
                    'name' => 'list_order',
                    'label' => __('Sort order', BPMJ_EDDCM_DOMAIN),
                    'type' => 'select',
                    'default' => 'DESC',
                    'options' => array(
                        'DESC' => __('Descending', BPMJ_EDDCM_DOMAIN),
                        'ASC' => __('Ascending', BPMJ_EDDCM_DOMAIN),
                    ),
                ),
                'display_categories' => array(
                    'name' => 'display_categories',
                    'label' => __('Display product categories', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox',
                    'default' => 'on',
                ),
                'display_tags' => array(
                    'name' => 'display_tags',
                    'label' => __('Display product tags', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox',
                    'default' => 'on',
                ),
            );
        }

        /**
         * @return mixed
         */
        public function get_settings_slug()
        {
            return $this->setting_slug;
        }

        /**
         * @return string
         */
        public function get_layout_template_settings_slug()
        {
            return $this->layout_template_settings_slug;
        }

        /**
         * @return Settings_API
         */
        public function get_settings_api()
        {
            return $this->settings_api;
        }

        /**
         * @return array
         */
        private function get_course_subscriptions_settings()
        {
            $no_access = !WPI()->packages->has_access_to_feature(Packages::FEAT_SUBSCRIPTIONS);
            $settings = apply_filters('bpmj_eddcm_subscriptions', array(
                array(
                    'name' => 'bpmj_renewal_discount',
                    'save_to' => 'edd_settings',
                    'label' => __('Discount codes', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('Select if you want to generate discount codes. This code can be added to the reminder.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox',
                    'size' => 'regular',
                    'disabled' => $no_access,
                ),
                array(
                    'name' => 'paid_content_renewal_discount_value_type',
                    'save_to' => 'edd_settings',
                    'label' => __('Value and type of discount code', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('Select the value of the discount code and its type (percentage or amount).', BPMJ_EDDCM_DOMAIN),
                    'type' => 'renewal_discount',
                    'size' => 'regular',
                    'disabled' => $no_access,
                ),
                array(
                    'name' => 'bpmj_renewal_discount_time',
                    'save_to' => 'edd_settings',
                    'label' => __('Discount code validity period', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('Determine how long the discount code should be valid from the moment it is generated.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'select',
                    'options' => array(
                        '+1day' => __('One day', BPMJ_EDDCM_DOMAIN),
                        '+2days' => __('Two days', BPMJ_EDDCM_DOMAIN),
                        '+3days' => __('Three days', BPMJ_EDDCM_DOMAIN),
                        '+5days' => __('Five days', BPMJ_EDDCM_DOMAIN),
                        '+1week' => __('One Week', BPMJ_EDDCM_DOMAIN),
                        '+2weeks' => __('Two weeks', BPMJ_EDDCM_DOMAIN),
                        '+1month' => __('One Month', BPMJ_EDDCM_DOMAIN),
                        'no-limit' => __('No limit time', BPMJ_EDDCM_DOMAIN)
                    ),
                    'size' => 'regular',
                    'disabled' => $no_access,
                ),
                array(
                    'name' => 'bpmj_expired_access_report_email',
                    'save_to' => 'edd_settings',
                    'label' => __('Email address where reports will be sent', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('A report about expired user subscriptions will be sent to this email every day.<br>Leave this blank if you don’t want to receive this report.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'text',
                    'size' => 'regular',
                    'disabled' => $no_access,
                ),
                array(
                    'name' => 'paid_content_renewal_times',
                    'save_to' => 'edd_settings',
                    'label' => __('Reminder Hours', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('What time should notifications be sent? The minimum interval is 5 hours.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'renewal_times',
                    'size' => 'regular',
                    'disabled' => $no_access,
                ),
                array(
                    'name' => 'paid_content_renewal',
                    'save_to' => 'edd_settings',
                    'label' => __('Reminder', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('Set reminders for users about expiring access time to the content.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'renewal',
                    'size' => 'regular',
                    'disabled' => $no_access,
                )
            ));
            if ($no_access) {
                array_unshift($settings, array(
                    'name' => 'subscriptions_disabled_info',
                    'label' => '<span class="text-danger">' . __('Upgrade needed', BPMJ_EDDCM_DOMAIN) . '</span>',
                    'desc' => '<span class="text-danger">' . WPI()->packages->feature_not_available_message(Packages::FEAT_SUBSCRIPTIONS, __('In order to be allowed to use subscription options, you need to upgrade your license to level: "%s"', BPMJ_EDDCM_DOMAIN)) . '</span>',
                    'type' => 'hidden',
                ));
            }

            return $settings;
        }

        /**
         *
         */
        private function reload_mailers()
        {
            WPI()->load_mailers();
            if (function_exists('bpmj_eddact_on_activate_callback')) {
                bpmj_eddact_on_activate_callback();
            }

            if (function_exists('bpmj_eddres_on_activate_callback')) {
                bpmj_eddres_on_activate_callback();
            }
        }

        /**
         * @return string
         */
        public function get_default_footer_html()
        {
            $footer_html = '<p>' . sprintf(__('Powered by %s', BPMJ_EDDCM_DOMAIN), sprintf('<a href="https://wpidea.pl/">%s</a>', __(Software_Variant::get_name(), BPMJ_EDDCM_DOMAIN))) . '</p>';

            return $footer_html;
        }

        /**
         *
         */
        public function hook_reload_api_cache()
        {
            if (!empty($_GET['bpmj_eddcm_reload_cache']) && !empty($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'bpmj_eddcm_reload_cache')) {
                $this->reload_integration_cache($_GET['bpmj_eddcm_reload_cache']);

                wp_redirect(wp_get_referer());
                exit;
            }
        }

        /**
         * @param string $type
         */
        public function reload_integration_cache($type)
        {
            switch ($type) {
                case 'mailers':
                    $this->delete_convertkit_transients();
                    $this->delete_freshmail_transients();
                    $this->delete_mailerlite_transients();
                    $this->delete_mailchimp_transients();
                    $this->delete_getresponse_transients();
                    $this->delete_activecampaign_transients();
                    break;
                case 'invoices':
                    delete_transient('bpmj_wpfa_products_options');
                    break;
            }
        }

        /**
         * @param array $all_pages_new
         * @param array $all_pages_with_turnoff
         *
         * @return array
         */
        public function get_general_settings(array $all_pages_new = array(), array $all_pages_with_turnoff = array())
        {
            $general_settings = [];
            $wp_settings_handler = $this->wp_settings_handler;

            $current_user_should_see_additional_wp_settings = User::currentUserHasAnyOfTheRoles([
                Caps::ROLE_LMS_ADMIN,
                Caps::ROLE_LMS_SUPPORT
            ]);

            $should_license_key_be_hidden = Helper::is_dev() ? false : Software_Variant::is_saas();

            $general_settings[] = array(
                'name' => 'license_key',
                'label' => $should_license_key_be_hidden ? '' : __('License key', BPMJ_EDDCM_DOMAIN),
                'desc' => __('Enter the license key for the WP Idea.', BPMJ_EDDCM_DOMAIN),
                'type' => $should_license_key_be_hidden ? 'hidden' : 'license_key',
                'default' => '',
                'class' => $should_license_key_be_hidden ? 'hidden' : '',
                'sanitize_callback' => array($this, 'sanitize_license_key')
            );

            if($current_user_should_see_additional_wp_settings) {
                $general_settings[] = [
                    'name' => WP_Settings_Handler::WP_SETTING_BLOGNAME,
                    'label' => __('Site title', BPMJ_EDDCM_DOMAIN),
                    'type' => 'text',
                    'size' => 'regular',
                    'default' => $wp_settings_handler->get_default_value_for(WP_Settings_Handler::WP_SETTING_BLOGNAME)
                ];

                $general_settings[] = [
                    'name' => WP_Settings_Handler::WP_SETTING_BLOGDESC,
                    'label' => __('Site description', BPMJ_EDDCM_DOMAIN),
                    'type' => 'text',
                    'size' => 'regular',
                    'default' => $wp_settings_handler->get_default_value_for(WP_Settings_Handler::WP_SETTING_BLOGDESC)
                ];
            }

            $general_settings['logo'] = array(
                'name' => 'logo',
                'label' => __('Your course logo', BPMJ_EDDCM_DOMAIN),
                'desc' => __('The optimal size is 165px x 165px (or 330px x 330px for retina screens).', BPMJ_EDDCM_DOMAIN),
                'type' => 'file',
                'size' => 'regular',
                'button_class' => 'btn-eddcm btn-eddcm-primary',
            );

            $general_settings['favicon'] = array(
                'name' => 'favicon',
                'label' => __('Favicon', BPMJ_EDDCM_DOMAIN),
                'desc' => __('The optimal size is 16px x 16px.', BPMJ_EDDCM_DOMAIN),
                'type' => 'file',
                'size' => 'regular',
                'button_class' => 'btn-eddcm btn-eddcm-primary',
            );

            $is_static_page_on_front = $wp_settings_handler->wp_option_value_is(WP_Settings_Handler::WP_SETTING_SHOW_ON_FRONT, 'page');
            if($current_user_should_see_additional_wp_settings && $is_static_page_on_front) {
                $general_settings[] = [
                    'name' => WP_Settings_Handler::WP_SETTING_PAGE_ON_FRONT,
                    'label' => __('Page on front', BPMJ_EDDCM_DOMAIN),
                    'type' => 'select',
                    'options' => $all_pages_new,
                    'default' => $wp_settings_handler->get_default_value_for(WP_Settings_Handler::WP_SETTING_PAGE_ON_FRONT)
                ];
            }

            $general_settings[] = array(
                'name' => 'profile_editor_page',
                'label' => __('Profile editor page', BPMJ_EDDCM_DOMAIN),
                'desc' => __('Choose what page will be the profile editor page.', BPMJ_EDDCM_DOMAIN),
                'type' => 'select',
                'options' => $all_pages_new,
            );

            $general_settings[] = array(
                'name' => 'page_to_redirect_to_after_login',
                'label' => __('Page to redirect to on login', BPMJ_EDDCM_DOMAIN),
                'desc' => __('Choose what page the user will be redirected to after successful login.', BPMJ_EDDCM_DOMAIN),
                'type' => 'select',
                'options' => $all_pages_new,
            );

            $general_settings[] = array(
                'name' => 'contact_page',
                'label' => __('Contact page', BPMJ_EDDCM_DOMAIN),
                'desc' => __('Choose what page will be the contact page', BPMJ_EDDCM_DOMAIN),
                'type' => 'select',
                'default' => '',
                'options' => $all_pages_with_turnoff,
            );

            $general_settings[] = array(
                'name' => 'contact_email',
                'label' => __('Contact email', BPMJ_EDDCM_DOMAIN),
                'desc' => __('Set the contact email that will be displayed on the contact page.', BPMJ_EDDCM_DOMAIN),
                'type' => 'text',
                'size' => 'regular',
                'sanitize_callback' => 'sanitize_email',
            );

            if($current_user_should_see_additional_wp_settings) {
                $general_settings[] = [
                    'name' => WP_Settings_Handler::WP_SETTING_COMMENTS_NOTIFY,
                    'label' => __('New comment notification', BPMJ_EDDCM_DOMAIN),
                    'desc' => __( 'Check if you want to receive an e-mail every time someone adds a comment', BPMJ_EDDCM_DOMAIN ),
                    'type' => 'checkbox',
                    'default' => $wp_settings_handler->get_default_value_for(WP_Settings_Handler::WP_SETTING_COMMENTS_NOTIFY) ? 'on' : 'off'
                ];
                $general_settings[] = [
                    'name' => WP_Settings_Handler::WP_SETTING_MODERATION_NOTIFY,
                    'label' => __('Comment awaiting moderation notification', BPMJ_EDDCM_DOMAIN),
                    'desc' => __( 'Check if you want to receive an e-mail every time a comment is awaiting moderation', BPMJ_EDDCM_DOMAIN ),
                    'type' => 'checkbox',
                    'default' => $wp_settings_handler->get_default_value_for(WP_Settings_Handler::WP_SETTING_MODERATION_NOTIFY) ? 'on' : 'off'
                ];
                $general_settings[] = [
                    'name' => WP_Settings_Handler::WP_SETTING_COMMENT_MODERATION,
                    'label' => __('Comments moderation before publishing', BPMJ_EDDCM_DOMAIN),
                    'desc' => __( 'Check if comments must be manually approved before they appear on the blog', BPMJ_EDDCM_DOMAIN ),
                    'type' => 'checkbox',
                    'default' => $wp_settings_handler->get_default_value_for(WP_Settings_Handler::WP_SETTING_COMMENT_MODERATION) ? 'on' : 'off'
                ];
                $general_settings[] = [
                    'name' => WP_Settings_Handler::WP_SETTING_COMMENT_PREVIOUSLY_APPROVED,
                    'label' => __('Allow comments from known authors', BPMJ_EDDCM_DOMAIN),
                    'desc' => __( 'Check if another comment by the same author must be approved before it appears on the blog', BPMJ_EDDCM_DOMAIN ),
                    'type' => 'checkbox',
                    'default' => $wp_settings_handler->get_default_value_for(WP_Settings_Handler::WP_SETTING_COMMENT_PREVIOUSLY_APPROVED) ? 'on' : 'off'
                ];
            }

            $general_settings[] = array(
                'name' => 'currency',
                'label' => __('Currency', BPMJ_EDDCM_DOMAIN),
                'type' => 'select',
                'options' => edd_get_currencies(),
                'save_to' => 'edd_settings',
            );

            $general_settings[] = array(
                'name' => 'thousands_separator',
                'label' => __('Thousands Separator', BPMJ_EDDCM_DOMAIN),
                'type' => 'select',
                'options' => [',' => __('Comma', BPMJ_EDDCM_DOMAIN), '.' => __('Dot', BPMJ_EDDCM_DOMAIN), ' ' => __('Space', BPMJ_EDDCM_DOMAIN), '' => __('Disabled', BPMJ_EDDCM_DOMAIN)],
                'save_to' => 'edd_settings',
            );

            $general_settings[] = array(
                'name' => 'decimal_separator',
                'label' => __('Decimal Separator', BPMJ_EDDCM_DOMAIN),
                'type' => 'select',
                'options' => [',' => __('Comma', BPMJ_EDDCM_DOMAIN), '.' => __('Dot', BPMJ_EDDCM_DOMAIN)],
                'save_to' => 'edd_settings',
            );

            $general_settings[] = array(
                'name' => 'footer_html',
                'label' => __('Footer contents', BPMJ_EDDCM_DOMAIN),
                'type' => 'wysiwyg',
                'options' => array(
                    'wpautop' => false,
                    'textarea_rows' => 1,
                    'teeny' => true,
                    'media_buttons' => false,
                    'quicktags' => false,
                ),
                'default' => $this->get_default_footer_html(),
            );

            $general_settings[] = array(
                'name' => 'cookie-bar',
                'label' => __('Cookie bar', BPMJ_EDDCM_DOMAIN),
                'type' => 'checkbox',
                'desc' => __('Check if you want to enable and configure the cookie bar.', BPMJ_EDDCM_DOMAIN),
                'class' => 'privacy-policy',
            );

            $pages = [];
            $pages[] = null;
            foreach (get_pages() as $page)
                $pages[$page->ID] = $page->post_title;

            if (count($pages) > 1) {
                $general_settings[] = array(
                    'name' => 'cookie-bar-privacy-policy',
                    'label' => __('Privacy Policy', BPMJ_EDDCM_DOMAIN),
                    'type' => 'select',
                    'desc' => __('Select page that contains privacy policy.', BPMJ_EDDCM_DOMAIN),
                    'options' => $pages,
                    'class' => 'privacy-policy-show-hide',
                );
            } else {
                $general_settings[] = array(
                    'name' => 'cookie-bar-privacy-policy',
                    'label' => __('Privacy Policy', BPMJ_EDDCM_DOMAIN),
                    'type' => 'text',
                    'desc' => '<a href="' . admin_url('post-new.php?post_type=page') . '">' . __('Create page with privacy policy.', BPMJ_EDDCM_DOMAIN) . '</a>',
                    'class' => 'privacy-policy-hidden',
                );
            }

            $general_settings[] = array(
                'name' => 'cookie-bar-content',
                'label' => __('Cookie bar content', BPMJ_EDDCM_DOMAIN),
                'type' => 'wysiwyg',
                'class' => 'privacy-policy-show-hide',
                'default' => __('This website is using cookies. You can change the settings for cookies in your web browser. By using this site, you accept the Privacy Policy.', BPMJ_EDDCM_DOMAIN),
            );

            $general_settings[] = array(
                'name' => 'cookie-bar-button-text',
                'label' => __('Cookie bar button text', BPMJ_EDDCM_DOMAIN),
                'type' => 'text',
                'class' => 'privacy-policy-show-hide',
                'default' => __('Accept', BPMJ_EDDCM_DOMAIN),
            );

            if ($this->subscription->get_plan() === Subscription_Const::PLAN_PRO) {
                $general_settings[] = [
                    'name' => Settings_Const::PARTNER_PROGRAM,
                    'label' => $this->translator->translate('settings.affiliate_program'),
                    'type' => 'checkbox',
                    'class' => '',
                ];

                $commission_options= [];
                foreach(range(1,100) as $n) {
                    $commission_options[$n] = $n.' %';
                }

                $general_settings[] = [
                    'name' => Settings_Const::PARTNER_PROGRAM_COMMISSION,
                    'label' => $this->translator->translate('settings.affiliate_program.commission_amount'),
                    'type' => 'select',
                    'class' => 'commission_hideable_setting',
                    'options' => $commission_options
                ];
            } else {
                $general_settings[] = [
                    'name' => Settings_Const::PARTNER_PROGRAM,
                    'label' => $this->translator->translate('settings.affiliate_program'),
                    'type' => 'checkbox',
                    'class' => '',
                    'desc' => __('Zmień pakiet: Aby korzystać z programu partnerskiego musisz zmienic swoją licence na PRO.'),
                    'disabled' => true
                ];
            }

            if (Helper::is_dev()) {
                array_unshift($general_settings, array(
                    'name' => 'trial_version_expiration_date',
                    'label' => __('Trial version due date', BPMJ_EDDCM_DOMAIN),
                    'type' => 'text',
                    'size' => 'med',
                    'class' => 'wp-datepicker-field',
                    'default' => '',
                ));
                array_splice($general_settings, 2, 0, array(
                    array(
                        'name' => 'enable_edd',
                        'label' => __('Enable EDD', BPMJ_EDDCM_DOMAIN),
                        'desc' => __('By default, most of the features of EDD are disabled. You can get access to those features by checking this box.', BPMJ_EDDCM_DOMAIN),
                        'type' => 'checkbox',
                    )
                ));
                array_splice($general_settings, 2, 0, array(
                    array(
                        'name' => 'enable_telemetry',
                        'label' => __('Enable Telemetry', BPMJ_EDDCM_DOMAIN),
                        'type' => 'checkbox',
                        'save_to' => Telemetry::TELEMETRY_ACTIVITY_SLUG,
                    )
                ));
            }

            return $general_settings;
        }

        /**
         * @return array
         */
        public function get_advanced_settings()
        {
            $advanced_settings = array(
                'purchase_limit_behaviour' => array(
                    'name' => 'purchase_limit_behaviour',
                    'label' => __('Purchase limit control', BPMJ_EDDCM_DOMAIN),
                    'type' => 'select',
                    'default' => 'BEGIN_PAYMENT',
                    'options' => array(
                        'BEGIN_PAYMENT' => __('When placing an order', BPMJ_EDDCM_DOMAIN),
                        'COMPLETE_PAYMENT' => __('On receipt of payment', BPMJ_EDDCM_DOMAIN),
                    ),
                    'desc' => __('Decide when a purchased item gets deducted from a purchase limit.', BPMJ_EDDCM_DOMAIN),
                ),
                'allow_inline_file_download' => array(
                    'name' => 'allow_inline_file_download',
                    'label' => __('Opening lesson attachments', BPMJ_EDDCM_DOMAIN),
                    'type' => 'radio',
                    'default' => 'attachment',
                    'options' => array(
                        'attachment' => __('Force download to disk', BPMJ_EDDCM_DOMAIN),
                        'inline' => __('Open in a browser when possible', BPMJ_EDDCM_DOMAIN),
                    ),
                    'desc' => __('Decide how a file should be opened when a user clicks on an attachment.', BPMJ_EDDCM_DOMAIN),
                ),
                'enable_sell_discount' => array(
                    'name' => 'enable_sell_discount',
                    'label' => __('Enable generating discounts', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox_one_empty',
                    'desc' => __('When enabled, you can turn on the automatic generation of a discount code on product purchase.', BPMJ_EDDCM_DOMAIN),
                ),
                'logo_in_courses_to_home_page' => array(
                    'name' => 'enable_logo_in_courses_to_home_page',
                    'label' => __('Enable redirecting logo to home page', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox_one_empty',
                    'desc' => __('When enabled, logo in courses, modules, lessons and quizzes will be redirected to the home page.', BPMJ_EDDCM_DOMAIN),
                    'default' => false,
                )
            );

            $desc_checkbox_courses_enabled = $this->translator->translate('courses.settings.courses_enable.desc');

            if($this->has_any_courses()){

                $desc_checkbox_courses_enabled .= '<p class="desc_checkbox_enabled">'.$this->translator->translate('courses.settings.courses_enable.is_courses').'</p>';

                $active_courses_settings = [
                    Settings_Const::COURSES_CHECKBOX_DISABLED => array(
                        'name' => '',
                        'label' => $this->translator->translate('courses.settings.courses_enable'),
                        'type' => 'checkbox_one_empty',
                        'desc' => $desc_checkbox_courses_enabled,
                        'default' => true,
                        'disabled' => true
                    ),
                    Settings_Const::COURSES_ENABLED => array(
                        'name' => Settings_Const::COURSES_ENABLED,
                        'class' => 'courses_enable_hideable_setting',
                        'type' => 'hidden',
                        'default' => 'on',
                    )
                ];
            }else{
                $active_courses_settings = [
                    Settings_Const::COURSES_ENABLED => array(
                        'name' => Settings_Const::COURSES_ENABLED,
                        'label' => $this->translator->translate('courses.settings.courses_enable'),
                        'type' => 'checkbox',
                        'desc' => $desc_checkbox_courses_enabled,
                        'default' => true,
                        'disabled' => false
                    )
                ];
            }

            $active_digital_products_services_settings = [
                Settings_Const::DIGITAL_PRODUCTS_ENABLED => array(
                    'name' => Settings_Const::DIGITAL_PRODUCTS_ENABLED,
                    'label' => $this->translator->translate('digital_products.settings.digital_products_enable'),
                    'type' => 'checkbox_one_empty',
                    'desc' => $this->translator->translate('digital_products.digital_products_enable.desc'),
                    'default' => false,
                ),
                Settings_Const::SERVICES_ENABLED => array(
                    'name' => Settings_Const::SERVICES_ENABLED,
                    'label' => $this->translator->translate('services.settings.enable_services'),
                    'type' => 'checkbox_one_empty',
                    'desc' => $this->translator->translate('services.settings.enable_services.desc'),
                    'default' => false,
                ),
            ];

            if ($this->subscription->get_plan() !== Subscription_Const::PLAN_START) {
                $active_sessions_limiter_settings = [
                    Settings_Const::ACTIVE_SESSIONS_LIMITER_ENABLED => array(
                        'name' => Settings_Const::ACTIVE_SESSIONS_LIMITER_ENABLED,
                        'label' => $this->translator->translate('settings.active_sessions_limiter'),
                        'type' => 'checkbox_one_empty',
                        'desc' => $this->translator->translate('settings.active_sessions_limiter.desc'),
                        'default' => false,
                    ),
                    Settings_Const::MAX_ACTIVE_SESSIONS_NUMBER => array(
                        'name' => Settings_Const::MAX_ACTIVE_SESSIONS_NUMBER,
                        'label' => $this->translator->translate('settings.max_active_sessions_number'),
                        'type' => 'number',
                        'class' => 'session_limit_hideable_setting',
                        'default' => 1,
                        'min' => 1
                    )
                ];
            }else{
                $active_sessions_limiter_settings = [
                    Settings_Const::ACTIVE_SESSIONS_LIMITER_ENABLED => array(
                        'name' => Settings_Const::ACTIVE_SESSIONS_LIMITER_ENABLED,
                        'label' => $this->translator->translate('settings.active_sessions_limiter'),
                        'type' => 'checkbox_one_empty',
                        'desc' => $this->translator->translate('settings.active_sessions_limiter.license'),
                        'disabled' => true
                    )
                ];
            }

            return apply_filters('bpmj_eddcm_advanced_settings', array_merge($advanced_settings, $active_courses_settings, $active_digital_products_services_settings, $active_sessions_limiter_settings));
        }

        public function get_certificates_settings()
        {
            $no_access_to_certificates = WPI()->packages->no_access_to_feature(Packages::FEAT_CERTIFICATES);
            $popup = Popup::create(
                'enable_new_version_certificates_popup',
                View::get_admin('/popup/new-certificate')
            );
            $is_new_version_of_certificate_templates_enabled = Certificate_Template::check_if_new_version_of_certificate_templates_is_enabled();

            $enable_certificates_settings = [
                'name' => 'enable_certificates',
                'label' => __('Enable certificates option', BPMJ_EDDCM_DOMAIN),
                'type' => 'checkbox',
                'disabled' => $no_access_to_certificates,
            ];

            $disable_new_certificate_templates = [
                    'name' => Certificate_Template::SETTINGS_DISABLE_NEW_VERSION,
                    'type' => 'checkbox',
                    'class' => 'settings-hidden',
                    'default' => 'off'
                ];

            if(!LMS_Settings::get_option('enable_certificates')){
                return  [$enable_certificates_settings, $disable_new_certificate_templates];
            }

            return [
                $enable_certificates_settings,
                [
                    'name' => 'enable_new_version_certificates_button',
                    'label' => __('Enable new version of certificates', BPMJ_EDDCM_DOMAIN),
                    'type' => 'html',
                    'desc' => Button::create(__('Enable', BPMJ_EDDCM_DOMAIN), Button::TYPE_MAIN_SMALL)->open_popup_on_click($popup)->get_html(),
                    'class' => $is_new_version_of_certificate_templates_enabled ? 'settings-hidden' : ''
                ],
                $disable_new_certificate_templates,
                [
                    'name' => 'certificates',
                    'label' => __('Certificate templates', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('Add template', BPMJ_EDDCM_DOMAIN),
                    'type' => 'certificates',
                    'size' => 'regular',
                    'disabled' => $no_access_to_certificates,
                    'class' => !$is_new_version_of_certificate_templates_enabled ? 'settings-hidden' : ''
                ],

                [
                    'name' => 'certificates_bg',
                    'label' => __('Background for certificates', BPMJ_EDDCM_DOMAIN),
                    'desc' => sprintf(__('Looking for a nice looking certificate template? Check out our %stemplates%s.', BPMJ_EDDCM_DOMAIN), '<a href="https://wpidea.pl/docs/certyfikaty-szablony-certyfikatow/" target="_BLANK">', '</a>'),
                    'type' => 'file',
                    'disabled' => $no_access_to_certificates,
                    'default' => get_home_url(null, '/wp-content/plugins/wp-idea/assets/imgs/bck1v.png'),
                    'class' => $is_new_version_of_certificate_templates_enabled ? 'settings-hidden' : ''
                ],
                [
                    'name' => 'certificate_template',
                    'label' => __('Certificate file template', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('Available variables: <br><code>{course_name}</code> - the name of the completed course<br><code>{course_price}</code> - the price of the completed course<br><code>{student_name}</code> - the student\'s name<br><code>{student_first_name}</code> - the student\'s first name<br><code>{student_last_name}</code> - the student\'s last name<br><code>{certificate_date}</code> - the certificate generation date', BPMJ_EDDCM_DOMAIN),
                    'type' => 'wysiwyg',
                    'size' => '100%',
                    'default' => $this->load_template_from_file(BPMJ_EDDCM_DIR . 'includes/certificates/template-pdf-certificate.php'),
                    'disabled' => $no_access_to_certificates,
                    'class' => $is_new_version_of_certificate_templates_enabled ? 'settings-hidden' : ''
                ],
                [
                    'name' => 'certificate_template_styles',
                    'label' => __('Certificate template styles (CSS)', BPMJ_EDDCM_DOMAIN),
                    'type' => 'textarea',
                    'size' => '100%',
                    'default' => $this->load_template_from_file(BPMJ_EDDCM_DIR . 'includes/certificates/template-pdf-certificate.css'),
                    'disabled' => $no_access_to_certificates,
                    'sanitize_callback' => [$this, 'sanitize_for_post_allowed_html_tags'],
                    'class' => $is_new_version_of_certificate_templates_enabled ? 'settings-hidden' : ''
                ],
                [
                    'name' => 'certificate_orientation',
                    'label' => __('PDF certificate orientation', BPMJ_EDDCM_DOMAIN),
                    'type' => 'radio',
                    'options' => [
                        'portrait' => __('Portrait', BPMJ_EDDCM_DOMAIN),
                        'landscape' => __('Landscape', BPMJ_EDDCM_DOMAIN),
                    ],
                    'default' => 'portrait',
                    'disabled' => $no_access_to_certificates,
                    'class' => $is_new_version_of_certificate_templates_enabled ? 'settings-hidden' : ''
                ],
                [
                    'name' => 'certificate_pdf_preview',
                    'type' => 'button_array',
                    'options' => [
                        'html' => __('Preview as HTML', BPMJ_EDDCM_DOMAIN),
                        'pdf' => __('Preview as PDF', BPMJ_EDDCM_DOMAIN),
                    ],
                    'disabled' => $no_access_to_certificates,
                    'class' => $is_new_version_of_certificate_templates_enabled ? 'settings-hidden' : ''
                ],
            ];
        }

        public function get_analytics_and_scripts_settings()
        {
            return array(
                array(
                    'name' => Analytics::PIXEL_FB_ID_SETTING_NAME,
                    'type' => 'text',
                    'label' => __('Facebook Pixel ID', BPMJ_EDDCM_DOMAIN),
                ),
                array(
                    'name' => Analytics::GA_ID_SETTING_NAME,
                    'type' => 'text',
                    'label' => __('Google Analytics ID', BPMJ_EDDCM_DOMAIN),
                ),
                array(
                    'name' => Analytics::GTM_ID_SETTING_NAME,
                    'type' => 'text',
                    'label' => __('Google Tag Manager ID', BPMJ_EDDCM_DOMAIN),
                ),
                array(
                    'name' => Analytics::BEFORE_END_HEAD_SETTING_NAME,
                    'type' => 'textarea',
                    'label' => sprintf(__('Scripts before %s', BPMJ_EDDCM_DOMAIN), htmlentities('</head>')),
                    'desc' => sprintf(__('Eg.: %s', BPMJ_EDDCM_DOMAIN), htmlentities('<script>alert(\'Example alert\')</script>')),
                ),
                array(
                    'name' => Analytics::AFTER_BEGIN_BODY_SETTING_NAME,
                    'type' => 'textarea',
                    'label' => sprintf(__('Scripts after %s', BPMJ_EDDCM_DOMAIN), htmlentities('<body>')),
                    'desc' => sprintf(__('Eg.: %s', BPMJ_EDDCM_DOMAIN), htmlentities('<script>alert(\'Example alert\')</script>')),
                ),
                array(
                    'name' => Analytics::BEFORE_END_BODY_SETTING_NAME,
                    'type' => 'textarea',
                    'label' => sprintf(__('Scripts before %s', BPMJ_EDDCM_DOMAIN), htmlentities('</body>')),
                    'desc' => sprintf(__('Eg.: %s', BPMJ_EDDCM_DOMAIN), htmlentities('<script>alert(\'Example alert\')</script>')),
                ),
            );
        }

        /**
         * @return string
         */
        public function get_purchase_limit_behaviour()
        {
            global $wpidea_settings;

            if (empty($wpidea_settings['purchase_limit_behaviour'])) {
                return 'BEGIN_PAYMENT';
            }

            return $wpidea_settings['purchase_limit_behaviour'];
        }

        /**
         * @return array
         */
        public function get_order_settings()
        {
            $order_settings = [
                [
                    'name' => 'show_email2_on_checkout',
                    'label' => __('Enable e-mail address verification on checkout', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('When you select this option, users will be forced to repeat their e-mail address for verification.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox',
                ],
                [
                    'name' => 'last_name_required',
                    'label' => __('Make "last name" required', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('By default, only the first name is required to be provided by customers. When you select this option, both first and last name will be required.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox',
                ],
                [
                    'name' => 'edd_id_hide_fname',
                    'label' => __('Hide firstname field', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('When you select this option, the firstname field will be hidden', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox',
                    'save_to' => 'edd_settings'
                ],
                [
                    'name' => 'edd_id_hide_lname',
                    'label' => __('Hide lastname field', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('When you select this option, the lastname field will be hidden.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox',
                    'save_to' => 'edd_settings'
                ],
                [
                    'name' => 'show_phone_number_field_on_checkout',
                    'label' => __('Enable phone number field on checkout', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('When you select this option, users will be able to provide their phone number during checkout.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox',
                ],
                [
                    'name' => 'phone_number_required_on_checkout',
                    'label' => __('Phone number required', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('When you select this option, users will be required to provide their phone number during checkout.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox',
                ],
                [
                    'name' => 'show_comment_field_on_checkout',
                    'label' => __('Enable order comment field on checkout', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('When you select this option, users will be able to provide additional information during checkout.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox',
                ],
                [
                    'name' => 'show_additional_checkbox_on_checkout',
                    'label' => __('Enable additional checkbox on checkout', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('When you select this option, you can define an additional checkbox that will be presented to users during checkout.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox',
                ],
                [
                    'name' => 'additional_checkbox_description',
                    'label' => __('Additional checkbox description', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('Custom description for the checkbox that can be enabled above.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'text',
                    'size' => 'regular',
                ],
                [
                    'name' => 'additional_checkbox_required',
                    'label' => __('Additional checkbox required', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('When you select this option, users will be required to select the checkbox during checkout.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox',
                ],
                [
                    'name' => 'show_additional_checkbox2_on_checkout',
                    'label' => __('Enable second additional checkbox on checkout', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('When you select this option, you can define an additional checkbox that will be presented to users during checkout.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox',
                ],
                [
                    'name' => 'additional_checkbox2_description',
                    'label' => __('Second additional checkbox description', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('Custom description for the checkbox that can be enabled above.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'text',
                    'size' => 'regular',
                ],
                [
                    'name' => 'additional_checkbox2_required',
                    'label' => __('Second additional checkbox required', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('When you select this option, users will be required to select the checkbox during checkout.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox',
                ],
                [
                    'name' => 'agree_label',
                    'label' => __('Agree to Terms Label', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('Label shown next to the agree to terms check box.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'text',
                    'save_to' => 'edd_settings'
                ],
            ];

            return $order_settings;
        }

        /**
         * @return array
         */
        public function get_gift_settings()
        {
            $no_access_to_buy_as_gift = WPI()->packages->no_access_to_feature(Packages::FEAT_BUY_AS_GIFT);
            $gift_settings = array(
                array(
                    'name' => 'enable_buy_as_gift',
                    'label' => __('Enable "Buy as a gift" option', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('When enabled users will be able to buy selected courses as gifts (voucher codes).', BPMJ_EDDCM_DOMAIN) . ($no_access_to_buy_as_gift
                            ? '<br /><span class="text-danger">' . WPI()->packages->feature_not_available_message(Packages::FEAT_BUY_AS_GIFT, __('In order to use the buy as a gift option, you need to upgrade your license to level: "%s"', BPMJ_EDDCM_DOMAIN)) . '</span>'
                            : ''),
                    'disabled' => $no_access_to_buy_as_gift,
                    'type' => 'checkbox',
                ),
                array(
                    'name' => 'buy_as_gift_expiration_period',
                    'label' => __('Default voucher expiration period', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('How long the gift code can be redeemed before it expires. This setting can be overridden by course-specific settings.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'date_offset',
                    'disabled' => $no_access_to_buy_as_gift,
                ),
                array(
                    'name' => 'buy_as_gift_email_body',
                    'label' => __('Gift purchase receipt email body', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('Enter the text that is sent as the purchase receipt email to users after completion of a successful purchase. HTML is accepted. Available template tags:', BPMJ_EDDCM_DOMAIN) . '<br/>' . bpmj_eddcm_get_gift_email_tag_list(),
                    'type' => 'wysiwyg',
                    'size' => '100%',
                    'default' => $this->load_template_from_file(BPMJ_EDDCM_DIR . 'includes/buy-as-gift/template-email.php'),
                    'disabled' => $no_access_to_buy_as_gift,
                ),
                array(
                    'name' => 'enable_gift_pdf_voucher',
                    'label' => __('Generate PDF voucher file', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('When enabled users will receive a PDF file with their gift voucher.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'checkbox',
                    'disabled' => $no_access_to_buy_as_gift,
                ),
                array(
                    'name' => 'gift_pdf_voucher_template',
                    'label' => __('PDF voucher file template', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('Specify the HTML template that will be used to generate PDF files from. Available template tags:', BPMJ_EDDCM_DOMAIN) . '<br/>' . bpmj_eddcm_get_gift_pdf_tag_list(),
                    'type' => 'wysiwyg',
                    'size' => '100%',
                    'default' => $this->load_template_from_file(BPMJ_EDDCM_DIR . 'includes/buy-as-gift/template-pdf-voucher.php'),
                    'disabled' => $no_access_to_buy_as_gift,
                ),
                array(
                    'name' => 'gift_pdf_voucher_styles',
                    'label' => __('PDF voucher file styles (CSS)', BPMJ_EDDCM_DOMAIN),
                    'desc' => __('Specify the styles for the PDF template.', BPMJ_EDDCM_DOMAIN),
                    'type' => 'textarea',
                    'size' => '100%',
                    'default' => $this->load_template_from_file(BPMJ_EDDCM_DIR . 'includes/buy-as-gift/template-pdf-voucher.css'),
                    'disabled' => $no_access_to_buy_as_gift,
                    'sanitize_callback' => array($this, 'sanitize_for_post_allowed_html_tags')
                ),
                array(
                    'name' => 'gift_pdf_voucher_orientation',
                    'label' => __('PDF voucher file page orientation', BPMJ_EDDCM_DOMAIN),
                    'type' => 'radio',
                    'options' => array(
                        'portrait' => __('Portrait', BPMJ_EDDCM_DOMAIN),
                        'landscape' => __('Landscape', BPMJ_EDDCM_DOMAIN),
                    ),
                    'default' => 'portrait',
                    'disabled' => $no_access_to_buy_as_gift,
                ),
                array(
                    'name' => 'voucher_bg',
                    'label' => __('Background for voucher', BPMJ_EDDCM_DOMAIN),
                    'desc' => sprintf(__('Looking for a nice looking voucher template? Check out our %stemplates%s.', BPMJ_EDDCM_DOMAIN), '<a href="https://wpidea.pl/docs/voucher-na-prezent-szablony-voucherow" target="_BLANK">', '</a>'),
                    'type' => 'file',
                    'disabled' => $no_access_to_buy_as_gift,
                    'default' => get_home_url(null, '/wp-content/plugins/wp-idea/assets/imgs/bck1v.png'),
                ),
                array(
                    'name' => 'gift_pdf_voucher_preview',
                    'type' => 'button_array',
                    'options' => array(
                        'html' => __('Preview as HTML', BPMJ_EDDCM_DOMAIN),
                        'pdf' => __('Preview as PDF', BPMJ_EDDCM_DOMAIN),
                    ),
                    'disabled' => $no_access_to_buy_as_gift,
                ),
            );

            return $gift_settings;
        }

        /**
         * @param string $file_path
         *
         * @return string
         */
        protected function load_template_from_file($file_path)
        {
            ob_start();
            /** @noinspection PhpIncludeInspection */
            include $file_path;

            return ob_get_clean();
        }

        /**
         *
         */
        protected function delete_convertkit_transients()
        {
            do_action('convertkit_clear_cache');
        }

        /**
         *
         */
        protected function delete_mailchimp_transients()
        {
            do_action('mailchimp_clear_cache');
        }

        /**
         *
         */
        protected function delete_freshmail_transients()
        {
            do_action('freshmail_clear_cache');
        }

        /**
         *
         */
        protected function delete_getresponse_transients()
        {
            do_action('getresponse_clear_cache');
        }

        /**
         *
         */
        protected function delete_activecampaign_transients()
        {
            do_action('activecampaign_clear_cache');
        }

        /**
         *
         */
        protected function delete_mailerlite_transients()
        {
            do_action('mailerlite_clear_cache');
        }

        private function get_payu_failure_return_url(): string
        {
            try {
                if(!function_exists('edd_get_failed_transaction_uri')) {
                    return '';
                }

                return edd_get_failed_transaction_uri() . '?payu_transaction=%transId%&payu_session=%sessionId%&payu_error=%error%';
            } catch (\Error $e) {
                return '';
            }
        }

        private function get_success_page_uri(): string
        {
            try {
                if(!function_exists('edd_get_success_page_uri')) {
                    return '';
                }

                return edd_get_success_page_uri() . '?payu_transaction=%transId%&payu_session=%sessionId%';
            } catch (\Error $e) {
                return '';
            }
        }

        private function has_any_courses(): bool
        {
            if(!$this->courses_repository->count()){
                return false;
            }

            return true;
        }

    }

}
