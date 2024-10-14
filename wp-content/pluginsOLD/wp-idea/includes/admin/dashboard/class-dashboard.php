<?php

namespace bpmj\wpidea\admin\dashboard;

use bpmj\wpidea\admin\menu\Site_Admin_Menu_Mode_Toggle;
use bpmj\wpidea\Caps;
use bpmj\wpidea\wolverine\user\User;

class Dashboard
{

    public function init()
    {
        new WP_Dashboard_Replacer();
        new Changelog();
        new Admin_Bar_Items_Renderer();
    }

    public static function is_dashboard_enabled_for_current_user(): bool
    {
        return User::currentUserHasAnyOfTheCapabilities([Caps::CAP_ACCESS_DASHBOARD]) ||
               self::is_idea_mode_enabled_by_user();
    }

    private static function is_idea_mode_enabled_by_user(): bool
    {
        $user = User::getCurrent();

        if ( ! $user) {
            return false;
        }

        return (bool)$user->getMeta(Site_Admin_Menu_Mode_Toggle::IDEA_MENU_MODE_ENABLED_META_KEY);
    }
}