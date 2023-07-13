<?php
/**
 * Adapted from https://github.com/tareq1988/wordpress-settings-api-class
 *
 */
/**
 * weDevs Settings API wrapper class
 *
 * @version 1.1
 *
 * @author Tareq Hasan <tareq@weDevs.com>
 * @link http://tareq.weDevs.com Tareq's Planet
 * @example src/settings-api.php How to use the class
 */
// Exit if accessed directly
namespace bpmj\wpidea\admin\settings;

use bpmj\wpidea\certificates\Certificate_Template;
use bpmj\wpidea\Helper;
use bpmj\wpidea\integrations\Integrations;
use bpmj\wpidea\View;

if (!defined('ABSPATH')) {
    exit;
}

if (class_exists('bpmj\wpidea\admin\settings\Settings_API')) {
    return;
}

class Settings_API
{

    /**
     * settings sections array
     *
     * @var array
     */
    private $settings_sections = array();

    /**
     * Settings fields array
     *
     * @var array
     */
    private $settings_fields = array();

    /**
     * Singleton instance
     *
     * @var object
     */
    private static $_instance;

    /*
	 * Name
	 */
    private $name;

    /*
	 * Prefix
	 */
    private $prefix;

    /**
     * Is the object detached from Wordpress Settings API
     * @var bool
     */
    private $detached;

    /**
     * Array for detached options - provided and handled manually
     * @var array
     */
    private $detached_options;

    /**
     * Array for detached field args
     * @var array
     */
    private $detached_field_args;

    /**
     * If true then the WP option is always saved to the DB, even if
     * there are no changes in fields. Normally WP skips updates
     * when there are no chnages
     * @var bool
     */
    private $force_save;

    /**
     * This array stores an array of unique keys used in "save_to"
     * field keys - it is used to update
     * multiple options at once
     * @var array
     */
    private $options_to_save = array();
    private $script_file_already_included = false;

    /*
	 * Constructor
	 *
	 * @param string $prefix - unique prefix for CSS classes and other names
	 * @param bool $detached - true if the API should be disconnected from WP Settings API
	 * @param bool $force_save - if true, the option is always saved to the DB, even if nothing changes
	 */

    public function __construct($name = '', $detached = false, $force_save = false)
    {
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

        $this->name = sanitize_title($name);
        $this->prefix = sanitize_title($name) . '-';
        $this->detached = $detached;
        $this->detached_options = array();
        $this->detached_field_args = array();
        $this->force_save = $force_save;
    }

    /**
     *
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * Enqueue scripts and styles
     */
    function admin_enqueue_scripts()
    {
        wp_enqueue_style('wp-color-picker');

        wp_enqueue_media();
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('jquery');
    }

    /**
     * Set settings sections
     *
     * @param array $sections setting sections array
     */
    function set_sections($sections)
    {
        $this->settings_sections = $sections;

        return $this;
    }

    /**
     * Add a single section
     *
     * @param array $section
     */
    function add_section($section)
    {
        $this->settings_sections[] = $section;

        return $this;
    }

    /**
     * Set settings fields
     *
     * @param array $fields settings fields array
     */
    function set_fields($fields)
    {
        $this->settings_fields = $this->detached ? array('default' => $fields) : $fields;

        return $this;
    }

    function add_field($section, $field)
    {
        $defaults = array(
            'name' => '',
            'label' => '',
            'desc' => '',
            'type' => 'text'
        );

        $arg = wp_parse_args($field, $defaults);
        $this->settings_fields[$section][] = $arg;

        return $this;
    }

    /**
     *
     * @param array $options
     */
    public function set_detached_options(array $options)
    {
        $this->detached_options = $options;
    }

    /**
     * Initialize and registers the settings sections and fileds to WordPress
     *
     * Usually this should be called at `admin_init` hook.
     *
     * This function gets the initiated settings sections and fields. Then
     * registers them to WordPress and ready for use.
     */
    public function settings_init()
    {

        if (false === $this->detached && false == get_option($this->name)) {
            add_option($this->name);
        }

        if (false === $this->detached) {
            //Register settings sections
            foreach ($this->settings_sections as $section) {
                $this->register_section($section);
            }

            $this->register_setting($this->name);
        }

        //Register settings fields
        foreach ($this->settings_fields as $section => $field) {
            foreach ($field as $option) {
                if (is_null($option)) continue;
                $type = isset($option['type']) ? $option['type'] : 'text';

                $args = $this->prepare_field_args($type, $option);

                if (false === $this->detached) {
                    $callback = array($this, 'callback_' . $type);
                    if (!is_callable($callback)) {
                        $callback = array($this, 'callback_text');
                    }
                    add_settings_field("$this->name[" . $option['name'] . ']', isset($option['label']) ? $option['label'] : '', $callback, $section, $section, $args);
                    $this->register_setting($args['save_to']);
                    if ($args['type'] === 'bpmj_groups' && !empty($args['options'])) {
                        /*
						 * This type holds additional possible settings
						 * - we need to register them too
						 */
                        foreach ($args['options'] as $sub_option) {
                            if (!empty($sub_option['save_to'])) {
                                $this->register_setting($sub_option['save_to']);
                            }
                        }
                    }
                } else {
                    $this->detached_field_args[$option['name']] = $args;
                }
            }
        }
    }

    /**
     * Registers the section to WP
     *
     * @param array $section
     */
    public function register_section($section)
    {
        if (isset($section['desc']) && !empty($section['desc'])) {
            $section['desc'] = '<div class="inside">' . $section['desc'] . '</div>';
            $callback = create_function('', 'echo "' . str_replace('"', '\"', $section['desc']) . '";');
        } else if (isset($section['callback'])) {
            $callback = $section['callback'];
        } else {
            $callback = null;
        }
        add_settings_section($section['id'], $section['title'], $callback, $section['id']);
        if (!empty($section['subsections'])) {
            foreach ($section['subsections'] as $subsection) {
                $this->register_section($subsection);
            }
        }
    }

    /**
     * Registers the setting to Wordpress
     *
     * @param string $option_name
     */
    public function register_setting($option_name)
    {
        if (in_array($option_name, $this->options_to_save)) {
            return;
        }
        if ('edd_settings' == $option_name) {
            register_setting($this->name, $option_name, array($this, 'edd_settings_sanitize'));
        } else {
            register_setting($this->name, $option_name, $this->prepare_sanitize_options_callback($option_name));
        }
        $this->options_to_save[] = $option_name;
    }

    /*
	 * Sanitize EDD params
	 *
	 */

    public function edd_settings_sanitize($input = array())
    {

        global $edd_options;

        if (empty($_POST['_wp_http_referer'])) {
            return $input;
        }

        if (empty($_POST['wp_idea'])) {
            return $input;
        }

        $input = $input ? $input : array();

        // Merge our new settings with the existing
        $output = array_merge($edd_options, $input);

        foreach ($input as $key => $value) {

            if (empty($input[$key]) || $input[$key] === '-1') {
                unset($output[$key]);
            }
        }

        $edd_options = $output; // dzięki temu "oszukamy" edd_settings_sanitize z EDD
        return $output;
    }

    /**
     *
     * @param string $field
     * @return array
     */
    public function get_detached_args($field)
    {
        return isset($this->detached_field_args[$field]) ? $this->detached_field_args[$field] : array();
    }

    /**
     * Get field description for display
     *
     * @param array $args settings field args
     */
    public function get_field_description($args)
    {
        if (!empty($args['desc'])) {

            $css_class = $this->prefix . 'description-field';

            $desc = sprintf('<p class="%s">%s</p>', $css_class, $args['desc']);
        } else {
            $desc = '';
        }

        return $desc;
    }

    /**
     * Displays a text field for a settings field
     *
     * @param array $args settings field args
     */
    function callback_text($args)
    {

        $value = esc_attr($this->get_value($args));
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
        $type = isset($args['type']) ? $args['type'] : 'text';
        $min = isset($args['min']) ? 'min="'.$args['min'].'"' : '';

        $html = sprintf('<input type="%1$s" class="%2$s-text %6$s" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s" style="%7$s" %8$s %9$s />', $type, $size, $args['save_to'], $args['id'], $value, $args['class'], $args['style'], disabled($args['disabled'], true, false), $min);
        $html .= $this->get_field_description($args);

        echo $html;
    }

    /**
     * Displays a hidden field for a settings field
     *
     * @param array $args settings field args
     */
    function callback_hidden($args)
    {

        $value = esc_attr($this->get_value($args));

        $html = sprintf('<input type="hidden" id="%1$s[%2$s]" name="%1$s[%2$s]" value="%3$s" />', $args['save_to'], $args['id'], $value);

        echo $html;
    }

    /**
     * Displays a text field for a license key field
     *
     * @param array $args settings field args
     */
    function callback_license_key($args)
    {

        $value_raw = $this->get_value($args);
        $value = esc_attr($value_raw);
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
        $type = 'text';

        $html = sprintf('<input type="%1$s" class="%2$s-text %6$s" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s" style="%7$s" %8$s />', $type, $size, $args['save_to'], $args['id'], $value, $args['class'], $args['style'], disabled($args['disabled'], true, false));
        $html .= $this->get_field_description($args);

        if (!$value_raw) {
            echo $html;
            return;
        }

        $license_data['license'] = get_option('bpmj_eddcm_license_status');
        $license_data['expires'] = get_option('bpmj_wpidea_license_expires');
        $license_data['error'] = get_option('bpmj_wpidea_license_connection_error');

        if($license_data['error']) {
            $html .= '<p class="' . $this->prefix . 'description-field' . '" style="color: #800;">' . __('Error while connecting to the license server!', BPMJ_EDDCM_DOMAIN) . '</p>';
        }

        if ('valid' === $license_data['license']) {
            $html .= '<p class="' . $this->prefix . 'description-field' . '" style="color: #080;">';
        } else {
            $html .= '<p class="' . $this->prefix . 'description-field' . '" style="color: #800;">';
        }

        switch ($license_data['license']) {
            case 'site_inactive':
            case 'inactive':
                $license_status_translated = __('inactive', BPMJ_EDDCM_DOMAIN);
                break;
            case 'valid':
                $license_status_translated = __('active', BPMJ_EDDCM_DOMAIN);
                break;
            case 'expired':
                $license_status_translated = __('expired', BPMJ_EDDCM_DOMAIN);
                if('lifetime' === $license_data['expires']) {
                    $license_data['expires'] = '';
                }
                break;
            default:
                $license_data['license'] = 'inactive';
                $license_status_translated = __('n/a', BPMJ_EDDCM_DOMAIN);
                $license_data['expires'] = __('n/a', BPMJ_EDDCM_DOMAIN);
                break;
        }

        if (empty($license_data['expires'])) {
            $license_data['expires'] = __('n/a', BPMJ_EDDCM_DOMAIN);
        }

        $html .= sprintf(__('License status: %1$s. Expiration date: %2$s.', BPMJ_EDDCM_DOMAIN), '<strong>' . $license_status_translated . '</strong>', substr($license_data['expires'], 0, 10));
        $html .= '</p>';

        echo $html;
    }

    /**
     * Displays a url field for a settings field
     *
     * @param array $args settings field args
     */
    function callback_url($args)
    {
        $this->callback_text($args);
    }

    /**
     * Displays a number field for a settings field
     *
     * @param array $args settings field args
     */
    function callback_number($args)
    {
        $this->callback_text($args);
    }

    /**
     * Displays a checkbox for a settings field
     *
     * @param array $args settings field args
     * @param string $on
     * @param string $off
     */
    function callback_checkbox($args, $on = null, $off = null)
    {

        $value = esc_attr($this->get_value($args));

        if ($on === null || $off === null) {
            if ($args['save_to'] == 'edd_settings') {
                $on = '1';
                $off = '-1';
            } else {
                $on = 'on';
                $off = 'off';
            }
        }

        $html = sprintf('<fieldset class="%1$s" style="%2$s">', $args['class'], $args['style']);
        $html .= sprintf('<label for="wpuf-%1$s[%2$s]">', $args['save_to'], $args['id']);
        $html .= sprintf('<input type="hidden" name="%1$s[%2$s]" value="' . $off . '" />', $args['save_to'], $args['id']);
        $html .= sprintf('<input type="checkbox" class="checkbox" autocomplete="off" id="wpuf-%1$s[%2$s]" name="%1$s[%2$s]" value="' . $on . '" %3$s %4$s />', $args['save_to'], $args['id'], checked($value, $on, false), disabled($args['disabled'], true, false));
        $html .= sprintf('%1$s</label>', $args['desc']);
        $html .= '</fieldset>';

        echo $html;
    }

    /**
     * Displays a checkbox for a settings field ('yes' or 'no')
     *
     * @param array $args settings field args
     */
    public function callback_checkbox_yes_no($args)
    {
        $this->callback_checkbox($args, 'yes', 'no');
    }

    /**
     * Displays a checkbox for a settings field ('true' or 'false')
     *
     * @param array $args settings field args
     */
    public function callback_checkbox_true_false($args)
    {
        $this->callback_checkbox($args, 'true', 'false');
    }

    /**
     * Displays a checkbox for a settings field ('true' or 'false')
     *
     * @param array $args settings field args
     */
    public function callback_checkbox_one_empty($args)
    {
        $this->callback_checkbox($args, '1', '');
    }

    /**
     * Displays a multicheckbox a settings field
     *
     * @param array $args settings field args
     */
    function callback_multicheck($args)
    {


        $value = $this->get_value($args);

        $html = sprintf('<fieldset class="%1$s" style="%2$s">', $args['class'], $args['style']);
        foreach ($args['options'] as $key => $label) {
            $checked = isset($value[$key]) ? $value[$key] : '0';
            $html .= sprintf('<label for="%1$s[%2$s][%3$s]">', $args['save_to'], $args['id'], $key);
            $html .= sprintf('<input type="checkbox" class="checkbox" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s %5$s />', $args['save_to'], $args['id'], $key, checked($checked, $key, false), disabled($args['disabled'], true, false));
            $html .= sprintf('%1$s</label><br>', $label);
        }
        $html .= $this->get_field_description($args);
        $html .= '</fieldset>';

        echo $html;
    }

    /**
     * Displays a multicheckbox a settings field
     *
     * @param array $args settings field args
     */
    function callback_radio($args)
    {

        $value = $this->get_value($args);

        $html = sprintf('<fieldset class="%1$s" style="%2$s">', $args['class'], $args['style']);
        foreach ($args['options'] as $key => $label) {
            $html .= sprintf('<label for="%1$s%2$s[%3$s][%4$s]">', $this->prefix, $args['save_to'], $args['id'], $key);
            $html .= sprintf('<input type="radio" class="radio" id="%1$s%2$s[%3$s][%4$s]" name="%2$s[%3$s]" value="%4$s" %5$s %6$s />', $this->prefix, $args['save_to'], $args['id'], $key, checked($value, $key, false), disabled($args['disabled'], true, false));
            $html .= sprintf(' %1$s</label><br>', $label);
        }
        $html .= $this->get_field_description($args);
        $html .= '</fieldset>';

        echo $html;
    }

    /**
     * Displays a multicheckbox a settings field
     *
     * @param array $args settings field args
     */
    function callback_radio_with_other($args)
    {

        $value = $this->get_value($args);

        $html = sprintf('<fieldset class="%1$s" style="%2$s">', $args['class'] . ' _radio_list_with_other_option', $args['style']);
        $found_value_in_radios = false;
        foreach ($args['options'] as $key => $label) {
            $html .= sprintf('<label for="%1$s%2$s[%3$s][%4$s]">', $this->prefix, $args['save_to'], $args['id'], $key);
            $html .= sprintf('<input type="radio" class="radio" id="%1$s%2$s[%3$s][%4$s]" name="%2$s[%3$s]" value="%4$s" %5$s />', $this->prefix, $args['save_to'], $args['id'], $key, checked('other' === $key && !$found_value_in_radios ? $key : $value, $key, false));
            if ($key === $value) {
                $found_value_in_radios = true;
            }
            $html .= sprintf(' %1$s</label>', $label);
            if ($key === 'other') {
                $other_value = $found_value_in_radios ? '' : $value;
                $html .= sprintf(' <input type="text" name="%1$s[%2$s]" value="%3$s" %4$s class="_other_option_input" />', $args['save_to'], $args['id'], esc_attr($other_value), disabled(true, $found_value_in_radios, false));
            }
            $html .= '<br>';
        }
        $html .= $this->get_field_description($args);
        $html .= '</fieldset>';

        echo $html;
    }

    /**
     * Displays a selectbox for a settings field
     *
     * @param array $args settings field args
     */
    function callback_select($args)
    {

        $value = esc_attr($this->get_value($args));
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';

        $html = sprintf('<select class="%1$s %4$s" name="%2$s[%3$s]" id="%2$s[%3$s]" style="%5$s" %6$s>', $size, $args['save_to'], $args['id'], $args['class'], $args['style'], disabled($args['disabled'], true, false));
        foreach ($args['options'] as $key => $label) {
            $option_args = array();
            if (is_array($label)) {
                $option_args = $label;
                $label = isset($option_args['label']) ? $option_args['label'] : $key;
            }
            $html .= sprintf('<option value="%1$s"%2$s%3$s>%4$s</option>', $key, selected($value, $key, false), isset($option_args['data']) ? $this->create_data_attributes($option_args['data']) : '', $label);
        }
        $html .= sprintf('</select>');
        $html .= $this->get_field_description($args);

        echo $html;
    }

    /**
     * Displays a select2 control for a settings field
     *
     * @param array $args
     */
    public function callback_select2($args)
    {
        $args['class'] = ($args['class'] ? $args['class'] : '') . ' select2';
        $this->callback_select($args);
    }

    /**
     * Displays a textarea for a settings field
     *
     * @param array $args settings field args
     */
    function callback_textarea($args)
    {

        $value = $this->get_value($args);
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';

        $html = sprintf('<textarea rows="5" cols="55" class="%1$s-text %5$s" id="%2$s[%3$s]" name="%2$s[%3$s]" style="%6$s" %7$s>%4$s</textarea>', $size, $args['save_to'], $args['id'], $value, $args['class'], $args['style'], disabled($args['disabled'], true, false));
        $html .= $this->get_field_description($args);

        echo $html;
    }

    /**
     * Displays a textarea for a settings field
     *
     * @param array $args settings field args
     */
    function callback_html($args)
    {
        echo $this->get_field_description($args);
    }

    /**
     * Displays a rich text textarea for a settings field
     *
     * @param array $args settings field args
     */
    function callback_wysiwyg($args)
    {

        $value = $this->get_value($args);
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : '500px';

        echo'<div style="display:none" class="hideable_container">';
            echo '<div style="max-width: ' . $size . ';">';

            $editor_settings = array(
                'teeny' => true,
                'textarea_name' => $args['save_to'] . '[' . $args['id'] . ']',
                'textarea_rows' => 10
            );
            if (isset($args['options']) && is_array($args['options'])) {
                $editor_settings = array_merge($editor_settings, $args['options']);
            }

            wp_editor($value, $args['save_to'] . '-' . $args['id'], $editor_settings);

            echo '</div>';

            echo $this->get_field_description($args);
        echo '</div>';
    }

    /**
     * Displays a file upload field for a settings field
     *
     * @param array $args settings field args
     */
    function callback_file($args)
    {

        $value = esc_attr($this->get_value($args));
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
        $id = $this->name . '[' . $args['id'] . ']';
        $label = isset($args['options']['button_label']) ?
            $args['options']['button_label'] :
            __('Choose File', BPMJ_EDDCM_DOMAIN);

        $html = sprintf('<input type="text" class="%1$s-text %3$s-url %6$s" id="%3$s[%4$s]" name="%2$s[%4$s]" value="%5$s" style="%7$s"/> ', $size, $args['save_to'], $this->name, $args['id'], $value, $args['class'], $args['style']);
        if (!empty($args['button_class'])) {
            $html .= '<a class="' . $this->prefix . 'browse ' . $args['button_class'] . '" href="javascript:;">' . $label . '</a>';
        } else {
            $html .= '<input type="button" class="button ' . $this->prefix . 'browse" value="' . $label . '" />';
        }
        $html .= $this->get_field_description($args);

        echo $html;
    }

    /**
     * Displays a password field for a settings field
     *
     * @param array $args settings field args
     */
    function callback_password($args)
    {

        $value = esc_attr($this->get_value($args));
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';

        $html = sprintf('<input type="password" class="%1$s-text %5$s" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" style="%6$s"/>', $size, $this->name, $args['id'], $value, $args['class'], $args['style']);
        $html .= $this->get_field_description($args);

        echo $html;
    }

    /**
     * Displays a color picker field for a settings field
     *
     * @param array $args settings field args
     */
    function callback_color($args)
    {

        $value = esc_attr($this->get_value($args));
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';

        $html = sprintf('<input type="text" class="%1$s-text wp-color-picker-field %6$s" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" data-default-color="%5$s" style="%7$s" />', $size, $args['save_to'], $args['id'], $value, $args['std'], $args['class'], $args['style']);
        $html .= $this->get_field_description($args);

        echo $html;
    }

    /**
     * Displays a color picker field for a templates
     */
    function callback_color_template($args)
    {
        global $wpidea_settings;

        $color = WPI()->templates->get_template_colors();

        $name = $args['id'];
        $name = explode('-', $name);
        unset($name[0]);
        unset($name[1]);
        foreach ($name as $part)
            $color = $color[$part];
        echo $color . '<br>';

        $value = isset($wpidea_settings[$args['id']]) ? $wpidea_settings[$args['id']] : $color;
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
        $html = sprintf('<input type="text" class="%1$s-text wp-color-picker-field" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s" data-default-color="%5$s" %6$s />', $size, $this->name, $args['id'], $value, $color, disabled($args['disabled'], true, false));
        $html .= $this->get_field_description($args);

        echo $html;
    }

    /**
     * Renewal Times Callback
     *
     * Renderuje pole wyboru godzin
     */
    function callback_renewal_times($args)
    {
        global $edd_options;

        $disabled = disabled($args['disabled'], true, false);
        ?>

        <?php _e('From', 'bpmj_eddpc'); ?> <input type="text" id="timeStart"
                                                  value="<?php echo isset($edd_options['bpmj_renewals_start']) ? $edd_options['bpmj_renewals_start'] : 14; ?>" <?php echo $disabled; ?>>
        <?php _e('To', 'bpmj_eddpc'); ?> <input type="text" id="timeEnd"
                                                value="<?php echo isset($edd_options['bpmj_renewals_end']) ? $edd_options['bpmj_renewals_end'] : 19; ?>" <?php echo $disabled; ?>>
        <?php _e('every day.', 'bpmj_eddpc'); ?>

        <label><?php echo $args['desc']; ?></label>
        <?php
    }

    /**
     * Renewal Discount Callback
     *
     * Renderuje pole wyboru wartości i typu zniżki
     */
    function callback_renewal_discount($args)
    {
        global $edd_options;
        ?>

        <input type="number" name="edd_settings[bpmj_renewal_discount_value]"
               value="<?php echo isset($edd_options['bpmj_renewal_discount_value']) ? $edd_options['bpmj_renewal_discount_value'] : ''; ?>" <?php disabled($args['disabled']) ?>>
        <select name="edd_settings[bpmj_renewal_discount_type]" <?php disabled($args['disabled']); ?>>
            <option value="percent" <?php echo isset($edd_options['bpmj_renewal_discount_type']) && $edd_options['bpmj_renewal_discount_type'] == 'percent' ? 'selected' : ''; ?>>
                %
            </option>
            <option value="flat" <?php echo isset($edd_options['bpmj_renewal_discount_type']) && $edd_options['bpmj_renewal_discount_type'] == 'flat' ? 'selected' : ''; ?>><?php echo edd_get_currency(); ?></option>
        </select>

        <label><?php echo $args['desc']; ?></label>
        <?php
    }

    /**
     * Renewal Callback
     *
     * Renderuje pole przypomnień w Ustawienia -> Dodatki
     *
     * @param array $args Arguments passed by the setting
     * @return void
     * @global $edd_options Array of all the EDD Options
     */
    function callback_renewal($args)
    {

        $add_url = esc_url(admin_url('admin.php?page=wp-idea-add-renewal'));
        $renewal_options = get_option('bmpj_eddpc_renewal');
        ?>
        <table id="edd_paid_content_renewal" class="wp-list-table widefat fixed posts">
            <thead>
            <tr>
                <th class="type"><?php _e('Reminder type', 'edd-paid-content'); ?></th>
                <th class="subject"><?php _e('Subject', 'edd-paid-content'); ?></th>
                <th class="send-period"><?php _e('Sending period', 'edd-paid-content'); ?></th>
                <th class="actions"><?php _e('Actions', 'edd-paid-content'); ?></th>
            </tr>
            </thead>

            <?php if (is_array($renewal_options)) { ?>
                <tbody>
                <?php
                foreach ($renewal_options as $key => $option) {
                    $edit_url = esc_url(admin_url('admin.php?page=wp-idea-edit-renewal&renewal-id=' . $key));
                    $delete_url = add_query_arg(array('wpid_action' => 'delete-renewal', 'renewal-id' => $key));
                    $type = !empty($option['type']) ? $option['type'] : 'renewal';
                    ?>
                    <tr>
                        <td><?php echo $type === 'renewal' ? __('Renewal', 'edd-paid-content') : __('Payment', 'edd-paid-content'); ?></td>
                        <td><?php echo $option['subject']; ?></td>
                        <td><?php echo bpmj_eddpc_renewal_period_description($option['send_period'], $type); ?></td>
                        <td>
                            <a class="bpmj-eddpc-renewal-edit"
                               href="<?php echo $edit_url; ?>"><?php _e('Edit', 'edd-paid-content'); ?></a> |
                            <a class="bpmj-eddpc-renewal-delete"
                               href="<?php echo $delete_url; ?>"><?php _e('Delete', 'edd-paid-content'); ?></a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            <?php } ?>

        </table>
        <p>
            <a href="<?php echo $add_url; ?>" class="button-secondary"
               id="edd_paid_content_add_renewal"><?php _e('Add a renewal reminder', 'edd-paid-content'); ?></a>
            <?php
            $add_payment_notice_label = __('Add a payment reminder', 'edd-paid-content');
            if (bpmj_eddpc_recurring_payments_possible()):
                ?>
                <a href="<?php echo $add_url; ?>&amp;bpmj-renewal-type=payment" class="button-secondary"
                   id="edd_paid_content_add_payment_notice"><?php echo $add_payment_notice_label; ?></a>
            <?php
            else:
                ?>
                <button disabled="disabled" class="button-secondary"
                        title="<?php esc_attr_e('You cannot add reminders - none of the enabled payment methods supports recurring payments.', 'edd-paid-content'); ?>"><?php echo $add_payment_notice_label; ?></button>
            <?php
            endif;
            ?>
        </p>
        <label><?php echo $args['desc']; ?></label>
        <?php
    }


    function callback_certificates($args)
    {
        $certificates = new Certificate_Template();
        $certificates = $certificates->find_all();

        echo View::get_admin('/certificate-template/list-certificate-template', [
                'certificates' => $certificates
        ]);

    }

    /**
     * Displays a payment methods (links)
     *
     * @param array $args settings field args
     */
    function callback_bpmj_groups($args)
    {
        if (in_array($args['groups_type'], array('mailers', 'invoices'))): ?>
            <div style="text-align: right; margin-bottom: 10px;">
                <a class="button" href="<?php echo wp_nonce_url(add_query_arg(array(
                    'page' => $_GET['page'],
                    'bpmj_eddcm_reload_cache' => $args['groups_type'],
                ), admin_url('admin.php')), 'bpmj_eddcm_reload_cache'); ?>">
						<span
                                class="dashicons dashicons-update"
                                style="vertical-align: middle;"></span> <?php _e('Reload API cache', BPMJ_EDDCM_DOMAIN); ?>
                </a>
            </div>
        <?php
        endif;
        ?>
        <div class="panel-group" id="accordion-<?php echo $args['groups_type']; ?>" role="tablist"
             aria-multiselectable="true">

            <?php
            foreach ($args['options'] as $slug => $gate) {

                // Form name
                $form_name = $gate['save_to'];
                $integration_type = null;

                // Type of group
                if ($args['groups_type'] == 'gateways') {
                    $integration_type = Integrations::TYPE_GATEWAYS;

                    if (strpos($slug, 'edd-') === 0) {
                        $slug = substr($slug, 4);
                    }
;
                    if ($slug == 'paypal' || $slug == 'manual' || $slug === 'payu' || $slug === 'coinbase' || $slug === 'stripe') {
                        $enable_method = 'edd_settings[gateways][' . $slug . ']';
                        $mode = $this->get_option(['gateways', $slug], false, 'edd_settings');
                    } else {
                        $slug = $slug . '_gateway';
                        $enable_method = 'edd_settings[gateways][' . $slug . ']';
                        $mode = $this->get_option(['gateways', $slug], false, 'edd_settings');
                    }

                    $enable_method_slug = $slug . '-gateway';
                } else {

                    if ($args['groups_type'] == 'mailers') {
                        $integration_type = Integrations::TYPE_MAILERS;
                    } else {
                        $integration_type = Integrations::TYPE_INVOICES;
                    }

                    $enable_method = 'wp_idea[integrations][' . $slug . ']';
                    $enable_method_slug = $slug . '-integration';
                    $mode = $this->get_option(['integrations', $slug], false, 'wp_idea');
                }


                $mode = is_numeric($mode) ? 'on' : 'off';

                if (!empty($gate['status']) && $gate['status'] == 'off') {
                    continue;
                }
                ?>

                <div class="panel panel-default">
                    <div class="panel-heading" role="tab"
                         id="heading-<?php echo $slug; ?>"<?php if (file_exists(BPMJ_EDDCM_DIR . 'assets/imgs/logos/' . $slug . '.jpg')) { ?> style="background: #fff url(<?php echo BPMJ_EDDCM_URL . 'assets/imgs/logos/' . $slug . '.jpg'; ?>); background-position: center right; background-repeat: no-repeat;" <?php } ?>>
                        <h4 class="panel-title <?php if (isset($mode) && $mode == 'on') echo 'active'; ?>">
                            <a role="button" class="collapsed" data-toggle="collapse"
                               data-parent="#accordion-<?php echo $args['groups_type']; ?>" href="#<?php echo $slug; ?>"
                               aria-expanded="false" aria-controls="<?php echo $slug; ?>">
                                <?php echo $gate['name']; ?><span class="dashicons"></span>
                            </a>
                        </h4>
                    </div>
                    <div id="<?php echo $slug; ?>" class="panel-collapse collapse" role="tabpanel"
                         aria-labelledby="<?php echo $gate['name']; ?>">
                        <div class="panel-body">
                            <?php
                            if (isset($gate['settings']) && is_array($gate['settings'])) {
                                foreach ($gate['settings'] as $name => $setting) {

                                    $size = isset($setting['size']) ? $setting['size'] : '47%';
                                    if (!isset($setting['type']) || $setting['type'] !== 'message') {
                                        echo '<fieldset style="width:' . $size . '" class="_' . $name . '">';
                                    }

                                    // Field Label
                                    $default = isset($setting['default']) ? $setting['default'] : '';
                                    $type = isset($setting['type']) ? $setting['type'] : 'text';

                                    switch ($type) {


                                        // Select with options
                                        case 'select':
                                            $value = $this->get_option($name, $default, $form_name);
                                            if (isset($setting['label'])) {
                                                echo '<label for="' . $name . '">' . $setting['label'] . '</label>';
                                            }
                                            echo '<select name="' . $form_name . '[' . $name . ']" id="' . $name . '">';
                                            if (isset($setting['options']) && is_array($setting['options'])) {
                                                foreach ($setting['options'] as $key => $option) {
                                                    $selected = $value == $key ? ' selected' : '';
                                                    echo '<option value="' . $key . '" ' . $selected . '>' . $option . '</option>';
                                                }
                                            }
                                            echo '</select>';
                                            break;

                                        // Radio with options
                                        case 'radio':
                                            $value = $this->get_option($name, $default, $form_name);
                                            if (isset($setting['label'])) {
                                                echo '<label for="' . $name . '">' . $setting['label'] . '</label>';
                                            }
                                            if (isset($setting['options']) && is_array($setting['options'])) {
                                                foreach ($setting['options'] as $key => $option) {
                                                    echo '<label><input type="radio" name="' . $form_name . '[' . $name . ']" id="' . $name . '" value="' . $key . '" ' . checked($value, $key, false) . '> ' . $option . '</label>';
                                                }
                                            }
                                            break;


                                        // Message
                                        case 'message':
                                            $class = isset($setting['class']) ? $setting['class'] : '';
                                            if (isset($setting['label'])) {
                                                echo '<label for="' . $name . '">' . $setting['label'] . '</label>';
                                            }
                                            echo '<p class="message ' . $class . '">' . $setting['text'] . '</p>';
                                            break;


                                        // Checkbox
                                        case 'checkbox':
                                            if (isset($setting['label'])) {
                                                echo '<label for="' . $name . '">';
                                            }
                                            echo '<input type="hidden" name="' . $form_name . '[' . $name . ']">';
                                            echo '<input type="checkbox" name="' . $form_name . '[' . $name . ']" id="' . $name . '" value="1" ' . checked($this->get_option($name, $default, $form_name), 1, false) . '>';
                                            if (isset($setting['label'])) {
                                                echo ' ' . $setting['label'] . '</label>';
                                            }
                                            break;


                                        // Email
                                        case 'email':
                                            if (isset($setting['label'])) {
                                                echo '<label for="' . $name . '">' . $setting['label'] . '</label>';
                                            }
                                            echo '<input type="email" name="' . $form_name . '[' . $name . ']" id="' . $name . '" value="' . $this->get_option($name, $default, $form_name) . '">';
                                            break;


                                        // Number
                                        case 'number':
                                            if (isset($setting['label'])) {
                                                echo '<label for="' . $name . '">' . $setting['label'] . '</label>';
                                            }
                                            echo '<input type="number" name="' . $form_name . '[' . $name . ']" id="' . $name . '" value="' . $this->get_option($name, $default, $form_name) . '">';
                                            break;

                                        // Number
                                        case 'password':
                                            if (isset($setting['label'])) {
                                                echo '<label for="' . $name . '">' . $setting['label'] . '</label>';
                                            }
                                            echo '<input type="password" name="' . $form_name . '[' . $name . ']" id="' . $name . '" value="' . $this->get_option($name, $default, $form_name) . '">';
                                            break;

                                        // ActiveCampaign Tags
                                        case 'activecampaign_tags':
                                            // SalesManago Tags
                                        case 'salesmanago-tags':
                                            // iPresso tags
                                        case 'ipresso_tags':
                                            if (isset($setting['label'])) {
                                                echo '<label for="' . $name . '">' . $setting['label'] . '</label>';
                                            }
                                            echo '<input name="' . $form_name . '[' . $name . ']" class="bpmj_eddcm_tags" value="' . $this->get_option($name, $default, $form_name) . '" />';
                                            break;

                                        // Textarea
                                        case 'textarea':
                                            if (isset($setting['label'])) {
                                                echo '<label for="' . $name . '">' . $setting['label'] . '</label>';
                                            }
                                            echo '<textarea name="' . $form_name . '[' . $name . ']" rows="7">' . esc_html($this->get_option($name, $default, $form_name)) . '</textarea>';
                                            break;

                                        // PayU return url input
                                        case 'payu_return_url':
                                            echo '<input type="text" class="payu-return-url regular-text" id="edd_settings[' . edd_sanitize_key($setting['id']) . ']" value="' . esc_attr($setting['value']) . '" readonly="readonly"/>';
                                            break;
                                        // Default is input text
                                        default:
                                            if (isset($setting['label'])) {
                                                echo '<label for="' . $name . '">' . $setting['label'] . '</label>';
                                            }
                                            $pattern_option = (isset($setting['validation_regex'])) ? 'pattern="' . $setting['validation_regex'] . '"' : '';
                                            echo '<input type="text" '.$pattern_option.' name="' . $form_name . '[' . $name . ']" id="' . $name . '" value="' . $this->get_option($name, $default, $form_name) . '">';
                                            break;
                                    }

                                    if (isset($setting['desc'])) {
                                        echo '<p>' . $setting['desc'] . '</p>';
                                    } else {
                                        echo '<p>&nbsp;</p>';
                                    }


                                    if (!isset($setting['type']) || $setting['type'] !== 'message') {
                                        echo '</fieldset>';
                                    }
                                }

                                if ('gateways' === $args['groups_type']):
                                    $modified_slug = str_replace( '_gateway', '', $slug );
                                    $checkout_label = $this->get_option($modified_slug . '_checkout_label', '', $form_name);
                                    ?>
                                    <div style="clear: both;"></div>
                                    <fieldset style="width: 47%">
                                        <label for="_<?php echo $form_name; ?>_label"><?php _e('Payment method\'s label', BPMJ_EDDCM_DOMAIN) ?></label>
                                        <input type="text" id="_<?php echo $form_name; ?>_label"
                                               name="<?php echo $form_name; ?>[<?php echo $modified_slug; ?>_checkout_label]"
                                               value="<?php echo esc_attr($checkout_label); ?>"
                                               placeholder="<?php echo esc_attr($gate['name']); ?>"/>
                                    </fieldset>
                                <?php endif;
                            }
                            ?>


                            <fieldset class="enable-method <?php echo $enable_method_slug; ?>">
                                <label for="<?php echo $enable_method_slug; ?>">
                                    <input type="checkbox" name="<?php echo $enable_method; ?>"
                                           id="<?php echo $enable_method_slug; ?>"
                                           value="1" <?php if ($mode == 'on') echo 'checked="checked"'; ?>>
                                    <?php
                                    if ($mode == 'on')
                                        _e("Uncheck to disable this integration", BPMJ_EDDCM_DOMAIN);
                                    else
                                        _e("Check to enable this integration", BPMJ_EDDCM_DOMAIN);
                                    ?>
                                </label>
                            </fieldset>
                            <?php // if ($mode == 'on') { ?>
                            <?php  if (false) { ?>
                            <fieldset  class="check-connection-fieldset">
                                <a class="button" href="<?= Integrations::get_check_connection_url($integration_type, $slug) ?>">
						            <?php _e('Check connection', BPMJ_EDDCM_DOMAIN); ?>
                                </a>
                            </fieldset>
                            <?php } ?>



                        </div>
                    </div>
                </div>

                <?php
            }
            ?>
        </div>
        <?php
    }

    function callback_gateways($args)
    {
        edd_gateway_select_callback($args);
    }

    public function callback_override_all($args)
    {
        $value = $this->get_value($args);
        $this->callback_checkbox($args);

        if ('on' !== $value) {
            // we show this only if switching from disabled to enabled

            $all_pages_new = $args['options'];
            $old_show_on_front = get_option('show_on_front');
            $old_show_on_front_label = '';
            switch ($old_show_on_front) {
                case 'posts':
                    $old_show_on_front_label = __('Your latest posts');
                    break;
                case 'page':
                    $home_page = get_post(get_option('page_on_front'));
                    $old_show_on_front_label = __('A static page') . ': ' . esc_attr($home_page->post_title);
                    break;
            }
            $all_pages_new = array('' => sprintf(__('Do not change - display %s', BPMJ_EDDCM_DOMAIN), $old_show_on_front_label)) + $all_pages_new;
            $select_option = array(
                'name' => 'course_list_page',
                'label' => __('Course list page', BPMJ_EDDCM_DOMAIN),
                'type' => 'select',
                'default' => '',
                'options' => $all_pages_new,
            );
            $select_args = $this->prepare_field_args('select', $select_option);
            ?>
            <div class="wp_idea-description-field" id="wp_idea-override-all-home-page" style="display: none;">
                <?php _e('Choose the page that will override your current home, typically a page with course list.'); ?>
                <div>
                    <?php $this->callback_select($select_args); ?>
                </div>
            </div>
            <script type="text/javascript">
                jQuery(function ($) {
                    $('#wp_idea-override-all-home-page').prev('fieldset').find('input[type="checkbox"]').click(function () {
                        $checkbox = $(this);
                        if ($checkbox.is(':checked')) {
                            $('#wp_idea-override-all-home-page').show();
                        } else {
                            $('#wp_idea-override-all-home-page').hide();
                        }
                    });
                });
            </script>
            <?php
        }
    }

    /**
     * @param array $args
     */
    public function callback_date_offset($args)
    {
        $value = $this->get_value($args);
        $number = 1;
        $unit = 'days';
        if (1 === preg_match('/^(\d+) (hours|days|weeks|months|years)$/', $value, $match)) {
            $number = $match[1];
            $unit = $match[2];
        }

        $html = sprintf('<input class="%1$s" name="%2$s[%3$s][number]" id="%2$s[%3$s][number]" style="%4$s; width: 70px;" value="%5$s" type="number" min="1" max="99" %6$s />', $args['class'], $args['save_to'], $args['id'], $args['style'], $number, disabled($args['disabled'], true, false));
        $html .= ' ';
        $html .= sprintf('<select class="%1$s" name="%2$s[%3$s][unit]" id="%2$s[%3$s][number]" style="%4$s" %5$s>', $args['class'], $args['save_to'], $args['id'], $args['style'], disabled($args['disabled'], true, false));

        $options = array(
            'hours' => __('Hours', BPMJ_EDDCM_DOMAIN),
            'days' => __('Days', BPMJ_EDDCM_DOMAIN),
            'weeks' => __('Weeks', BPMJ_EDDCM_DOMAIN),
            'months' => __('Months', BPMJ_EDDCM_DOMAIN),
            'years' => __('Years', BPMJ_EDDCM_DOMAIN),
        );

        if (!empty($args['options'])) {
            if ($args['options'] === array_values($args['options'])) {
                // $args['options'] is a sequential numeric array
                $options_filtered = $args['options'];
            } else {
                // $args['options'] is an associative array
                $options_filtered = array_keys($args['options']);
            }
            $options = array_intersect_key($options, array_flip($options_filtered));
        }

        foreach ($options as $key => $label) {
            $html .= sprintf('<option value="%1$s" %2$s>%3$s</option>', $key, selected($unit, $key, false), $label);
        }

        $html .= '</select>';
        $html .= $this->get_field_description($args);

        echo $html;
    }

    /**
     * @param array $args
     */
    public function callback_button_array($args)
    {
        $html = '';
        if (!empty($args['options'])) {
            foreach ($args['options'] as $key => $label) {
                $html .= sprintf('<button id="%1$s" class="%2$s" style="%3$s" value="%5$s" %6$s>%4$s</button>', $args['save_to'] . '_' . $args['id'] . '_' . $key, $args['class'], $args['style'], esc_html($label), $key, disabled($args['disabled'], true, false)) . ' ';
            }
        }

        echo $html;
    }

    /**
     * @param array $input_value
     *
     * @return string
     */
    public function sanitize_date_offset($input_value)
    {
        if (!is_array($input_value) || empty($input_value['number']) || empty($input_value['unit'])) {
            return '';
        }

        $date_offset_string = "{$input_value['number']} {$input_value['unit']}";
        if (false === strtotime($date_offset_string)) {
            return '';
        }

        return $date_offset_string;
    }

    /**
     * Sanitize options on default option name
     *
     * @param array $value_array
     * @return array
     */
    public function sanitize_options($value_array)
    {
        $callback = $this->prepare_sanitize_options_callback($this->name);
        return $callback($value_array);
    }

    /**
     * Prepare sanitize callback for Settings API
     * We define a closure here so we can bypass a feature (probably a bug)
     * of Wordpress that sanitize_option passes 3 arguments (with
     * desired by us $option_name), but register_setting actually
     * sets the callback to accept only one argument
     *
     * Wordpress version: 4.6
     *
     * @see sanitize_option
     * @see register_setting
     */
    public function prepare_sanitize_options_callback($option_name)
    {
        $that = $this;
        $main_option_name = $this->name;
        $sanitize_options_closure = function ($value_array) use ($option_name, $that, $main_option_name) {
            if (!isset($value_array) || empty($value_array) || !is_array($value_array)) {
                return $value_array;
            }

            foreach ($value_array as $option_slug => $option_value) {
                $sanitize_callback = $that->get_sanitize_callback($option_slug);

                // If callback is set, call it
                if ($sanitize_callback) {
                    $value_array[$option_slug] = call_user_func($sanitize_callback, $option_value);
                    continue;
                }
            }

            /*
			 * If we are updating a foreign option we need to pull
			 * it's original state and merge with our changes - probably
			 * we don't set every single possible key by ourselves
			 */
            if ($option_name !== $main_option_name) {
                $option_value = get_option($option_name, array());
                $value_array = array_merge(is_array($option_value) ? $option_value : array(), $value_array);
            }

            return $value_array;
        };
        return $sanitize_options_closure;
    }

    /**
     * Get sanitization callback for given option slug
     *
     * @param string $slug option slug
     *
     * @return mixed string or bool false
     */
    public function get_sanitize_callback($slug = '')
    {
        if (empty($slug)) {
            return false;
        }

        // Iterate over registered fields and see if we can find proper callback
        foreach ($this->settings_fields as $section => $options) {
            foreach ($options as $option) {
                if(is_null($option)) {
                    continue;
                }

                if ($option['name'] != $slug) {
                    continue;
                }

                // Return the callback name
                $callback = isset($option['sanitize_callback']) && is_callable($option['sanitize_callback']) ? $option['sanitize_callback'] : false;
                if (false === $callback) {
                    $callback = $this->get_standard_sanitize_callback($option['type']);
                }

                return $callback;
            }
        }

        return false;
    }

    /**
     * Get the value of a settings field
     *
     * @param array|string $option settings field name
     * @param string $default default text if it's not found
     * @param string $option_name
     *
     * @return string
     */
    function get_option($option, $default = '', $option_name = '')
    {

        $options = $this->detached ? $this->detached_options : get_option($option_name);

        if (is_array($option)) {
            if (isset($options[$option[0]][$option[1]])) {
                return $options[$option[0]][$option[1]];
            }
        } elseif (isset($options[$option])) {
            return $options[$option];
        }

        return $default;
    }

    /**
     * Returns detached field's value
     *
     * @param string $field
     * @return mixed
     */
    public function get_detached_option_value($field)
    {
        $args = $this->get_detached_args($field);
        return $this->get_value($args);
    }

    /**
     * Creates HTML data- attributes string from array
     *
     * @param array $data
     * @return string
     */
    public function create_data_attributes(array $data)
    {
        $attribute_array = array();
        foreach ($data as $key => $value) {
            $attribute_array[] = 'data-' . sanitize_title_with_dashes($key) . '="' . esc_attr($value) . '"';
        }
        if (!empty($attribute_array)) {
            return ' ' . implode(' ', $attribute_array);
        }
        return '';
    }

    /**
     * Show navigations as tab
     *
     * Shows all the settings section labels as tab
     */
    function show_navigation()
    {
        $html = '<h2 class="nav-tab-wrapper ' . $this->prefix . 'nav-tab-wrapper">';

        foreach ($this->settings_sections as $tab) {
            $html .= sprintf('<a href="#%1$s" class="nav-tab" id="%1$s-tab">%2$s</a>', $tab['id'], $tab['title']);
        }

        $html .= '</h2>';

        echo $html;
    }

    /**
     * Show the section settings forms
     *
     * This function displays every sections in a different form
     */
    function show_forms()
    {
        ?>
        <div class="metabox-holder">
            <form id="bpmj-eddcm-pdf-preview-hidden-form" method="post" target="_blank"
                  action="<?php echo get_admin_url(); ?>admin.php?page=wp-idea-settings">
                <input type="hidden" name="action" value="gift_pdf_voucher_preview">
                <input type="hidden" name="type">
                <input type="hidden" name="content">
                <input type="hidden" name="styles">
                <input type="hidden" name="orientation">
                <input type="hidden" name="pdftype" value="cert">
            </form>
            <form class="wptao-settings-form" method="post" action="options.php">
                <?php settings_fields($this->name); ?>
                <input type="hidden" name="<?php echo $this->name; ?>[enable_edd]"
                       value="<?php echo bpmj_eddcm_get_option('enable_edd', 'off'); ?>"/>
                <?php if ($this->force_save): ?>
                    <input type="hidden" name="<?php echo $this->name; ?>[_force_save_ts]"
                           value="<?php echo microtime(); ?>"/>
                <?php endif; ?>
                <input type="hidden" name="wp_idea[course_list_page]"
                       value="<?php echo bpmj_eddcm_get_option('course_list_page'); ?>">
                <input type="hidden" name="wp_idea[voucher_page]"
                       value="<?php echo bpmj_eddcm_get_option('voucher_page'); ?>">
                <input type="hidden" name="wp_idea[certificates_page]"
                       value="<?php echo bpmj_eddcm_get_option('certificates_page'); ?>">
                <?php if (!Helper::is_dev()) : ?>
                    <input type="hidden" name="wp_idea[trial_version_expiration_date]"
                           value="<?php echo bpmj_eddcm_get_option('trial_version_expiration_date', ''); ?>">
                <?php endif ?>
                <?php foreach ($this->settings_sections as $form) { ?>
                    <div id="<?php echo $form['id']; ?>" class="<?php echo $this->prefix; ?>group"
                         style="display: none;">

                        <?php
                        do_action($this->prefix . 'form_top_' . $form['id'], $form);
                        do_settings_sections($form['id']);
                        do_action($this->prefix . 'form_before_subsections_' . $form['id'], $form);
                        if (!empty($form['subsections'])) {
                            foreach ($form['subsections'] as $subsection):
                                do_action($this->prefix . 'form_top_' . $subsection['id'], $subsection);
                                do_settings_sections($subsection['id']);
                                do_action($this->prefix . 'form_bottom_' . $subsection['id'], $subsection);
                            endforeach;
                        }
                        do_action($this->prefix . 'form_bottom_' . $form['id'], $form);
                        ?>
                        <div style="padding-left: 10px">
                            <?php submit_button(); ?>
                        </div>

                    </div>
                <?php } ?>
            </form>
        </div>
        <?php

        $this->script();
    }

    public function script_file($skip_script_tag = true)
    {
        if ($this->script_file_already_included) {
            return;
        }
        $this->script_file_already_included = true;
    if (!$skip_script_tag):
        ?>
        <script type="text/javascript">
            <?php
            endif;
            ?>
            jQuery(document).ready(function () {
                jQuery('.<?php echo $this->prefix; ?>browse').on('click', function (event) {
                    event.preventDefault();

                    var self = jQuery(this);

                    // Create the media frame.
                    var file_frame = wp.media.frames.file_frame = wp.media({
                        title: self.data('uploader_title'),
                        button: {
                            text: self.data('uploader_button_text'),
                        },
                        multiple: false
                    });

                    file_frame.on('select', function () {
                        attachment = file_frame.state().get('selection').first().toJSON();

                        self.prev('.<?php echo $this->prefix; ?>url').val(attachment.url);
                    });

                    // Finally, open the modal
                    file_frame.open();
                });
            });
            <?php
            if ( !$skip_script_tag ):
            ?>
        </script>
    <?php
    endif;
    }

    public function script_radio_with_other_option($skip_script_tag = true)
    {
    if (!$skip_script_tag):
        ?>
        <script type="text/javascript">
            <?php
            endif;
            ?>
            $('._radio_list_with_other_option').click(function (ev) {
                var $radio = $(ev.target).is('input[type="radio"]') ? $(ev.target) : false;
                if ($radio) {
                    if ('other' === $radio.val()) {
                        $radio.closest('label').next('input[type="text"]').attr('disabled', false).focus();
                    } else {
                        $(ev.delegateTarget).find('._other_option_input').attr('disabled', 'disabled');
                    }
                }
            });
            <?php
            if ( !$skip_script_tag ):
            ?>
        </script>
    <?php
    endif;
    }

    /**
     * Tabbable JavaScript codes & Initiate Color Picker
     *
     * This code uses localstorage for displaying active tabs
     */
    function script()
    {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                //Initiate Color Picker
                if ($('.wp-color-picker-field').length > 1) {
                    $('.wp-color-picker-field').wpColorPicker();
                }

                // Switches option sections
                $('.<?php echo $this->prefix; ?>group').hide();
                var activetab = '';
                var maybe_active = '';

                var url = new URL(location.href);
                var autofocus = url.searchParams.get("autofocus");

                if (autofocus) {
                    maybe_active = '#' + autofocus
                } else if (typeof (localStorage) != 'undefined') {
                    maybe_active = localStorage.getItem('<?php echo $this->prefix; ?>settings-active-tab');
                }

                if (maybe_active) {

                    // Check if tabs exists
                    $('.<?php echo $this->prefix; ?>nav-tab-wrapper a').each(function () {

                        if ($(this).attr('href') === maybe_active) {
                            activetab = maybe_active;
                        }
                    });

                }

                if (activetab != '' && $(activetab).length) {
                    $(activetab).fadeIn();
                } else {
                    $('.<?php echo $this->prefix; ?>group:first').fadeIn();
                }
                $('.<?php echo $this->prefix; ?>group .collapsed').each(function () {
                    $(this).find('input:checked').parent().parent().parent().nextAll().each(
                        function () {
                            if ($(this).hasClass('last')) {
                                $(this).removeClass('hidden');
                                return false;
                            }
                            $(this).filter('.hidden').removeClass('hidden');
                        });
                });

                if (activetab != '' && $(activetab + '-tab').length) {
                    $(activetab + '-tab').addClass('nav-tab-active');
                } else {
                    $('.<?php echo $this->prefix; ?>nav-tab-wrapper a:first').addClass('nav-tab-active');
                }
                $('.<?php echo $this->prefix; ?>nav-tab-wrapper a').click(function (evt) {

                    if (typeof (localStorage) != 'undefined') {
                        localStorage.setItem('<?php echo $this->prefix; ?>settings-active-tab', $(this).attr('href'));
                    }

                    $('.<?php echo $this->prefix; ?>nav-tab-wrapper a').removeClass('nav-tab-active');

                    $(this).addClass('nav-tab-active').blur();
                    var clicked_group = $(this).attr('href');


                    $('.<?php echo $this->prefix; ?>group').hide();
                    $(clicked_group).fadeIn();
                    evt.preventDefault();
                });

                <?php $this->script_file(); ?>
                <?php $this->script_radio_with_other_option(); ?>
            });
        </script>

        <style type="text/css">
            /** WordPress 3.8 Fix **/
            .form-table th {
                padding: 20px 10px;
            }

            #wpbody-content .metabox-holder {
                padding-top: 5px;
            }
        </style>
        <?php
    }

    /**
     *
     * @param string $field
     */
    public function show_detached_field_row($field, array $html_hooks = array())
    {
        ?>
        <tr>
            <th scope="row">
                <?php echo(isset($html_hooks['pre_label']) ? $html_hooks['pre_label'] : ''); ?>
                <?php $this->show_detached_field_label($field); ?>
                <?php echo(isset($html_hooks['post_label']) ? $html_hooks['post_label'] : ''); ?>
            </th>
            <td>
                <?php echo(isset($html_hooks['pre_field']) ? $html_hooks['pre_field'] : ''); ?>
                <?php $this->show_detached_field($field); ?>
                <?php echo(isset($html_hooks['post_field']) ? $html_hooks['post_field'] : ''); ?>
            </td>
        </tr>
        <?php
    }

    public function show_detached_field($field)
    {
        $args = $this->get_detached_args($field);
        $type = $args['type'];
        $callback = 'callback_' . $type;
        if (false === method_exists($this, $callback)) {
            $callback = 'callback_text';
        }
        call_user_func(array($this, $callback), $args);
    }

    public function show_detached_field_label($field)
    {
        $args = $this->get_detached_args($field);
        ?>
        <label><?php echo esc_html($args['name']); ?></label>
        <?php
    }

    /**
     * @param string $type
     * @param array $option
     *
     * @return array
     */
    public function prepare_field_args($type, $option)
    {
        $args = array(
            'id' => $option['name'],
            'label_for' => $args['label_for'] = "{$this->name}[{$option[ 'name' ]}]",
            'desc' => isset($option['desc']) ? $option['desc'] : '',
            'name' => isset($option['label']) ? $option['label'] : '',
            'size' => isset($option['size']) ? $option['size'] : null,
            'class' => isset($option['class']) ? $option['class'] : null,
            'button_class' => isset($option['button_class']) ? $option['button_class'] : null,
            'style' => isset($option['style']) ? $option['style'] : null,
            'options' => isset($option['options']) ? $option['options'] : '',
            'std' => isset($option['default']) ? $option['default'] : '',
            'sanitize_callback' => isset($option['sanitize_callback']) ? $option['sanitize_callback'] : '',
            'type' => $type,
            'save_to' => isset($option['save_to']) ? $option['save_to'] : $this->name,
            'groups_type' => isset($option['groups_type']) ? $option['groups_type'] : '',
            'disabled' => isset($option['disabled']) && $option['disabled'] ? true : false,
            'min' => isset($option['min']) ? $option['min'] : null,
        );
        if (isset($option['explicit_value'])) {
            $args['explicit_value'] = $option['explicit_value'];
        }

        return $args;
    }

    public function output_field($option)
    {
        $type = isset($option['type']) ? $option['type'] : 'text';
        $args = $this->prepare_field_args($type, $option);

        $callback = 'callback_' . $type;
        call_user_func(array($this, $callback), $args);
    }

    /**
     * @param array $args
     *
     * @return string
     */
    private function get_value($args)
    {
        if (isset($args['explicit_value'])) {
            return $args['explicit_value'];
        }

        if (!isset($args['id'])) {
            return null;
        }
        $std = isset($args['std']) ? $args['std'] : null;
        $save_to = isset($args['save_to']) ? $args['save_to'] : null;

        return $this->get_option($args['id'], $std, $save_to);
    }

    /**
     * @param array $option_array_new
     * @param array $option_array_old
     * @param string $key
     *
     * @return bool
     */
    public function has_option_key_changed($option_array_new, $option_array_old, $key)
    {
        if (empty($option_array_new[$key]) && empty($option_array_old[$key])) {
            return false;
        }
        if (empty($option_array_new[$key]) && !empty($option_array_old[$key])) {
            return true;
        }
        if (!empty($option_array_new[$key]) && empty($option_array_old[$key])) {
            return true;
        }
        return $option_array_new[$key] !== $option_array_old[$key];
    }

    /**
     * @param string $type
     *
     * @return array|bool
     */
    public function get_standard_sanitize_callback($type)
    {
        $callback_name = 'sanitize_' . $type;
        if (method_exists($this, $callback_name)) {
            return array($this, $callback_name);
        }

        return false;
    }

}
