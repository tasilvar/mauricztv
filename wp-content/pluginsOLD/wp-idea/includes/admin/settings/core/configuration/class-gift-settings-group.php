<?php

namespace bpmj\wpidea\admin\settings\core\configuration;

use bpmj\wpidea\admin\settings\core\collections\{Additional_Fields_Collection, Fields_Collection};
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\{Abstract_Setting_Field,
	Configure_Popup_Setting_Field,
	Media_Setting_Field,
	Number_And_Select_Field,
	Select_Setting_Field,
	Text_Area_Setting_Field,
	Toggle_Setting_Field,
	Wysiwyg_Setting_Field};
use bpmj\wpidea\admin\settings\core\entities\Setting_Field_Validation_Result;
use bpmj\wpidea\Packages;

class Gift_Settings_Group extends Abstract_Settings_Group
{
    public const NAME = 'gift';
    private const BUY_AS_GIFT_EXPIRATION_PERIOD = 'buy_as_gift_expiration_period';
    public const ENABLE_BUY_AS_GIFT = 'enable_buy_as_gift';
    public const BUY_AS_GIFT_EMAIL_BODY = 'buy_as_gift_email_body';
    public const ENABLE_GIFT_PDF_VOUCHER = 'enable_gift_pdf_voucher';
    public const VOUCHER_BG = 'voucher_bg';
    public const GIFT_PDF_VOUCHER_ORIENTATION = 'gift_pdf_voucher_orientation';
    public const PORTRAIT = 'portrait';
    public const LANDSCAPE = 'landscape';
    public const GIFT_PDF_VOUCHER_TEMPLATE = 'gift_pdf_voucher_template';
    public const VOUCHER_TEMPLATE_POPUP1 = 'voucher_template_popup_1';
    public const GIFT_PDF_VOUCHER_STYLES = 'gift_pdf_voucher_styles';
    public const VOUCHER_TEMPLATE_POPUP2 = 'voucher_template_popup_2';
    public const GIFT_EXPIRATION_POPUP = 'gift_expiration_popup';

    public function get_name(): string
    {
        return self::NAME;
    }

    public function register_fields(): void
    {
        $this->add_fieldset(
            $this->translator->translate('settings.sections.gifts.enable'),
            (new Fields_Collection())->add(
                (new Toggle_Setting_Field(
                    self::ENABLE_BUY_AS_GIFT,
                    $this->translator->translate('settings.sections.gifts.enable')
                ))->set_related_feature(Packages::FEAT_BUY_AS_GIFT)
            )
                ->add($this->get_popup_expiration_period_field())
                ->add($this->get_email_body_field())
        );

        $this->add_fieldset(
            $this->translator->translate('settings.sections.gifts.fieldset.voucher_as_pdf'),
            (new Fields_Collection())
                ->add(
                    (new Toggle_Setting_Field(
                        self::ENABLE_GIFT_PDF_VOUCHER,
                        $this->translator->translate('settings.sections.gifts.generate_pdf'),
                    ))->set_related_feature(Packages::FEAT_BUY_AS_GIFT)
                )
                ->add($this->get_voucher_template_field())
                ->add($this->get_voucher_css_field())
                ->add($this->get_pdf_orientation_field())
                ->add($this->voucher_background_field())
        );
    }

    private function get_email_body_field(): Configure_Popup_Setting_Field
    {
        $wysiwyg = new Wysiwyg_Setting_Field(
            self::BUY_AS_GIFT_EMAIL_BODY,
            $this->translator->translate('settings.sections.gifts.email_body')
        );
        $additional_fields = new Additional_Fields_Collection();
        $additional_fields->add($wysiwyg);
        $popup_field = new Configure_Popup_Setting_Field(
            'email_body_popup',
            $this->translator->translate('settings.sections.gifts.email_body'),
            null,
            null,
            $additional_fields
        );
        $popup_field->set_popup($this->settings_popup, $this->translator->translate('settings.sections.gifts.email_body'));
        $popup_field->set_related_feature(Packages::FEAT_BUY_AS_GIFT);
        return $popup_field;
    }

    private function voucher_background_field(): Media_Setting_Field
    {
        $field = new Media_Setting_Field(
            self::VOUCHER_BG,
            $this->translator->translate('settings.sections.gifts.voucher_bg')
        );
        $field->set_related_feature(Packages::FEAT_BUY_AS_GIFT);
        return $field;
    }

    private function get_pdf_orientation_field(): Select_Setting_Field
    {
        $field = new Select_Setting_Field(
            self::GIFT_PDF_VOUCHER_ORIENTATION,
            $this->translator->translate('settings.sections.gifts.voucher_orientation'),
            null,
            null,
            null,
            [
                self::PORTRAIT => $this->translator->translate('portrait'),
                self::LANDSCAPE => $this->translator->translate('landscape')
            ]
        );
        $field->set_related_feature(Packages::FEAT_BUY_AS_GIFT);
        return $field;
    }

    private function get_voucher_template_field(): Configure_Popup_Setting_Field
    {
        $additional_fields = new Fields_Collection();
        $additional_fields->add(
            new Wysiwyg_Setting_Field(
                self::GIFT_PDF_VOUCHER_TEMPLATE,
                $this->translator->translate('settings.sections.gifts.voucher_template'),
                $this->translator->translate('settings.sections.gifts.voucher_template.desc')
            )
        );
        $field = new Configure_Popup_Setting_Field(
            self::VOUCHER_TEMPLATE_POPUP1,
            $this->translator->translate('settings.sections.gifts.voucher_template'),
            null,
            null,
            $additional_fields
        );
        $field->set_popup(
            $this->settings_popup,
            $this->translator->translate('settings.sections.gifts.voucher_template')
        );
        $field->set_related_feature(Packages::FEAT_BUY_AS_GIFT);
        return $field;
    }

    private function get_voucher_css_field(): Configure_Popup_Setting_Field
    {
        $additional_fields = new Fields_Collection();
        $additional_fields->add(
            new Text_Area_Setting_Field(
                self::GIFT_PDF_VOUCHER_STYLES,
                $this->translator->translate('settings.sections.gifts.voucher_css'),
                $this->translator->translate('settings.sections.gifts.voucher_css.desc')
            )
        );
        $field = new Configure_Popup_Setting_Field(
            self::VOUCHER_TEMPLATE_POPUP2,
            $this->translator->translate('settings.sections.gifts.voucher_css'),
            null,
            null,
            $additional_fields
        );
        $field->set_popup(
            $this->settings_popup,
            $this->translator->translate('settings.sections.gifts.voucher_css'),
        );
        $field->set_related_feature(Packages::FEAT_BUY_AS_GIFT);
        return $field;
    }

    private function get_popup_expiration_period_field(): Configure_Popup_Setting_Field
    {
        $additional_field = new Additional_Fields_Collection();
        $additional_field->add(
            $this->get_buy_as_gift_expiration_period_field()
        );

        $popup_field = new Configure_Popup_Setting_Field(
            self::GIFT_EXPIRATION_POPUP,
            $this->translator->translate('settings.sections.gifts.expiration'),
            null,
            null,
            $additional_field
        );
        $popup_field->set_popup(
            $this->settings_popup,
            $this->translator->translate('settings.sections.gifts.expiration')
        );

        $popup_field->set_related_feature(Packages::FEAT_BUY_AS_GIFT);

        return $popup_field;
    }

    private function get_buy_as_gift_expiration_period_field(): Abstract_Setting_Field
    {
        $field = new Number_And_Select_Field(
            self::BUY_AS_GIFT_EXPIRATION_PERIOD,
            $this->translator->translate('settings.sections.gifts.period'),
            null,
            null,
            $this->get_buy_as_gift_expiration_period_unit_options()
        );
        $field->set_sanitize_callback(function ($value) {
            return trim($value);
        });
        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();

            if (!filter_var((int)$value, FILTER_VALIDATE_INT) || $value < 0) {
                $results->add_error_message('settings.sections.gifts.period.validation');
            }
            return $results;
        });

        return $field;
    }

    private function get_buy_as_gift_expiration_period_unit_options(): array
    {
        return [
            'hours' => $this->translator->translate('hours'),
            'days' => $this->translator->translate('days'),
            'weeks' => $this->translator->translate('weeks'),
            'months' => $this->translator->translate('months'),
            'years' => $this->translator->translate('years')
        ];
    }


}