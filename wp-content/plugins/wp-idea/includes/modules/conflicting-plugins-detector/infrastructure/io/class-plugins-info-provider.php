<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\conflicting_plugins_detector\infrastructure\io;

use bpmj\wpidea\modules\conflicting_plugins_detector\core\io\Interface_Plugins_Info_Provider;

class Plugins_Info_Provider implements Interface_Plugins_Info_Provider
{
    public function is_plugin_active(string $plugin_slug): bool
    {
        return is_plugin_active($plugin_slug);
    }

    public function get_plugin_name(string $plugin_slug): ?string
    {
        $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_slug);

        return $plugin_data['Name'] ?? null;
    }
}