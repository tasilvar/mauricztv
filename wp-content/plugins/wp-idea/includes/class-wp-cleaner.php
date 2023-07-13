<?php

namespace bpmj\wpidea;

use bpmj\wpidea\Caps;
use bpmj\wpidea\wolverine\user\User;

class WP_Cleaner {

    const DEFAULT_PATH_FAVICON = BPMJ_EDDCM_URL . 'assets/imgs/favicon.ico';

    const WHITELISTED_AUTHOR_ROLES = [
         Caps::ROLE_SITE_ADMIN,
         Caps::ROLE_EDITOR,
         Caps::ROLE_AUTHOR,
         Caps::ROLE_CONTRIBUTOR
    ];

    const WHITELISTED_SITEMAP_POST_TYPES = [
        'post',
    ];

    public function __construct()
    {
        $this->add_hook_to_hide_admin_bar_wp_icon();
        $this->hide_generator_info();
        $this->remove_twentytwenty_editor_styles();
        $this->clean_wp_health_check();
        $this->change_favicon();
        $this->hide_wp_update_info();
        $this->remove_sitemap_elements();
        $this->disable_author_page_for_regular_users();
    }

    private function add_hook_to_hide_admin_bar_wp_icon()
    {
        add_action( 'wp_before_admin_bar_render', array( $this, 'hide_admin_bar_wp_icon' ), 0 );
    }

    private function remove_twentytwenty_editor_styles()
    {
        add_action( 'enqueue_block_editor_assets', [$this, 'dequeue_twentytwenty_editor_styles'], 100 );
    }

    public function dequeue_twentytwenty_editor_styles()
    {
        wp_dequeue_style( 'twentytwenty-block-editor-styles' );
    }

    public function hide_admin_bar_wp_icon()
    {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu( 'wp-logo' );
    }

    private function hide_generator_info()
    {
        add_filter('the_generator', array( $this, 'rm_generator_filter' ) );
        add_filter('get_the_generator_html', array( $this, 'rm_generator_filter') ) ;
        add_filter('get_the_generator_xhtml', array( $this, 'rm_generator_filter' ) );
        add_filter('get_the_generator_atom', array( $this, 'rm_generator_filter' ) );
        add_filter('get_the_generator_rss2', array( $this, 'rm_generator_filter' ) );
        add_filter('get_the_generator_comment', array( $this, 'rm_generator_filter' ) );
        add_filter('get_the_generator_export', array( $this, 'rm_generator_filter' ) );
        add_filter('wf_disable_generator_tags', array( $this, 'rm_generator_filter' ) );
    }

    public function rm_generator_filter()
    {
        return '';
    }

    private function clean_wp_health_check()
    {
        add_filter('site_status_tests', [$this, 'filter_site_status_tests']);
    }

    public function filter_site_status_tests( $tests ) {
        /**
         * Remove tests that are throwing errors because of WPI blocking rest API for security reasons
         */
        unset( $tests['direct']['rest_availability'] );
        unset( $tests['async']['loopback_requests'] );

        return $tests;
    }

    private function change_favicon()
    {
        if(!Software_Variant::is_saas()){
            return;
        }

        if(!empty(get_site_icon_url())){
            return;
        }

        add_filter( 'get_site_icon_url' , [$this, 'get_default_favicon']);
    }

    public function get_default_favicon()
    {
        return self::DEFAULT_PATH_FAVICON;
    }

    private function hide_wp_update_info()
    {
        add_action('after_setup_theme', [$this, 'remove_wp_update_notifications']);
    }

    public function remove_wp_update_notifications()
    {
        if (!Software_Variant::is_saas()) return;

        if (!User::currentUserHasAnyOfTheRoles([
            Caps::ROLE_LMS_ADMIN,
            Caps::ROLE_LMS_SUPPORT
        ])) {
            return;
        }

        add_action('init', function(){
            remove_action('init', 'wp_version_check');
        }, 2);

        add_filter('pre_option_update_core', '__return_null');
        add_filter('pre_site_transient_update_core', '__return_null');
    }

    private function remove_sitemap_elements() : void
    {
        add_filter( 'wp_sitemaps_add_provider', [ $this, 'remove_selected_providers_from_sitemap' ], 10, 2 );
        add_filter( 'wp_sitemaps_post_types', [ $this, 'whitelist_post_types_for_sitemap' ] );
        add_filter( 'wp_sitemaps_taxonomies', [ $this, 'remove_all_the_taxonomies_from_sitemap' ] );
    }

    public function remove_selected_providers_from_sitemap( $provider, $name )
    {
        if ( 'users' === $name ) {
            return false;
        }

        return $provider;
    }

    public function whitelist_post_types_for_sitemap( $post_types )
    {
        foreach ( $post_types as $post_type_key => $post_type ) {
            if ( ! in_array( $post_type_key, self::WHITELISTED_SITEMAP_POST_TYPES ) ) {
                unset( $post_types[ $post_type_key ] );
            }
        }

        return $post_types;
    }

    public function remove_all_the_taxonomies_from_sitemap( $taxonomies )
    {
        return [];
    }

    private function disable_author_page_for_regular_users() : void
    {
        add_filter( 'author_link', function() { return '#'; }, 99 );
        add_action( 'template_redirect', [ $this, 'disable_author_page_for_regular_users_action' ] );
    }

    public function disable_author_page_for_regular_users_action()
    {
        global $wp_query;

        if( !is_author() ) {
            return;
        }

        $author_id = $wp_query->get( 'author', null );
        if( empty( $author_id ) ) {
            return;
        }

        $user = User::find( $author_id );

        if( empty( $user ) ) {
            return;
        }

        if( $user->hasAnyOfTheRoles( self::WHITELISTED_AUTHOR_ROLES ) ) {
            return;
        }

        $wp_query->set_404();
        status_header(404);
        nocache_headers();
    }
}
