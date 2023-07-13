<?php

namespace bpmj\wpidea\templates_system\admin;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;

class Template_Groups_Page
{
    public function get_url(): string
    {
        return admin_url('admin.php?page=' . Admin_Menu_Item_Slug::TEMPLATE_GROUPS);
    }
}