<?php

namespace bpmj\wpidea\admin;

use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\admin\tools\importer\Students_Importer;
use bpmj\wpidea\API_V2;
use bpmj\wpidea\settings\Interface_Settings;

// Exit if accessed directly

if (!defined('ABSPATH')) {
    exit;
}

if (class_exists('bpmj\wpidea\admin\settings\Settings_API')) {
    return;
}

class Tools
{

    /**
     * Tools sections
     * @var array
     */
    private $sections = array();
    private Interface_Settings $settings;

    public function __construct(Interface_Settings $settings)
    {
        $this->settings = $settings;
        $this->sections = $this->get_default_sections();

        // init students importer actions
        new Students_Importer();
    }


    /**
     * Get default tools sections
     * @return array
     */
    private function get_default_sections()
    {
        $sections = array(
            array(
                'id' => 'import_students',
                'title' => __('Import students', BPMJ_EDDCM_DOMAIN),
                'view_callback' => array($this, 'import_students_view_callback'),
                'fields' => array(
                    array(
                        'id' => 'file',
                        'title' => __('CSV file with students', BPMJ_EDDCM_DOMAIN),
                        'type' => 'file',
                        'settings' => array(),
                    ),
                    array(
                        'id' => 'courses',
                        'title' => __('Courses for imported students', BPMJ_EDDCM_DOMAIN),
                        'type' => 'select',
                        'settings' => array(
                            'multiple' => true,
                        ),
                    ),
                    array(
                        'id' => 'access',
                        'title' => __('Send access information to new accounts', BPMJ_EDDCM_DOMAIN),
                        'type' => 'checkbox',
                        'settings' => array(),
                    ),
                    array(
                        'id' => 'notification',
                        'title' => __('Send purchase notifications', BPMJ_EDDCM_DOMAIN),
                        'type' => 'checkbox',
                        'settings' => array(),
                    ),
                    array(
                        'id' => 'mailing',
                        'title' => __('Add users to courses mailing lists', BPMJ_EDDCM_DOMAIN),
                        'type' => 'checkbox',
                        'settings' => array(),
                    ),
                ),
            ),
            array(
                'id' => 'banned_emails',
                'title' => __('Banned emails', BPMJ_EDDCM_DOMAIN),
                'view_callback' => array($this, 'banned_emails_view_callback'),
            ),
            array(
                'id'            => 'api',
                'title'         => __( 'API key', BPMJ_EDDCM_DOMAIN),
                'view_callback' => array( $this, 'api_key_view_callback' ),
            ),
        );

        $courses_functionality_enabled = $this->settings->get(Settings_Const::COURSES_ENABLED) ?? true;

        if(!$courses_functionality_enabled) {
            $sections = array_filter($sections, static function ($section) {
                return $section['id'] !== 'import_students';
            });
        }

        return array_values($sections);
    }

    public function import_students_view_callback()
    {
        require_once BPMJ_EDDCM_DIR . 'includes/admin/views/tools/import-students.php';
    }

    public function banned_emails_view_callback()
    {
        require_once BPMJ_EDDCM_DIR . 'includes/admin/views/tools/banned-emails.php';
    }
    public function api_key_view_callback()
    {
        if(empty(API_V2::get_api_key())){
            API_V2::generate_and_save_api_key();
        }

        if(
                ! empty( $_POST['regenerate'] ) AND
                wp_verify_nonce( $_POST['regenerate_api_key_nonce'], 'regenerate_api_key_nonce' ) AND
                current_user_can( 'manage_shop_settings' )
        ) {
            API_V2::generate_and_save_api_key();
        }


        require_once BPMJ_EDDCM_DIR . 'includes/admin/views/tools/api-key.php';
    }

    public function get_sections()
    {
        return $this->sections;
    }

    public function get_section_field($section_id, $field_id)
    {
        $section = array();

        foreach ($this->sections as $sect)
            if ($sect['id'] === $section_id)
                $section = $sect;

        foreach ($section['fields'] as $field) {
            if ($field['id'] === $field_id) {
                return $field;
            }
        }
    }

    public function display_section_field($section_id, $field_id, $settings = array())
    {
        $section = array();

        foreach ($this->sections as $sect)
            if ($sect['id'] === $section_id)
                $section = $sect;

        foreach ($section['fields'] as $field) {
            if ($field['id'] === $field_id) {
                if (isset($field['settings']))
                    $settings = array_merge($settings, $field['settings']);

                call_user_func(array($this, 'display_field_' . $field['type'] . '_type'), $section_id, $field_id, $settings);
            }
        }
    }

    public function display_field_file_type($section_id, $field_id, $settings)
    {
        $value = isset($_POST['wp_idea']['tools'][$section_id][$field_id]) ? esc_url($_POST['wp_idea']['tools'][$section_id][$field_id]) : '';
        ?>
        <input class="regular-text wp_idea-browse-url" type="text"
               name="wp_idea[tools][<?php echo $section_id; ?>][<?php echo $field_id; ?>]"
               value="<?php echo $value; ?>">
        <input type="button" class="button wp_idea-browse wp_idea-[<?php echo $section_id; ?>]-browse"
               value="<?php _e('Choose file', BPMJ_EDDCM_DOMAIN); ?>">
        <?php
    }

    public function display_field_select_type($section_id, $field_id, $settings)
    {
        $is_multiple = isset($settings['multiple']) && $settings['multiple'] ? true : false;

        $value = '';
        if (isset($_POST['wp_idea']['tools'][$section_id][$field_id]) && is_array($_POST['wp_idea']['tools'][$section_id][$field_id])) {
            $value = array();
            foreach ($_POST['wp_idea']['tools'][$section_id][$field_id] as $val)
                $value[] = sanitize_key($val);
        } else if (isset($_POST['wp_idea']['tools'][$section_id][$field_id])) {
            $value = $_POST['wp_idea']['tools'][$section_id][$field_id] ? sanitize_key($_POST['wp_idea']['tools'][$section_id][$field_id]) : '';
        }

        ?>
        <select name="wp_idea[tools][<?php echo $section_id; ?>][<?php echo $field_id; ?>]<?php echo $is_multiple ? '[]' : ''; ?>"<?php echo $is_multiple ? ' multiple' : ''; ?>>
            <?php if (isset($settings['options'])) : ?>
                <?php foreach ($settings['options'] as $option_key => $option) : ?>
                    <?php
                    $is_selected = false;
                    if (is_array($value) && in_array($option_key, $value))
                        $is_selected = true;
                    ?>
                    <option value="<?php echo $option_key; ?>"<?php echo $is_selected ? ' selected' : ''; ?>><?php echo $option; ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        <?php
    }

    public function display_field_checkbox_type($section_id, $field_id, $settings)
    {
        $value = isset($_POST['wp_idea']['tools'][$section_id][$field_id]) ? $_POST['wp_idea']['tools'][$section_id][$field_id] : '';
        $is_checked = false;
        if (isset($_POST['wp_idea']['tools'][$section_id][$field_id]))
            $is_checked = true;
        ?>
        <input type="checkbox"
               name="wp_idea[tools][<?php echo $section_id; ?>][<?php echo $field_id; ?>]"<?php echo $is_checked ? ' checked' : ''; ?>>
        <?php
    }
}
