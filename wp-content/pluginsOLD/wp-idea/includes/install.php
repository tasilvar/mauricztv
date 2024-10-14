<?php

/**
 * Funkcja odpala się przy WŁĄCZANIU wtyczki
 * UWAGA: nie odpala się przy aktualizacji - jeśli coś ma się dodać (np. jakaś strona / ustawienia) przy aktualizacji to trzeba je dodać w admin/class-upgrades.php
 *
 * @param type $network_wide
 */

use bpmj\wpidea\admin\settings\core\configuration\Accounting_Settings_Group;
use bpmj\wpidea\admin\settings\core\configuration\Design_Settings_Group;
use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\Packages;
use bpmj\wpidea\packages\Packages_API_Static_Helper;
use bpmj\wpidea\Software_Variant;
use bpmj\wpidea\telemetry\Telemetry;

function bpmj_wpidea_install( $network_wide = false ) {
	edd_install( $network_wide );
	delete_transient( '_edd_activation_redirect' );

	$edd_settings	 = get_option( 'edd_settings' );
	$wpidea_settings = get_option( 'wp_idea', array() );
	if ( false !== $edd_settings && empty( $edd_settings[ 'wpidea' ] ) ) {

		$checkout = array(
			'ID'		 => $edd_settings[ 'purchase_page' ],
			'post_title' => __( 'Checkout', BPMJ_EDDCM_DOMAIN ),
			'post_name'	 => sanitize_title( __( 'Checkout', BPMJ_EDDCM_DOMAIN ) )
		);
		wp_update_post( $checkout );

		$success = array(
			'ID'			 => $edd_settings[ 'success_page' ],
			'post_title'	 => __( 'Purchase Confirmation', BPMJ_EDDCM_DOMAIN ),
			'post_name'		 => sanitize_title( __( 'Purchase Confirmation', BPMJ_EDDCM_DOMAIN ) ),
			'post_content'	 => '[edd_receipt]',
			'post_parent'	 => 0
		);
		wp_update_post( $success );

		$failure = array(
			'ID'			 => $edd_settings[ 'failure_page' ],
			'post_title'	 => __( 'Transaction Failed', BPMJ_EDDCM_DOMAIN ),
			'post_name'		 => sanitize_title( __( 'Transaction Failed', BPMJ_EDDCM_DOMAIN ) ),
			'post_content'	 => __( 'Your transaction failed, please try again or contact site support.', BPMJ_EDDCM_DOMAIN ),
			'post_parent'	 => 0
		);
		wp_update_post( $failure );

        $purchase_history = array(
            'ID'			 => $edd_settings[ 'purchase_history_page' ],
            'post_title'	 => __( 'Purchase History', BPMJ_EDDCM_DOMAIN ),
            'post_name'		 => sanitize_title( __( 'Purchase History', BPMJ_EDDCM_DOMAIN ) ),
            'post_content'	 => '[purchase_history]',
            'post_parent'	 => 0
        );
        wp_update_post( $purchase_history );

        if ( !Software_Variant::is_international() ) {
            $edd_settings_currency            = 'PLN';
            $edd_settings_thousands_separator = '';
            $edd_settings_decimal_separator   = '.';
        } else {
            $edd_settings_currency            = 'USD';
            $edd_settings_thousands_separator = ',';
            $edd_settings_decimal_separator   = '.';
        }

		$arr = array(
			'wpidea'                                => 1,
			'enable_ajax_cart'                      => '1',
			'base_country'                          => 'PL',
			'base_state'                            => '',
			'currency'                              => $edd_settings_currency,
			'currency_position'                     => 'after',
			'thousands_separator'                   => $edd_settings_thousands_separator,
			'decimal_separator'                     => $edd_settings_decimal_separator,
			'from_name'                             => Software_Variant::get_name(),
			'from_email'                            => __( 'your@email.com', BPMJ_EDDCM_DOMAIN ),
			'purchase_subject'                      => __( 'Purchase confirmation', BPMJ_EDDCM_DOMAIN ),
			'purchase_heading'                      => __( 'Purchase confirmation', BPMJ_EDDCM_DOMAIN ),
			'purchase_receipt'                      => __( 'Hello', BPMJ_EDDCM_DOMAIN ) . ' {name},
' .
__( 'Thank you for your purchase. Below you can find a list of purchased products:', BPMJ_EDDCM_DOMAIN ) . '

{download_list}

{sitename}',
			'sale_notification_subject'             => Translator_Static_Helper::translate('settings.sections.messages.admin_sale_notification.title.default'),
			'sale_notification'                     => Translator_Static_Helper::translate('settings.sections.messages.admin_sale_notification.message.default'),
			'admin_notice_emails'                   => __( 'your@email.com', BPMJ_EDDCM_DOMAIN ),
			'disable_admin_notices'                 => '1',
			'download_method'                       => 'redirect',
			'show_agree_to_terms'                   => '1',
			'agree_label'                           => __( 'I agree to the <a href="/tos/" target="_blank">terms and conditions</a>', BPMJ_EDDCM_DOMAIN ),
			'agree_text'                            => '',
			'bpmj_edd_arc_subject'                  => __( 'Your login details for WP Idea', BPMJ_EDDCM_DOMAIN ),
			'bpmj_edd_arc_content'                  => __( 'Hello', BPMJ_EDDCM_DOMAIN ) . ' {firstname},

' .
__( 'Your order has been accepted.', BPMJ_EDDCM_DOMAIN ) . '
' .
__( 'We\'ve created an account for you on the WP Idea platform. Here are your login details:', BPMJ_EDDCM_DOMAIN ) . '

Login: {login}
' .
__( 'Link to set password', BPMJ_EDDCM_DOMAIN ) . ': <a href="{password_reset_link}">{password_reset_link}</a>

--' .
__( 'This is an automatically generated email, please do not reply.', BPMJ_EDDCM_DOMAIN ) . '
' . home_url(),
			'edd_auto_register_disable_admin_email' => '1',
			'gateways'                              =>
				array(
					'tpay_gateway' => '1',
				),
			'default_gateway'                       => 'tpay_gateway',
			'edd_id_gateways'                       =>
				array(
					'tpay_gateway' => '1',
				),
			'edd_id_person'                         => '1',
			'tpay_id'                               => '1010',
			'tpay_pin'                              => 'demo',
			'file_download_limit'                   => '',
			'download_link_expiration'              => '99999',
			'checkout_label'                        => __( 'Purchase', BPMJ_EDDCM_DOMAIN ),
			'stripe_billing_fields'					=> 'zip_country',
			'stripe_use_existing_cards'				=> '1'
        );

		$edd_settings = array_merge( $edd_settings, $arr );
		update_option( 'edd_settings', $edd_settings );

		// This is needed to mark all upgrades as done
		WPI()->auto_upgrade( true );
	}

	$new_options = array();
	if ( !key_exists( 'course_list_page', $wpidea_settings ) ) {
		$new_options[ 'course_list_page' ] = wp_insert_post(
			array(
			    'post_title'	 => Translator_Static_Helper::translate('breadcrumbs.list_products'),
			    'post_content'	 => '[courses]',
			    'post_status'	 => 'publish',
			    'post_author'	 => 1,
			    'post_type'	 => 'page',
			    'comment_status' => 'closed'
			)
		);
	}

	if ( ! key_exists( 'page_to_redirect_to_after_login', $wpidea_settings ) ) {
		$new_options[ 'page_to_redirect_to_after_login' ] = empty( $wpidea_settings[ 'course_list_page' ] ) ? $new_options[ 'course_list_page' ] : $wpidea_settings[ 'course_list_page' ];
	}

	if ( ! key_exists( 'profile_editor_page', $wpidea_settings ) ) {
		$new_options[ 'profile_editor_page' ] = wp_insert_post(
			array(
				'post_title'     => __( 'Profile', BPMJ_EDDCM_DOMAIN ),
				'post_content'   => '[edd_profile_editor]',
				'post_status'    => 'publish',
				'post_author'    => 1,
				'post_type'      => 'page',
				'comment_status' => 'closed',
			)
		);
	}
	if ( ! key_exists( 'enable_responsive_videos', $wpidea_settings ) ) {
		$new_options[ 'enable_responsive_videos' ] = 'on';
	}
	if ( ! key_exists( 'override_all', $wpidea_settings ) ) {
		$new_options[ 'override_all' ] = 'on';
	}

    if( ! key_exists( 'voucher_page', $wpidea_settings ) || null === get_post( bpmj_eddcm_get_option( 'voucher_page' ) ) ) {
        $new_options[ 'voucher_page' ] = wp_insert_post(
            array(
                'post_title'     => __( 'Voucher', BPMJ_EDDCM_DOMAIN ),
                'post_content'   => '[download_checkout]',
                'post_status'    => 'publish',
                'post_author'    => 1,
                'post_type'      => 'page',
                'comment_status' => 'closed',
            )
        );
    }

    if( ! key_exists( 'certificates_page', $wpidea_settings ) || null === get_post( bpmj_eddcm_get_option( 'certificates_page' ) ) ) {
        $new_options[ 'certificates_page' ] = wp_insert_post(
            array(
                'post_title'     => __( 'My certificates', BPMJ_EDDCM_DOMAIN ),
                'post_content'   => '',
                'post_status'    => 'publish',
                'post_author'    => 1,
                'post_type'      => 'page',
                'comment_status' => 'closed',
            )
        );
    }

	if ( ! key_exists( 'scarlet_cart_additional_info_1_title', $wpidea_settings ) ) {
		$new_options[ 'scarlet_cart_additional_info_1_title' ] = __( 'Satisfaction guarantee', BPMJ_EDDCM_DOMAIN );
	}

	if ( ! key_exists( 'scarlet_cart_additional_info_1_desc', $wpidea_settings ) ) {
		$new_options[ 'scarlet_cart_additional_info_1_desc' ] = 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo';
	}

	if ( ! key_exists( 'scarlet_cart_additional_info_2_title', $wpidea_settings ) ) {
		$new_options[ 'scarlet_cart_additional_info_2_title' ] = __( 'Secure connection', BPMJ_EDDCM_DOMAIN );
	}

	if ( ! key_exists( 'scarlet_cart_additional_info_2_desc', $wpidea_settings ) ) {
		$new_options[ 'scarlet_cart_additional_info_2_desc' ] = 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo';
	}

	if ( ! key_exists( 'scarlet_cart_secure_payments_cb', $wpidea_settings ) ) {
		$new_options[ 'scarlet_cart_secure_payments_cb' ] = 'on';
	}

	if (!key_exists(Accounting_Settings_Group::ENABLED_GUS_API, $wpidea_settings) && Packages_API_Static_Helper::has_access_to_feature(Packages::FEAT_GUS_API)) {
		$new_options[Accounting_Settings_Group::ENABLED_GUS_API] = 'on';
	}

    if (!key_exists(Design_Settings_Group::DISPLAY_AUTHOR_INFO, $wpidea_settings)) {
        $new_options[Design_Settings_Group::DISPLAY_AUTHOR_INFO] = 'on';
    }

    if (!key_exists('show_available_quantities', $wpidea_settings)) {
        $new_options['show_available_quantities'] = 'on';
    }

    if (!key_exists('template', $wpidea_settings)) {
        $new_options['template'] = 'scarlet';
    }

    if (!key_exists(Settings_Const::RIGHT_CLICK_BLOCKING_QUIZ, $wpidea_settings)) {
        $new_options[Settings_Const::RIGHT_CLICK_BLOCKING_QUIZ] = 'on';
    }

	WPI()->get_upgrades_class()->u0009_dynamic_menu( true );
    WPI()->get_upgrades_class()->u0020_remove_unnecessary_wpfa_integrations();
    WPI()->get_upgrades_class()->u0037_default_certificate_template();
    WPI()->get_upgrades_class()->u0044_ensure_templates_system_integrity();
    WPI()->get_upgrades_class()->u0047_setup_logs_persistence();
    WPI()->get_upgrades_class()->u0050_update_users_roles_form_pending_user_to_subscriber();
    WPI()->get_upgrades_class()->u0051_remove_pending_user();
    WPI()->get_upgrades_class()->u0052_setup_webhooks_persistence_and_migration();
    WPI()->get_upgrades_class()->u0058_setup_partner_persistence();
    WPI()->get_upgrades_class()->u0059_setup_commission_persistence();
    WPI()->get_upgrades_class()->u0060_setup_video_persistence();
    WPI()->get_upgrades_class()->u0063_set_the_default_message_of_the_lost_cart_recovery_mechanism();

	if ( ! empty( $new_options ) ) {
		update_option( 'wp_idea', array_merge( $wpidea_settings, $new_options ) );
        add_option( Telemetry::TELEMETRY_DEFAULT_SLUG, $new_options );
	}

    WPI()->get_upgrades_class()->u0064_activate_courses_module();
    WPI()->get_upgrades_class()->u0067_setup_note_persistence();
    WPI()->get_upgrades_class()->u0068_setup_external_landing_link_persistence();
    WPI()->get_upgrades_class()->u0069_add_campaign_column_in_commission_table();
    WPI()->get_upgrades_class()->u0071_setup_increasing_sales_offers_persistence();
    WPI()->get_upgrades_class()->u0072_setup_price_history_persistence();
    WPI()->get_upgrades_class()->u0075_setup_opinions_persistence();

    Telemetry::activate();

	if ( function_exists( 'bpmj_eddact_on_activate_callback' ) ) {
		bpmj_eddact_on_activate_callback();
	}

	if ( function_exists( 'bpmj_eddres_on_activate_callback' ) ) {
		bpmj_eddres_on_activate_callback();
	}

	update_option( 'edd_tracking_notice', '1' );
	update_option( 'edds_stripe_connect_intro_notice_dismissed', true );

	update_option( 'wp_idea_activated', '1' );

	$first_installation_timestamp = get_option('wpi_first_installation_timestamp');
	if(!$first_installation_timestamp) {
        update_option( 'wpi_first_installation_timestamp', time() );
    }
}

register_activation_hook( BPMJ_EDDCM_FILE, 'bpmj_wpidea_install' );

function bpmj_wpidea_after_install() {

    if ( is_admin() && get_option( 'wp_idea_activated' ) == '1' ) {

        delete_option( 'wp_idea_activated' );

        do_action( 'bpmj_eddcm_after_upgrade' );
    }
}
add_action( 'admin_init', 'bpmj_wpidea_after_install' );
