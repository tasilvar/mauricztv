<?php

namespace bpmj\wpidea\admin\pages\product_editor\core\configuration;

use bpmj\wpidea\admin\pages\product_editor\core\fields\{Code_Gtu_Field, Flat_Rate_Tax_Symbol_Field};
use bpmj\wpidea\admin\settings\core\collections\Additional_Fields_Collection;
use bpmj\wpidea\admin\settings\core\collections\Fields_Collection;
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\{Abstract_Setting_Field,
	Configure_Popup_Setting_Field,
	Message,
	Text_Setting_Field};
use bpmj\wpidea\admin\settings\core\entities\Setting_Field_Validation_Result;
use bpmj\wpidea\app\courses\Courses_App_Service;
use bpmj\wpidea\integrations\Integrations;
use bpmj\wpidea\integrations\Interface_Invoice_Service_Status_Checker;
use bpmj\wpidea\sales\product\api\Interface_Product_API;
use bpmj\wpidea\sales\product\Flat_Rate_Tax_Symbol_Helper;
use bpmj\wpidea\sales\product\model\Gtu;

abstract class Abstract_Invoices_Group extends Abstract_Settings_Group
{
    private const GTU_POPUP = 'gtu_popup';
    protected const GTU = 'gtu';

    private const FLAT_RATE_TAX_SYMBOL_POPUP = 'flat_rate_tax_symbol_popup';
    protected const FLAT_RATE_TAX_SYMBOL = 'flat_rate_tax_symbol';
    private const VAT_RATE = 'vat_rate';

    private const ENABLE_VAT_RATE_OPTION_NAME = 'invoices_is_vat_payer';
    private const DEFAULT_VAT_RATE_OPTION_NAME = 'invoices_default_vat_rate';

    private const ENABLE_FLAT_RATE_TAX_SYMBOL_OPTION_NAME = 'enable_flat_rate_tax_symbol';
    private const DEFAULT_VAT_RATE = '23';
    private Interface_Invoice_Service_Status_Checker $invoice_service_status_checker;
    protected Interface_Product_API $product_api;
    protected Courses_App_Service $courses_app_service;

    public function __construct(
        Interface_Invoice_Service_Status_Checker $invoice_service_status_checker,
        Interface_Product_API $product_api,
        Courses_App_Service $courses_app_service
    )
    {
        $this->invoice_service_status_checker = $invoice_service_status_checker;
        $this->product_api = $product_api;
        $this->courses_app_service = $courses_app_service;
    }

    public function get_name(): string
    {
        return 'invoices';
    }

    public function register_fields(): void
    {
        $this->add_fieldset(
            $this->translator->translate('service_editor.sections.invoices.fieldset.general'),
            $this->get_fieldset_general()
        );
    }

    private function get_fieldset_general(): Fields_Collection
    {
        $fieldset_general = new Fields_Collection();

        $flat_rate_tax_symbol = $this->get_flat_rate_tax_symbol_popup_field();
        if ($flat_rate_tax_symbol) {
            $flat_rate_tax_symbol_warning = $this->get_not_supported_warning(self::FLAT_RATE_TAX_SYMBOL);
            if ($flat_rate_tax_symbol_warning) {
                $fieldset_general->add($flat_rate_tax_symbol_warning);
            }

            $fieldset_general->add($flat_rate_tax_symbol);
        }

        $vat_rate = $this->get_vat_rate_field();
        if ($vat_rate) {
            $fieldset_general->add($vat_rate);
        }

        $gtu_warning = $this->get_not_supported_warning(self::GTU);
        if ($gtu_warning) {
            $fieldset_general->add($gtu_warning);
        }

        $fieldset_general->add($this->get_gtu_popup_field());

        return $fieldset_general;
    }

    private function get_flat_rate_tax_symbol_popup_field(): ?Abstract_Setting_Field
    {
        if (!$this->app_settings->get(self::ENABLE_FLAT_RATE_TAX_SYMBOL_OPTION_NAME)) {
            return null;
        }

        return (new Configure_Popup_Setting_Field(
            self::FLAT_RATE_TAX_SYMBOL_POPUP,
            $this->translator->translate('service_editor.sections.invoices.flat_rate_tax_symbol'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_flat_rate_tax_symbol_field())
        ))->set_popup(
            $this->settings_popup,
            $this->translator->translate('service_editor.sections.invoices.flat_rate_tax_symbol'),
        );
    }

    protected function get_flat_rate_tax_symbol_field(): Abstract_Setting_Field
    {
        return new Flat_Rate_Tax_Symbol_Field(
            self::FLAT_RATE_TAX_SYMBOL,
            $this->translator->translate('service_editor.sections.invoices.flat_rate_tax_symbol'),
            null,
            null,
            null,
            $this->get_flat_rate_tax_symbol_field_options()
        );
    }

    private function get_vat_rate_field(): ?Abstract_Setting_Field
    {
        if ($this->app_settings->get(self::ENABLE_VAT_RATE_OPTION_NAME) !== 'yes') {
            return null;
        }

        $field = new Text_Setting_Field(
            self::VAT_RATE,
            $this->translator->translate('service_editor.sections.invoices.vat_rate'),
            $this->translator->translate('invoices.vat_rate.empty') . ' <strong>' . $this->get_default_vat_rate() . '%</strong>.'
        );
        $field->set_sanitize_callback(function ($value) {
            return strtolower(trim($value));
        });
        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();

            if(!empty($value)) {
                if (filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 99]]) === false && 'zw' != $value) {
                    $results->add_error_message('product_editor.sections.invoices.vat_rate.validation');
                }
            }

            return $results;
        });

        return $field;
    }

    protected function get_gtu_field_options(): array
    {
        $options = [];

        $options[Gtu::NO_GTU] = $this->translator->translate('service_editor.sections.invoices.no_gtu');

        foreach (Gtu::AVAILABLE_CODES as $code) {
            $options[$code] = strtoupper($code);
        }

        return $options;
    }

    protected function get_flat_rate_tax_symbol_field_options(): array
    {
        $options = [];

        $options[Flat_Rate_Tax_Symbol_Helper::NO_TAX_SYMBOL] = $this->translator->translate('service_editor.sections.invoices.no_tax_symbol');

        foreach (Flat_Rate_Tax_Symbol_Helper::AVAILABLE_TAX_SYMBOLS as $tax) {
            $options[$tax] = strtoupper($tax);
        }

        return $options;
    }

    private function get_not_supported_warning(?string $name = null): ?Abstract_Setting_Field
    {
        if (!$name) {
            return null;
        }

        $invoice_methods = Integrations::INVOICE_INTEGRATIONS;

        $not_supported_for_gtu = [];
        $not_supported_for_flat_rate = [];

        foreach ($invoice_methods as $slug => $method) {
            if (!$method['gtu_supported'] && $this->invoice_service_status_checker->is_integration_enabled($slug)) {
                $not_supported_for_gtu[] = $method['label'];
            }

            if (!$method['flat_rate_supported'] && $this->invoice_service_status_checker->is_integration_enabled($slug)) {
                $not_supported_for_flat_rate[] = $method['label'];
            }
        }

        $not_supported_for = $name !== self::GTU ? $not_supported_for_flat_rate : $not_supported_for_gtu;

        if (empty($not_supported_for)) {
            return null;
        }

        return new Message(
            $this->translator->translate('service_editor.sections.invoices.' . $name . '.not_supported_for')
            . ' ' . implode(', ', $not_supported_for) . '.'
        );
    }

    private function get_gtu_popup_field(): Abstract_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::GTU_POPUP,
            $this->translator->translate('service_editor.sections.invoices.gtu'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_gtu_field())
        ))->set_popup(
            $this->settings_popup,
            $this->translator->translate('service_editor.sections.invoices.gtu'),
        );
    }

    protected function get_gtu_field(): Abstract_Setting_Field
    {
        return new Code_Gtu_Field(
            self::GTU,
            $this->translator->translate('service_editor.sections.invoices.gtu'),
            null,
            null,
            null,
            $this->get_gtu_field_options()
        );
    }

    private function get_default_vat_rate(): string
    {
        $vat_rate = $this->app_settings->get(self::DEFAULT_VAT_RATE_OPTION_NAME);

        return empty($vat_rate) ? self::DEFAULT_VAT_RATE : $vat_rate;
    }
}