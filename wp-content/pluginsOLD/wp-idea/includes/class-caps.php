<?php

namespace bpmj\wpidea;

use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\admin\subscription\models\Subscription_Const;
use bpmj\wpidea\caps\Access_Filters;
use bpmj\wpidea\caps\Admin_Account_Protector;
use bpmj\wpidea\caps\Edd_Download_Access;
use bpmj\wpidea\caps\Menu_And_Customization_Access;
use bpmj\wpidea\caps\Quiz_Answers_Access;
use bpmj\wpidea\caps\Quiz_Edit_Access;
use bpmj\wpidea\caps\Settings_Access;
use bpmj\wpidea\caps\Settings_Save_Access;
use bpmj\wpidea\caps\Tools_Menu_Access;
use bpmj\wpidea\modules\app_view\ams\caps\App_My_Site_Access;
use bpmj\wpidea\translator\Interface_Translator;
use EDD_Roles;

/**
 *
 * The class responsible for capabilities
 *
 */

class Caps {
    public const CAP_MANAGE_PRODUCTS = 'lms_manage_courses';
    public const CAP_MANAGE_SETTINGS = 'lms_manage_settings';
    public const CAP_MANAGE_QUIZZES = 'lms_manage_quizes';
    public const CAP_VIEW_SENSITIVE_DATA = 'lms_view_sensitive_data';
    public const CAP_VIEW_REPORTS = 'lms_view_reports';
    public const CAP_EXPORT_REPORTS = 'lms_export_reports';
    public const CAP_MANAGE_DISCOUNTS = 'lms_manage_discounts';
    public const CAP_VIEW_CUSTOMERS = 'lms_view_customers';
    public const CAP_MANAGE_CUSTOMERS = 'lms_manage_customers';
    public const CAP_DELETE_CUSTOMERS = 'lms_delete_customers';
    public const CAP_MANAGE_USERS = 'edit_users';
    public const CAP_MANAGE_POSTS = 'edit_posts';
    public const CAP_MANAGE_OPTIONS ='manage_options';
    public const CAP_MANAGE_ORDERS = 'lms_manage_orders';
    public const CAP_MANAGE_CERTIFICATES = 'lms_manage_certificates';
    public const CAP_MANAGE_STUDENTS = 'lms_manage_students';

    public const CAP_ACCESS_DASHBOARD = 'lms_access_dashboard';
    public const CAP_USE_WP_IDEA_MODE = 'lms_use_wp_idea_mode';

    public const ROLE_SITE_ADMIN = 'administrator';
    public const ROLE_LMS_ADMIN = 'lms_admin';
    public const ROLE_LMS_SUPPORT = 'lms_support';
    public const ROLE_EDITOR = 'editor';
    public const ROLE_AUTHOR = 'author';
    public const ROLE_CONTRIBUTOR = 'contributor';
    public const ROLE_SUBSCRIBER = 'subscriber';
    public const ROLE_LMS_ACCOUNTANT = 'lms_accountant';
    public const ROLE_LMS_ASSISTANT = 'lms_assistant';
    public const ROLE_LMS_CONTENT_MANAGER = 'lms_content_manager';
    public const ROLE_LMS_PARTNER = 'lms_partner';

    private const USER_EDIT_SCREEN_ID = 'user-edit';
    private const USER_PROFILE_SCREEN_ID = 'profile';
    private const DISABLE_PROFILE_INPUTS_JS_VAR = 'disable_profile_inputs';

    public const ROLES_ADMINS_SUPPORT_SUBSCRIBER = [Caps::ROLE_LMS_ADMIN,Caps::ROLE_LMS_SUPPORT, Caps::ROLE_SITE_ADMIN, Caps::ROLE_SUBSCRIBER];
    public const ROLES_ADMINS_SUPPORT = [Caps::ROLE_LMS_ADMIN,Caps::ROLE_LMS_SUPPORT, Caps::ROLE_SITE_ADMIN];

    public $access_filters;

    private $subscription;

    /**
     * @var Admin_Account_Protector
     */
    private $admin_account_protector;

    private Interface_Translator $translator;
    private Current_Request $current_request;

    public function __construct(
        Admin_Account_Protector $admin_account_protector,
        Subscription $subscription,
        Interface_Translator $translator,
        Current_Request $current_request
    ) {
        $this->admin_account_protector = $admin_account_protector;
        $this->subscription = $subscription;
        $this->translator = $translator;
        $this->current_request = $current_request;

        $this->init();
    }

    private function init()
    {
        $this->access_filters = new Access_Filters();

        $this->init_actions();

        add_filter( 'user_has_cap', [$this, 'grant_caps'], 10, 3 );
    }

    private function init_actions()
    {

        add_action('init', [$this, 'add_caps']);

        add_action('admin_init', [$this, 'remove_edd_roles']);

        add_action('admin_init', [$this, 'create_roles']);

        add_action('admin_enqueue_scripts', [$this, 'disable_profile_inputs']);

        $this->admin_account_protector->init_protection_filter();

    }

    public function disable_profile_inputs()
    {
        $screen_id = get_current_screen()->id ?? null;
        $is_user_edit_screen = $screen_id === self::USER_EDIT_SCREEN_ID || $screen_id === self::USER_PROFILE_SCREEN_ID;

        if(!$is_user_edit_screen) return;

        wp_localize_script('bpmj_eddmc_admin_script', self::DISABLE_PROFILE_INPUTS_JS_VAR, [
            'value' => $this->access_filters->cannot_see_sensitive_data(),
        ]);
    }

    /**
     * Remove default EDD roles
     *
     * @return void
     */
    public function remove_edd_roles(){
        $edd_roles = [
            'shop_manager',
            'shop_accountant',
            'shop_worker',
            'shop_vendor'
        ];

        foreach ($edd_roles as $role) {
            if( get_role( $role ) ){
                remove_role( $role );
            }
        }
    }

    /**
     * Create Idea LMS roles
     */
    public function create_roles(){
        $lms_admin_caps = [
			'read'                   => true,
			'edit_posts'             => true,
			'delete_posts'           => true,
			'unfiltered_html'        => true,
			'upload_files'           => true,
			'export'                 => true,
			'import'                 => true,
			'delete_others_pages'    => true,
			'delete_others_posts'    => true,
			'delete_pages'           => true,
			'delete_private_pages'   => true,
			'delete_private_posts'   => true,
			'delete_published_pages' => true,
			'delete_published_posts' => true,
			'edit_others_pages'      => true,
			'edit_others_posts'      => true,
			'edit_pages'             => true,
			'edit_private_pages'     => true,
			'edit_private_posts'     => true,
			'edit_published_pages'   => true,
			'edit_published_posts'   => true,
			'manage_categories'      => true,
			'manage_links'           => true,
			'moderate_comments'      => true,
			'publish_pages'          => true,
			'publish_posts'          => true,
            'read_private_pages'     => true,
            'read_private_posts'     => true,
			'edit_users'             => true,
			'edit_roles'             => true,
			'list_users'             => true,
            'remove_users'           => true,
            'delete_users'           => true,
            'create_users'           => true,
            'promote_user'           => true,
            'promote_users'          => true
        ];

        $lms_admin_caps[ Caps::CAP_MANAGE_PRODUCTS ] = true;
        $lms_admin_caps[ Caps::CAP_MANAGE_SETTINGS ] = true;
        $lms_admin_caps[ Caps::CAP_MANAGE_QUIZZES ] = true;
        $lms_admin_caps[ Caps::CAP_VIEW_SENSITIVE_DATA ] = true;
        $lms_admin_caps[ Caps::CAP_VIEW_REPORTS ] = true;
        $lms_admin_caps[ Caps::CAP_EXPORT_REPORTS ] = true;
        $lms_admin_caps[ Caps::CAP_MANAGE_DISCOUNTS ] = true;
        $lms_admin_caps[ Caps::CAP_VIEW_CUSTOMERS ] = true;
        $lms_admin_caps[ Caps::CAP_DELETE_CUSTOMERS ] = true;
        $lms_admin_caps[ Caps::CAP_MANAGE_ORDERS ] = true;
        $lms_admin_caps[ Caps::CAP_MANAGE_CUSTOMERS ] = true;
        $lms_admin_caps[ Caps::CAP_MANAGE_CERTIFICATES ] = true;
        $lms_admin_caps[ Caps::CAP_ACCESS_DASHBOARD ] = true;
        $lms_admin_caps[ Caps::CAP_USE_WP_IDEA_MODE] = true;
        $lms_admin_caps[ Caps::CAP_MANAGE_STUDENTS ] = true;

        //add default edd roles
        $lms_admin_caps[ 'view_shop_reports' ] = true;
        $lms_admin_caps[ 'view_shop_sensitive_data' ] = true;
        $lms_admin_caps[ 'export_shop_reports' ] = true;
        $lms_admin_caps[ 'manage_shop_settings' ] = true;

        // add core edd roles
		if( !class_exists( 'EDD_Roles' ) ) {
			return;
		}

        $edd_roles = new EDD_Roles();
        $capabilities = $edd_roles->get_core_caps();
        foreach ( $capabilities as $cap_group ) {
            foreach ( $cap_group as $cap ) {
                $lms_admin_caps[ $cap ] = true;
            }
        }

         $this->create_roles_in_proper_order($lms_admin_caps);
        }

        private function create_roles_in_proper_order(array $lms_admin_caps): void
        {
            // lms support caps
            $lms_support_caps = array_merge( $lms_admin_caps, [
                Caps::CAP_VIEW_SENSITIVE_DATA   => false,
                'list_users'                    => false,
                'remove_users'                  => false,
                'delete_users'                  => false,
                'create_users'                  => false,
                'promote_user'                  => false,
                'promote_users'                 => false,
            ]);

            if ( $this->subscription->get_plan() === Subscription_Const::PLAN_PRO ) {
                $this->create_role(
                    static::ROLE_LMS_PARTNER,
                    $this->translator->translate('users.column.role.lms_partner'),
                    []
                );
            }

            $this->create_role( static::ROLE_LMS_SUPPORT, $this->translator->translate('users.column.role.lms_support'), $lms_support_caps);

            if ( $this->subscription->get_plan() === Subscription_Const::PLAN_PRO ) {
                // lms accountant caps
                $lms_accountant_caps = [];
                $lms_accountant_caps[ Caps::CAP_VIEW_CUSTOMERS ] = true;
                $lms_accountant_caps[ Caps::CAP_DELETE_CUSTOMERS ] = true;
                $lms_accountant_caps[ Caps::CAP_MANAGE_ORDERS ] = true;
                $lms_accountant_caps[ Caps::CAP_VIEW_SENSITIVE_DATA ] = true;
                $lms_accountant_caps[ Caps::CAP_MANAGE_CUSTOMERS ] = true;
                $lms_accountant_caps[ Caps::CAP_USE_WP_IDEA_MODE] = true;
                $lms_accountant_caps[ 'edit_shop_payments' ] = true;
                $lms_accountant_caps[ 'view_shop_reports' ] = true;
                $this->create_role( static::ROLE_LMS_ACCOUNTANT, $this->translator->translate('users.column.role.lms_accountant'), $lms_accountant_caps);

                // lms assistant
                $lms_assistant_caps = [];
                $lms_assistant_caps[ Caps::CAP_MANAGE_CERTIFICATES ] = true;
                $lms_assistant_caps[ Caps::CAP_MANAGE_QUIZZES ] = true;
                $lms_assistant_caps[ Caps::CAP_VIEW_SENSITIVE_DATA ] = true;
                $lms_assistant_caps[ Caps::CAP_USE_WP_IDEA_MODE] = true;
                $this->create_role(static::ROLE_LMS_ASSISTANT, $this->translator->translate('users.column.role.lms_assistant'), $lms_assistant_caps);

                // lms content manager
                $lms_content_manager_caps = [
                    // wp
                    'read' => true,
                    // wp idea mode
                    Caps::CAP_USE_WP_IDEA_MODE => true,
                    // pages
                    'delete_others_pages' => true,
                    'delete_pages' => true,
                    'delete_private_pages' => true,
                    'delete_published_pages' => true,
                    'edit_others_pages' => true,
                    'edit_pages' => true,
                    'edit_private_pages' => true,
                    'edit_published_pages' => true,
                    'publish_pages' => true,
                    'read_private_pages' => true,
                    // posts
                    'delete_others_posts' => true,
                    'delete_posts' => true,
                    'delete_private_posts' => true,
                    'delete_published_posts' => true,
                    'edit_others_posts' => true,
                    'edit_posts' => true,
                    'edit_private_posts' => true,
                    'edit_published_posts' => true,
                    'publish_posts' => true,
                    'read_private_posts' => true,
                    // courses
                    'lms_manage_courses' => true,
                    'upload_files' => true,
                    // products
                    'assign_product_terms' => true,
                    'delete_others_products' => true,
                    'delete_private_products' => true,
                    'delete_product' => true,
                    'delete_product_terms' => true,
                    'delete_products' => true,
                    'delete_published_products' => true,
                    'edit_others_products' => true,
                    'edit_private_products' => true,
                    'edit_product' => true,
                    'edit_product_terms' => true,
                    'edit_products' => true,
                    'edit_published_products' => true,
                    'manage_product_terms' => true,
                    'publish_products' => true,
                    'read_private_products' => true,
                    'read_product' => true,
                    'view_product_stats' => true,
                ];
                $this->create_role(
                    static::ROLE_LMS_CONTENT_MANAGER,
                    $this->translator->translate('users.column.role.lms_content_manager'),
                    $lms_content_manager_caps
                );

            } else {
                $this->remove_role( static::ROLE_LMS_ASSISTANT );
                $this->remove_role( static::ROLE_LMS_ACCOUNTANT );
                $this->remove_role(static::ROLE_LMS_CONTENT_MANAGER);
                $this->remove_role(static::ROLE_LMS_PARTNER);
            }

            $this->create_role( static::ROLE_LMS_ADMIN, $this->translator->translate('users.column.role.lms_admin'), $lms_admin_caps);
        }

        /**
         * Create role
         *
         * @param string $role Role name.
         * @param string $display_name Display name for role.
         * @param array $capabilities List of capabilities, e.g. array( 'edit_posts' => true, 'delete_posts' => false );
         * @return void
         */
        private function create_role( $role, $display_name, $capabilities ){
            add_role( $role, $display_name, $capabilities );
        }

        /**
         * @param string $role
         */
        private function remove_role( $role )
        {
            remove_role( $role );
        }

        /**
         * Add new capabilities
         *
         * @return void
         */
        public function add_caps(){
            $this->add_cap( self::CAP_MANAGE_PRODUCTS );
            $this->add_cap( self::CAP_MANAGE_SETTINGS );
            $this->add_cap( self::CAP_MANAGE_QUIZZES );
            $this->add_cap( self::CAP_VIEW_SENSITIVE_DATA );
            $this->add_cap( self::CAP_VIEW_REPORTS );
            $this->add_cap( self::CAP_EXPORT_REPORTS );
            $this->add_cap( self::CAP_MANAGE_DISCOUNTS );
            $this->add_cap( self::CAP_VIEW_CUSTOMERS );
            $this->add_cap( self::CAP_DELETE_CUSTOMERS );
            $this->add_cap( self::CAP_MANAGE_CUSTOMERS );
            $this->add_cap( self::CAP_MANAGE_ORDERS );
            $this->add_cap( self::CAP_MANAGE_CERTIFICATES );
            $this->add_cap( self::CAP_MANAGE_STUDENTS );
        }

        /**
         * Add cap to role
         *
         * @param string $cap capability name
         * @param string $role_name default 'administrator'
         *
         * @return boolean
         */
        public function add_cap( $cap, $role_name = 'administrator')
        {
            $role = get_role( $role_name );

            if($role === null) return false;

            if( $role->has_cap( $cap ) ) return false;

            return $role->add_cap( $cap ) ? true : false;
        }

        /**
         * Grant access to pages
         *
         * @see https://codex.wordpress.org/Plugin_API/Filter_Reference/user_has_cap
         *
         * @param array $allcaps All the capabilities of the user
         * @param array $cap     [0] Required capability
         * @param array $args    [0] Requested capability
         */
        public function grant_caps( $all, $cap, $args )
        {
            if (    'edit_post'             != $args[0]
                &&	'edit_others_posts'     != $args[0]
                &&	'edit_published_posts'  != $args[0]
                &&  'edit_pages'            != $args[0]
                &&  'edit_others_pages'     != $args[0]
                &&	'edit_published_pages'  != $args[0]
                &&  'manage_options'        != $args[0]
                &&  'customize'             != $args[0]
                &&  'edit_theme_options'    != $args[0]
            ){
                return $all;
            }

            $post = $this->get_current_post( $args );

            $check_access = new Settings_Access( $all );
            $check_access
                ->thenCheck( new Tools_Menu_Access( $all ) )
                ->thenCheck( new Settings_Save_Access( $all ) )
                ->thenCheck( new Quiz_Answers_Access( $all ) )
                ->thenCheck( new Quiz_Edit_Access( $all ) )
                ->thenCheck( new Edd_Download_Access( $all ) )
                ->thenCheck( new Menu_And_Customization_Access( $all ) )
                ->thenCheck( new App_My_Site_Access($all, $this->current_request) );

            $access_granted = $check_access->verifyPage( $post );

            $all =  $access_granted ? $access_granted : $all;

            return $all;

        }

        /**
         * Get current post if exists
         *
         * @param array $args
         */
        private function get_current_post( array $args )
        {
            $post = null;

            $is_edit_action =
                    ( !empty( $_GET['action'] ) && $_GET['action'] === 'edit' )
                ||  ( !empty( $_POST['action'] ) && $_POST['action'] === 'editpost' );

            if($is_edit_action){
                $post_id = !empty( $_GET['post'] ) ? $_GET['post'] : null;
                if( empty($post_id) ) $post_id = !empty( $_POST['post_ID'] ) ? $_POST['post_ID'] : null;

                if( $post_id ){
                    $post = get_post( $post_id );
			}
		}

        if( empty($post) && !empty( $args[2] ) )
            $post = get_post( $args[2] );

        return $post;
    }
}
