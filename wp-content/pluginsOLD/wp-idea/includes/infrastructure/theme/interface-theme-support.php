<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\theme;

interface Interface_Theme_Support
{
    public const ADMIN_BAR = 'admin-bar';
    public function remove_theme_support(string $theme_option): void;
}