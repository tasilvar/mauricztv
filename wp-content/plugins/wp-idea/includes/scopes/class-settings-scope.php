<?php

namespace bpmj\wpidea\scopes;


use bpmj\wpidea\Current_Request;
use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;

if (!defined('ABSPATH')) {
    exit;
}

class Settings_Scope extends Abstract_Scope
{
    private const PAGE_SLUG = Admin_Menu_Item_Slug::SETTINGS;

    public function check_scope(): bool
    {
        $request = new Current_Request();
        if ($request->get_query_arg('page') === self::PAGE_SLUG) {
            return true;
        }

        if (str_contains($request->get_query_arg('wpi_route'), 'admin/settings_fields_ajax')) {
            return true;
        }

        return false;
    }
}
