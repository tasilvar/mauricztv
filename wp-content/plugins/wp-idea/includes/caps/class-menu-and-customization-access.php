<?php

namespace bpmj\wpidea\caps;

// Exit if accessed directly
use bpmj\wpidea\Caps;

if (!defined('ABSPATH'))
    exit;

class Menu_And_Customization_Access extends Access
{
    const MENUS_PAGE_NAME = 'nav-menus.php';
    const CUSTOMIZATION_PAGE_NAME = 'customize.php';

    public function __construct( array $all_caps ) {
        parent::__construct( $all_caps );
        
        add_action('admin_menu', [ $this, 'change_menu_items_access' ] );
    }
    
    public function verifyPage($post = null)
    {
        global $pagenow;

        $is_menus_page = self::MENUS_PAGE_NAME === $pagenow;

        if ($is_menus_page || $this->is_customization_page() || $this->is_ajax_add_menu_item_request()) {
            return $this->grant_access();
        }

        return parent::verifyPage($post);
    }

    private function is_customization_page()
    {
        global $pagenow;

        $is_customizer_preview = !empty( $_GET['customize_changeset_uuid'] );
        $is_customization_page = ( self::CUSTOMIZATION_PAGE_NAME === $pagenow ) || $is_customizer_preview;
        
        return $is_customization_page;
    }

    private function is_ajax_add_menu_item_request()
    {
        global $pagenow;

        $is_ajax_request = 'admin-ajax.php' === $pagenow;
        $is_add_item_request = !empty($_POST['action']) && $_POST['action'] == 'add-menu-item';

        return $is_ajax_request && $is_add_item_request;
    }

    public function grant_access()
    {
        if (!empty($this->all_caps[Caps::CAP_MANAGE_SETTINGS])) {
            $this->all_caps['customize'] = true;
            $this->all_caps['edit_posts'] = true;
            $this->all_caps['edit_theme_options'] = true;
        }

        return $this->all_caps;
    }

    public function change_menu_items_access()
    {
        $can_manage_lms_settings = $this->all_caps[ Caps::CAP_MANAGE_SETTINGS ] ?? false;
        if (!$can_manage_lms_settings) return;

        $is_admin = current_user_can( 'manage_options' );
        if ( $is_admin ) return;

        global $menu, $submenu;
        
        // change 'Appearence' menu cap
        $menu[60][1] = Caps::CAP_MANAGE_SETTINGS;
    
        // get rid of all submenu items
        $submenu['themes.php'] = [];
    
        // bring back Menu and Customize options with changed caps
        $submenu['themes.php'][5][0] = __( 'Menu' );
        $submenu['themes.php'][5][1] = Caps::CAP_MANAGE_SETTINGS;
        $submenu['themes.php'][5][2] = Menu_And_Customization_Access::MENUS_PAGE_NAME;
    
        $submenu['themes.php'][10][0] = __( 'Customize' );
        $submenu['themes.php'][10][1] = Caps::CAP_MANAGE_SETTINGS;
        $submenu['themes.php'][10][2] = Menu_And_Customization_Access::CUSTOMIZATION_PAGE_NAME;
    }
}
