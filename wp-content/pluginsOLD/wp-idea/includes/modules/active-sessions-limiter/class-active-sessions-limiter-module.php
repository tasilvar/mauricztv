<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\active_sessions_limiter;

use bpmj\wpidea\modules\active_sessions_limiter\infrastructure\events\Event_Handlers_Initiator;
use bpmj\wpidea\shared\abstractions\modules\Interface_Module;
use bpmj\wpidea\modules\active_sessions_limiter\core\io\Interface_Settings_Provider;
use bpmj\wpidea\modules\active_sessions_limiter\core\value_objects\Limiter_Settings;
use bpmj\wpidea\modules\active_sessions_limiter\infrastructure\io\Active_Sessions_Limit_Enforcer_Initiator;
use Psr\Container\ContainerInterface;

class Active_Sessions_Limiter_Module implements Interface_Module
{
    private Interface_Settings_Provider $settings_provider;
    private Active_Sessions_Limit_Enforcer_Initiator $limit_enforcer_initiator;
    private ContainerInterface $container;

    public function __construct(
        ContainerInterface $container,
        Interface_Settings_Provider $settings_provider,
        Active_Sessions_Limit_Enforcer_Initiator $limit_enforcer_initiator
    )
    {
        $this->settings_provider = $settings_provider;
        $this->limit_enforcer_initiator = $limit_enforcer_initiator;
        $this->container = $container;
    }

    public function init(): void
    {
        if(!$this->settings_provider->is_active_sessions_limiter_enabled()) {
            return;
        }

        $limiter_settings = Limiter_Settings::create($this->settings_provider->get_max_active_sessions_number());

        $this->limit_enforcer_initiator->init($limiter_settings);

        $this->container->get(Event_Handlers_Initiator::class);
    }

    public function get_routes(): array
    {
        return [
        ];
    }

    public function get_translations(): array
    {
        return [
            'pl_PL' => [
            ],
            'en_US' => [
            ]
        ];
    }
}