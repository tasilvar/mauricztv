<?php

namespace bpmj\wpidea\modules\google_analytics;

use bpmj\wpidea\events\actions\{Interface_Actions, Action_Name};
use bpmj\wpidea\modules\google_analytics\infrastructure\events\Event_Handlers_Initiator;
use bpmj\wpidea\modules\google_analytics\web\Google_Analytics_Scripts_Renderer;
use bpmj\wpidea\shared\abstractions\modules\Interface_Module;
use bpmj\wpidea\modules\google_analytics\core\providers\Interface_Google_Analytics_Config_Provider;
use bpmj\wpidea\modules\google_analytics\api\{Google_Analytics_API, Google_Analytics_API_Static_Helper};
use bpmj\wpidea\infrastructure\assets\Interface_Script_Loader;

class Google_Analytics_Module implements Interface_Module
{
    public const PARAM_SESSION_NAME = 'ga4_events';

    private Interface_Google_Analytics_Config_Provider $google_analytics_config_provider;
    private Google_Analytics_Scripts_Renderer $google_analytics_scripts_renderer;
    private Event_Handlers_Initiator $event_handlers_initiator;
    private Interface_Actions $actions;
    private Google_Analytics_API $google_analytics_api;
    private Interface_Script_Loader $script_loader;

    public function __construct(
        Interface_Google_Analytics_Config_Provider $google_analytics_config_provider,
        Google_Analytics_Scripts_Renderer $google_analytics_scripts_renderer,
        Event_Handlers_Initiator $event_handlers_initiator,
        Interface_Actions $actions,
        Google_Analytics_API $google_analytics_api,
        Interface_Script_Loader $script_loader
    ) {
        $this->google_analytics_config_provider = $google_analytics_config_provider;
        $this->google_analytics_scripts_renderer = $google_analytics_scripts_renderer;
        $this->event_handlers_initiator = $event_handlers_initiator;
        $this->actions = $actions;
        $this->google_analytics_api = $google_analytics_api;
        $this->script_loader = $script_loader;
    }

    public function init(): void
    {
        Google_Analytics_API_Static_Helper::init($this->google_analytics_api);

        if ($this->google_analytics_config_provider->is_ga4_enabled()) {
            $this->google_analytics_scripts_renderer->init();
            $this->actions->add(Action_Name::ENQUEUE_SCRIPTS, [$this, 'register_scripts']);
        }
    }

    public function get_routes(): array
    {
        return [];
    }

    public function get_translations(): array
    {
        return [];
    }
    
    public function register_scripts(): void
    {
        $this->script_loader->enqueue_script('wpi_google_analytics', BPMJ_EDDCM_URL . 'includes/modules/google-analytics/web/assets/analytics.js', [
            'jquery',
        ], BPMJ_EDDCM_VERSION);
    }
}