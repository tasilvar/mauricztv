<?php

namespace bpmj\wpidea\admin;

use bpmj\wpidea\admin\settings\Settings_API;
use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\Caps;
use bpmj\wpidea\certificates\Certificate_Template;
use bpmj\wpidea\infrastructure\logs\persistence\Logs_Persistence;
use bpmj\wpidea\integrations\Integrations;
use bpmj\wpidea\modules\affiliate_program\infrastructure\persistence\Interface_Commission_Persistence;
use bpmj\wpidea\modules\affiliate_program\infrastructure\persistence\Interface_External_Landing_Link_Persistence;
use bpmj\wpidea\modules\affiliate_program\infrastructure\persistence\Interface_Partner_Persistence;
use bpmj\wpidea\modules\increasing_sales\infrastructure\persistence\Interface_Offers_Persistence;
use bpmj\wpidea\modules\learning\notes\infrastructure\persistence\Interface_Note_Persistence;
use bpmj\wpidea\modules\opinions\infrastructure\persistence\Interface_Opinions_Persistence;
use bpmj\wpidea\modules\videos\core\persistence\Interface_Video_Persistence;
use bpmj\wpidea\modules\videos\web\video_changes_info\Video_Changes_Info;
use bpmj\wpidea\modules\webhooks\infrastructure\migrations\Webhook_New_Persistence_Migrator;
use bpmj\wpidea\options\Interface_Options;
use bpmj\wpidea\sales\price_history\infrastructure\persistence\Interface_Price_History_Persistence;
use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\templates_system\admin\Templates_Integrity_Checker;
use bpmj\wpidea\templates_system\Templates_System;
use bpmj\wpidea\translator\Interface_Translator;
use WP_Post;
use WP_User;

/**
 * Created by PhpStorm.
 * User: psypek
 * Date: 31.12.16
 * Time: 17:17
 */
class Upgrades
{

    /**
     *
     */
    const OPTION_KEY = 'wpidea_version';

    /**
     *
     */
    const OPTION_KEY_UPGRADES = 'wpidea_upgrades';

    /**
     *
     */
    const UPGRADE_METHOD_PATTERN = '/^u(\d{4})\_.+/';

    /**
     *
     */
    const UPGRADE_STATUS_PARTIAL = 'partial';

    /**
     *
     */
    const IS_UPGRADING_TRANSIENT = 'bpmj_eddcm_is_upgrading';

    /**
     * @var Upgrades
     */
    protected static $instance;

    /**
     * @var array
     */
    protected $upgrades;

    /**
     * @var string
     */
    protected $current_version;

    /**
     * @var Templates_Integrity_Checker
     */
    private $templates_integrity_checker;

    /**
     * @var Templates_System
     */
    private $templates_system;

    /**
     * @var Logs_Persistence
     */
    private $logs_persistence;


    private Interface_External_Landing_Link_Persistence $external_landing_link_persistence;
    private Interface_Note_Persistence $note_persistence;
    private Webhook_New_Persistence_Migrator $webhook_new_persistence_migrator;
    private Interface_Commission_Persistence $commissions_persistance;
    private Interface_Partner_Persistence $partner_persistence;
    private Video_Changes_Info $video_changes_info;
    private Interface_Video_Persistence $video_persistence;
    private Interface_Translator $translator;
    private Interface_Options $options;
    private Interface_Offers_Persistence $increasing_sales_offers_persistence;
    private Interface_Price_History_Persistence $price_history_persistence;
    private Interface_Opinions_Persistence $opinions_persistence;

    /**
     * @return bool
     */
    public function is_upgrade_needed()
    {
        if (1 === version_compare(BPMJ_EDDCM_VERSION, $this->current_version)) {
            $is_upgrading = get_transient(self::IS_UPGRADING_TRANSIENT);
            if (!$is_upgrading) {
                return true;
            }
        }

        return false;
    }

    public function __construct(
        Templates_Integrity_Checker $templates_integrity_checker,
        Templates_System $templates_system,
        Logs_Persistence $logs_persistence,
        Webhook_New_Persistence_Migrator $webhook_new_persistence_migrator,
        Interface_Commission_Persistence $commissions_persistance,
        Interface_Partner_Persistence $partner_persistence,
        Video_Changes_Info $video_changes_info,
        Interface_Video_Persistence $video_persistence,
        Interface_Translator $translator,
        Interface_Options $options,
        Interface_Note_Persistence $note_persistence,
        Interface_External_Landing_Link_Persistence $external_landing_link_persistence,
        Interface_Offers_Persistence $increasing_sales_offers_persistence,
        Interface_Price_History_Persistence $price_history_persistence,
        Interface_Opinions_Persistence $opinions_persistence
    ) {
        $this->current_version = get_option(self::OPTION_KEY, '0.0.0.0');
        $this->upgrades = get_option(self::OPTION_KEY_UPGRADES, []);

        $this->templates_integrity_checker = $templates_integrity_checker;
        $this->templates_system = $templates_system;
        $this->logs_persistence = $logs_persistence;
        $this->webhook_new_persistence_migrator = $webhook_new_persistence_migrator;
        $this->commissions_persistance = $commissions_persistance;
        $this->partner_persistence = $partner_persistence;
        $this->video_persistence = $video_persistence;
        $this->video_changes_info = $video_changes_info;
        $this->translator = $translator;
        $this->options = $options;
        $this->note_persistence = $note_persistence;
        $this->external_landing_link_persistence = $external_landing_link_persistence;
        $this->increasing_sales_offers_persistence = $increasing_sales_offers_persistence;
        $this->price_history_persistence = $price_history_persistence;
        $this->opinions_persistence = $opinions_persistence;
    }

    /**
     * @return array
     */
    public function get_pending_upgrades()
    {
        $methods = get_class_methods($this);
        $pending_upgrades = array();
        foreach ($methods as $method) {
            $method_entry = isset($this->upgrades[$method]) ? $this->upgrades[$method] : null;
            if (1 === preg_match(self::UPGRADE_METHOD_PATTERN, $method) && (!$method_entry || static::UPGRADE_STATUS_PARTIAL === $method_entry['result'])) {
                $pending_upgrades[] = $method;
            }
        }
        sort($pending_upgrades);

        return $pending_upgrades;
    }

    /**
     * @param bool $mark_only
     */
    public function auto_upgrade($mark_only = false)
    {
        if ($this->is_upgrade_needed() && (!defined('DOING_AJAX') || !DOING_AJAX)) {
            set_transient(self::IS_UPGRADING_TRANSIENT, true, 60);
            do_action('bpmj_eddcm_before_upgrade');
            $upgrade_finished = $this->do_pending_upgrades($mark_only);
            if ($upgrade_finished) {
                do_action('bpmj_eddcm_after_upgrade');
                update_option(self::OPTION_KEY, BPMJ_EDDCM_VERSION);
            }
            delete_transient(self::IS_UPGRADING_TRANSIENT);
        }
    }

    /**
     * @param bool $mark_only
     *
     * @return bool
     */
    public function do_pending_upgrades($mark_only = false)
    {
        $anything_partial = false;
        foreach ($this->get_pending_upgrades() as $upgrade_method) {
            $result = null;
            if (!$mark_only) {
                $result = $this->{$upgrade_method}();
                if (static::UPGRADE_STATUS_PARTIAL === $result) {
                    $anything_partial = true;
                }
            }
            $this->mark_upgrade_as_complete($upgrade_method, $result);
        }

        return !$anything_partial;
    }

    /**
     * @param string $upgrade_method
     * @param bool|mixed $result
     */
    private function mark_upgrade_as_complete($upgrade_method, $result)
    {
        $this->upgrades[$upgrade_method] = array(
            'date' => date('Y-m-d H:i:s'),
            'version' => BPMJ_EDDCM_VERSION,
            'result' => $result,
            'iteration' => $this->get_upgrade_iteration($upgrade_method) + 1,
        );
        update_option(self::OPTION_KEY_UPGRADES, $this->upgrades);
    }

    /**
     * @param string $upgrade_method
     *
     * @return int
     */
    private function get_upgrade_iteration($upgrade_method)
    {
        if (!isset($this->upgrades[$upgrade_method])) {
            return 0;
        }
        if (!isset($this->upgrades[$upgrade_method]['iteration'])) {
            return 0;
        }

        return (int)$this->upgrades[$upgrade_method]['iteration'];
    }

    /*******************************
     * ADD UPGRADE FUNCTIONS BELOW *
     *******************************/

    /**
     * Issue #68
     * Issue #70
     */
    public function u0001_product_page()
    {
        flush_rewrite_rules();
    }

    /**
     * Issue #88
     */
    public function u0003_courses_list_page()
    {
        $wpidea_settings = get_option('wp_idea');
        if (empty($wpidea_settings['course_list_page'])) {
            // Create new page
            $list_page_id = wp_insert_post(
                array(
                    'post_title' => __('Courses list', BPMJ_EDDCM_DOMAIN),
                    'post_content' => '[courses]',
                    'post_status' => 'publish',
                    'post_author' => 1,
                    'post_type' => 'page',
                    'comment_status' => 'closed',
                )
            );
            $wpidea_settings['course_list_page'] = $list_page_id;
        }
        if (!empty($wpidea_settings['override_all']) && 'on' === $wpidea_settings['override_all']) {
            $old_show_on_front = get_option('show_on_front');
            if ('page' !== $old_show_on_front) {
                $wpidea_settings['_old_show_on_front'] = $old_show_on_front;
                update_option('show_on_front', 'page');
                update_option('page_on_front', $wpidea_settings['course_list_page']);
            }
        }
        update_option('wp_idea', $wpidea_settings);
    }

    /**
     * Issue #61
     */
    public function u0005_update_meta_keys()
    {
        global $wpdb;
        $query_meta_ids = "
			SELECT pm.meta_id 
			  FROM {$wpdb->postmeta} pm
			 WHERE pm.meta_key = '_progress_tracking'
				   AND EXISTS (SELECT 1 FROM {$wpdb->postmeta} pm2 WHERE pm2.post_id = pm.post_id AND pm2.meta_key = '_bpmj_eddcm')";
        $meta_ids = $wpdb->get_col($query_meta_ids);
        if (!empty($meta_ids)) {
            $meta_ids_comma_separated = implode(',', $meta_ids);
            $update_query = "
				UPDATE {$wpdb->postmeta} pm 
				   SET pm.meta_key = 'progress_tracking'
				 WHERE pm.meta_id IN ({$meta_ids_comma_separated})";

            return $wpdb->query($update_query);
        }

        return 0;
    }

    /**
     * Issue #87
     */
    public function u0006_responsive_video()
    {
        $wpidea_settings = get_option('wp_idea');
        if (empty($wpidea_settings['enable_responsive_videos'])) {
            $wpidea_settings['enable_responsive_videos'] = 'on';
        }
        update_option('wp_idea', $wpidea_settings);
    }

    /**
     * Issue #95
     *
     * @param bool $add_my_courses
     */
    public function u0009_dynamic_menu($add_my_courses = false)
    {
        $wpidea_settings = get_option('wp_idea');
        $menu_id = 0;
        $locations = get_nav_menu_locations();
        $menu_items = array();
        if (has_nav_menu('bpmj_eddcm_courses')) {
            return;
        }
        if (!$menu_id) {
            // Check for WP Idea menu from previous installations
            $already_existing_menu_id = WPI()->courses->get_courses_menu();
            if ($already_existing_menu_id) {
                $locations['bpmj_eddcm_courses'] = $already_existing_menu_id;
                set_theme_mod('nav_menu_locations', $locations);

                return;
            }

            // No menu pinned to the location - we create a new one
            $menu_id = wp_create_nav_menu('WP Idea menu');
            $locations['bpmj_eddcm_courses'] = $menu_id;
            set_theme_mod('nav_menu_locations', $locations);
        }

        // Move existing menu items down
        foreach ($menu_items as $menu_item) {
            /* @var $menu_item WP_Post */
            $menu_item_data = array(
                'menu-item-object-id' => $menu_item->object_id,
                'menu-item-object' => $menu_item->object,
                'menu-item-parent-id' => $menu_item->menu_item_parent,
                'menu-item-position' => $menu_item->menu_order + ($add_my_courses ? 2 : 1),
                'menu-item-type' => $menu_item->type,
                'menu-item-title' => $menu_item->title,
                'menu-item-url' => $menu_item->url,
                'menu-item-description' => $menu_item->description,
                'menu-item-attr-title' => $menu_item->attr_title,
                'menu-item-target' => $menu_item->target,
                'menu-item-classes' => is_array($menu_item->classes) ? implode(' ', $menu_item->classes) : $menu_item->classes,
                'menu-item-xfn' => $menu_item->xfn,
                'menu-item-status' => $menu_item->post_status,
            );
            wp_update_nav_menu_item($menu_id, $menu_item->ID, $menu_item_data);
        }

        // Add course panel menu item
        $panel_menu_item_data = array(
            'menu-item-position' => 1,
            'menu-item-type' => 'custom',
            'menu-item-title' => __('Course Panel', BPMJ_EDDCM_DOMAIN),
            'menu-item-url' => '#bpmj-eddcm-panel#',
            'menu-item-status' => 'publish',
        );
        wp_update_nav_menu_item($menu_id, 0, $panel_menu_item_data);

        if ($add_my_courses) {
            // Add course panel menu item
            $panel_menu_item_data = array(
                'menu-item-position' => 2,
                'menu-item-type' => 'custom',
                'menu-item-title' => __('My courses', BPMJ_EDDCM_DOMAIN),
                'menu-item-url' => '#bpmj-eddcm-my-courses#',
                'menu-item-status' => 'publish',
            );
            wp_update_nav_menu_item($menu_id, 0, $panel_menu_item_data);
        }

        if (isset($wpidea_settings['contact_page']) && is_numeric($wpidea_settings['contact_page'])) {
            // Add contact page menu item (if it's specified)
            $contact_page = get_post($wpidea_settings['contact_page']);
            $contact_menu_item_data = array(
                'menu-item-object-id' => $contact_page->ID,
                'menu-item-object' => $contact_page->post_type,
                'menu-item-type' => 'post_type',
                'menu-item-title' => $contact_page->post_title,
                'menu-item-url' => get_permalink($contact_page->ID),
                'menu-item-status' => $contact_page->post_status,
            );
            wp_update_nav_menu_item($menu_id, 0, $contact_menu_item_data);
        }

        // Add login/logout menu item
        $login_menu_item_data = array(
            'menu-item-position' => 0,
            'menu-item-type' => 'custom',
            'menu-item-title' => __('Login', BPMJ_EDDCM_DOMAIN) . '|' . __('Log Out', BPMJ_EDDCM_DOMAIN),
            'menu-item-url' => '#bpmj-eddcm-login#',
            'menu-item-status' => 'publish',
        );
        wp_update_nav_menu_item($menu_id, 0, $login_menu_item_data);
    }

    /**
     * Add profile editor page
     * Issue #104
     */
    public function u0010_profile_editor_page()
    {
        $wpidea_settings = get_option('wp_idea');
        if (empty($wpidea_settings['profile_editor_page'])) {
            // Create new page
            $profile_editor_page_id = wp_insert_post(
                array(
                    'post_title' => __('Profile', BPMJ_EDDCM_DOMAIN),
                    'post_content' => '[edd_profile_editor]',
                    'post_status' => 'publish',
                    'post_author' => 1,
                    'post_type' => 'page',
                    'comment_status' => 'closed',
                )
            );
            $wpidea_settings['profile_editor_page'] = $profile_editor_page_id;
            update_option('wp_idea', $wpidea_settings);
        }
    }

    /**
     * Issue #97
     */
    public function u0011_fix_edd_variable_prices()
    {
        $args = array(
            'post_status' => 'any',
            'post_type' => 'download',
            'posts_per_page' => -1,
            'fields' => 'ids',
        );

        $all_downloads = get_posts($args);

        /* @var int $download_id */
        foreach ($all_downloads as $download_id) {
            if (!is_array(get_post_meta($download_id, 'edd_variable_prices', true))) {
                update_post_meta($download_id, 'edd_variable_prices', array());
            }
        }
    }

    /**
     *
     */
    public function u0012_fix_edd_freshmail_meta_keys()
    {
        $args = array(
            'post_status' => 'any',
            'post_type' => 'courses',
            'posts_per_page' => -1,
            'fields' => 'ids',
        );

        $all_courses = get_posts($args);

        foreach ($all_courses as $course_id) {
            $product_id = get_post_meta($course_id, 'product_id', true);
            $edd_freshmail_course = get_post_meta($course_id, 'edd_freshmail', true);
            if (!empty($edd_freshmail_course)) {
                delete_post_meta($course_id, 'edd_freshmail');
                update_post_meta($course_id, '_edd_freshmail', $edd_freshmail_course);
            }
            if ($product_id) {
                $edd_freshmail_product = get_post_meta($product_id, 'edd_freshmail', true);
                if (!empty($edd_freshmail_product)) {
                    delete_post_meta($product_id, 'edd_freshmail');
                    update_post_meta($product_id, '_edd_freshmail', $edd_freshmail_product);
                }
            }
        }
    }

    /**
     * Issue #186
     */
    public function u0013_product_page()
    {
        flush_rewrite_rules();
    }

    /**
     *
     */
    public function u0014_change_comment_status_on_products()
    {
        $args = array(
            'post_status' => 'any',
            'post_type' => 'download',
            'posts_per_page' => -1,
            'comment_status' => 'open',
            'fields' => 'ids',
        );

        $all_products = get_posts($args);

        foreach ($all_products as $product_id) {
            if (WPI()->courses->get_course_by_product($product_id)) {
                // Disable comments for products linked with courses
                wp_update_post(array(
                    'ID' => $product_id,
                    'comment_status' => 'closed',
                ));
            }
        }
    }

    /**
     *
     */
    public function u0015_reset_course_participants()
    {
        $courses = WPI()->courses->get_courses();
        foreach ($courses as $course) {
            delete_post_meta($course['id'], '_bpmj_eddcm_participants');
        }
    }

    /**
     *
     */
    public function u0016_redirect_to_after_login()
    {
        $wpidea_settings = get_option('wp_idea');
        if (empty($wpidea_settings['page_to_redirect_to_after_login'])) {
            $wpidea_settings['page_to_redirect_to_after_login'] = $wpidea_settings['profile_editor_page'];
            update_option('wp_idea', $wpidea_settings);
        }
    }

    /**
     * @return bool|string
     */


    public function u0019_fix_products_meta()
    {
        $product_ids = get_posts(array(
            'post_type' => 'download',
            'fields' => 'ids',
        ));

        foreach ($product_ids as $product_id) {
            $course = WPI()->courses->get_course_by_product($product_id);
            if (false !== $course) {
                $course_id = $course->ID;
                $download_files = get_post_meta($product_id, 'edd_download_files', true);
                if (!is_array($download_files)) {
                    $course_page_id = get_post_meta($course_id, 'course_id', true);
                    $download_files = array();
                    $download_files[$course_page_id] = array(
                        'index' => 1,
                        'name' => __('Course Panel', BPMJ_EDDCM_DOMAIN),
                        'file' => get_permalink($course_page_id),
                        'attachment_id' => 0,
                        'condition' => 'all'
                    );
                    update_post_meta($product_id, 'edd_download_files', $download_files);
                }
            }
        }
    }

    /**
     *
     */
    public function u0020_remove_unnecessary_wpfa_integrations()
    {
        $wpfa_options = get_option('bpmj_wpfa_settings', array());
        if (empty($wpfa_options)) {
            $wpfa_options = array();
        }
        $wpfa_options = array_merge($wpfa_options, array(
            'bp_integration' => '1',
            'woo_integration' => '1',
        ));

        update_option('bpmj_wpfa_settings', $wpfa_options);
    }

    /**
     * Add voucher page
     * Issue #303
     */
    public function u0021_voucher_page()
    {
        $wpidea_settings = get_option('wp_idea');
        if (empty($wpidea_settings['voucher_page']) || null === get_post(bpmj_eddcm_get_option('voucher_page'))) {
            $voucher_page_id = wp_insert_post(
                array(
                    'post_title' => __('Voucher', BPMJ_EDDCM_DOMAIN),
                    'post_content' => '[download_checkout]',
                    'post_status' => 'publish',
                    'post_author' => 1,
                    'post_type' => 'page',
                    'comment_status' => 'closed',
                )
            );
            $wpidea_settings['voucher_page'] = $voucher_page_id;
        }
        update_option('wp_idea', $wpidea_settings);
    }

    /**
     *
     */
    public function u0022_purchase_notification_options()
    {
        $wpidea_settings = get_option('wp_idea', array());
        if (!empty($wpidea_settings['send_comment_field_to_staff']) && 'on' === $wpidea_settings['send_comment_field_to_staff']) {
            $wpidea_settings['bpmj_eddcm_admin_notice_policy'] = 'comments';
            update_option('wp_idea', $wpidea_settings);
        }
    }

    /**
     *
     */
    public function u0023_fix_course_post_dates()
    {
        $args = array(
            'post_status' => 'any',
            'post_type' => 'courses',
            'posts_per_page' => -1,
            'fields' => 'ids',
        );

        $all_courses = get_posts($args);
        foreach ($all_courses as $course_id) {
            $course = get_post($course_id);
            $course_page_id = get_post_meta($course_id, 'course_id', true);
            $product_id = get_post_meta($course_id, 'product_id', true);
            if ($course) {
                if ($course_id) {
                    wp_update_post(array(
                        'ID' => $course_page_id,
                        'post_date' => $course->post_date,
                        'post_date_gmt' => $course->post_date_gmt,
                    ));
                }
                if ($product_id) {
                    wp_update_post(array(
                        'ID' => $product_id,
                        'post_date' => $course->post_date,
                        'post_date_gmt' => $course->post_date_gmt,
                    ));
                }
            }
        }
    }

    /**
     *
     */
    public function u0024_move_categories_and_tags_settings()
    {
        $settings_slug = 'wp_idea';
        $template_settings_slug = 'wp_idea-layout-template-settings';

        $wpidea_settings = get_option($settings_slug);
        $wpidea_layout_settings = get_option($template_settings_slug);

        if (empty($wpidea_layout_settings['default'])) {
            return;
        }
        $wpidea_layout_settings = $wpidea_layout_settings['default'];

        if (empty($wpidea_layout_settings['display_categories']) && empty($wpidea_layout_settings['display_tags'])) {
            return;
        }

        $wpidea_settings['display_categories'] = empty($wpidea_layout_settings['display_categories']) ? 'on' : $wpidea_layout_settings['display_categories'];
        $wpidea_settings['display_tags'] = empty($wpidea_layout_settings['display_tags']) ? 'on' : $wpidea_layout_settings['display_tags'];

        update_option($settings_slug, $wpidea_settings);
    }

    /**
     *
     */
    public function u0025_upgrade_tpay_settings()
    {
        $edd_options = get_option('edd_settings');
        $update = false;

        if (!empty($edd_options['transferuj_id'])
            && !empty($edd_options['transferuj_pin'])
            && empty($edd_options['tpay_id'])
            && empty($edd_options['tpay_pin'])
        ) {
            $edd_options['tpay_id'] = $edd_options['transferuj_id'];
            $edd_options['tpay_pin'] = $edd_options['transferuj_pin'];
            $update = true;
        }

        $enabled_gateways = !empty($edd_options['gateways']) && is_array($edd_options['gateways']) ? $edd_options['gateways'] : array();

        if (key_exists('transferuj_gateway', $enabled_gateways)) {
            $enabled_gateways['tpay_gateway'] = '1';
            unset($enabled_gateways['transferuj_gateway']);

            $update = true;
            $edd_options['gateways'] = $enabled_gateways;
        }

        if (isset($edd_options['default_gateway']) && 'transferuj_gateway' === $edd_options['default_gateway']) {
            $update = true;
            $edd_options['default_gateway'] = 'tpay_gateway';
        }

        if ($update) {
            update_option('edd_settings', $edd_options);
        }
    }

    /**
     *
     */
    public function u0026_invoices()
    {
        $settings = new Settings_API();
        $mode = $settings->get_option('invoices', false, 'wp_idea');

        $options = get_option('edd_settings');

        $options['edd_id_gateways'] = array();

        // Turn on payment gates for invoices
        if ($mode == 'on') {

            $options['edd_id_gateways'] = array(
                'manual' => 1,
                'paypal' => 1,
                'tpay_gateway' => 1,
                'dotpay_gateway' => 1,
                'przelewy24_gateway' => 1,
                'payu' => 1,
                'stripe' => 1,
                'paynow_gateway' => 1,
            );

        }

        update_option('edd_settings', $options);
    }

    /**
     *
     */
    public function u0027_certificates_page()
    {
        $wpidea_settings = get_option('wp_idea');
        if (empty($wpidea_settings['certificates_page']) || null === get_post(bpmj_eddcm_get_option('certificates_page'))) {
            $certificates_page_id = wp_insert_post(
                array(
                    'post_title' => __('My certificates', BPMJ_EDDCM_DOMAIN),
                    'post_content' => '',
                    'post_status' => 'publish',
                    'post_author' => 1,
                    'post_type' => 'page',
                    'comment_status' => 'closed',
                )
            );
            $wpidea_settings['certificates_page'] = $certificates_page_id;
        }
        update_option('wp_idea', $wpidea_settings);
    }

    public function u0028_update_resolved_quizes_for_filtering_in_admin()
    {
        $query = new \WP_Query(array(
            'post_type' => 'tests',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'course_id',
                    'compare' => 'NOT EXISTS',
                ),
            ),
        ));

        foreach ($query->get_posts() as $post) {
            $quiz_id = get_post_meta($post->ID, 'quiz_id', true);
            update_post_meta($post->ID, 'course_id', WPI()->courses->get_course_top_page($quiz_id));

            $user_email = get_post_meta($post->ID, 'user_email', true);

            $user = get_user_by( 'email', $user_email );

            update_post_meta($post->ID, 'user_first_name', $user->first_name);
            update_post_meta($post->ID, 'user_last_name', $user->last_name);
        }
    }

    public function u0029_update_is_passed_for_tests()
    {
        $query = new \WP_Query(array(
            'post_type' => 'tests',
            'posts_per_page' => -1,
        ));

        foreach ($query->get_posts() as $post) {
            $quiz_id = get_post_meta($post->ID, 'quiz_id', true);
            $evaluated = get_post_meta($quiz_id, 'evaluated_by_admin_mode', true);

            $points = get_post_meta($post->ID, 'points', true);
            $pass_points = get_post_meta($post->ID, 'test_questions_points_pass', true);
            if ('on' == $evaluated) {
                // should have is_passed if evaluated
            } else if ($points >= $pass_points) {
                update_post_meta($post->ID, 'is_passed', 'yes');
            } else {
                update_post_meta($post->ID, 'is_passed', 'no');
            }
        }
    }

    public function u0030_flush()
    {
        flush_rewrite_rules();
    }

    public function u0031_update_thousands_separator()
    {
        $edd_settings = get_option('edd_settings');
        $edd_settings['thousands_separator'] = '';
        update_option('edd_settings', $edd_settings);
    }

    public function u0032_stripe_initial_config()
    {
        @EDD()->customer_meta->create_table();

        $edd_settings = get_option('edd_settings');
        $edd_settings['stripe_billing_fields'] = 'zip_country';
        $edd_settings['stripe_use_existing_cards'] = '1';
        update_option('edd_settings', $edd_settings);

        update_option('edds_stripe_connect_intro_notice_dismissed', true);
    }

    public function u0033_fix_background_images_path_settings()
    {
        $layout_settings_key = 'wp_idea-layout-template-settings';
        $layout_settings = get_option($layout_settings_key);
        $did_something_changed = false;

        // fields sanitized with sanitize_image function
        $fields_to_fix = array(
            'login_bg_file',
            'bg_file',
            'section_bg_file'
        );

        // templates settings indexes in the settings array
        $templates_to_fix = array(
            'scarlet',
            'default'
        );

        foreach ($templates_to_fix as $template_name) {
            foreach ($fields_to_fix as $field_name) {
                $field_value = !empty($layout_settings[$template_name][$field_name]) ? $layout_settings[$template_name][$field_name] : null;

                // skip if field is empty
                if (empty($field_value)) continue;

                // skip if path is not wrong
                if (strpos($field_value, '../../../../../../') === false) continue;

                // replace wrong path
                $layout_settings[$template_name][$field_name] = str_replace('../../../../../../', '../../../../', $field_value);

                // something changed
                $did_something_changed = true;
            }
        }

        // update only if something changed
        if ($did_something_changed) {
            update_option($layout_settings_key, $layout_settings);
        }
    }

    public function u0034_fix_lms_admin_users_list_access()
    {
        $role = get_role(Caps::ROLE_LMS_ADMIN);

        if (empty($role)) return;

        $role->add_cap('list_users');
        $role->add_cap('remove_users');
        $role->add_cap('delete_users');
        $role->add_cap('create_users');
    }

    public function u0035_paynow_invoices()
    {
        $settings = new Settings_API();
        $mode = $settings->get_option('invoices', false, 'wp_idea');

        $options = get_option('edd_settings');

        $options['edd_id_gateways'] = array();

        // Turn on payment gates for invoices
        if ($mode == 'on') {

            $options['edd_id_gateways'] = WPI()->diagnostic->get_gateways_for_invoices();

        }

        update_option('edd_settings', $options);
    }

    public function u0036_reload_caps_for_roles()
    {
        $role = get_role(Caps::ROLE_LMS_ADMIN);

        $role->add_cap('promote_user');
        $role->add_cap('promote_users');
    }

    public function u0037_fix_lms_admin_caps()
    {
        $roles_to_fix = [
            Caps::ROLE_LMS_ADMIN,
            Caps::ROLE_LMS_SUPPORT
        ];

        foreach ($roles_to_fix as $index => $role_name) {
            $role = get_role($role_name);

            if (empty($role)) continue;

            $role->add_cap('read_private_posts');
        }
    }

    public function u0038_reload_caps_for_roles()
    {
        $role_lms_admin = get_role(Caps::ROLE_LMS_ADMIN);
        $role_lms_admin->add_cap(Caps::CAP_VIEW_REPORTS);
        $role_lms_admin->add_cap(Caps::CAP_EXPORT_REPORTS);

        $role_lms_support = get_role(Caps::ROLE_LMS_SUPPORT);
        $role_lms_support->add_cap(Caps::CAP_VIEW_REPORTS);
        $role_lms_support->add_cap(Caps::CAP_EXPORT_REPORTS);
    }

    public function u0039_change_role_names()
    {
        global $wpdb;
        $user_roles = get_option( $wpdb->prefix . 'user_roles' );
        if ( __( 'LMS Admin', BPMJ_EDDCM_DOMAIN ) !== $user_roles['lms_admin']['name'] ) {
            $user_roles['lms_admin']['name'] = __('LMS Admin', BPMJ_EDDCM_DOMAIN);
            $user_roles['lms_support']['name'] = __('LMS Support', BPMJ_EDDCM_DOMAIN);
            update_option($wpdb->prefix . 'user_roles', $user_roles);
        }
    }

    public function u0040_add_manage_discounts_caps()
    {
        $role_lms_admin = get_role(Caps::ROLE_LMS_ADMIN);
        $role_lms_admin->add_cap(Caps::CAP_MANAGE_DISCOUNTS);

        $role_lms_support = get_role(Caps::ROLE_LMS_SUPPORT);
        $role_lms_support->add_cap(Caps::CAP_MANAGE_DISCOUNTS);
    }

    public function u0041_minify()
    {
        WPI()->templates->minify_css();
    }

    public function u0042_reload_caps_for_roles()
    {
        $role_lms_admin = get_role(Caps::ROLE_LMS_ADMIN);
        $role_lms_admin->add_cap(Caps::CAP_VIEW_CUSTOMERS);
        $role_lms_admin->add_cap(Caps::CAP_DELETE_CUSTOMERS);

        $role_lms_support = get_role(Caps::ROLE_LMS_SUPPORT);
        $role_lms_support->add_cap(Caps::CAP_VIEW_CUSTOMERS);
        $role_lms_support->add_cap(Caps::CAP_DELETE_CUSTOMERS);
    }

    public function u0037_default_certificate_template()
    {
        $certificate = new Certificate_Template();
        if($certificate->was_installed()){
            return;
        }

        $classic = '<div id="pb-page" data-orientation="horizontal" style="width: 1122px; display: inline-block; margin:0; height: 794px; position: relative; overflow: hidden; border: none; background: url('.get_home_url().'/wp-content/plugins/wp-idea/assets/imgs/cert01.jpeg) 0% 0% / contain;"><div class="helperx" style="display: none; left: 554.5px;"></div><div class="helpery" style="display: none; top: 460px;"></div><div data-readonly="true" style="position: absolute; min-height: 30px; min-width: 50px; height: auto; width: 809px; top: 413.212px; left: 150.979px; right: auto; bottom: auto;" class="ui-draggable ui-resizable"><div class="component-box mce-content-body" id="pY5HYCuQCx" data-name="course_name" data-readonly="true" title="Variable (the content will be dynamically replaced when generating the certificate)" contenteditable="true" style="position: relative;" spellcheck="false"><p style=" font-size: 24pt;  line-height: 1.5; margin: 0px;" data-mce-style=" font-size: 24pt;  line-height: 1.5; margin: 0px;"><span style="font-family: arial, helvetica, sans-serif;" data-mce-style="font-family: arial, helvetica, sans-serif;">{course_name}</span></p></div><input type="hidden" name="pY5HYCuQCx"><input type="hidden" name="pY5HYCuQCx"><input type="hidden" name="pY5HYCuQCx"><div class="component-elements-bg" style="display:none"><div class="draggable-icon ui-draggable-handle" title="Move"><span class="dashicons dashicons-move"></span></div><div class="remove-element" title="Usuń"><span class="dashicons dashicons-no-alt"></span></div></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div></div><div data-readonly="true" style="position: absolute; min-height: 30px; min-width: 50px; height: auto; width: 814px; top: 333.215px; left: 149.99px; right: auto; bottom: auto;" class="ui-draggable ui-resizable"><div class="component-box mce-content-body" id="NYvZMMPJOv" data-name="student_name" data-readonly="true" title="Variable (the content will be dynamically replaced when generating the certificate)" contenteditable="true" style="position: relative;" spellcheck="false"><p style=" font-size: 24pt;  line-height: 1.5; margin: 0px;" data-mce-style=" font-size: 24pt;  line-height: 1.5; margin: 0px;"><span style="font-family: arial, helvetica, sans-serif;" data-mce-style="font-family: arial, helvetica, sans-serif;">{student_name}</span></p></div><input type="hidden" name="NYvZMMPJOv"><input type="hidden" name="NYvZMMPJOv"><input type="hidden" name="NYvZMMPJOv"><div class="component-elements-bg" style="display:none"><div class="draggable-icon ui-draggable-handle" title="Move"><span class="dashicons dashicons-move"></span></div><div class="remove-element" title="Usuń"><span class="dashicons dashicons-no-alt"></span></div></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div></div><div data-readonly="true" style="position: absolute; min-height: 30px; min-width: 50px; height: auto; width: 200px; top: 576.222px; left: 756.535px; right: auto; bottom: auto;" class="ui-draggable ui-resizable"><div class="component-box mce-content-body" id="gfbPPwmLEx" data-name="certificate_date" data-readonly="true" title="Variable (the content will be dynamically replaced when generating the certificate)" contenteditable="true" style="position: relative;" spellcheck="false"><p style=" font-size: 24pt;  line-height: 1.5; margin: 0px; text-align: center;" data-mce-style=" font-size: 24pt;  line-height: 1.5; margin: 0px; text-align: center;"><span style="font-family: arial, helvetica, sans-serif;" data-mce-style="font-family: arial, helvetica, sans-serif;">{certificate_date}</span></p></div><input type="hidden" name="gfbPPwmLEx"><input type="hidden" name="gfbPPwmLEx"><input type="hidden" name="gfbPPwmLEx"><div class="component-elements-bg" style="display:none"><div class="draggable-icon ui-draggable-handle" title="Move"><span class="dashicons dashicons-move"></span></div><div class="remove-element" title="Usuń"><span class="dashicons dashicons-no-alt"></span></div></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div></div><div style="width: 232px; height: 83px; position: absolute; min-height: 30px; min-width: 50px; right: auto; bottom: auto; left: 144px; top: 547.979px;" class="ui-draggable ui-resizable"><div class="component-box" data-disable-editor="true" id="wCCbFdKT3b" style="width:100%;height:100%;background:url('.get_home_url().'/wp-content/plugins/wp-idea/assets/imgs/wp-idea-logo.png);background-size: contain;background-repeat: no-repeat"></div><div class="component-elements-bg" style="display:none"><div class="draggable-icon ui-draggable-handle" title="Move"><span class="dashicons dashicons-move"></span></div><div class="remove-element" title="Usuń"><span class="dashicons dashicons-no-alt"></span></div></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-n" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-nw" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-ne" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-sw" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 90;"></div></div></div>';
        $certificate = new Certificate_Template();
        if($certificate->get_count_certificates() == 0){
            $certificate->set_is_default(true);
        }
        $certificate->set_page($classic);
        $certificate->set_name('Klasyczny');
        $certificate->save();

        $golden = '<div id="pb-page" data-orientation="horizontal" style="width: 1122px; display: inline-block; margin:0; height: 794px; position: relative; overflow: hidden; border: none; background: url('.get_home_url().'/wp-content/plugins/wp-idea/assets/imgs/cert02.png) 0% 0% / contain;"><div class="helperx" style="display: none; left: 559px;"></div><div class="helpery" style="display: none; top: 678px;"></div><div data-readonly="false" style="position: absolute; min-height: 30px; min-width: 50px; height: auto; width: 1118px; top: 232.212px; left: 1.97919px; right: auto; margin-left: auto; margin-right: auto; text-align: center; bottom: auto;" class="ui-draggable ui-resizable"><div class="component-box mce-content-body" id="WT4Gn47sAu" data-name="text_input" data-readonly="false" title="" contenteditable="true" style="position: relative;" spellcheck="false"><p style=" font-size: 24pt;  line-height: 1.5; margin: 0px; text-align: center;" data-mce-style=" font-size: 24pt;  line-height: 1.5; margin: 0px; text-align: center;"><span style="font-size: 30pt;" data-mce-style="font-size: 30pt;"><strong><span style="font-family: tahoma, arial, helvetica, sans-serif;" data-mce-style="font-family: tahoma, arial, helvetica, sans-serif;">Certyfikat ukończenia</span></strong></span></p></div><input type="hidden" name="WT4Gn47sAu"><input type="hidden" name="WT4Gn47sAu"><input type="hidden" name="WT4Gn47sAu"><div class="component-elements-bg" style="display:none"><div class="draggable-icon ui-draggable-handle" title="Move"><span class="dashicons dashicons-move"></span></div><div class="remove-element" title="Usuń"><span class="dashicons dashicons-no-alt"></span></div></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div></div><div data-readonly="true" style="position: absolute; min-height: 30px; min-width: 50px; height: auto; width: 1108px; top: 327.205px; left: 6.97919px; right: auto; margin-left: auto; margin-right: auto; text-align: center; bottom: auto;" class="ui-draggable ui-resizable"><div class="component-box mce-content-body" id="7KQ6a3NXzI" data-name="course_name" data-readonly="true" title="Variable (the content will be dynamically replaced when generating the certificate)" contenteditable="true" style="position: relative;" spellcheck="false"><p style=" font-size: 24pt;  line-height: 1.5; margin: 0px;" data-mce-style=" font-size: 24pt;  line-height: 1.5; margin: 0px;"><span style="font-size: 40pt;" data-mce-style="font-size: 40pt;"><strong><span style="font-family: arial, helvetica, sans-serif; color: rgb(238, 214, 165);" data-mce-style="font-family: arial, helvetica, sans-serif; color: #eed6a5;"><em>{course_name}</em></span></strong></span></p></div><input type="hidden" name="7KQ6a3NXzI"><input type="hidden" name="7KQ6a3NXzI"><input type="hidden" name="7KQ6a3NXzI"><div class="component-elements-bg" style="display:none"><div class="draggable-icon ui-draggable-handle" title="Move"><span class="dashicons dashicons-move"></span></div><div class="remove-element" title="Usuń"><span class="dashicons dashicons-no-alt"></span></div></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div></div><div data-readonly="false" style="position: absolute; min-height: 30px; min-width: 50px; height: auto; width: 1100px; top: 426.222px; left: 10.9896px; right: auto; margin-left: auto; margin-right: auto; text-align: center; bottom: auto;" class="ui-draggable ui-resizable"><div class="component-box mce-content-body" id="s6tELNYP3Z" data-name="text_input" data-readonly="false" title="" contenteditable="true" style="position: relative;" spellcheck="false"><p style=" font-size: 24pt;  line-height: 1.5; margin: 0px; text-align: center;" data-mce-style=" font-size: 24pt;  line-height: 1.5; margin: 0px; text-align: center;"><span style="font-family: tahoma, arial, helvetica, sans-serif; font-size: 30pt;" data-mce-style="font-family: tahoma, arial, helvetica, sans-serif; font-size: 30pt;">dla</span></p></div><input type="hidden" name="s6tELNYP3Z"><input type="hidden" name="s6tELNYP3Z"><input type="hidden" name="s6tELNYP3Z"><div class="component-elements-bg" style="display:none"><div class="draggable-icon ui-draggable-handle" title="Move"><span class="dashicons dashicons-move"></span></div><div class="remove-element" title="Usuń"><span class="dashicons dashicons-no-alt"></span></div></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div></div><div data-readonly="true" style="position: absolute; min-height: 30px; min-width: 50px; height: auto; width: 1109px; top: 507.212px; left: 7.48267px; right: auto; margin-left: auto; margin-right: auto; text-align: center; bottom: auto;" class="ui-draggable ui-resizable"><div class="component-box mce-content-body" id="Et5VW5bnAE" data-name="student_name" data-readonly="true" title="Variable (the content will be dynamically replaced when generating the certificate)" contenteditable="true" style="position: relative;" spellcheck="false"><p style=" font-size: 24pt;  line-height: 1.5; margin: 0px; text-align: center;" data-mce-style=" font-size: 24pt;  line-height: 1.5; margin: 0px; text-align: center;"><span style="color: rgb(238, 214, 165); font-family: arial black, sans-serif; font-size: 34pt;" data-mce-style="color: #eed6a5; font-family: \'arial black\', sans-serif; font-size: 34pt;">{student_name}</span></p></div><input type="hidden" name="Et5VW5bnAE"><input type="hidden" name="Et5VW5bnAE"><input type="hidden" name="Et5VW5bnAE"><div class="component-elements-bg" style="display:none"><div class="draggable-icon ui-draggable-handle" title="Move"><span class="dashicons dashicons-move"></span></div><div class="remove-element" title="Usuń"><span class="dashicons dashicons-no-alt"></span></div></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div></div><div style="width: 269px; height: 100px; position: absolute; min-height: 30px; min-width: 50px; right: auto; bottom: auto; left: 786px; top: 630.986px;" class="ui-draggable ui-resizable"><div class="component-box" data-disable-editor="true" id="9L5Z90w70u" style="width:100%;height:100%;background:url('.get_home_url().'/wp-content/plugins/wp-idea/assets/imgs/wp-idea-logo.png);background-size: contain;background-repeat: no-repeat"></div><div class="component-elements-bg" style="display:none"><div class="draggable-icon ui-draggable-handle" title="Move"><span class="dashicons dashicons-move"></span></div><div class="remove-element" title="Usuń"><span class="dashicons dashicons-no-alt"></span></div></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-n" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-nw" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-ne" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-sw" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 90;"></div></div><div data-readonly="true" style="position: absolute; min-height: 30px; min-width: 50px; height: auto; width: 200px; right: auto; bottom: auto; left: 86.9965px; top: 655.976px;" class="ui-draggable ui-resizable"><div class="component-box mce-content-body" id="zsmBzdcc7V" data-name="certificate_date" data-readonly="true" title="Variable (the content will be dynamically replaced when generating the certificate)" contenteditable="true" style="position: relative;" spellcheck="false"><p style=" font-size: 24pt;  line-height: 1.5; margin: 0px; text-align: center;" data-mce-style=" font-size: 24pt;  line-height: 1.5; margin: 0px; text-align: center;"><em><span style="font-family: tahoma, arial, helvetica, sans-serif;" data-mce-style="font-family: tahoma, arial, helvetica, sans-serif;">{certificate_date}</span></em></p></div><input type="hidden" name="zsmBzdcc7V"><input type="hidden" name="zsmBzdcc7V"><input type="hidden" name="zsmBzdcc7V"><div class="component-elements-bg" style="display:none"><div class="draggable-icon ui-draggable-handle" title="Move"><span class="dashicons dashicons-move"></span></div><div class="remove-element" title="Usuń"><span class="dashicons dashicons-no-alt"></span></div></div><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;"></div><div class="ui-resizable-handle ui-resizable-w" style="z-index: 90;"></div></div></div>';
        $certificate = new Certificate_Template();
        if($certificate->get_count_certificates() == 0){
            $certificate->set_is_default(true);
        }
        $certificate->set_page($golden);
        $certificate->set_name('Złoty');
        $certificate->save();
    }

    public function u0043_disable_new_version_certificates_template()
    {
        if(LMS_Settings::get_option('enable_certificates')){
            LMS_Settings::update(Certificate_Template::SETTINGS_DISABLE_NEW_VERSION, 'on');
        }
    }

    public function u0044_ensure_templates_system_integrity()
    {
        $this->templates_integrity_checker->ensure_integrity();
    }

    public function u0045_disable_new_templates_for_existing_installations()
    {
        if ($this->templates_system->is_legacy_experimental_templates_option_turned_on()) {
            return;
        }

        $this->templates_system->disable_new_templates();
    }

    public function u0046_ensure_templates_system_integrity()
    {
        $this->templates_integrity_checker->ensure_integrity();
    }

    public function u0047_setup_logs_persistence()
    {
        $this->logs_persistence->setup();
    }

    public function u0048_add_new_caps_to_existing_roles()
    {
        $role = get_role(Caps::ROLE_LMS_ADMIN);
        if (! empty($role)) {
            $role->add_cap(Caps::CAP_MANAGE_ORDERS);
            $role->add_cap(Caps::CAP_MANAGE_CUSTOMERS);
        }

        $role = get_role(Caps::ROLE_LMS_SUPPORT);
        if (! empty($role)) {
            $role->add_cap(Caps::CAP_MANAGE_ORDERS);
            $role->add_cap(Caps::CAP_MANAGE_CUSTOMERS);
        }
    }

    public function u0049_add_new_assistant_caps_to_existing_roles()
    {
        $role = get_role(Caps::ROLE_LMS_ADMIN);
        if (! empty($role)) {
            $role->add_cap(Caps::CAP_MANAGE_CERTIFICATES);
            $role->add_cap(Caps::CAP_MANAGE_QUIZZES);
        }

        $role = get_role(Caps::ROLE_LMS_SUPPORT);
        if (! empty($role)) {
            $role->add_cap(Caps::CAP_MANAGE_CERTIFICATES);
            $role->add_cap(Caps::CAP_MANAGE_QUIZZES);
        }
    }


    public function u0050_update_users_roles_form_pending_user_to_subscriber()
    {
        $default_role = get_option('default_role');
        $pending_user = 'pending_user';

        $users = get_users(array(
            'role' => $pending_user,
            'orderby' => 'ID'
        ));

        if (empty($users)) {
            return true;
        }

        /** @var WP_User $user */
        foreach ($users as $user) {
            $user->remove_role($pending_user);
            $user->add_role($default_role);
        }
    }

    public function u0051_remove_pending_user()
    {
        $pending_user = 'pending_user';

        if(get_role($pending_user)){
            remove_role($pending_user);
        }
    }


    public function u0052_setup_webhooks_persistence_and_migration(): void
    {
        $this->webhook_new_persistence_migrator->migrate();
    }


    public function u0053_add_dashboard_access_cap_to_roles()
    {
        $roles_with_access_to_dashboard = [
            Caps::ROLE_LMS_ADMIN,
            Caps::ROLE_LMS_SUPPORT
        ];

        foreach ($roles_with_access_to_dashboard as $role_name) {
            $role = get_role($role_name);
            if ($role && !$role->has_cap(Caps::CAP_ACCESS_DASHBOARD)) {
                $role->add_cap(Caps::CAP_ACCESS_DASHBOARD);
            }
        }
    }

    public function u0054_add_can_user_wp_idea_mode_cap_to_roles()
    {
        $roles_with_access_to_wp_idea_mode = [
            Caps::ROLE_LMS_ADMIN,
            Caps::ROLE_LMS_SUPPORT,
            Caps::ROLE_LMS_ACCOUNTANT,
            Caps::ROLE_LMS_ASSISTANT
        ];

        foreach ($roles_with_access_to_wp_idea_mode as $role_name) {
            $role = get_role($role_name);
            if ($role && !$role->has_cap(Caps::CAP_USE_WP_IDEA_MODE)) {
                $role->add_cap(Caps::CAP_USE_WP_IDEA_MODE);
            }
        }
    }

    public function u0056_move_vat_rates_to_new_field()
    {
        $wpidea_settings = get_option('wp_idea');

        if ( empty( $wpidea_settings['integrations'] ) ) {
            return;
        }

        $options_map = [
            'wp-fakturownia' => 'bpmj_wpfa_vat',
            'wp-ifirma' => 'bpmj_wpifirma_vat',
            'wp-wfirma' => 'bpmj_wpwf_vat',
            'wp-infakt' => 'bpmj_wpinfakt_vat',
            'wp-taxe' => 'bpmj_wptaxe_vat',
        ];

        $keys = array_keys($wpidea_settings['integrations']);

        $integration_name = '';
        foreach ($keys as $key) {
            if(array_key_exists($key, $options_map)) {
                $integration_name = $key;
                break;
            }
        }

        if(empty($integration_name)) {
            return;
        }

        $integration_option_name = Integrations::INVOICE_INTEGRATIONS[ $integration_name ]['option'];

        $integration_settings = get_option( $integration_option_name );
        $wpidea_settings['invoices_is_vat_payer'] = $integration_settings['invoice_type'] === 'faktura-vat' ? 'yes' : 'no';
        $wpidea_settings['invoices_default_vat_rate'] = $integration_settings['default_vat'];
        update_option( 'wp_idea', $wpidea_settings );

        $query = new \WP_Query( [
            'post_type' => 'courses',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ] );

        foreach ( $query->get_posts() as $post ) {
            $product_id = get_post_meta( $post->ID, 'product_id', true );

            update_post_meta( $product_id, 'invoices_vat_rate', get_post_meta( $product_id, $options_map[ $integration_name ], true ) );
        }
    }

    public function u0057_add_manage_students_role_to_lms_admin()
    {
        $roles_with_access_to_dashboard = [
            Caps::ROLE_LMS_ADMIN,
            Caps::ROLE_LMS_SUPPORT
        ];

        foreach ($roles_with_access_to_dashboard as $role_name) {
            $role = get_role($role_name);
            if ($role && !$role->has_cap(Caps::CAP_MANAGE_STUDENTS)) {
                $role->add_cap(Caps::CAP_MANAGE_STUDENTS);
            }
        }
    }

    public function u0058_setup_partner_persistence()
    {
        $this->partner_persistence->setup();
    }

    public function u0059_setup_commission_persistence(): void
    {
        $this->commissions_persistance->setup();
    }

    public function u0060_setup_video_persistence(): void
    {
        $this->video_persistence->setup();
    }

    public function u0061_init_video_changes_info(): void
    {
        $this->video_changes_info->start_showing_info();
    }

    public function u0062_invoices()
    {
        $settings = new Settings_API();
        $mode = $settings->get_option('invoices', false, 'wp_idea');

        $options = get_option('edd_settings');

        $options['edd_id_gateways'] = array();

        // Turn on payment gates for invoices
        if ($mode == 'on') {

            $options['edd_id_gateways'] = WPI()->diagnostic->get_gateways_for_invoices();

        }

        update_option('edd_settings', $options);
    }

    public function u0063_set_the_default_message_of_the_lost_cart_recovery_mechanism(): void
    {
        $settings_payment_reminders = $this->options->get(Settings_Const::PAYMENT_REMINDERS);

        if(
            !empty($settings_payment_reminders[Settings_Const::PAYMENT_REMINDERS_NUMBER_DAYS])
            && !empty($settings_payment_reminders[Settings_Const::PAYMENT_REMINDERS_MESSAGE_SUBJECT])
            && !empty($settings_payment_reminders[Settings_Const::PAYMENT_REMINDERS_MESSAGE_CONTENT])
        ){
            return;
        }

        $default_settings_payment_reminders = [
            Settings_Const::PAYMENT_REMINDERS_NUMBER_DAYS => '3',
            Settings_Const::PAYMENT_REMINDERS_MESSAGE_SUBJECT => $this->translator->translate('settings.messages.payment_reminders.message_subject.default'),
            Settings_Const::PAYMENT_REMINDERS_MESSAGE_CONTENT => $this->translator->translate('settings.messages.payment_reminders.message_content.default')
        ];

        $this->options->set(Settings_Const::PAYMENT_REMINDERS, $default_settings_payment_reminders);
    }

    public function u0064_activate_courses_module(): void
    {
        $publigo_settings = get_option('wp_idea');
        if (empty($publigo_settings[Settings_Const::COURSES_ENABLED])) {
            $publigo_settings[Settings_Const::COURSES_ENABLED] = LMS_Settings::VALUE_ENABLED;
            update_option('wp_idea', $publigo_settings);
        }
    }

    public function u0065_set_the_default_message_of_the_lost_cart_recovery_mechanism(): void
    {
        $settings_payment_reminders = $this->options->get(Settings_Const::PAYMENT_REMINDERS);

        if(!empty($settings_payment_reminders[Settings_Const::PAYMENT_REMINDERS_ENABLED])){
            return;
        }

        $default_settings_payment_reminders = [
            Settings_Const::PAYMENT_REMINDERS_NUMBER_DAYS => '3',
            Settings_Const::PAYMENT_REMINDERS_MESSAGE_SUBJECT => $this->translator->translate('settings.messages.payment_reminders.message_subject.default'),
            Settings_Const::PAYMENT_REMINDERS_MESSAGE_CONTENT => $this->translator->translate('settings.messages.payment_reminders.message_content.default')
        ];

        $this->options->set(Settings_Const::PAYMENT_REMINDERS, $default_settings_payment_reminders);
    }

    public function u0066_ensure_templates_system_integrity()
    {
        $this->templates_integrity_checker->ensure_integrity();
    }

    public function u0067_setup_note_persistence(): void
    {
        $this->note_persistence->setup();
    }

    public function u0068_setup_external_landing_link_persistence(): void
    {
        $this->external_landing_link_persistence->setup();
    }

    public function u0069_add_campaign_column_in_commission_table(): void
    {
        $this->commissions_persistance->add_campaign_column_in_table();
    }

    public function u0070_add_can_user_wp_idea_mode_cap_to_roles()
    {
        $this->u0054_add_can_user_wp_idea_mode_cap_to_roles();
    }

    public function u0071_setup_increasing_sales_offers_persistence(): void
    {
        $this->increasing_sales_offers_persistence->setup();
    }

    public function u0072_setup_price_history_persistence(): void
    {
        $this->price_history_persistence->setup();
    }

    public function u0073_update_admin_sale_notification(): void
    {
        $edd_settings = get_option( 'edd_settings' );
        $edd_settings['sale_notification_subject'] = $this->translator->translate('settings.sections.messages.admin_sale_notification.title.default');
        $edd_settings['sale_notification'] = $this->translator->translate('settings.sections.messages.admin_sale_notification.message.default');
        update_option('edd_settings', $edd_settings);
    }

    public function u0074_update_admin_sale_notification(): void
    {
        $this->u0073_update_admin_sale_notification();
    }

    public function u0075_setup_opinions_persistence(): void
    {
        $this->opinions_persistence->setup();
    }

    public function u0076_fix_cart_template() {
        $query = new \WP_Query([
            'posts_per_page' => 1,
            'post_type' => 'wpi_page_templates',
            'meta_query' => array(
                array(
                    'key' => '_class_name',
                    'value'   => 'bpmj\wpidea\templates_system\templates\scarlet\Experimental_Cart_Template'
                ),
            )
        ]);

        foreach ($query->get_posts() as $post) {
            $new_content = str_replace([
                '<!-- wp:column {"width":66.66} -->',
                '<!-- wp:column {"width":33.33} -->',
                'style="flex-basis:30%; margin-left:3.3%;"'
            ],[
                '<!-- wp:column {"width":"66.66%"} -->',
                '<!-- wp:column {"width":"33.33%"} -->',
                'style="flex-basis:33.33%"'
            ], $post->post_content);

            wp_update_post(['ID' => $post->ID, 'post_content' => $new_content]);
        }
    }

}