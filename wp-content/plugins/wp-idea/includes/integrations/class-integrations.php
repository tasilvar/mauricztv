<?php

namespace bpmj\wpidea\integrations;

use Airbrake\Exception;
use bpmj\wp\eddact\ActiveCampaign;
use bpmj\wp\eddfm\Freshmail;
use bpmj\wp\eddip\iPresso;
use bpmj\wp\eddres\Getresponse;
use BPMJ_EDD_Interspire;
use BPMJ_EDD_Mailerlite;
use BPMJ_EDD_Salesmanago;
use BPMJ_WP_Fakturownia;
use BPMJ_WP_iFirma;
use BPMJ_WP_Infakt;
use BPMJ_WP_Taxe;
use BPMJ_WP_wFirma;
use Dotpay;
use EDD_Coinbase;
use EDD_ConvertKit;
use EDD_MailChimp;
use EDD_Stripe;
use Paynow;
use PayPal;
use Payu;
use Przelewy24;
use Tpay;

if (!defined('ABSPATH'))
    exit;

class Integrations
{
    public const TYPE_INVOICES = 'invoices';
    public const TYPE_GATEWAYS = 'gates';
    public const TYPE_MAILERS = 'mailers';

    public const INVOICE_INTEGRATIONS = [
        'wp-fakturownia' => [
            'label' => 'Fakturownia',
            'model' => BPMJ_WP_Fakturownia::class,
            'inputs' => ['apikey', 'departments_id'],
            'option' => 'bpmj_wpfa_settings',
            'gtu_supported' => true,
            'flat_rate_supported' => true
        ],
        'wp-ifirma' => [
            'label' => 'iFirma',
            'model' => BPMJ_WP_iFirma::class,
            'inputs' => ['ifirma_email','ifirma_invoice_key','ifirma_subscriber_key'],
            'option' => 'bpmj_wpifirma_settings',
            'gtu_supported' => true,
            'flat_rate_supported' => true
        ],
        'wp-wfirma' => [
            'label' => 'wFirma',
            'model' => BPMJ_WP_wFirma::class,
            'inputs' => ['wf_login','wf_pass'],
            'option' => 'bpmj_wpwf_settings',
            'gtu_supported' => false,
            'flat_rate_supported' => true
        ],
        'wp-infakt' => [
            'label' => 'Infakt',
            'model' => BPMJ_WP_Infakt::class,
            'inputs' => ['infakt_api_key'],
            'option' => 'bpmj_wpinfakt_settings',
            'gtu_supported' => true,
            'flat_rate_supported' => true
        ],
        'wp-taxe' => [
            'label' => 'Taxe',
            'model' => BPMJ_WP_Taxe::class,
            'inputs' => ['taxe_login','taxe_api_key'],
            'option' => 'bpmj_wptaxe_settings',
            'gtu_supported' => true,
            'flat_rate_supported' => false
        ]
    ];

    const GATEWAYS_INTEGRATIONS = [
        'dotpay_gateway' => [
            'model' => Dotpay::class,
            'inputs' => ['dotpay_id','dotpay_pin']
        ],
        'payu' => [
            'model' => Payu::class,
            'inputs' => ['payu_pos_id','payu_pos_auth_key','payu_key1','payu_key2']
        ],
        'przelewy24_gateway' => [
            'model' => Przelewy24::class,
            'inputs' => ['przelewy24_id','przelewy24_pin']
        ],
        'tpay_gateway' => [
            'model' => Tpay::class,
            'inputs' => ['tpay_id','tpay_pin'],
            'check_manually' => true
        ],
        'coinbase' => [
            'model' => EDD_Coinbase::class,
            'inputs' => ['edd_coinbase_api_key']
        ],
        'stripe' => [
            'model' => EDD_Stripe::class,
            'inputs' => ['live_secret_key','live_publishable_key','test_secret_key','test_publishable_key']
        ],
        'paynow_gateway' => [
            'model' => Paynow::class,
            'inputs' => ['paynow_access_key','paynow_signature_key']
        ],
        'paypal' => [
            'model' => PayPal::class,
            'inputs' => ['paypal_email'],
            'check_manually' => true
        ],
    ];

    const MAILERS_INTEGRATIONS = [
        'edd-mailchimp' => [
            'model' =>EDD_MailChimp::class,
            'inputs' => ['eddmc_api']
        ],
        'edd-mailerlite' => [
            'model' =>BPMJ_EDD_Mailerlite::class,
            'inputs' => ['bpmj_edd_ml_api']
        ],
        'edd-getresponse' => [
            'model' =>Getresponse::class,
            'inputs' => ['bpmj_eddres_token']
        ],
        'edd-salesmanago' => [
            'model' =>BPMJ_EDD_Salesmanago::class,
            'inputs' => ['salesmanago_owner','salesmanago_endpoint','salesmanago_client_id','salesmanago_api_secret']
        ],
        'edd-freshmail' => [
            'model' =>Freshmail::class,
            'inputs' => ['bpmj_eddfm_api','bpmj_eddfm_api_secret']
        ],
        'edd-activecampaign' => [
            'model' =>ActiveCampaign::class,
            'inputs' => ['bpmj_eddact_api_url','bpmj_eddact_api_token']
        ],
        'edd-interspire' => [
            'model' =>BPMJ_EDD_Interspire::class,
            'inputs' => ['bpmj_edd_in_username','bpmj_edd_in_token','bpmj_edd_in_xmlEndpoint']
        ],
        'edd-ipresso' => [
            'model' =>iPresso::class,
            'inputs' => ['bpmj_eddip_api_endpoint','bpmj_eddip_api','bpmj_eddip_api_login','bpmj_eddip_api_password']
        ],
        'edd-convertkit' => [
            'model' =>EDD_ConvertKit::class,
            'inputs' => ['edd_convertkit_api','edd_convertkit_api_secret']
        ],
    ];

    public static function get_integration_model(string $type, string $name)
    {
        $integrations = self::get_integrations_by_type($type);

        $integration_model = null;
        if(isset($integrations[$name]['model'])){
            $integration_model = $integrations[$name]['model'];
        }
        if(!$integration_model){
            throw new Exception($name . ' has no class assigned');
        }

        $class =  $integration_model;
        return new $class;
    }

    public static function get_integrations_by_type(string $type): array
    {
        $integrations = [];
        if($type == Integrations_Notices::TYPE_INVOICES){
            $integrations = self::INVOICE_INTEGRATIONS;
        }
        if($type == Integrations_Notices::TYPE_GATEWAYS){
            $integrations = self::GATEWAYS_INTEGRATIONS;
        }
        if($type == Integrations_Notices::TYPE_MAILERS){
            $integrations = self::MAILERS_INTEGRATIONS;
        }
        return $integrations;
    }

    public static function check_is_enabled($type, $name): ?bool
    {
        if(
            $type == Integrations::TYPE_INVOICES ||
            $type == Integrations::TYPE_MAILERS
        ){
            return WPI()->diagnostic->is_integration_enabled( $name );
        }
        if($type == Integrations::TYPE_GATEWAYS){
            $name = str_replace('_gateway', '', $name);
            return WPI()->diagnostic->is_payment_gate_enabled($name);
        }
    }

    public static function is_check_manually($type, $name): bool
    {
        $integrations = self::get_integrations_by_type($type);
        if(isset($integrations[$name]['check_manually'])){
            return false;
        }
        return true;
    }

    public static function get_check_connection_url($type, $name)
    {
        return wp_nonce_url(add_query_arg(array(
            'page' => $_GET['page'],
            Integrations_Connect::CHECK_INTEGRATION_CONNECTION_NAME_REQUEST => $name,
            Integrations_Connect::CHECK_INTEGRATION_CONNECTION_TYPE_REQUEST => $type,
        ), admin_url('admin.php')));
    }
}
