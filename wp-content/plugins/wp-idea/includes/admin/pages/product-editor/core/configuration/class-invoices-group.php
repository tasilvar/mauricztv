<?php

namespace bpmj\wpidea\admin\pages\product_editor\core\configuration;

use bpmj\wpidea\admin\settings\core\collections\Fields_Collection;
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\{Abstract_Setting_Field,
    Message,
    Select_Setting_Field,
    Text_Setting_Field};
use bpmj\wpidea\admin\settings\core\entities\Setting_Field_Validation_Result;
use bpmj\wpidea\sales\product\model\Gtu;
use bpmj\wpidea\sales\product\Flat_Rate_Tax_Symbol_Helper;

class Invoices_Group extends Abstract_Settings_Group
{
    private const GTU = 'gtu';
    private const FLAT_RATE_TAX_SYMBOL = 'flat_rate_tax_symbol';
    private const VAT_RATE = 'vat_rate';

    private const ENABLE_VAT_RATE_OPTION_NAME = 'invoices_is_vat_payer';
    private const DEFAULT_VAT_RATE_OPTION_NAME = 'invoices_default_vat_rate';

    private const ENABLE_FLAT_RATE_TAX_SYMBOL_OPTION_NAME = 'enable_flat_rate_tax_symbol';
    private const DEFAULT_VAT_RATE = '23';

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

        $flat_rate_tax_symbol = $this->get_flat_rate_tax_symbol_field();
        if ($flat_rate_tax_symbol) {
            $fieldset_general->add($flat_rate_tax_symbol);
        }

        $vat_rate = $this->get_vat_rate_field();
        if ($vat_rate) {
            $fieldset_general->add($vat_rate);
        }

        $gtu_warning = $this->get_gtu_not_supported_warning();
        if ($gtu_warning) {
            $fieldset_general->add($gtu_warning);
        }

        $fieldset_general->add($this->get_gtu_field());

        return $fieldset_general;
    }

    private function get_flat_rate_tax_symbol_field(): ?Abstract_Setting_Field
    {
        if(!$this->app_settings->get(self::ENABLE_FLAT_RATE_TAX_SYMBOL_OPTION_NAME)) {
            return null;
        }

        return new Select_Setting_Field(
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
        if($this->app_settings->get(self::ENABLE_VAT_RATE_OPTION_NAME) !== 'yes') {
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
            
            if ((int)$value < 0) {
                $results->add_error_message('product_editor.sections.invoices.vat_rate.validation');
            }
            else if (strlen($value) > 2) {
                $results->add_error_message('product_editor.sections.invoices.vat_rate.validation.max_length');
            }
            else if(!empty($value)) {
                if (filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 99]]) === false && 'zw' != $value) {
                    $results->add_error_message('product_editor.sections.invoices.vat_rate.validation');
                }
            }
            
            return $results;
        });

        return $field;
    }

    private function get_gtu_field_options(): array
    {
        $options = [];

        $options[Gtu::NO_GTU] = $this->translator->translate('service_editor.sections.invoices.no_gtu');

        foreach (Gtu::AVAILABLE_CODES as $code) {
            $options[$code] = strtoupper($code);
        }

        return $options;
    }

    private function get_flat_rate_tax_symbol_field_options(): array
    {
        $options = [];

        $options[Flat_Rate_Tax_Symbol_Helper::NO_TAX_SYMBOL] = $this->translator->translate('service_editor.sections.invoices.no_tax_symbol');

        foreach (Flat_Rate_Tax_Symbol_Helper::AVAILABLE_TAX_SYMBOLS as $tax) {
            $options[$tax] = strtoupper($tax);
        }

        return $options;
    }

    private function get_gtu_not_supported_warning(): ?Abstract_Setting_Field
    {
        $invoice_methods = [
            'wp-fakturownia' => [
                'label' => 'Fakturownia',
                'name' => 'bpmj_wpfa',
                'gtu_supported' => true
            ],
            'wp-ifirma' => [
                'label' => 'iFirma',
                'name' => 'bpmj_wpifirma',
                'gtu_supported' => true
            ],
            'wp-wfirma' => [
                'label' => 'wFirma',
                'name' => 'bpmj_wpwf',
                'gtu_supported' => false
            ],
            'wp-infakt' => [
                'label' => 'Infakt',
                'name' => 'bpmj_wpinfakt',
                'gtu_supported' => true
            ],
            'wp-taxe' => [
                'label' => 'Taxe',
                'name' => 'bpmj_wptaxe',
                'gtu_supported' => true
            ]
        ];
        $gtu_not_supported_for = [];

        foreach ($invoice_methods as $slug => $method) {
            if (!$method['gtu_supported'] && WPI()->diagnostic->is_integration_enabled($slug)) {
                $gtu_not_supported_for[] = $method['label'];
            }
        }

        if (empty($gtu_not_supported_for)) {
            return null;
        }

        return new Message(
            $this->translator->translate('service_editor.sections.invoices.gtu.not_supported_for')
            . ' ' . implode(', ', $gtu_not_supported_for) . '.'
        );
    }

    private function get_gtu_field(): Abstract_Setting_Field
    {
        return new Select_Setting_Field(
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

        return empty( $vat_rate ) ? self::DEFAULT_VAT_RATE : $vat_rate;
    }
}