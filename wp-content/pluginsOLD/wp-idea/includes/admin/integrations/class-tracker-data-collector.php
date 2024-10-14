<?php

namespace bpmj\wpidea\admin\integrations;

use bpmj\wpidea\modules\webhooks\core\repositories\Interface_Webhook_Repository;
use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\modules\purchase_redirects\core\services\Check_Redirects;
use bpmj\wpidea\modules\webhooks\infrastructure\persistence\Webhook_Query_Criteria;
use bpmj\wpidea\settings\Interface_Settings;

class Tracker_Data_Collector implements Interface_Initiable
{
    protected Interface_Tracker $tracker;
    protected Subscription $subscription;
    protected Interface_Settings $settings;
    protected Check_Redirects $check_redirect;
    protected Interface_Webhook_Repository $webhook_repository;

    public function __construct(
        Interface_Tracker $tracker,
        Subscription $subscription,
        Interface_Settings $settings,
        Check_Redirects $check_redirect,
        Interface_Webhook_Repository $webhook_repository
    ) {
        $this->tracker = $tracker;
        $this->subscription = $subscription;
        $this->settings = $settings;
        $this->check_redirect = $check_redirect;
        $this->webhook_repository = $webhook_repository;
    }

    public function init(): void
    {
        if ($this->tracker instanceof Interface_Initiable) {
            $this->tracker->init();
        }

        $this->add_software_data();
        $this->add_gateways_data();
        $this->add_voucher_data();
        $this->add_template_data();
        $this->add_modules_status();
    }

    private function add_software_data(): void
    {
        $this->tracker->add_data('software.type', $this->subscription->is_go() ? 'go' : 'box');
        $this->tracker->add_data('software.plan', $this->subscription->get_plan());
        $this->tracker->add_data('software.version', BPMJ_EDDCM_VERSION);
        $this->tracker->add_data('system.php.version', WPI_PHP_VERSION());
    }

    private function add_gateways_data(): void
    {
        $this->tracker->add_data('settings.gateways.manual.status', $this->get_gateway_status_by_slug('manual'), Interface_Tracker::TYPE_TOGGLEABLE);
        $this->tracker->add_data('settings.gateways.tpay.status', $this->get_gateway_status_by_slug('tpay_gateway'), Interface_Tracker::TYPE_TOGGLEABLE);
        $this->tracker->add_data(
            'settings.gateways.przelewy24.status',
            $this->get_gateway_status_by_slug('przelewy24_gateway'),
            Interface_Tracker::TYPE_TOGGLEABLE
        );
        $this->tracker->add_data('settings.gateways.dotpay.status', $this->get_gateway_status_by_slug('dotpay_gateway'), Interface_Tracker::TYPE_TOGGLEABLE);
        $this->tracker->add_data('settings.gateways.paynow.status', $this->get_gateway_status_by_slug('paynow_gateway'), Interface_Tracker::TYPE_TOGGLEABLE);
        $this->tracker->add_data('settings.gateways.payu.status', $this->get_gateway_status_by_slug('payu'), Interface_Tracker::TYPE_TOGGLEABLE);
        $this->tracker->add_data('settings.gateways.paypal.status', $this->get_gateway_status_by_slug('paypal'), Interface_Tracker::TYPE_TOGGLEABLE);
        $this->tracker->add_data('settings.gateways.coinbase.status', $this->get_gateway_status_by_slug('coinbase'), Interface_Tracker::TYPE_TOGGLEABLE);
        $this->tracker->add_data('settings.gateways.stripe.status', $this->get_gateway_status_by_slug('stripe'), Interface_Tracker::TYPE_TOGGLEABLE);
    }

    private function add_voucher_data(): void
    {
        $vouchers = $this->settings->get(Settings_Const::ENABLE_BUY_AS_GIFT);
        $status = $this->parse_bool_to_on_off($vouchers);
        $this->tracker->add_data('settings.voucher.status', $status, Interface_Tracker::TYPE_TOGGLEABLE);
    }

    private function get_gateway_status_by_slug(string $gateway_slug): string
    {
        $gateways = $this->settings->get('gateways');

        return isset($gateways[$gateway_slug]) ? 'on' : 'off';
    }

    private function add_template_data(): void
    {
        $template = $this->settings->get('template');
        if (!$template) {
            $template = 'off';
        }
        $this->tracker->add_data('settings.template', $template, Interface_Tracker::TYPE_STRING);
    }

    private function add_modules_status(): void
    {
        $digital_products = $this->settings->get(Settings_Const::DIGITAL_PRODUCTS_ENABLED);
        $services = $this->settings->get(Settings_Const::SERVICES_ENABLED);
        $affiliate = $this->settings->get(Settings_Const::PARTNER_PROGRAM);
        $purchase_redirects = $this->check_redirect->has_any_redirects();
        $webhooks_count = $this->webhook_repository->count_by_criteria(new Webhook_Query_Criteria());

        $this->tracker->add_data(
            'settings.modules.digital_products.status',
            $this->parse_bool_to_on_off($digital_products),
            Interface_Tracker::TYPE_TOGGLEABLE
        );
        $this->tracker->add_data('settings.modules.services.status', $this->parse_bool_to_on_off($services), Interface_Tracker::TYPE_TOGGLEABLE);
        $this->tracker->add_data('settings.modules.affiliate.status', $this->parse_bool_to_on_off($affiliate), Interface_Tracker::TYPE_TOGGLEABLE);
        $this->tracker->add_data(
            'settings.modules.purchase_redirects.status',
            $this->parse_bool_to_on_off($purchase_redirects),
            Interface_Tracker::TYPE_TOGGLEABLE
        );
        $this->tracker->add_data('settings.modules.webhooks.status', $this->parse_count_to_on_off($webhooks_count), Interface_Tracker::TYPE_TOGGLEABLE);
    }

    private function parse_bool_to_on_off(?bool $status): string
    {
        return $status === true ? 'on' : 'off';
    }

    private function parse_count_to_on_off(int $count): string
    {
        return $count > 0 ? 'on' : 'off';
    }
}
