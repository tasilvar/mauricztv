<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\assets;

class Wp_Script_Loader implements Interface_Script_Loader
{
    public function enqueue_script($handle, $src = '', $deps = [], $ver = false, $in_footer = false): void
    {
        wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);
    }

    public function enqueue_style($handle, $src = '', $deps = [], $ver = false): void
    {
        wp_enqueue_style($handle, $src, $deps, $ver);
    }

    public function localize_script($handle, $object_name, $l10n): void
    {
        wp_localize_script($handle, $object_name, $l10n);
    }
}