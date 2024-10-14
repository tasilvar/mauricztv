<?php

namespace bpmj\wpidea\modules\gus_api\core\events\external\handlers;

use bpmj\wpidea\modules\gus_api\core\services\Interface_Site_Info_Getter;
use bpmj\wpidea\admin\subscription\api\Interface_Subscription_API;
use bpmj\wpidea\modules\gus_api\core\services\Hash_Subscription_Key_Generator;
use bpmj\wpidea\modules\gus_api\Gus_API_Module;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\infrastructure\assets\Interface_Script_Loader;
use bpmj\wpidea\instantiator\Interface_Initiable;

class Initiate_Checkout_Handler implements Interface_Initiable
{
    private const BPMJ_WPI_GUS_API_I18N = 'BPMJ_WPI_GUS_API_I18N';
    private Interface_Events $events;
    private Interface_Actions $actions;
    private Interface_Script_Loader $script_loader;
    private Interface_Translator $translator;
    private Interface_Subscription_API $subscription_api;
    private Hash_Subscription_Key_Generator $hash_subscription_key_generator;
    private Interface_Site_Info_Getter $site_info_getter;

    public function __construct(
        Interface_Events $events,
        Interface_Actions $actions,
        Interface_Script_Loader $script_loader,
        Interface_Translator $translator,
        Interface_Subscription_API $subscription_api,
        Hash_Subscription_Key_Generator $hash_subscription_key_generator,
        Interface_Site_Info_Getter $site_info_getter
    ) {
        $this->events = $events;
        $this->actions = $actions;
        $this->script_loader = $script_loader;
        $this->translator = $translator;
        $this->subscription_api = $subscription_api;
        $this->hash_subscription_key_generator = $hash_subscription_key_generator;
        $this->site_info_getter = $site_info_getter;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::CHECKOUT_INITIATED, [$this, 'register_scripts']);
    }

    public function register_scripts(): void
    {
        $this->actions->add(Action_Name::PRINT_FOOTER_SCRIPT, [$this, 'print_transalation_strings_as_js_variable']);

        $this->actions->add(Action_Name::ENQUEUE_SCRIPTS, function () {
            $this->script_loader->enqueue_script('wpi_gus_api', BPMJ_EDDCM_URL . 'includes/modules/gus-api/web/assets/gus-api.min.js', [
                'jquery',
            ], BPMJ_EDDCM_VERSION);
        });
    }

    public function print_transalation_strings_as_js_variable(): void
    {
        echo "<script>let " . self::BPMJ_WPI_GUS_API_I18N . "=" . $this->get_json_translations() . "</script>";
    }

    private function get_json_translations(): string
    {
        return json_encode($this->get_settings_as_variable_in_array()) ?: '[]';
    }

    private function get_settings_as_variable_in_array(): array
    {
        return [
            'search_data_endpoint' => Gus_API_Module::SEARCH_DATA_ENDPOINT,
            'subscription_type' => $this->subscription_api->get_plan_for_current_user(),
            'subscription_key' => $this->hash_subscription_key_generator->get_hash_subscription_key_by_type(),
            'host' => $this->site_info_getter->get_home_url(),
            'error_processing_request' => $this->translator->translate('gus_api.error_processing_request'),
            'download_from_gus' => '<i class="fa fa-download"></i> &nbsp; ' . $this->translator->translate('invoice_data.button.download_from_gus'),
            'downloading' => '<i class="fa fa-download"></i> &nbsp; ' . $this->translator->translate('invoice_data.button.downloading'),
            'wrong_tax_id' => $this->translator->translate('invoice_data.nip_field.error'),
            'entity_not_found' => $this->translator->translate('invoice_data.nip_field.data_empty'),
        ];
    }
}
