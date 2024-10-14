<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\conflicting_plugins_detector\core\services;

use bpmj\wpidea\modules\conflicting_plugins_detector\core\io\Interface_Plugins_Info_Provider;

class Conflicting_Plugins_Detector
{
    private const CONFLICTING_PLUGINS = [
        'official-facebook-pixel/facebook-for-wordpress.php',
        'ultimate-member/ultimate-member.php'
    ];

    private Interface_Plugins_Info_Provider $plugins_info_provider;

    public function __construct(
        Interface_Plugins_Info_Provider $plugins_info_provider
    )
    {
        $this->plugins_info_provider = $plugins_info_provider;
    }


    public function get_active_conflicting_plugins_name_list(): array
    {
        $active_conflicting_plugins_names = [];

        foreach (self::CONFLICTING_PLUGINS as $plugin_slug) {
            if(!$this->plugins_info_provider->is_plugin_active($plugin_slug)) {
                continue;
            }

            $active_conflicting_plugins_names[] = $this->plugins_info_provider->get_plugin_name($plugin_slug) ?? $plugin_slug;
        }

        return $active_conflicting_plugins_names;
    }
}