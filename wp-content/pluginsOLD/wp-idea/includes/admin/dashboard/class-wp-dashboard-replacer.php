<?php
namespace bpmj\wpidea\admin\dashboard;

class WP_Dashboard_Replacer
{
    const PAGE_SLUG = 'wpidea-dashboard';

    const SCREEN_ID = 'dashboard';

    protected $title;

    protected $capability = 'read';

    public function __construct() {
        if(is_admin()) add_action( 'init', [$this, 'init'] );
    }

    public function init() {     
        if(!Dashboard::is_dashboard_enabled_for_current_user()) return;

        $this->set_title();
        add_filter( 'admin_title', [$this, 'admin_title'], 10, 2 );
        add_action( 'admin_menu', [$this, 'admin_menu'] );
        add_action( 'current_screen', [$this, 'current_screen'] );
    }

    public static function is_wpi_dashboard_page()
    {
        global $pagenow;
        
        return 'admin.php' == $pagenow && isset( $_GET['page'] ) && self::PAGE_SLUG == $_GET['page'];
    }

    public function set_title() {
        if( ! isset( $this->title ) ) {
            $this->title = __( 'Dashboard' );
        }
    }

    public function admin_title( $admin_title, $title ) {
        if(self::is_wpi_dashboard_page()) {
            $admin_title = $this->title . $admin_title;
        }
        return $admin_title;
    }

    public function admin_menu() {
        /**
         * Adds a custom page to WordPress
         */
        add_menu_page($this->title, '', $this->capability, self::PAGE_SLUG, [new Dashboard_Renderer(), 'render']);

        /**
         * Remove the custom page from the admin menu
         */
        remove_menu_page(self::PAGE_SLUG);

        /**
         * Make dashboard menu item the active item
         */
        global $parent_file, $submenu_file;
        $parent_file = 'index.php';
        $submenu_file = 'index.php';

        /**
         * Rename the dashboard menu item
         */
        global $menu;
        $menu[2][0] = $this->title;

        /**
         * Rename the dashboard submenu item
         */
        global $submenu;
        $submenu['index.php'][0][0] = $this->title;

    }

    /**
     * Redirect users from the normal dashboard to your custom dashboard
     */
    public function current_screen( $screen ) {
        if( self::SCREEN_ID == $screen->id ) {
            wp_safe_redirect(self::get_dashboard_url());
            exit;
        }
    }

    public static function get_dashboard_url()
    {
        return admin_url('admin.php?page=' . self::PAGE_SLUG);
    }
}
