<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\assets;

interface Interface_Script_Loader
{
    public function enqueue_script($handle, $src = '', $deps = [], $ver = false, $in_footer = false): void;

    public function enqueue_style($handle, $src = '', $deps = [], $ver = false): void;

    public function localize_script($handle, $object_name, $l10n): void;
}