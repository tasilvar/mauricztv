<?php

namespace bpmj\wpidea;

use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\modules\app_view\api\App_View_API_Static_Helper;
use bpmj\wpidea\templates_system\Experimental_Cart_View_Handler;
use bpmj\wpidea\translator\Interface_Translator;
use _WP_Dependency;
use bpmj\wpidea\controllers\Payment_Controller;
use bpmj\wpidea\data_types\Url;
use bpmj\wpidea\events\filters\Filter_Name;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\notices\User_Notice;
use bpmj\wpidea\notices\User_Notice_Service;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\settings\readers\Interface_App_Visual_Settings_Reader;
use bpmj\wpidea\templates_system\admin\modules\Interface_Templates_System_Modules_Factory;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\routing\Router;
use bpmj\wpidea\environment;
use DOMDocument;

/**
 * Klasa odpowiedzialna za obsługę
 * szablonów lekcji/modułów/koszyka itp.
 */
class Templates
{
    const FEATURE_COURSE_WELCOME_BANNER = 'feature_course_welcome_banner';
    const FEATURE_COURSE_WELCOME_VIDEO = 'feature_course_welcome_video';
    const FEATURE_COURSE_SECOND_SECTION = 'feature_course_second_section';
    const FEATURE_LESSON_NAVIGATION_POSITION = 'feature_lesson_navigation_position';
    const FEATURE_LESSON_PROGRESS_POSITION = 'feature_lesson_progress_position';
    const FEATURE_LESSON_FILES_POSITION = 'feature_lesson_files_position';
    const FEATURE_LESSON_SUBTITLE = 'feature_lesson_subtitle';
    const FEATURE_LESSON_SHORT_DESCRIPTION = 'feature_lesson_short_description';

    private $url_generator;

    /**
     * Wybrany szablon
     */
    public $template;

    /**
     * Current template's options
     * @var array
     */
    public $template_config;

    /**
     * Zarejestrowane szablony
     * @var array
     */
    public $registered_templates;

    /**
     *
     * @var bool
     */
    protected $override_all;

    /**
     * List of hooks responsible for enqueuing scripts on a page
     * @var array
     */
    private static $enqueue_script_hooks = array('wp_enqueue_scripts', 'login_enqueue_scripts');

    /**
     * @var array
     */
    protected $layout_settings = array();

    private $templates_settings_handler;

    private $user_notice;

    private Interface_Filters $filters;
    private Interface_Settings $settings;
    private Interface_Translator $translator;
    private Experimental_Cart_View_Handler $experimental_cart_view_handler;
    private Interface_Actions $actions;

    function __construct(
        Interface_Url_Generator $url_generator,
        Interface_Templates_System_Modules_Factory $templates_system_modules_factory,
        User_Notice_Service $user_notice,
        Interface_Filters $filters,
        Interface_Settings $settings,
        Interface_Translator $translator,
        Experimental_Cart_View_Handler $experimental_cart_view_handler,
        Interface_Actions $actions
    ) {
        $this->templates_settings_handler = $templates_system_modules_factory->get_settings_handler();
        $this->url_generator = $url_generator;
        $this->user_notice = $user_notice;
        $this->filters = $filters;
        $this->settings = $settings;
        $this->translator = $translator;
        $this->experimental_cart_view_handler = $experimental_cart_view_handler;
        $this->actions = $actions;

        /**
         * Sprawdzamy jaki szablon jest wybrany;
         * jeżeli żaden, ładujemy domyślny
         */
        $wpidea_settings = get_option('wp_idea');
        if (!isset($wpidea_settings['template'])) {
            $this->template = 'scarlet';
        } elseif ($wpidea_settings['template'] != 'off') {
            $this->template = $wpidea_settings['template'];
        }

        $override_all = $this->templates_settings_handler->get_override_all_option_value();
        $this->override_all = !empty($override_all) && 'on' === $override_all;

        $this->init_available_templates();

        add_action('setup_theme', array($this, 'template_disable'));
    }

    public function should_show_go_back_button(): bool
    {
        return $this->experimental_cart_view_handler->should_show_go_back_button();
    }

    public function get_go_back_button_text(): ?string
    {
        return $this->experimental_cart_view_handler->get_button_text();
    }

    public function get_go_back_button_url(): ?string
    {
        return $this->experimental_cart_view_handler->get_button_url();
    }

    /**
     *
     * @return array
     */
    public function get_allowed_page_types()
    {
        return apply_filters('bpmj_eddcm_allowed_page_types', array(
            'test',
            'lesson',
            'full',
            'home',
            'contact',
            'checkout',
            'checkout_success',
            'checkout_failure',
            'purchase_history_page',
            'voucher',
            'certificate',
        ));
    }

    /**
     * Akcje i filtry szablonów
     */
    public function init()
    {
        add_filter('template_include', array($this, 'custom_template'), 99);
        foreach (self::$enqueue_script_hooks as $enqueue_script_hook) {
            add_action($enqueue_script_hook, array(
                $this,
                'hook_scripts'
            ), 1 /* $priority - enqueues other scripts, so need to run as early as possible */);
        }

        add_action('wp_enqueue_scripts', array($this, 'hook_lessons_scripts'));

        add_action('setup_theme', array($this, 'hook_init_template'), 100);

        $is_json_api_request = strpos($_SERVER['REQUEST_URI'], '/wp-json/') !== false;
        if (!$is_json_api_request && $this->is_on_supported_page() && !is_admin()) {

            /**
             * użycie tego filtra kiedy w adresie obecna jest zmienna customizera 'customize_changeset_uuid'
             * skutkuje krytycznym błędem
             */
            $http_referer = $_SERVER['HTTP_REFERER'] ?? '';
            if (!array_key_exists('customize_changeset_uuid', $_GET) && false === strpos($http_referer, 'site-editor.php')) {
                add_filter('stylesheet', array($this, 'custom_stylesheet_path'), 99);
            }
        }
        add_filter('edd_template_paths', array($this, 'edd_template_paths'));
        add_filter('edd_purchase_link_defaults', array($this, 'edd_purchase_link_defaults'));
        add_filter('embed_oembed_html', array($this, 'hook_embed_oembed_html'), 20);
        add_filter('wp_video_shortcode', array($this, 'hook_wp_video_shortcode'), 10, 2);
        add_filter('shortcode_atts_downloads', array($this, 'filter_downloads_shortcode_atts'), 10, 3);
        add_filter('bpmj_eddcm_layout_options', array($this, 'filter_set_layout_options'));

        if(!Software_Variant::is_saas()){
            add_action('add_meta_boxes', array($this, 'disable_wp_idea_template'));
        }
        add_action('save_post', array($this, 'disable_wp_idea_template_save_post'));

        // print custom css in the footer
        add_action('wp_print_footer_scripts', array($this, 'print_custom_css_template_string'));
    }

    /**
     * This hook needs to check if we are on supported page and add/remove
     * scripts only under that condition
     */
    public function hook_scripts()
    {
        if ($this->is_on_supported_page()) {
            foreach (self::$enqueue_script_hooks as $enqueue_script_hook) {
                add_action($enqueue_script_hook, array($this, 'hook_enqueue_scripts'));
                add_action($enqueue_script_hook, array(
                    $this,
                    'hook_clear_unneeded_scripts'
                ), 100 /* $priority - needs to run as the very last */);
            }
        }
    }

    public function hook_lessons_scripts()
    {
        wp_enqueue_style('bpmj-eddcm-jquery-toast', BPMJ_EDDCM_URL . 'assets/css/jquery.toast.min.css');
        wp_enqueue_script('bpmj-eddcm-jquery-toast', BPMJ_EDDCM_URL . 'assets/js/jquery.toast.min.js');
    }

    public function hook_init_template()
    {
        if (!isset($this->registered_templates[$this->template])) {
            $this->template = 'default';
        }
        $template = $this->template;
        $options_file = $this->get_template_root_dir($this->template) . '/template-config.php';
        if (file_exists($options_file)) {
            $this->template_config = include $options_file;
        }
        $hooks_file = $this->get_template_root_dir($this->template) . '/template-hooks.php';
        if (file_exists($hooks_file)) {
            include $hooks_file;
        }
        $functions_file = $this->get_template_root_dir($this->template) . '/template-functions.php';
        if (file_exists($functions_file)) {
            include $functions_file;
        }

        if($this->courses_functionality_enabled()){
            register_nav_menu('bpmj_eddcm_courses', __('WP Idea courses menu', BPMJ_EDDCM_DOMAIN));
        }
    }

    /*
     * We need to remove some styles from parent theme
     */

    public function hook_clear_unneeded_scripts()
    {
        global $wp_scripts, $wp_styles;

        $script_blacklist = apply_filters('bpmj_eddcm_script_blacklist', array(get_template() . '*'));
        $style_blacklist = apply_filters('bpmj_eddcm_style_blacklist', array(
            get_template() . '*',
            'login',
            'edd-styles',
            'bpmj_edd_invoice_data_form',
            'bpmj_wpfa_style',
        ));

        /**
         * @param string $script_handle
         * @param _WP_Dependency $script_info
         * @param array $blacklist
         *
         * @return bool
         */
        $is_blacklisted = function ($script_handle, $script_info, $blacklist) {
            foreach ($blacklist as $blacklisted_pattern) {
                if ($script_handle === $blacklisted_pattern) {
                    return true;
                }
                if (false !== strpos($blacklisted_pattern, '*')) {
                    $regex_pattern = '/^' . str_replace('\\*', '.*?', preg_quote($blacklisted_pattern, '$/')) . '/';
                    $script_src = '';
                    if ($script_info instanceof _WP_Dependency && $script_info->src) {
                        $script_src = str_replace(get_theme_root_uri() . '/', '', $script_info->src);
                    }
                    if (1 === preg_match($regex_pattern, $script_handle) || $script_src && 1 === preg_match($regex_pattern, $script_src)) {
                        return true;
                    }
                }
            }

            return false;
        };
        if (!empty($wp_scripts->queue)) {
            foreach ($wp_scripts->queue as $handle) {
                $script_info = $wp_scripts->registered[$handle];
                if ($is_blacklisted($handle, $script_info, $script_blacklist)) {
                    wp_dequeue_script($handle);
                }
            }
        }
        if (!empty($wp_styles->queue)) {
            foreach ($wp_styles->queue as $handle) {
                $style_info = $wp_styles->registered[$handle];
                if ($is_blacklisted($handle, $style_info, $style_blacklist)) {
                    wp_dequeue_style($handle);
                }
            }
        }
    }

    public function hook_enqueue_scripts()
    {
        $template_settings = $this->get_template_settings($this->template);

        if (!empty(WPI()->page)) {
            if (!WPI()->page->disable_wpi_css_for_current_template()) $this->enqueue_styles($template_settings);
        } else {
            $this->enqueue_styles($template_settings);
        }

        $this->enqueue_scripts($template_settings);
    }

    protected function enqueue_styles($template_settings)
    {
        $template_url = $this->get_template_url($this->template);

        if (!empty($this->template_config['styles'])) {
            $styles = is_array($this->template_config['styles']) ? $this->template_config['styles'] : array($this->template_config['styles']);
            foreach ($styles as $key => $style) {
                $style_url = $style;
                if (0 === preg_match('#^https?\://#i', $style_url)) {
                    $style_url = $template_url . '/' . $style;
                }
                wp_enqueue_style('bpmj-eddcm-style-' . $key, $style_url, array('dashicons'), apply_filters('bpmj_eddcm_layout_script_version', $style, $template_settings));
            }
        }
    }

    protected function enqueue_scripts($template_settings)
    {
        $template_url = $this->get_template_url($this->template);

        if (!empty($this->template_config['javascripts'])) {
            $javascripts = is_array($this->template_config['javascripts']) ? $this->template_config['javascripts'] : array($this->template_config['javascripts']);
            $first = true;
            foreach ($javascripts as $key => $js) {
                $js_url = $js;
                if (0 === preg_match('#^https?\://#i', $js_url)) {
                    $js_url = $template_url . '/' . $js;
                }
                wp_enqueue_script('bpmj-eddcm-script-' . $key, $js_url, array('jquery'), apply_filters('bpmj_eddcm_layout_script_version', $js, $template_settings));
                if ($first) {
                    $first = false;
                    wp_localize_script('bpmj-eddcm-script-' . $key, 'wpidea', array(
                        'ajaxurl' => admin_url('admin-ajax.php'),
                        'sales_disabled' => __('Sales of this course are currently disabled.'),
                        'wrong_quiz_file_type' => __('You loaded the wrong file type', BPMJ_EDDCM_DOMAIN),
                        'no_quiz_file' => __('Warning! You did not add any file to this answer.', BPMJ_EDDCM_DOMAIN),
                        'nonce_value' => Nonce_Handler::create(),
                        'nonce_name' => Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME,
                        'urls' => [
                            'payment_load_gateway' => $this->url_generator->generate(Payment_Controller::class,'load_gateway'),
                            'payment_process_checkout' => $this->url_generator->generate(Payment_Controller::class,'process_checkout'),
                            'payment_add_to_cart' =>$this->url_generator->generate(Payment_Controller::class,'add_to_cart'),
                        ]
                    ));
                }
            }
        }

        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
    }

    /**
     * @param string $key
     *
     * Set's up the list of registered templates
     */
    private function init_available_templates()
    {
        $this->registered_templates = apply_filters('bpmj_eddcm_registered_templates', array(
            'default' => array(
                'name' => __('Default template', BPMJ_EDDCM_DOMAIN),
                'path' => BPMJ_EDDCM_TEMPLATES_DIR . 'default',
                'url' => BPMJ_EDDCM_TEMPLATES_URL . 'default',
            ),
            'scarlet' => array(
                'name' => __('Scarlet', BPMJ_EDDCM_DOMAIN),
                'path' => BPMJ_EDDCM_TEMPLATES_DIR . 'scarlet',
                'url' => BPMJ_EDDCM_TEMPLATES_URL . 'scarlet',
            ),
        ));
    }

    public function template_disable()
    {
        if(is_admin()) {
            $this->init();
            return;
        }

        if ($this->template) {
            $url = "//" . ($_SERVER['HTTP_HOST'] ?? '') . $_SERVER['REQUEST_URI'];
            $id = url_to_postid($url);
            $parsed_url = parse_url($url);

            if (0 === $id) {
                $parsed_site_url = parse_url(get_site_url());
                if(!isset($parsed_site_url['path']) || !isset($parsed_url['path'])) {
                    $this->init();
                    return;
                }
                $path = str_replace($parsed_site_url['path'], '', $parsed_url['path']);
                $page = get_page_by_path($path);
                if(!$page) {
                    $this->init();
                    return;
                }
                $id = $page->ID;
            }

            $permalink = parse_url(get_permalink($id));

            //sometimes url_to_postid returns wrong id, so we have to check if returned post path equals current path
            $path_match = false;
            if (!empty($permalink['path']) && !empty($parsed_url['path'])) {
                $path_match = $permalink['path'] == $parsed_url['path'];
            }

            $value = get_post_meta($id, 'bpmj_eddcm_disable_wp_idea_template', true);

            if ('yes' !== $value || !$path_match)
                $this->init();
        }
    }

    /**
     * Get configuration for the given template. Specify $key for
     * getting specific key of the configuration array
     *
     * @param string $template
     * @param string $key
     *
     * @return mixed
     */
    public function get_template_config($template, $key = null)
    {
        if (isset($this->registered_templates[$template])) {
            if ($key) {
                return isset($this->registered_templates[$template][$key]) ? $this->registered_templates[$template][$key] : null;
            } else {
                return $this->registered_templates[$template];
            }
        }

        return $key ? null : array();
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get_current_template_config($key = null)
    {
        return $this->get_template_config($this->template, $key);
    }

    public function get_current_template(): ?string
    {
        return $this->template;
    }

    /**
     * Jaki jest typ strony
     */
    public function page_type()
    {
        global $wpidea_settings, $edd_options;
        $get_options_post_id = function ($key, $options) {
            if (isset($options[$key])) {
                return ( int )$options[$key];
            }

            return 0;
        };
        $wpidea_map = array(
            'contact_page' => 'contact',
        );
        $edd_map = array(
            'purchase_page' => 'checkout',
            'success_page' => 'checkout_success',
            'failure_page' => 'checkout_failure',
            'purchase_history_page' => 'purchase_history_page',
        );
        $post_id = get_the_id();
        foreach ($wpidea_map as $key => $page_type) {
            if ($post_id === $get_options_post_id($key, $wpidea_settings)) {
                return $page_type;
            }
        }
        foreach ($edd_map as $key => $page_type) {
            if ($post_id === $get_options_post_id($key, $edd_options)) {
                return $page_type;
            }
        }

        $mode = get_post_meta($post_id, 'mode', true);

        return $mode;
    }

    /**
     * Body strony
     */
    public function get_body_class()
    {
        $page_type = $this->page_type();
        $body_class = $page_type . ' contact form';
        switch ($page_type) {
            case 'home':
            case 'full':
                $body_class = 'home';
                break;
            case 'lesson':
                $body_class = 'lesson';
                break;
        }

        if ($this->has_shortcode('courses') || $this->has_shortcode('products')) {
            $body_class = 'courses';
        }

        return apply_filters('bpmj_cm_get_body_class', $body_class);
    }

    /**
     * Pobierz header szablonu
     */
    public function header()
    {
        include $this->get_template_path('header');
    }

    public function experimental_cart_header()
    {
        include $this->get_template_path('experimental-cart-header');
    }

    /**
     * Pobierz footer szablonu
     */
    public function footer($footer_class = '')
    {
        $footer_class = apply_filters('bpmj_wpi_footer_class', $footer_class);

        include $this->get_template_path('footer');
    }

    /**
     * Get lesson top bar
     */
    public function lesson_top_bar()
    {
        include $this->get_template_path('template_parts/lesson/top-bar');
    }

    /**
     * Get all hooks for the specified action
     *
     * @param string $tag
     *
     * @return array
     */
    public function get_action_hooks($tag)
    {
        $hooks = $GLOBALS['wp_filter'][$tag];
        $hooks_flat = array();
        foreach ($hooks as $priority => $priority_hooks) {
            foreach (array_keys($priority_hooks) as $hook_unique_id) {
                $hooks_flat[] = array($priority, $hook_unique_id);
            }
        }

        return $hooks_flat;
    }

    /**
     * Pobieranie danych meta
     */
    public function get_meta($key)
    {
        $postID = get_the_id();

        return get_post_meta($postID, $key, true);
    }

    /**
     * Wyświetlanie danych meta
     *
     * @param string $key
     * @param bool $esc_html
     */
    public function the_meta($key, $esc_html = true)
    {
        echo $esc_html ? esc_html($this->get_meta($key)) : $this->get_meta($key);
    }

    /**
     * Wyświetlanie odpowiednich ikon dla plików
     */
    public function the_file_icon($file_id)
    {
        $type = get_post_mime_type($file_id);

        switch ($type) {

            case 'application/pdf':
                $typeName = 'pdf';
                break;

            case 'application/vnd.ms-excel':
                $typeName = 'doc';
                break;
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                $typeName = 'doc';
                break;
            case 'application/msword':
                $typeName = 'doc';
                break;
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                $typeName = 'doc';
                break;

            case 'audio/wav':
                $typeName = 'audio';
                break;
            case 'audio/x-wav':
                $typeName = 'audio';
                break;
            case 'audio/mpeg':
                $typeName = 'audio';
                break;
            case 'audio/x-midi':
                $typeName = 'audio';
                break;
            case 'audio/basic':
                $typeName = 'audio';
                break;

            default:
                $typeName = 'doc';
                break;
        }

        echo apply_filters('bpmj_eddcm_the_file_icon', $typeName);
    }

    /**
     * Generowanie całego głównego menu
     */
    public function get_main_menu($html_wrapper_args = array())
    {
        global $wpidea_settings;

        $tag = 'ul';
        if (!empty($html_wrapper_args)) {
            $tag = empty($html_wrapper_args['tag']) ? 'ul' : $html_wrapper_args['tag'];
            $html = '<' . $tag;
            foreach ($html_wrapper_args as $attr => $value) {
                if (in_array($attr, array('class', 'id', 'style')) || 'data-' === substr($attr, 0, 5)) {
                    $html .= ' ' . $attr . '="' . esc_attr($value) . '"';
                }
            }
            $html .= '>';
        } else {
            $html = '<' . $tag . ' class="main-menu">';
        }

        // Panel kursu
//		if ( $this->is_in_course() ) {
//			$html .= $this->get_course_panel_menu();
//		}

        #if (has_nav_menu('bpmj_eddcm_courses')) {
            $html .= wp_nav_menu(array(
                'theme_location' => 'bpmj_eddcm_courses',
                'menu_class' => 'primary-menu',
                'container' => '',
                'echo' => false,
                'items_wrap' => '%3$s',
            ));
        #}

        // Kontakt
//		if ( isset( $wpidea_settings['contact_page'] ) && is_numeric( $wpidea_settings['contact_page'] ) ) {
//			$html .= '<li><a target="_blank" href="' . get_permalink( $wpidea_settings['contact_page'] ) . '">' . __( 'Contact', BPMJ_EDDCM_DOMAIN ) . '</a></li>';
//		}


//		if ( is_user_logged_in() ) {
//			// Wylogowanie się
//			$html .= '<li><a  href="' . wp_logout_url() . '">' . __( 'Log Out', BPMJ_EDDCM_DOMAIN ) . '</a></li>';
//		}

        $html .= '</' . $tag . '>';

        return $html;
    }

    /**
     * Generowanie breadcrumbs
     */
    public function breadcrumbs()
    {
        global $wp_query;

        $breadcrumbs_template = apply_filters('bpmj_eddcm_breadcrumbs_template', '
	        <p class="breadcrumbs">
	            %s
	        </p>
	    ');
        $breadcrumbs_separator_template = apply_filters('bpmj_eddcm_breadcrumbs_separator_template', '<span class="arrow">&rsaquo;</span>');
        $breadcrumbs_element_template = apply_filters('bpmj_eddcm_breadcrumbs_element_template', '<a href="%2$s">%1$s</a>');
        $breadcrumbs_current_element_template = apply_filters('bpmj_eddcm_breadcrumbs_current_element_template', '<span class="current">%1$s</span>');

        $breadcrumbs = '';
        $parents = apply_filters('bpmj_eddcm_breadcrumbs_parents_ids', get_post_ancestors(get_the_id()));

        if (is_array($parents) && !empty($parents)) {
            $parents = array_reverse($parents);
            foreach ($parents as $id) {
                $breadcrumbs .= sprintf($breadcrumbs_element_template, get_the_title($id), get_the_permalink($id));
                $breadcrumbs .= $breadcrumbs_separator_template;
            }
        }

        if (is_tax()) {
            $term_name = $wp_query->queried_object->name;
            $breadcrumbs .= sprintf($breadcrumbs_current_element_template, $term_name, get_the_permalink());
        } else {
            $current_element_title = apply_filters('bpmj_eddcm_breadcrumbs_current_element_title', get_the_title());

            $breadcrumbs .= sprintf($breadcrumbs_current_element_template, $current_element_title, get_the_permalink());
        }

        printf($breadcrumbs_template, $breadcrumbs);
    }

    /**
     * Pobranie głównego rodzica
     *
     * @args: id (default), permalink, title
     *
     * @param string $args
     *
     * @return false|int|mixed|string
     */
    public function get_main_parent($args = 'id')
    {
        $parents = get_post_ancestors(get_the_id());
        $mainParent = is_array($parents) && !empty($parents) ? end($parents) : get_the_id();

        switch ($args) {
            case 'permalink':
                if ($this->page_type() == 'contact' || $this->page_type() == 'order') {
                    return get_bloginfo('url');
                } else {
                    return get_permalink($mainParent);
                }
                break;

            case 'title':
                return get_the_title($mainParent);
                break;

            default:
                return $mainParent;
                break;
        }
    }

    /**
     * Pobranie logotypu kursu
     */
    public function get_logo()
    {
        $logo = $this->get_app_logo_url();

        if ($this->is_in_course() && !is_tax()) {
            $top_id = $this->get_main_parent();
            $course_logo = get_post_meta($top_id, 'logo', true);
            if ($course_logo) {
                $logo = $course_logo;
            }
        }

        $logo_links_to_the_homepage = $this->settings->get('enable_logo_in_courses_to_home_page') ?? null;

        $logo = '<a href="' . (($this->is_in_course() && !is_tax() && '1' !== $logo_links_to_the_homepage) ?  get_home_url() : get_home_url()) . '"><img src="' . $logo . '"></a>';

        return $logo;
    }

    public function get_app_logo_url(): string
    {
        $logo_url = $this->settings->get('logo');

        return !empty($logo_url) ? $logo_url : $this->get_default_logo_url();
    }

    private function get_default_logo_url(): string
    {
        return $this->filters->apply(
            Filter_Name::DEFAULT_APP_LOGO_URL,
            WPI()->templates->get_template_url() . '/assets/gfx/wp-idea-logo.png'
        );
    }

    /**
     * Check if we are currently on a page supported by WP Idea
     *
     * @param string $page_type_arg
     *
     * @return boolean
     */
    public function is_on_supported_page($page_type_arg = null)
    {
        if ($this->override_all) {
            return apply_filters('bpmj_eddcm_is_on_supported_page', true);
        }
        $page_type = $page_type_arg ? $page_type_arg : $this->page_type();
        if (in_array($page_type, $this->get_allowed_page_types()) || $this->has_shortcode('courses')) {
            if (false !== $this->get_template_path($page_type)) {
                return apply_filters('bpmj_eddcm_is_on_supported_page', true);
            }
        }

        $post = get_post();
        /*
         * The post is a download and it's linked with a course
         */
        if (!empty($post)) {
            if (WPI()->courses->get_course_by_product($post->ID) && !bpmj_eddcm_enable_edd()) {
                return apply_filters('bpmj_eddcm_is_on_supported_page', true);
            }
        }
        return apply_filters('bpmj_eddcm_is_on_supported_page', false);
    }

    /**
     * Szablony
     */
    public function custom_template($template)
    {
        $page_type = $this->page_type();
        if (!$this->is_on_supported_page($page_type)) {
            return $template;
        }
        if ($this->is_in_course()) {
            if (WPI()->courses->user_shouldnt_have_access_to_course_page(get_the_ID())) {
                $page_type = 'no-access';
            }
        }
        do_action('bpmj_eddcm_prepare_custom_template');
        add_filter('comments_template', array($this, 'get_comments_template_path'));

        $custom_template = $this->get_template_path($page_type);
        if ($custom_template) {
            return $custom_template;
        }

        return $template;
    }

    /**
     * Get custom template's path (if it exists)
     *
     * @param string $page
     * @param string $fallback_page
     *
     * @return bool|mixed
     */
    public function get_template_path($page, $fallback_page = 'page')
    {
        if (substr($page, -4) !== '.php') {
            $page .= '.php';
        }
        if ($fallback_page && substr($fallback_page, -4) !== '.php') {
            $fallback_page .= '.php';
        }

        $template_root_dir = $this->get_template_root_dir($this->template);

        $page = apply_filters('bpmj_cm_get_template_path_page', $page);

        /*
         * We check the paths in this order:
         * 1. {template_name}/{page}
         * 2. default/{page}
         * 3. {template_name}/{$fallback_page}
         * 4. default/{$fallback_page}
         */
        $test_paths = array(
            $template_root_dir . '/' . $page,
            BPMJ_EDDCM_TEMPLATES_DIR . '/default/' . $page,
        );
        if ($fallback_page) {
            $test_paths[] = $template_root_dir . '/' . $fallback_page;
            $test_paths[] = BPMJ_EDDCM_TEMPLATES_DIR . '/default/' . $fallback_page;
        }
        foreach ($test_paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return false;
    }

    /**
     * Displays specified sub-template and declares passed variables locally
     * to be accessible by template script
     *
     * @param string $part
     * @param array $variables
     *
     * @return boolean
     */
    public function include_element_template($part, array $variables = array())
    {
        $template_path = $this->get_template_path('elements/' . $part, false);
        if ($template_path) {
            // Declare variables locally
            foreach ($variables as $var_name => $var_value) {
                $$var_name = $var_value;
            }
            include $template_path;

            return true;
        }

        return false;
    }

    /**
     * Get custom theme template
     *
     * @param $theme_template
     *
     * @return bool|mixed
     */
    public function get_comments_template_path($theme_template)
    {
        $new_theme_template = $this->get_template_path('comments');
        if (false === $new_theme_template) {
            return $theme_template;
        }

        return $new_theme_template;
    }

    /**
     * Navigation - left (previous) arrow with caption
     *
     * @param string $prefix
     * @param string $link_class
     *
     * @return string
     */
    public function get_previous_lesson_nav($prefix = '', $link_class = '')
    {
        return $this->get_lesson_nav('previous', $prefix, '', $link_class);
    }

    /**
     * Navigation - right (next) arrow with caption
     *
     * @param string $suffix
     * @param string $link_class
     *
     * @return string
     */
    public function get_next_lesson_nav($suffix = '', $link_class = '')
    {
        return $this->get_lesson_nav('next', '', $suffix, $link_class);
    }

    /**
     * Checks the settings and returns a label for previous/next anchor in a course.
     * A $sibling is used in case of "lesson_title" setting
     *
     * @param string $type
     * @param Course_Page|null $sibling
     *
     * @return string
     */
    public function get_lesson_nav_label($type = 'next', Course_Page $sibling = null)
    {
        global $post, $wpidea_settings;

        $option_label = 'navigation_' . $type . '_lesson_label';

        $course_id = !empty($post) ? get_post_meta($post->ID, '_bpmj_eddcm', true) : null;
        $option = '';
        if ($course_id) {
            $option = get_post_meta($course_id, $option_label, true);
        }
        if (!$option) {
            $option = isset($wpidea_settings[$option_label]) ? $wpidea_settings[$option_label] : 'lesson';
        }
        if ('lesson_title' === $option && !$sibling) {
            $option = 'lesson';
        }

        switch ($option) {
            case 'lesson':
                $label = 'next' === $type ? __('Next lesson', BPMJ_EDDCM_DOMAIN) : __('Previous lesson', BPMJ_EDDCM_DOMAIN);
                break;
            case 'lesson_title':
                if ($sibling) {
                    $label = $sibling->post_title;
                    break;
                }

                $label = '';
                break;
            default:
                $label = $option;
        }

        return apply_filters('bpmj_eddcm_get_lesson_nav_label', $label);
    }

    /**
     * Navigation
     *
     * @param string $type
     * @param string $prefix
     * @param string $suffix
     * @param string $link_class
     *
     * @return string
     */
    public function get_lesson_nav($type = 'next', $prefix = '', $suffix = '', $link_class = '')
    {
        global $post;

        $course_id = get_post_meta($post->ID, '_bpmj_eddcm', true);
        $course_page_id = get_post_meta($course_id, 'course_id', true);

        $sibling = $type === 'next'
            ? WPI()->courses->get_next_sibling_of($course_page_id, $post->ID)
            : WPI()->courses->get_previous_sibling_of($course_page_id, $post->ID);

        if (!$sibling) {
            return '';
        }

        $link_style = '';
        if ($sibling->should_be_grayed_out()) {
            $link_class .= ' disabled';
        }

        $anchor = $prefix . $this->get_lesson_nav_label($type, $sibling) . $suffix;

        $permalink = $sibling->get_permalink();

        return sprintf('<a href="%1$s" title="%2$s" class="%4$s" %5$s>%3$s</a>', $permalink, strip_tags($anchor), $anchor, $link_class, $link_style);
    }

    /**
     * Contact e-mail address
     */
    public function get_contact_email()
    {
        global $wpidea_settings;

        if (isset($wpidea_settings['contact_email']) && !empty($wpidea_settings['contact_email'])) {
            return $wpidea_settings['contact_email'];
        }

        return get_bloginfo('admin_email');
    }

    /**
     * Get custom template's root path
     *
     * @param string $template
     *
     * @return string
     */
    public function get_template_root_dir($template)
    {
        return $this->get_template_config($template, 'path');
    }

    /**
     * Get custom template's root URL
     *
     * @param string $template
     *
     * @return string
     */
    public function get_template_url($template = null)
    {
        if (!$template) {
            $template = $this->template;
        }

        return $this->get_template_config($template, 'url');
    }

    /**
     * Does the post contain the specified shortcode
     *
     * @param $shortcode
     *
     * @return bool
     */
    public function has_shortcode($shortcode)
    {
        if (!is_singular()) {
            return false;
        }
        $post = get_post();

        return has_shortcode($post->post_content, $shortcode);
    }

    /**
     * Are we on a course page?
     *
     * @return bool
     */
    public function is_in_course()
    {
        return in_array($this->page_type(), array('home', 'lesson', 'test', 'full')) && !is_tax();
    }

    /**
     * Custom footer HTML
     */
    public function output_footer_html(bool $display_scripts = false): void
    {
        $wpidea_settings = get_option('wp_idea');
        $footer_html = empty($wpidea_settings['footer_html']) ? WPI()->settings->get_default_footer_html() : $wpidea_settings['footer_html'];

        $scripts = '';

        if ($display_scripts) {
            ob_start();
                $this->actions->do('wp_footer');
            $scripts = ob_get_clean();
        }

        echo apply_filters('bpmj_eddcm_footer_html', nl2br($footer_html)) . $scripts;
    }

    /**
     * Get the position for lesson downloads section
     *
     * @return string
     */
    public function get_download_section_position()
    {
        $position = $this->get_meta('download_section_position');
        if (!$position || 'default' === $position) {
            $wpidea_settings = get_option('wp_idea');
            $position = ($wpidea_settings['download_section'] ?? null) ? $wpidea_settings['download_section'] : 'side';
        }

        return $position;
    }

    /**
     * A path to theme override files
     *
     * @return string
     */
    public function custom_stylesheet_path()
    {
        $theme_root = get_theme_root(true);
        $template_root_dir = $this->get_template_root_dir($this->template);

        return $this->compute_content_path_difference($theme_root, $template_root_dir) . '/theme_override';
    }

    /**
     * Compute path difference
     *
     * @param string $left_path
     * @param string $right_path
     *
     * @return string
     */
    public function compute_content_path_difference($left_path, $right_path)
    {
        if (strpos($left_path, WP_CONTENT_DIR) === 0 && strpos($right_path, WP_CONTENT_DIR) === 0) {
            $left_path_rel = ltrim(str_replace(WP_CONTENT_DIR, '', $left_path), '/');
            $right_path_rel = ltrim(str_replace(WP_CONTENT_DIR, '', $right_path), '/');
            $from_path_parts = explode('/', $left_path_rel);
            $to_path_parts = explode('/', $right_path_rel);
            $i = 0;
            while (isset($from_path_parts[$i]) && isset($to_path_parts[$i]) && $from_path_parts[$i] === $to_path_parts[$i]) {
                ++$i;
            }
            $relative_path = str_repeat('../', count($from_path_parts) - $i);
            $relative_path .= implode('/', array_slice($to_path_parts, $i));

            return $relative_path;
        }

        return $left_path;
    }

    /**
     * Add new path to EDD's template lookup dirs
     *
     * @param $file_paths
     *
     * @return mixed
     */
    public function edd_template_paths($file_paths)
    {
        $page_type = $this->page_type();
        if (!$this->is_on_supported_page($page_type)) {
            return $file_paths;
        }

        $file_paths[0] = $this->get_template_root_dir($this->template) . '/theme_override';

        return $file_paths;
    }

    /**
     * Override EDD's purchase link defaults.
     * Currently it's needed to hide the price if site owner decided to do so in settings.
     *
     * @param $defaults
     *
     * @return mixed
     */
    public function edd_purchase_link_defaults($defaults)
    {
        $wpidea_settings = get_option('wp_idea');

        if ( isset( $wpidea_settings['list_price_button'] ) ) {
            $defaults['price'] = $wpidea_settings['list_price_button'];
        }

        return $defaults;
    }

    public function get_navigation_section_position()
    {
        $position = $this->get_meta('lesson_navigation_section_position');
        if (!$position || 'default' === $position) {
            $wpidea_settings = get_option('wp_idea');
            $position = isset($wpidea_settings['lesson_navigation_section']) ? $wpidea_settings['lesson_navigation_section'] : 'off';
        }

        return $position;
    }

    public function get_progress_section_position()
    {
        $position = $this->get_meta('lesson_progress_section_position');
        if (!$position || 'default' === $position) {
            $wpidea_settings = get_option('wp_idea');
            $position = !empty($wpidea_settings['lesson_progress_section']) ? $wpidea_settings['lesson_progress_section'] : 'side';
        }

        return $position;
    }

    public function html_navigation_section($navigation_section_position, $course_page_id, $lesson_page_id)
    {
        if (App_View_API_Static_Helper::is_active()) {
            return;
        }

        if (!$navigation_section_position) {
            $navigation_section_position = $this->get_navigation_section_position();
        }
        $this->include_element_template('lesson-navigation', array(
            'navigation_section_position' => $navigation_section_position,
            'course_page_id' => $course_page_id,
            'lesson_page_id' => $lesson_page_id,
        ));
    }

    public function html_progress_section($progress)
    {
        $this->include_element_template('progress', array(
            'progress' => $progress,
            'page_type' => $this->page_type(),
        ));
    }

    /**
     * @param $html
     *
     * @return string
     */
    public function hook_embed_oembed_html($html)
    {
        global $wpidea_settings;
        if (!$this->is_on_supported_page()
            || !class_exists('DOMDocument', false)
            || is_admin()
            || 'on' !== $wpidea_settings['enable_responsive_videos']
        ) {
            return $html;
        }

        return $this->wrap_embed_oembed_html($html);
    }

    /**
     * @param string $html
     * @param string $margin
     * @param bool $center_vertically
     *
     * @return string
     */
    public function wrap_embed_oembed_html($html, $margin = null, $center_vertically = false)
    {
        $doc = new DOMDocument();
        $internal_errors = libxml_use_internal_errors(true);
        @$doc->loadHTML('<html><body>' . $html . '</body></html>');
        libxml_use_internal_errors($internal_errors);
        $iframes = $doc->getElementsByTagName('iframe');
        if (1 !== $iframes->length) {
            return $html;
        }
        $iframe = $iframes->item(0);
        $padding = '75%'; // 4:3 format
        $width = (int)$iframe->getAttribute('width');
        $height = (int)$iframe->getAttribute('height');
        $iframe_src = (string)$iframe->getAttribute('src');
        if (!empty($width) && !empty($height)) {
            $padding = number_format($height * 100 / $width, 4, '.', '') . '%';
        }
        ob_start();
        ?>
        <div class="wpidea-embed-wrapper" style="display: block;<?php if (null !== $margin) {
            echo 'margin: ' . $margin . ';';
        } ?>">
            <div style="padding-bottom:<?php echo $padding; ?>; display: block;">
                <iframe class="wp-embedded-content" src="<?php echo esc_attr($iframe_src); ?>" width="100%" height="100%"
                        frameborder="0" webkitallowfullscreen="webkitallowfullscreen"
                        mozallowfullscreen="mozallowfullscreen" allowfullscreen="allowfullscreen"></iframe>
            </div>
        </div>
        <?php

        $new_html = ob_get_clean();
        if ($center_vertically) {
            $new_html = '<div style="padding: ' . number_format((100 - $height * 100 / $width) / 2, 4, '.', '') . '% 0; display: block;">' . $new_html . '</div>';
        }

        // Strip all surrounding whitespace from HTML
        return preg_replace('#>\s+<#', '><', $new_html);
    }

    /**
     * @param string $html
     *
     * @return string
     */
    public function hook_wp_video_shortcode($html, $atts)
    {
        global $wpidea_settings;
        if (!$this->is_on_supported_page()
            || is_admin()
            || 'on' !== $wpidea_settings['enable_responsive_videos']
        ) {
            return $html;
        }
        $padding = '75%'; // 4:3 format
        $width = (int)$atts['width'];
        $height = (int)$atts['height'];
        if (!empty($width) && !empty($height)) {
            $padding = number_format($height * 100 / $width, 2, '.', '') . '%';
        }
        $html = preg_replace(array(
            '/width: \d+px/',
            '/height: \d+px/',
            '/width="\d+"/',
            '/height="\d+"/',
        ), array(
            'width: 100%',
            'height: 100%',
            'width="100%"',
            'height="100%"',
        ), $html);
        ob_start();
        ?>
        <div class="wpidea-embed-wrapper">
            <div style="padding-bottom:<?php echo $padding; ?>;">
                <?php echo $html; ?>
            </div>
        </div>
        <?php

        // Strip all surrounding whitespace from HTML
        return preg_replace('#>\s+<#', '><', ob_get_clean());
    }

    /**
     * @param string $template
     * @param string $key
     *
     * @return mixed
     */
    public function get_template_settings($template, $key = null)
    {
        if (!isset($this->layout_settings[$template])) {
            $layout_settings_slug = WPI()->settings->get_layout_template_settings_slug();
            $layout_settings = get_option($layout_settings_slug);

            $this->layout_settings[$template] = isset($layout_settings[$template]) ? $layout_settings[$template] : array();
        }

        if ($key) {
            return isset($this->layout_settings[$template][$key]) ? $this->layout_settings[$template][$key] : null;
        }

        return $this->layout_settings[$template];
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get_current_template_settings($key = null)
    {
        return $this->get_template_settings($this->template, $key);
    }

    /**
     * This helps telling [courses] and [downloads] apart
     *
     * @param array $out
     * @param array $pairs
     * @param array $atts
     *
     * @return array
     */
    public function filter_downloads_shortcode_atts($out, $pairs, $atts)
    {
        if (isset($atts['bpmj_eddcm_courses_tag'])) {
            $out['bpmj_eddcm_courses_tag'] = $atts['bpmj_eddcm_courses_tag'];
        }

        return $out;
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function filter_set_layout_options($options)
    {
        $off_option = $options['off'];
        unset($options['off']);
        foreach ($this->registered_templates as $template_id => $template_info) {
            if ('default' === $template_id) {
                continue;
            }
            if ('scarlet' === $template_id) {
                $options[$template_id] = $template_info['name'] . ' (' . __('Default', BPMJ_EDDCM_DOMAIN) . ')';
            } else {
                $options[$template_id] = $template_info['name'];
            }
        }
        $options['off'] = $off_option;

        return $options;
    }

    /**
     * @param string $feature
     * @param string $template
     *
     * @return bool
     */
    public function is_feature_supported($feature, $template = null)
    {
        $config = array();
        if (!$template || $template === $this->template) {
            $config = $this->template_config;
        } else {
            $options_file = $this->get_template_root_dir($template) . '/template-config.php';
            if (file_exists($options_file)) {
                $config = include $options_file;
            }
        }

        if (empty($config['features']) || !is_array($config['features'])) {
            return false;
        }

        return in_array($feature, $config['features']);
    }

    public function disable_wp_idea_template()
    {
        add_meta_box('bpmj_eddcm_template_override', __('Disable WP Idea template', BPMJ_EDDCM_DOMAIN), array($this, 'disable_wp_idea_template_meta_box_body'), array('page', 'post'), 'side');
    }

    public function disable_wp_idea_template_meta_box_body($post)
    {
        $value = get_post_meta($post->ID, 'bpmj_eddcm_disable_wp_idea_template', true);
        ?>
        <br>
        <label for="bpmj-eddcm-disable-wp-idea-template">
            <input id="bpmj-eddcm-disable-wp-idea-template" type="checkbox" name="bpmj_eddcm_disable_wp_idea_template"
                   value="yes" <?php echo ('yes' === $value) ? 'checked' : ''; ?>>
            <?php _e('Check if you want to disable the WP Idea template for this page / post', BPMJ_EDDCM_DOMAIN); ?>
        </label>
        <?php
    }

    public function disable_wp_idea_template_save_post($post_id)
    {
        if (isset($_POST['action']) && 'elementor_ajax' === $_POST['action'])
            return;

        if (isset($_POST['bpmj_eddcm_disable_wp_idea_template']) && 'yes' === $_POST['bpmj_eddcm_disable_wp_idea_template']) {
            update_post_meta($post_id, 'bpmj_eddcm_disable_wp_idea_template', 'yes');
        } else {
            update_post_meta($post_id, 'bpmj_eddcm_disable_wp_idea_template', 'no');
        }
    }

    /**
     * Get user link or pure user name if current user cannot manage other users
     *
     * @param string $field The field to retrieve the user with. id | ID | slug | email | login.
     * @param int|string $value A value for $field. A user ID, slug, email address, or login name.
     *
     * @return string
     */
    public static function get_user_link_by($field, $value)
    {
        $user = get_user_by($field, $value);
        if (false === $user) {
            $id = $field === 'id' || $field === 'ID' ? $value : null;
            $u = apply_filters('lms_filter_sensitive__customer_email', $value, $id);
        } else {
            $user_string = $user->user_login;
            if (empty($user->first_name)) {
                $user_string = apply_filters('lms_filter_sensitive__customer_login', $user_string, $user->ID, $user->user_email);
            } else {
                $first_name = apply_filters('lms_filter_sensitive__customer_first_name', $user->first_name, $user->ID, $user->user_email);
                $last_name = apply_filters('lms_filter_sensitive__customer_last_name', $user->last_name, $user->ID, $user->user_email);

                $user_string = $first_name . ' ' . $last_name;
            }

            $u = current_user_can('edit_users')
                ? '<a href="' . get_edit_user_link($user->ID) . '" target="_blank">' . $user_string . '</a>'
                : $user_string;
        }

        return $u;
    }

    /**
     * Print custom css setting value (from template settings)
     *
     * Prints full template string (with <style></style> tags)
     *
     * @return void
     */
    public function print_custom_css_template_string()
    {
        echo '<style>' . $this->templates_settings_handler->get_custom_css_field_value() . '</style>';
    }

    public function get_spinner_url()
    {
        return admin_url('/images/spinner.gif');
    }

    private function minify_assets($assets, $extension)
    {
        foreach ($assets as $destination_path => $asset_paths){

            $minify = new Minifier(
                $asset_paths,
                $destination_path,
                $extension
            );

            $minify->minify();
        }
    }

    public function minify_css()
    {
        if(! isset($this->template_config['styles_for_minification'])){
            return;
        }

        $this->minify_assets(
            $this->template_config['styles_for_minification'],
            Minifier::EXTENSION_CSS
        );
    }

    public function minify_js()
    {
        if(! isset($this->template_config['javascripts_for_minification'])){
            return;
        }

        $this->minify_assets(
            $this->template_config['javascripts_for_minification'],
            Minifier::EXTENSION_JS
        );
    }

    public function reload_template_config()
    {
        $template = $this->template;
        $options_file = $this->get_template_root_dir($this->template) . '/template-config.php';
        if (file_exists($options_file)) {
            $this->template_config = include $options_file;
        }
    }

    public function should_show_user_notice(): bool
    {
        return $this->user_notice->should_show_notice();
    }

    public function get_user_notice_content(): string
    {
        return $this->user_notice->get_html();
    }

    private function courses_functionality_enabled(): bool
    {
        return $this->settings->get(Settings_Const::COURSES_ENABLED) ?? true;
    }

}
