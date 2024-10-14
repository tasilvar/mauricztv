<?php

namespace bpmj\wpidea\admin\settings\core\configuration;

use bpmj\wpidea\admin\settings\core\collections\{
    Additional_Fields_Collection,
    Fields_Collection
};
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\Setting_Field_Validation_Result;
use bpmj\wpidea\admin\subscription\models\Subscription_Const;
use bpmj\wpidea\Caps;
use bpmj\wpidea\admin\settings\core\entities\fields\{Message,
    Text_Setting_Field,
    Toggle_Setting_Field,
    Checkbox_Setting_Field,
    Select_Setting_Field,
    Text_Area_Setting_Field,
    Wysiwyg_Setting_Field,
    Media_Setting_Field,
    Configure_Popup_Setting_Field};

class Analytics_Settings_Group extends Abstract_Settings_Group
{
    public const GA4_ID = 'ga4_id';
    public const ENABLE_GA4_DEBUG_VIEW = 'ga4_debug_view';
    public const GA_ID = 'ga_id';
    public const GTM_ID = 'gtm_id';
    public const PIXEL_FB_ID = 'pixel_fb_id';
    public const FB_ACCESS_TOKEN = 'fb_access_token';
    public const BEFORE_END_HEAD_SWITCH = 'before_end_head_switch';
    public const BEFORE_END_HEAD = 'before_end_head';
    public const AFTER_BEGIN_BODY_SWITCH = 'after_begin_body_switch';
    public const AFTER_BEGIN_BODY = 'after_begin_body';
    public const BEFORE_END_BODY_SWITCH = 'before_end_body_switch';
    public const BEFORE_END_BODY = 'before_end_body';
    public const PIXEL_META_POPUP = 'pixel_meta_popup';

    public function get_name(): string
    {
        return 'analytics';
    }

    public function register_fields(): void
    {
        $this->add_fieldset(
            $this->translator->translate('settings.sections.analytics.fieldset.google'),
            (new Fields_Collection())
                ->add($this->get_ga4_id_field())
                ->add($this->get_enable_debug_view_ga4_field())
                ->add($this->get_ga_id_field())
                ->add($this->get_gtm_id_field())
        );

        $this->add_fieldset(
            $this->translator->translate('settings.sections.analytics.fieldset.facebook'),
            (new Fields_Collection())
                ->add($this->get_fb_configuration_popup())
        );

        $this->add_fieldset(
            $this->translator->translate('settings.sections.analytics.fieldset.additional_scripts'),
            (new Fields_Collection())
                ->add($this->get_before_end_head_field())
                ->add($this->get_after_begin_body_field())
                ->add($this->get_before_end_body_field())
        );
    }

    private function get_ga4_id_field(): Text_Setting_Field
    {
        return new Text_Setting_Field(
            self::GA4_ID,
            $this->translator->translate('settings.sections.analytics.ga4_id'),
            $this->translator->translate('settings.sections.analytics.ga4_id.desc'),
            $this->translator->translate('settings.sections.analytics.ga4_id.tooltip')
        );
    }

    private function get_enable_debug_view_ga4_field(): Toggle_Setting_Field
    {
        return new Toggle_Setting_Field(
            self::ENABLE_GA4_DEBUG_VIEW,
            $this->translator->translate('settings.sections.analytics.enable_debug_view_ga4')
        );
    }

    private function get_ga_id_field(): Text_Setting_Field
    {
        return (new Text_Setting_Field(
            self::GA_ID,
            $this->translator->translate('settings.sections.analytics.ga_id'),
            $this->translator->translate('settings.sections.analytics.ga_id.desc'),
            $this->translator->translate('settings.sections.analytics.ga_id.tooltip')
        ))->change_visibility($this->ga_id_exists());
    }

    private function get_gtm_id_field(): Text_Setting_Field
    {
        return new Text_Setting_Field(
            self::GTM_ID,
            $this->translator->translate('settings.sections.analytics.gtm_id'),
            $this->translator->translate('settings.sections.analytics.gtm_id.desc'),
            $this->translator->translate('settings.sections.analytics.gtm_id.tooltip')
        );
    }

    private function get_pixel_fb_id_field(): Text_Setting_Field
    {
        return new Text_Setting_Field(
            self::PIXEL_FB_ID,
            $this->translator->translate('settings.sections.analytics.pixel_fb_id'),
            $this->translator->translate('settings.sections.analytics.pixel_fb_id.desc'),
            $this->translator->translate('settings.sections.analytics.pixel_fb_id.tooltip')
        );
    }

    private function get_before_end_head_field(): Configure_Popup_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::BEFORE_END_HEAD_SWITCH,
            $this->translator->translate('settings.sections.analytics.before_end_head'),
            $this->translator->translate('settings.sections.analytics.before_end_head.desc'),
            $this->translator->translate('settings.sections.analytics.before_end_head.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_before_end_head_additional_field())
        ))->set_popup($this->settings_popup, $this->translator->translate('settings.sections.analytics.before_end_head.popup.title'));
    }

    private function get_before_end_head_additional_field(): Text_Area_Setting_Field
    {
        return (new Text_Area_Setting_Field(
            self::BEFORE_END_HEAD,
            $this->translator->translate('settings.sections.analytics.before_end_head_additional'),
            $this->translator->translate('settings.sections.analytics.before_end_head_additional.desc'),
            $this->translator->translate('settings.sections.analytics.before_end_head_additional.tooltip')
        ))->set_use_raw_value(true);
    }

    private function get_after_begin_body_field(): Configure_Popup_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::AFTER_BEGIN_BODY_SWITCH,
            $this->translator->translate('settings.sections.analytics.after_begin_body'),
            $this->translator->translate('settings.sections.analytics.after_begin_body.desc'),
            $this->translator->translate('settings.sections.analytics.after_begin_body.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_after_begin_body_additional_field())
        ))->set_popup($this->settings_popup, $this->translator->translate('settings.sections.analytics.after_begin_body.popup.title'));
    }

    private function get_after_begin_body_additional_field(): Text_Area_Setting_Field
    {
        return (new Text_Area_Setting_Field(
            self::AFTER_BEGIN_BODY,
            $this->translator->translate('settings.sections.analytics.after_begin_body_additional'),
            $this->translator->translate('settings.sections.analytics.after_begin_body_additional.desc'),
            $this->translator->translate('settings.sections.analytics.after_begin_body_additional.tooltip')
        ))->set_use_raw_value(true);
    }

    private function get_before_end_body_field(): Configure_Popup_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::BEFORE_END_BODY_SWITCH,
            $this->translator->translate('settings.sections.analytics.before_end_body'),
            $this->translator->translate('settings.sections.analytics.before_end_body.desc'),
            $this->translator->translate('settings.sections.analytics.before_end_body.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_before_end_body_additional_field())
        ))->set_popup($this->settings_popup, $this->translator->translate('settings.sections.analytics.before_end_body.popup.title'));
    }

    private function get_before_end_body_additional_field(): Text_Area_Setting_Field
    {
        return (new Text_Area_Setting_Field(
            self::BEFORE_END_BODY,
            $this->translator->translate('settings.sections.analytics.before_end_body_additional'),
            $this->translator->translate('settings.sections.analytics.before_end_body_additional.desc'),
            $this->translator->translate('settings.sections.analytics.before_end_body_additional.tooltip')
        ))->set_use_raw_value(true);
    }

    private function get_fb_configuration_popup(): Configure_Popup_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::PIXEL_META_POPUP,
            $this->translator->translate('settings.sections.analytics.pixel_meta'),
            $this->translator->translate('settings.sections.analytics.pixel_meta.desc'),
            $this->translator->translate('settings.sections.analytics.pixel_meta.tooltip'),
            (new Additional_Fields_Collection())
                ->add(new Message($this->translator->translate('settings.sections.analytics.pixel_meta.popup.additional_information')))
                ->add($this->get_pixel_fb_id_field())
                ->add($this->get_fb_access_token_field())
        ))->set_popup($this->settings_popup, $this->translator->translate('settings.sections.analytics.pixel_meta.popup.title'));
    }

    private function get_fb_access_token_field(): Text_Setting_Field
    {
        return new Text_Setting_Field(
            self::FB_ACCESS_TOKEN,
            $this->translator->translate('settings.sections.analytics.pixel_meta.access_token'),
            $this->translator->translate('settings.sections.analytics.pixel_meta.access_token.desc'),
            $this->translator->translate('settings.sections.analytics.pixel_meta.access_token.tooltip')
        );
    }

    private function ga_id_exists(): bool
    {
        return !empty($this->app_settings->get(self::GA_ID));
    }
}