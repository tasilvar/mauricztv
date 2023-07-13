<?php
namespace bpmj\wpidea\admin\dashboard;

use bpmj\wpidea\admin\bar\Admin_Bar_Item_Position;

class Admin_Bar_Items_Renderer
{
    const DASHBOARD_ITEM_ID = 'wpi-dashboard';

    public function __construct() {
        if(is_admin()) add_action('init', [$this, 'render']);
    }

    public function render()
    {
        if(Dashboard::is_dashboard_enabled_for_current_user()) $this->render_links();
    }

    protected function render_links()
    {
        if(!isset(WPI()->admin_bar)) {
            return;
        }

        $this->render_dashboard_link();
    }

    protected function render_dashboard_link()
    {
        WPI()->admin_bar->register_item(
            self::DASHBOARD_ITEM_ID,
            __('Dashboard', BPMJ_EDDCM_DOMAIN),
            Admin_Bar_Item_Position::from_string(Admin_Bar_Item_Position::INSIDE_WPI_INFO_BAR),
            WP_Dashboard_Replacer::get_dashboard_url()
        );
    }
}