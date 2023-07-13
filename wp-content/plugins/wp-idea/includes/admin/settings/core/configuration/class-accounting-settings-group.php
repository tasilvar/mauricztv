<?php

namespace bpmj\wpidea\admin\settings\core\configuration;

use bpmj\wpidea\admin\settings\core\collections\{Additional_Fields_Collection, Fields_Collection};
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\{Select_Setting_Field, Text_Setting_Field, Toggle_Setting_Field};
use bpmj\wpidea\admin\settings\core\entities\Setting_Field_Validation_Result;
use bpmj\wpidea\Packages;

class Accounting_Settings_Group extends Abstract_Settings_Group
{
    public const CURRENCY = 'currency';
    public const THOUSANDS_SEPARATOR = 'thousands_separator';
    public const DECIMAL_SEPARATOR = 'decimal_separator';
    public const EDD_ID_FORCE = 'edd_id_force';
    public const EDD_ID_PERSON = 'edd_id_person';
    public const EDD_ID_DISABLE_TAX = 'edd_id_disable_taxid_verification';
    public const EDD_ID_ENABLE_VAT_MOSS = 'edd_id_enable_vat_moss';
    public const INVOICES_IS_VAT_PAYER = 'invoices_is_vat_payer';
    private const INVOICES = 'invoices';
    private const INVOICES_DEFAULT_VAT_RATE = 'invoices_default_vat_rate';
    private const NIP_FOR_RECEIPTS = 'nip_for_receipts';
    private const ENABLE_FLAT_RATE_TAX_SYMBOL = 'enable_flat_rate_tax_symbol';
    public const ENABLED_GUS_API = 'enable_gus_api';

    public function get_name(): string
    {
        return 'accounting';
    }

    public function register_fields(): void
    {
        $this->add_fieldset(
            $this->translator->translate('settings.sections.accounting.fieldset.currency'),
            (new Fields_Collection())
                ->add($this->get_currency_field())
                ->add($this->get_thousands_separator_field())
                ->add($this->get_decimal_separator_field())
        );

        $this->add_fieldset(
            $this->translator->translate('settings.sections.accounting.fieldset.invoicing'),
            (new Fields_Collection())
                ->add($this->get_enable_invoices_field())
                ->add($this->get_invoices_is_vat_payer_field())
                ->add($this->get_edd_id_force_field())
                ->add($this->get_edd_id_person_field())
                ->add($this->get_edd_id_disable_tax_id_verification_field())
                ->add($this->get_nip_for_receipts_field())
                ->add($this->get_edd_id_enable_vat_moss_field())
                ->add($this->get_enable_flat_rate_tax_symbol_field())
        );

        $this->add_fieldset(
            $this->translator->translate('settings.sections.accounting.fieldset.gus'),
            (new Fields_Collection())
                ->add($this->get_gus_field())
        );
    }

    private function get_currency_field(): Select_Setting_Field
    {
        return new Select_Setting_Field(
            self::CURRENCY,
            $this->translator->translate('settings.sections.accounting.currency'),
            null,
            $this->translator->translate('settings.sections.accounting.currency.tooltip'),
            null,
            edd_get_currencies()
        );
    }

    private function get_thousands_separator_field(): Select_Setting_Field
    {
        return new Select_Setting_Field(
            self::THOUSANDS_SEPARATOR,
            $this->translator->translate('settings.sections.accounting.thousands_separator'),
            null,
            $this->translator->translate('settings.sections.accounting.thousands_separator.tooltip'),
            null,
            $this->get_thousands_separator_options()
        );
    }

    private function get_decimal_separator_field(): Select_Setting_Field
    {
        return new Select_Setting_Field(
            self::DECIMAL_SEPARATOR,
            $this->translator->translate('settings.sections.accounting.decimal_separator'),
            null,
            $this->translator->translate('settings.sections.accounting.decimal_separator.tooltip'),
            null,
            $this->get_separator_options()
        );
    }

    private function get_enable_invoices_field(): Toggle_Setting_Field
    {
        return new Toggle_Setting_Field(
            self::INVOICES,
            $this->translator->translate('settings.sections.accounting.enable_invoices'),
            null,
            $this->translator->translate('settings.sections.accounting.enable_invoices.tooltip')
        );
    }

    private function get_invoices_is_vat_payer_field(): Toggle_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::INVOICES_IS_VAT_PAYER,
            $this->translator->translate('settings.sections.accounting.invoices_is_vat_payer'),
            null,
            $this->translator->translate('settings.sections.accounting.invoices_is_vat_payer.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_invoices_default_vat_rate_additional_field())
        ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.accounting.invoices_is_vat_payer.popup.title'));
    }

    private function get_invoices_default_vat_rate_additional_field(): Text_Setting_Field
    {
        $field = new Text_Setting_Field(
            self::INVOICES_DEFAULT_VAT_RATE,
            $this->translator->translate('settings.sections.accounting.invoices_is_vat_payer.popup.invoices_default_vat_rate'),
            $this->translator->translate('settings.sections.accounting.invoices_is_vat_payer.popup.invoices_default_vat_rate.desc')
        );
        $field->set_sanitize_callback(function ($value) {
            return trim($value);
        });
        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();
            
            if (filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 99]]) === false) {
                $results->add_error_message('settings.field.validation.invalid_default_vat_rate');
            }
            return $results;
        });
        return $field;
    }

    private function get_edd_id_force_field(): Toggle_Setting_Field
    {
        return new Toggle_Setting_Field(
            self::EDD_ID_FORCE,
            $this->translator->translate('settings.sections.accounting.edd_id_force'),
            null,
            $this->translator->translate('settings.sections.accounting.edd_id_force.tooltip')
        );
    }

    private function get_edd_id_person_field(): Toggle_Setting_Field
    {
        return new Toggle_Setting_Field(
            self::EDD_ID_PERSON,
            $this->translator->translate('settings.sections.accounting.edd_id_person'),
            null,
            $this->translator->translate('settings.sections.accounting.edd_id_person.tooltip')
        );
    }

    private function get_edd_id_disable_tax_id_verification_field(): Toggle_Setting_Field
    {
        return new Toggle_Setting_Field(
            self::EDD_ID_DISABLE_TAX,
            $this->translator->translate('settings.sections.accounting.edd_id_disable_tax_id_verification'),
            null,
            $this->translator->translate('settings.sections.accounting.edd_id_disable_tax_id_verification.tooltip')
        );
    }

    private function get_nip_for_receipts_field(): Toggle_Setting_Field
    {
        return new Toggle_Setting_Field(
            self::NIP_FOR_RECEIPTS,
            $this->translator->translate('settings.sections.accounting.nip_for_receipts'),
            null,
            $this->translator->translate('settings.sections.accounting.nip_for_receipts.tooltip')
        );
    }

    private function get_edd_id_enable_vat_moss_field(): Toggle_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::EDD_ID_ENABLE_VAT_MOSS,
            $this->translator->translate('settings.sections.accounting.edd_id_enable_vat_moss'),
            null,
            $this->translator->translate('settings.sections.accounting.edd_id_enable_vat_moss.tooltip')
        ))->set_related_feature(Packages::FEAT_VAT_MOSS);
    }

    private function get_enable_flat_rate_tax_symbol_field(): Toggle_Setting_Field
    {
        return new Toggle_Setting_Field(
            self::ENABLE_FLAT_RATE_TAX_SYMBOL,
            $this->translator->translate('settings.sections.accounting.enable_flat_rate_tax_symbol'),
            null,
            $this->translator->translate('settings.sections.accounting.flat_rate_tax_symbol.tooltip')
        );
    }

    private function get_gus_field(): Toggle_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::ENABLED_GUS_API,
            $this->translator->translate('settings.sections.accounting.gus'),
            null,
            $this->translator->translate('settings.sections.accounting.gus.tooltip')
           ))->set_related_feature(Packages::FEAT_GUS_API);
    }

    private function get_thousands_separator_options(): array
    {
        return array_merge([
            '' => $this->translator->translate('settings.sections.accounting.option.separator.disabled'),
            ' ' => $this->translator->translate('settings.sections.accounting.option.separator.space')
        ],$this->get_separator_options());
    }

    private function get_separator_options(): array
    {
        return [
            ',' => $this->translator->translate('settings.sections.accounting.option.separator.comma'),
            '.' => $this->translator->translate('settings.sections.accounting.option.separator.dot')
        ];
    }
}