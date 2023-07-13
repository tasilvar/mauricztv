<?php

namespace bpmj\wpidea\admin\settings\core\configuration;

use bpmj\wpidea\admin\settings\core\collections\{Additional_Fields_Collection, Fields_Collection};
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\{Abstract_Setting_Field,
	Configure_Popup_Setting_Field,
	Message,
	Number_Setting_Field,
	Paid_Content_Renewal_Table_Field,
	Section_Heading,
	Select_Setting_Field,
	Text_Setting_Field,
	Toggle_Setting_Field,
	Wysiwyg_Setting_Field};
use bpmj\wpidea\admin\settings\core\entities\Setting_Field_Validation_Result;
use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\infrastructure\system\System;
use bpmj\wpidea\Packages;

class Messages_Settings_Group extends Abstract_Settings_Group
{
    public const FROM_NAME = 'from_name';
    public const FROM_EMAIL = 'from_email';
    public const PURCHASE_SUBJECT = 'purchase_subject';
    public const PURCHASE_HEADING = 'purchase_heading';
    public const PURCHASE_RECEIPT_POPUP = 'purchase_receipt_popup';
    public const PURCHASE_RECEIPT = 'purchase_receipt';
    public const ARC_SUBJECT = 'bpmj_edd_arc_subject';
    public const ARC_CONTENT_POPUP = 'edd_arc_content_popup';
    public const ARC_CONTENT = 'bpmj_edd_arc_content';
    public const RENEWAL_DISCOUNT = 'bpmj_renewal_discount';
    public const RENEWAL_DISCOUNT_VALUE = 'bpmj_renewal_discount_value';
    public const RENEWAL_DISCOUNT_TYPE = 'bpmj_renewal_discount_type';
    public const RENEWAL_DISCOUNT_TIME = 'bpmj_renewal_discount_time';
    public const EXPIRED_ACCESS_REPORT_EMAIL = 'bpmj_expired_access_report_email';
    public const PAID_CONTENT_RENEWAL = 'paid_content_renewal';
    public const RENEWALS_START = 'bpmj_renewals_start';
    public const RENEWALS_END = 'bpmj_renewals_end';

    private System $system;
    private Subscription $subscription;

    public function __construct(
        System $system,
        Subscription $subscription
    )
    {
        $this->system = $system;
        $this->subscription = $subscription;
    }

    public function get_name(): string
    {
        return 'messages';
    }

    public function register_fields(): void
    {
        // External news

        $this->add_field(new Section_Heading(
            $this->translator->translate('settings.sections.messages.external_news')
        ));

        $messages_fieldset = new Fields_Collection();
        $messages_fieldset->add($this->get_messages_from_name_field());
        $this->add_sender_message_info_on_go($messages_fieldset);
        $messages_fieldset->add($this->get_messages_from_email_field());

        $this->add_fieldset(
            $this->translator->translate('settings.sections.messages.fieldset.sender'),
            $messages_fieldset
        );

        $this->add_fieldset(
            $this->translator->translate('settings.sections.messages.fieldset.message_after_purchase'),
            (new Fields_Collection())
                ->add($this->get_messages_purchase_subject_field())
                ->add($this->get_messages_purchase_heading_field())
                ->add($this->get_messages_purchase_receipt_field())
        );

        $this->add_fieldset(
            $this->translator->translate('settings.sections.messages.fieldset.message_after_creating_account'),
            (new Fields_Collection())
                ->add($this->get_messages_arc_subject_field())
                ->add($this->get_messages_arc_content_popup_field())
        );


        // Messages for subscription

        $this->add_field(new Section_Heading(
            $this->translator->translate('settings.sections.messages.messages_subscription')
        ));
        $this->add_fieldset(
            $this->translator->translate('settings.sections.messages.fieldset.discount_codes'),
            (new Fields_Collection())
                ->add($this->get_messages_renewal_discount_field())
                ->add($this->get_messages_renewal_discount_value_field())
                ->add($this->get_messages_renewal_discount_type_field())
                ->add($this->get_messages_renewal_discount_time_field())
        );
        $this->add_fieldset(
            $this->translator->translate('settings.sections.messages.fieldset.reports'),
            (new Fields_Collection())
                ->add($this->get_messages_expired_access_report_email_field())
        );
        $this->add_fieldset(
            $this->translator->translate('settings.sections.messages.fieldset.reminders'),
            (new Fields_Collection())
                ->add($this->get_messages_renewals_start_field())
                ->add($this->get_messages_renewals_end_field())
                ->add($this->get_messages_paid_content_renewal_field())
        );
    }

    private function add_sender_message_info_on_go(Fields_Collection $fieldset): void
    {
        if ($this->subscription->is_go()) {
            $fieldset->add(
                new Message($this->translator->translate('settings.sections.messages.sender_info'))
            );
        }
    }

    private function get_messages_from_name_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::FROM_NAME,
            $this->translator->translate('settings.sections.messages.sender.' . self::FROM_NAME),
            null,
            $this->translator->translate('settings.sections.messages.sender.' . self::FROM_NAME . '.tooltip')
        );
    }

    private function get_messages_from_email_field(): Abstract_Setting_Field
    {
        $field = new Text_Setting_Field(
            self::FROM_EMAIL,
            $this->translator->translate('settings.sections.messages.sender.' . self::FROM_EMAIL),
            null,
            $this->translator->translate('settings.sections.messages.sender.' . self::FROM_EMAIL . '.tooltip')
        );
        $field->set_sanitize_callback(function ($value) {
            return trim($value);
        });
        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $results->add_error_message('settings.field.validation.invalid_email');
            }
            return $results;
        });
        return $field;

    }

    private function get_messages_purchase_subject_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::PURCHASE_SUBJECT,
            $this->translator->translate('settings.sections.messages.message_after_purchase.' . self::PURCHASE_SUBJECT),
            null,
            $this->translator->translate('settings.sections.messages.message_after_purchase.' . self::PURCHASE_SUBJECT . '.tooltip')
        );
    }

    private function get_messages_purchase_heading_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::PURCHASE_HEADING,
            $this->translator->translate('settings.sections.messages.message_after_purchase.' . self::PURCHASE_HEADING),
            null,
            $this->translator->translate('settings.sections.messages.message_after_purchase.' . self::PURCHASE_HEADING . '.tooltip')
        );
    }

    private function get_messages_purchase_receipt_field(): Configure_Popup_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::PURCHASE_RECEIPT_POPUP,
            $this->translator->translate('settings.sections.messages.message_after_purchase.' . self::PURCHASE_RECEIPT_POPUP),
            null,
            $this->translator->translate('settings.sections.messages.message_after_purchase.' . self::PURCHASE_RECEIPT_POPUP . '.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_messages_purchase_receipt_additional_field())
        ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.messages.message_after_purchase.' . self::PURCHASE_RECEIPT_POPUP));
    }

    private function get_messages_purchase_receipt_additional_field(): Abstract_Setting_Field
    {
        return new Wysiwyg_Setting_Field(
            self::PURCHASE_RECEIPT,
            $this->translator->translate('settings.sections.messages.message_after_purchase.' . self::PURCHASE_RECEIPT),
            $this->translator->translate('settings.sections.messages.message_after_purchase.' . self::PURCHASE_RECEIPT . '.desc'),
            $this->translator->translate('settings.sections.messages.message_after_purchase.' . self::PURCHASE_RECEIPT . '.tooltip')
        );
    }

    private function get_messages_arc_subject_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::ARC_SUBJECT,
            $this->translator->translate('settings.sections.messages.message_after_creating_account.' . self::ARC_SUBJECT),
            null,
            $this->translator->translate('settings.sections.messages.message_after_creating_account.' . self::ARC_SUBJECT . '.tooltip')
        );
    }

    private function get_messages_arc_content_popup_field(): Configure_Popup_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::ARC_CONTENT_POPUP,
            $this->translator->translate('settings.sections.messages.message_after_creating_account.' . self::ARC_CONTENT_POPUP),
            null,
            $this->translator->translate('settings.sections.messages.message_after_creating_account.' . self::ARC_CONTENT_POPUP . '.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_messages_arc_content_additional_field())
        ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.messages.message_after_creating_account.' . self::ARC_CONTENT_POPUP));
    }

    private function get_messages_arc_content_additional_field(): Abstract_Setting_Field
    {
        return new Wysiwyg_Setting_Field(
            self::ARC_CONTENT,
            $this->translator->translate('settings.sections.messages.message_after_creating_account.' . self::ARC_CONTENT),
       		$this->translator->translate('settings.sections.messages.message_after_creating_account.' . self::ARC_CONTENT . '.desc'),
            $this->translator->translate('settings.sections.messages.message_after_creating_account.' . self::ARC_CONTENT . '.tooltip')
        );
    }

    private function get_messages_renewal_discount_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::RENEWAL_DISCOUNT,
            $this->translator->translate('settings.sections.messages.discount_codes.' . self::RENEWAL_DISCOUNT),
            null,
            $this->translator->translate('settings.sections.messages.discount_codes.' . self::RENEWAL_DISCOUNT . '.tooltip')
        ))->set_related_feature(Packages::FEAT_SUBSCRIPTIONS);
    }

    private function get_messages_renewal_discount_value_field(): Abstract_Setting_Field
    {
        return (new Number_Setting_Field(
            self::RENEWAL_DISCOUNT_VALUE,
            $this->translator->translate('settings.sections.messages.discount_codes.' . self::RENEWAL_DISCOUNT_VALUE),
            null,
            $this->translator->translate('settings.sections.messages.discount_codes.' . self::RENEWAL_DISCOUNT_VALUE . '.tooltip')
        ))->set_related_feature(Packages::FEAT_SUBSCRIPTIONS);
    }

    private function get_messages_renewal_discount_type_field(): Abstract_Setting_Field
    {
        $options = [
             'percent' => '%',
             'flat' => $this->system->get_system_currency()
        ];

        return (new Select_Setting_Field(
            self::RENEWAL_DISCOUNT_TYPE,
            $this->translator->translate('settings.sections.messages.discount_codes.' . self::RENEWAL_DISCOUNT_TYPE),
            null,
            $this->translator->translate('settings.sections.messages.discount_codes.' . self::RENEWAL_DISCOUNT_TYPE . '.tooltip'),
            null,
            $options
        ))->set_related_feature(Packages::FEAT_SUBSCRIPTIONS);;
    }

    private function get_messages_renewal_discount_time_field(): Abstract_Setting_Field
    {
        $options = [
            '+1day' => $this->translator->translate('settings.sections.messages.discount_codes.' . self::RENEWAL_DISCOUNT_TIME . '.one_day'),
            '+2days' => $this->translator->translate('settings.sections.messages.discount_codes.' . self::RENEWAL_DISCOUNT_TIME . '.two_days'),
            '+3days' => $this->translator->translate('settings.sections.messages.discount_codes.' . self::RENEWAL_DISCOUNT_TIME . '.three_days'),
            '+5days' => $this->translator->translate('settings.sections.messages.discount_codes.' . self::RENEWAL_DISCOUNT_TIME . '.five_days'),
            '+1week' => $this->translator->translate('settings.sections.messages.discount_codes.' . self::RENEWAL_DISCOUNT_TIME . '.week'),
            '+2weeks' => $this->translator->translate('settings.sections.messages.discount_codes.' . self::RENEWAL_DISCOUNT_TIME . '.two_weeks'),
            '+1month' => $this->translator->translate('settings.sections.messages.discount_codes.' . self::RENEWAL_DISCOUNT_TIME . '.month'),
            'no-limit' => $this->translator->translate('settings.sections.messages.discount_codes.' . self::RENEWAL_DISCOUNT_TIME . '.no_limit'),
        ];

        return (new Select_Setting_Field(
            self::RENEWAL_DISCOUNT_TIME,
            $this->translator->translate('settings.sections.messages.discount_codes.' . self::RENEWAL_DISCOUNT_TIME),
            null,
            $this->translator->translate('settings.sections.messages.discount_codes.' . self::RENEWAL_DISCOUNT_TIME . '.tooltip'),
            null,
            $options
        ))->set_related_feature(Packages::FEAT_SUBSCRIPTIONS);;
    }

    private function get_messages_expired_access_report_email_field(): Text_Setting_Field
    {
        $field = new Text_Setting_Field(
            self::EXPIRED_ACCESS_REPORT_EMAIL,
            $this->translator->translate('settings.sections.messages.reports.' . self::EXPIRED_ACCESS_REPORT_EMAIL),
            null,
            $this->translator->translate('settings.sections.messages.reports.' . self::EXPIRED_ACCESS_REPORT_EMAIL . '.tooltip'),
        );
        $field->set_sanitize_callback(function ($value) {
            return trim($value);
        });
        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();
            if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $results->add_error_message('settings.field.validation.invalid_email');
            }
            return $results;
        });
        $field->set_related_feature(Packages::FEAT_SUBSCRIPTIONS);
        return $field;
    }

    private function get_messages_paid_content_renewal_field(): Abstract_Setting_Field
    {
        return (new Paid_Content_Renewal_Table_Field(
            self::PAID_CONTENT_RENEWAL,
            $this->translator->translate('settings.sections.messages.reminders.' . self::PAID_CONTENT_RENEWAL),
            null,
            $this->translator->translate('settings.sections.messages.reminders.' . self::PAID_CONTENT_RENEWAL . '.tooltip')
        ))->set_related_feature(Packages::FEAT_SUBSCRIPTIONS);;
    }

    private function get_messages_renewals_start_field(): Abstract_Setting_Field
    {
        $options = $this->generate_options_for_hours_select();

        $field = new Select_Setting_Field(
            self::RENEWALS_START,
            $this->translator->translate('settings.sections.messages.reminder_hours.' . self::RENEWALS_START),
            null,
            $this->translator->translate('settings.sections.messages.reminder_hours.' . self::RENEWALS_START . '.tooltip'),
            null,
            $options
        );
        $field->set_related_feature(Packages::FEAT_SUBSCRIPTIONS);

        $edd_settings = get_option('edd_settings');
        $end_value = $edd_settings[self::RENEWALS_END] ?? 19;
        $field->set_validation_callback(function ($value) use ($end_value) {
            $results = new Setting_Field_Validation_Result();

            if (($end_value - $value) < 5) {
                $results->add_error_message('settings.field.validation.difference_must_be_at_least_5_hours');
            }
            return $results;
        });

        return $field;
    }

    private function get_messages_renewals_end_field(): Abstract_Setting_Field
    {
        $options = $this->generate_options_for_hours_select();

        $field = new Select_Setting_Field(
            self::RENEWALS_END,
            $this->translator->translate('settings.sections.messages.reminder_hours.' . self::RENEWALS_END),
            null,
            $this->translator->translate('settings.sections.messages.reminder_hours.' . self::RENEWALS_END . '.tooltip'),
            null,
            $options
        );
        $field->set_related_feature(Packages::FEAT_SUBSCRIPTIONS);

        $edd_settings = get_option('edd_settings');
        $start_value = $edd_settings[self::RENEWALS_START] ?? 14;
        $field->set_validation_callback(function ($value) use ($start_value) {
            $results = new Setting_Field_Validation_Result();

            if (($value - $start_value) < 5) {
                $results->add_error_message('settings.field.validation.difference_must_be_at_least_5_hours');
            }
            return $results;
        });

        return $field;
    }

    private function generate_options_for_hours_select(): array
    {
        $options = [];

        for ($i = 0; $i <= 24; $i++) {
            $options[$i] = $i . ':00';
        }

        return $options;
    }

}