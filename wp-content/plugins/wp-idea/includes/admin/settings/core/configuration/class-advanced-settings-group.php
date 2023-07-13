<?php

namespace bpmj\wpidea\admin\settings\core\configuration;

use bpmj\wpidea\admin\settings\core\collections\{Additional_Fields_Collection, Fields_Collection};
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\{Checkbox_Setting_Field,
    Configure_Popup_Setting_Field,
    Number_Setting_Field,
    Select_Setting_Field,
    Text_Setting_Field,
    Toggle_Setting_Field,
    Wysiwyg_Setting_Field};
use bpmj\wpidea\admin\settings\core\entities\Setting_Field_Validation_Result;
use bpmj\wpidea\admin\settings\infrastructure\persistence\storage_place\Default_Storage_Places_Registry;
use bpmj\wpidea\Packages;

class Advanced_Settings_Group extends Abstract_Settings_Group
{
    private const ALLOW_INLINE_FILE_DOWNLOAD = 'allow_inline_file_download';
    public const ENABLE_LOGO_IN_COURSE_TO_HOME_PAGE = 'enable_logo_in_courses_to_home_page';
    public const ENABLE_ACTIVE_SESSIONS_LIMITER = 'enable_active_sessions_limiter';
    private const MAX_ACTIVE_SESSION_NUMBER = 'max_active_sessions_number';
    public const ENABLE_PAYMENT_REMINDERS = 'enable_payment_reminders';
    public const PAYMENT_REMINDERS_NUMBER_DAYS = 'payment_reminders_number_days';
    public const PAYMENT_REMINDERS_MESSAGE_SUBJECT = 'payment_reminders_message_subject';
    public const PAYMENT_REMINDERS_MESSAGE_CONTENT = 'payment_reminders_message_content';
    public const ENABLE_SELL_DISCOUNTS = 'enable_sell_discount';
    private const PURCHASE_LIMIT_BEHAVIOUR = 'purchase_limit_behaviour';
    private const QUIZ_SETTINGS = 'quiz_settings';
    private const RIGHT_CLICK_BLOCKING_QUIZ = 'right_click_blocking_quiz';
    private Default_Storage_Places_Registry $default_storage_places_registry;

    public function __construct(
        Default_Storage_Places_Registry $default_storage_places_registry
    )
    {
        $this->default_storage_places_registry = $default_storage_places_registry;
    }

    public function get_name(): string
    {
        return 'advanced';
    }

    public function register_fields(): void
    {
        $this->add_fieldset(
            $this->translator->translate('settings.sections.advanced.fieldset'),
            (new Fields_Collection())
                ->add($this->get_inline_file_download_field())
                ->add($this->get_enable_logo_in_courses_to_home_page_field())
                ->add($this->get_enable_active_session_limiter_field())
                ->add($this->get_enable_payment_reminders_field())
                ->add($this->get_quiz_settings_field())
                ->add($this->get_enable_sell_discounts_field())
                ->add($this->get_purchase_limit_behaviour_field())
        );
    }

    private function get_inline_file_download_field(): Select_Setting_Field
    {
        $options = [
            'inline' => $this->translator->translate('settings.sections.advanced.allow_inline_file_download.option.inline'),
            'attachment' => $this->translator->translate('settings.sections.advanced.allow_inline_file_download.option.attachment'),
        ];

        return new Select_Setting_Field(
            self::ALLOW_INLINE_FILE_DOWNLOAD,
            $this->translator->translate('settings.sections.advanced.allow_inline_file_download'),
            $this->translator->translate('settings.sections.advanced.allow_inline_file_download.desc'),
            $this->translator->translate('settings.sections.advanced.allow_inline_file_download.tooltip'),
            null,
            $options
        );
    }

    private function get_enable_logo_in_courses_to_home_page_field(): Toggle_Setting_Field
    {
        return new Toggle_Setting_Field(
            self::ENABLE_LOGO_IN_COURSE_TO_HOME_PAGE,
            $this->translator->translate('settings.sections.advanced.enable_logo_in_courses_to_home_page'),
            $this->translator->translate('settings.sections.advanced.enable_logo_in_courses_to_home_page.desc'),
            $this->translator->translate('settings.sections.advanced.enable_logo_in_courses_to_home_page.tooltip')
        );
    }

    private function get_enable_active_session_limiter_field(): Toggle_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::ENABLE_ACTIVE_SESSIONS_LIMITER,
            $this->translator->translate('settings.sections.advanced.enable_active_sessions_limiter'),
            $this->translator->translate('settings.sections.advanced.enable_active_sessions_limiter.desc'),
            $this->translator->translate('settings.sections.advanced.enable_active_sessions_limiter.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_enable_active_session_count_additional_field())
        ))
            ->set_popup($this->settings_popup,
                $this->translator->translate('settings.sections.advanced.enable_active_sessions_limiter.popup.title'))
            ->set_related_feature(Packages::FEAT_ACTIVE_SESSIONS_LIMITER);
    }

    private function get_enable_active_session_count_additional_field(): Number_Setting_Field
    {
        $field = new Number_Setting_Field(
            self::MAX_ACTIVE_SESSION_NUMBER,
            $this->translator->translate('settings.sections.advanced.enable_active_sessions_limiter.max_active_sessions_number'),
            $this->translator->translate('settings.sections.advanced.enable_active_sessions_limiter.max_active_sessions_number.desc'),
            $this->translator->translate('settings.sections.advanced.enable_active_sessions_limiter.max_active_sessions_number.tooltip'),
            null,
            1
        );
        $field->set_sanitize_callback(function ($value) {
            return trim($value);
        });
        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();
            if (!filter_var($value, FILTER_VALIDATE_INT) || $value <= 0) {
                $results->add_error_message('settings.field.validation.must_be_int');
            }
            return $results;
        });
        return $field;
    }

    private function get_enable_payment_reminders_field(): Toggle_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::ENABLE_PAYMENT_REMINDERS,
            $this->translator->translate('settings.sections.advanced.enable_payment_reminders'),
            $this->translator->translate('settings.sections.advanced.enable_payment_reminders.desc'),
            $this->translator->translate('settings.sections.advanced.enable_payment_reminders.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_payment_reminders_count_additional_field())
                ->add($this->get_payment_reminders_message_subject_additional_field())
                ->add($this->get_payment_reminders_message_content_additional_field())
        ))
            ->set_popup($this->settings_popup,
               $this->translator->translate('settings.sections.advanced.enable_payment_reminders.popup.title'))
            ->set_related_feature(Packages::FEAT_PAYMENT_REMINDERS)
            ->set_storage_place($this->default_storage_places_registry->get_payment_reminders_module_storage_place());
    }

    private function get_payment_reminders_count_additional_field(): Number_Setting_Field
    {
        $field = new Number_Setting_Field(
            self::PAYMENT_REMINDERS_NUMBER_DAYS,
            $this->translator->translate('settings.sections.advanced.enable_payment_reminders.payment_reminders_number_days'),
            $this->translator->translate('settings.sections.advanced.enable_payment_reminders.payment_reminders_number_days.desc'),
            $this->translator->translate('settings.sections.advanced.enable_payment_reminders.payment_reminders_number_days.tooltip'),
            null,
            1
        );
        $field->set_sanitize_callback(function ($value) {
            return trim($value);
        });
        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();
            if (!filter_var($value, FILTER_VALIDATE_INT) || $value <= 0) {
                $results->add_error_message('settings.field.validation.must_be_int');
            }
            return $results;
        });

        $field->set_storage_place($this->default_storage_places_registry->get_payment_reminders_module_storage_place());

        return $field;
    }

    private function get_payment_reminders_message_subject_additional_field(): Text_Setting_Field
    {
        return (new Text_Setting_Field(
            self::PAYMENT_REMINDERS_MESSAGE_SUBJECT,
            $this->translator->translate('settings.sections.advanced.enable_payment_reminders.payment_reminders_message_subject'),
            $this->translator->translate('settings.sections.advanced.enable_payment_reminders.payment_reminders_message_subject.desc'),
            $this->translator->translate('settings.sections.advanced.enable_payment_reminders.payment_reminders_message_subject.tooltip'),
            null,
            $this->translator->translate('settings.sections.advanced.enable_payment_reminders.payment_reminders_message_subject.value')
        ))->set_storage_place($this->default_storage_places_registry->get_payment_reminders_module_storage_place());

    }

    private function get_payment_reminders_message_content_additional_field(): Wysiwyg_Setting_Field
    {
        return (new Wysiwyg_Setting_Field(
            self::PAYMENT_REMINDERS_MESSAGE_CONTENT,
            $this->translator->translate('settings.sections.advanced.enable_payment_reminders.payment_reminders_message_content'),
            $this->translator->translate('settings.sections.advanced.enable_payment_reminders.payment_reminders_message_content.desc'),
            $this->translator->translate('settings.sections.advanced.enable_payment_reminders.payment_reminders_message_content.tooltip'),
        ))->set_storage_place($this->default_storage_places_registry->get_payment_reminders_module_storage_place());
    }

    private function get_quiz_settings_field(): Configure_Popup_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::QUIZ_SETTINGS,
            $this->translator->translate('settings.sections.advanced.quiz_settings'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_right_click_blocking_quiz_field())
        ))->set_popup($this->settings_popup, $this->translator->translate('settings.sections.advanced.quiz_settings'));
    }

    private function get_right_click_blocking_quiz_field(): Checkbox_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::RIGHT_CLICK_BLOCKING_QUIZ,
            $this->translator->translate('settings.sections.advanced.right_click_blocking_quiz')
        );
    }

    private function get_enable_sell_discounts_field(): Toggle_Setting_Field
    {
        return new Toggle_Setting_Field(
            self::ENABLE_SELL_DISCOUNTS,
            $this->translator->translate('settings.sections.advanced.enable_sell_discounts'),
            $this->translator->translate('settings.sections.advanced.enable_sell_discounts.desc'),
            $this->translator->translate('settings.sections.advanced.enable_sell_discounts.tooltip')
        );
    }

    private function get_purchase_limit_behaviour_field(): Select_Setting_Field
    {
        $options = [
            'BEGIN_PAYMENT' => $this->translator->translate('settings.sections.advanced.purchase_limit_behaviour.option.begin_payment'),
            'COMPLETE_PAYMENT' => $this->translator->translate('settings.sections.advanced.purchase_limit_behaviour.option.complete_payment'),
        ];

        return new Select_Setting_Field(
            self::PURCHASE_LIMIT_BEHAVIOUR,
            $this->translator->translate('settings.sections.advanced.purchase_limit_behaviour'),
            $this->translator->translate('settings.sections.advanced.purchase_limit_behaviour.desc'),
            $this->translator->translate('settings.sections.advanced.purchase_limit_behaviour.tooltip'),
            null,
            $options
        );
    }
}