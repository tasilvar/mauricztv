<?php

namespace bpmj\wpidea\caps;

// Exit if accessed directly
use bpmj\wpidea\Caps;

if (!defined('ABSPATH'))
    exit;

class Tools_Menu_Access extends Access
{
    const TOOLS_PAGE_NAME = 'tools.php';
    const TOOLS_PAGE_INDEX = 75;

    public function __construct( array $all_caps ) {
        parent::__construct( $all_caps );
        
        add_action('admin_menu', [$this, 'remove_tools_menu']);
    }

    public function remove_tools_menu()
    {
        $user = wp_get_current_user();

        $is_lms_admin_or_support = in_array(Caps::ROLE_LMS_ADMIN, (array) $user->roles) || in_array(Caps::ROLE_LMS_SUPPORT, (array) $user->roles);
        if (!$is_lms_admin_or_support) return;

        $is_admin = current_user_can( 'manage_options' );
        if ( $is_admin ) return;

        global $menu, $submenu;
        
        // remove 'Tools' menu
        if(isset($menu[self::TOOLS_PAGE_INDEX])) unset($menu[self::TOOLS_PAGE_INDEX]);
    
        // get rid of all submenu items
        if(isset($submenu[self::TOOLS_PAGE_NAME])) unset($submenu[self::TOOLS_PAGE_NAME]);
    }
}
