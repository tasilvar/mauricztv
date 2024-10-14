<?php

namespace bpmj\wpidea\templates_system\groups\settings;

use ArrayObject;
use bpmj\wpidea\admin\helpers\wp\WP_Pages_Query_Helper;
use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\templates_system\admin\ajax\Group_Settings_Ajax_Handler;
use bpmj\wpidea\templates_system\groups\settings\fields\Template_Group_Settings_Field;
use bpmj\wpidea\templates_system\groups\settings\fields\Template_Group_Settings_Field_Checkbox;
use bpmj\wpidea\templates_system\groups\settings\fields\Template_Group_Settings_Field_File;
use bpmj\wpidea\templates_system\groups\settings\fields\Template_Group_Settings_Field_Font_Select;
use bpmj\wpidea\templates_system\groups\settings\fields\Template_Group_Settings_Field_Select;
use bpmj\wpidea\templates_system\groups\settings\fields\Template_Group_Settings_Field_Textarea;
use bpmj\wpidea\templates_system\groups\Template_Group;
use InvalidArgumentException;

class Template_Group_Settings_Fields extends ArrayObject
{
    private const COURSES_SHORTCODE_NAME = 'courses';
    private const USER_ACCOUNT_SHORTCODE_NAME = 'edd_profile_editor';

    public static function default_fields_array(string $legacy_base_template): array
    {
        return [
            self::create_main_font_field($legacy_base_template),
            self::create_secondary_font_field($legacy_base_template),
            self::create_background_image_field($legacy_base_template),
            self::create_login_background_image_field($legacy_base_template),
            self::create_section_background_image_field($legacy_base_template),
            self::create_custom_css_field($legacy_base_template),
            self::create_override_all_field($legacy_base_template),
            self::create_courses_page_field($legacy_base_template),
            self::create_user_account_page_field($legacy_base_template)
        ];
    }

    private static function create_main_font_field(string $legacy_base_template): Template_Group_Settings_Field_Font_Select
    {
        $field = new Template_Group_Settings_Field_Font_Select(
            Template_Group_Settings::OPTION_MAIN_FONT,
            __('Main font', BPMJ_EDDCM_DOMAIN)
        );

        return $field
            ->set_ajax_get_options_action(Group_Settings_Ajax_Handler::AJAX_ACTION_GET_GOOGLE_FONTS_LIST)
            ->set_corresponding_legacy_template_field('main_font', $legacy_base_template)
            ->set_default('open-sans');
    }

    private static function create_secondary_font_field(string $legacy_base_template): Template_Group_Settings_Field_Font_Select
    {
        $field = new Template_Group_Settings_Field_Font_Select(
            Template_Group_Settings::OPTION_SECONDARY_FONT,
            __('Secondary font', BPMJ_EDDCM_DOMAIN)
        );

        return $field
            ->set_ajax_get_options_action(Group_Settings_Ajax_Handler::AJAX_ACTION_GET_GOOGLE_FONTS_LIST)
            ->set_corresponding_legacy_template_field('secondary_font', $legacy_base_template)
            ->set_default($legacy_base_template === Template_Group::BASE_TEMPLATE_CLASSIC ? 'hind' : 'montserrat');
    }

    private static function create_background_image_field(string $legacy_base_template): Template_Group_Settings_Field_File
    {
        $field = new Template_Group_Settings_Field_File(
            Template_Group_Settings::OPTION_BG_FILE,
            __( 'Background image', BPMJ_EDDCM_DOMAIN )
        );

        return $field
            ->set_corresponding_legacy_template_field('bg_file', $legacy_base_template);
    }

    private static function create_login_background_image_field(string $legacy_base_template): Template_Group_Settings_Field_File
    {
        $field = new Template_Group_Settings_Field_File(
            Template_Group_Settings::OPTION_LOGIN_BG_FILE,
            __( 'Login page background image', BPMJ_EDDCM_DOMAIN )
        );

        return $field
            ->set_corresponding_legacy_template_field('login_bg_file', $legacy_base_template);
    }

    private static function create_section_background_image_field(string $legacy_base_template): Template_Group_Settings_Field_File
    {
        $field = new Template_Group_Settings_Field_File(
            Template_Group_Settings::OPTION_SECTION_BG_FILE,
            __( 'Section background image', BPMJ_EDDCM_DOMAIN )
        );

        return $field
            ->set_corresponding_legacy_template_field('section_bg_file', $legacy_base_template);
    }

    private static function create_custom_css_field(string $legacy_base_template): Template_Group_Settings_Field_Textarea
    {
        $field = new Template_Group_Settings_Field_Textarea(
            Template_Group_Settings::OPTION_CUSTOM_CSS,
            __( 'Custom CSS', BPMJ_EDDCM_DOMAIN )
        );

        return $field
            ->set_corresponding_legacy_template_field('css', $legacy_base_template);
    }

    private static function create_override_all_field(string $legacy_base_template): Template_Group_Settings_Field_Checkbox
    {
        $field = new Template_Group_Settings_Field_Checkbox(
            Template_Group_Settings::OPTION_OVERRIDE_ALL,
            __( 'Override mode', BPMJ_EDDCM_DOMAIN )
        );

        return $field
            ->set_default(Template_Group_Settings_Field_Checkbox::VALUE_ON)
            ->set_corresponding_legacy_template_field('override_all', $legacy_base_template);
    }

    private static function create_courses_page_field(string $legacy_base_template): Template_Group_Settings_Field_Select
    {
        $field = new Template_Group_Settings_Field_Select(
            Template_Group_Settings::OPTION_COURSES_PAGE,
            Translator_Static_Helper::translate('templates_system.scarlet.settings.products_list_page')
        );

        $options = [null] + WP_Pages_Query_Helper::get_all_pages();

        $field
            ->set_corresponding_legacy_template_field('course_list_page', $legacy_base_template)
            ->set_options($options);


        $page_with_shortcode_id = WP_Pages_Query_Helper::get_id_of_first_page_containing_shortcode(self::COURSES_SHORTCODE_NAME);

        if($page_with_shortcode_id !== null) {
            $field->set_default($page_with_shortcode_id);
        }

        return $field;
    }

    private static function create_user_account_page_field(string $legacy_base_template): Template_Group_Settings_Field_Select
    {
        $field = new Template_Group_Settings_Field_Select(
            Template_Group_Settings::OPTION_USER_ACCOUNT_PAGE,
            __('User account page', BPMJ_EDDCM_DOMAIN)
        );

        $options = [null] + WP_Pages_Query_Helper::get_all_pages();

        $field
            ->set_corresponding_legacy_template_field('profile_editor_page', $legacy_base_template)
            ->set_options($options);


        $page_with_shortcode_id = WP_Pages_Query_Helper::get_id_of_first_page_containing_shortcode(self::USER_ACCOUNT_SHORTCODE_NAME);

        if($page_with_shortcode_id !== null) {
            $field->set_default($page_with_shortcode_id);
        }

        return $field;
    }

    public function offsetSet($index, $newval)
    {
        if (!($newval instanceof Template_Group_Settings_Field)) {
            throw new InvalidArgumentException("Element must be an instance of the Template_Group_Settings_Field class");
        }

        parent::offsetSet($index, $newval);
    }

    public function find_by_name(string $option_name): ?Template_Group_Settings_Field
    {
        $matches = array_filter($this->getArrayCopy(), static function($item) use ($option_name){
            /** @var Template_Group_Settings_Field $item */
            return $item->get_name() === $option_name;
        });

        if(empty($matches)) {
            return null;
        }

        return reset($matches);
    }
}