<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\conflicting_plugins_detector\core\io;

interface Interface_Plugins_Info_Provider
{
    public function is_plugin_active(string $plugin_slug): bool;

    public function get_plugin_name(string $plugin_slug): ?string;
}