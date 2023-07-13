<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\theme;

class Wp_Theme_Support implements Interface_Theme_Support
{
    public function remove_theme_support(string $theme_option): void
    {
        add_theme_support($theme_option, ['callback' => '__return_false']);
    }
}