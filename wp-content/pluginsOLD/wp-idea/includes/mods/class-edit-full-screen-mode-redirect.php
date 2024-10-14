<?php

namespace bpmj\wpidea\mods;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\Helper;
use bpmj\wpidea\helpers\Interface_Debug_Helper;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\WP_Redirector;

class Edit_Full_Screen_Mode_Redirect implements Interface_Initiable
{
    private $actions;

    private $redirector;

    private $debug_helper;

    public function __construct(
        Interface_Actions $actions,
        WP_Redirector $redirector,
        Interface_Debug_Helper $debug_helper
    )
    {
        $this->actions = $actions;
        $this->redirector = $redirector;
        $this->debug_helper = $debug_helper;
    }

    public function init(): void
    {
        $this->actions->add('admin_init', [$this, 'redirect']);
    }

    public function redirect(): void
    {
        if ( $this->debug_helper->is_dev_mode_enabled() ) {
            return;
        }

        $this->redirector->redirect(admin_url('admin.php?page=' . Admin_Menu_Item_Slug::COURSES));
    }
}
