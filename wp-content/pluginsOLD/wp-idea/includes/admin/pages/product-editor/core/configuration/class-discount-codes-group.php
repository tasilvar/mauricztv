<?php

namespace bpmj\wpidea\admin\pages\product_editor\core\configuration;

use bpmj\wpidea\admin\settings\core\collections\Fields_Collection;
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Message;
use bpmj\wpidea\admin\settings\core\entities\fields\Number_And_Select_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Select_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\Setting_Field_Validation_Result;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\infrastructure\system\System;
use bpmj\wpidea\sales\discount_codes\core\collections\Discount_Collection;
use bpmj\wpidea\sales\discount_codes\core\repositories\Discount_Query_Criteria;
use bpmj\wpidea\sales\discount_codes\core\repositories\Interface_Discount_Repository;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Amount;

class Discount_Codes_Group extends Abstract_Settings_Group
{
    private const CODE_PATTERN = 'sell_discount_code';
    private const CODE_PERIOD_VALIDITY = 'discount_code_period_validity';

    private Interface_Discount_Repository $discount_repository;
    private System $system;

    public function __construct(
        Interface_Discount_Repository $discount_repository,
        System $system
    )
    {
        $this->discount_repository = $discount_repository;
        $this->system = $system;
    }

    public function get_name(): string
    {
        return 'discount_code';
    }

    public function register_fields(): void
    {
        $this->add_field(
            new Message($this->translator->translate('service_editor.sections.discount_code.message'))
        );

        $this->add_fieldset(
            $this->translator->translate('service_editor.sections.discount_code.fieldset.discount_code'),
            (new Fields_Collection())
                ->add($this->get_code_pattern_field())
                ->add($this->get_sell_discount_time_and_type_field())
        );
    }

    private function get_code_pattern_field(): Abstract_Setting_Field
    {
        return new Select_Setting_Field(
            self::CODE_PATTERN,
            $this->translator->translate('service_editor.sections.discount_code.code_pattern'),
            $this->translator->translate('service_editor.sections.discount_code.code_pattern.desc'),
            null,
            null,
            null,
            null,
            [$this, 'get_code_pattern_options']
        );
    }

    private function get_sell_discount_time_and_type_field(): Abstract_Setting_Field
    {
        $separator = '-';
        $field = (new Number_And_Select_Field(
            self::CODE_PERIOD_VALIDITY,
            $this->translator->translate('service_editor.sections.discount_code.code_time'),
            $this->translator->translate('service_editor.sections.discount_code.code_time.desc'),
            null,
            $this->get_code_time_type_options()
        ))->set_separator($separator);

        $field->set_sanitize_callback(function ($value) use ($separator) {
            $value = trim($value);
            [$number_value, $select_value] = explode($separator, $value);

            $number_value = !empty($number_value) ? (int)$number_value : $number_value;

            return $number_value . $separator . $select_value;
        });
        $field->set_validation_callback(function ($value) use ($separator) {
            $results = new Setting_Field_Validation_Result();
            [$number_value, $select_value] = explode($separator, $value);

            if(empty($number_value)) {
                return $results;
            }

            if(!is_numeric($number_value)) {
                $results->add_error_message('service_editor.sections.discount_code.code_time.validation.must_be_a_number');
            }

            if(empty($select_value)) {
                $results->add_error_message('service_editor.sections.discount_code.code_time.validation.must_not_be_empty');
            }

            if ((int)$number_value < 0) {
                $results->add_error_message('service_editor.sections.discount_code.code_time.validation');
            }

            return $results;
        });

        return $field;
    }

    public function get_code_pattern_options(): array
    {
        $discount_codes = $this->get_non_auto_generated_discount_codes();

        if (!$discount_codes->valid()) {
            return [$this->translator->translate('service_editor.sections.discount_code.code_pattern.no_code.label')];
        }

        $options[''] = $this->translator->translate('settings.field.select.choose');

        foreach ($discount_codes as $discount_code) {
            $options[$discount_code->get_id()->to_int()] = $discount_code->get_name()->get_value() . ' (' .$this->format_amount($discount_code->get_amount()). ')';
        }

        return $options;
    }

    private function format_amount(Amount $amount): string
    {
        if ($amount->get_type() === Amount::TYPE_PERCENTAGE) {
            return $amount->get_amount() . '%';
        }

        if (!isset($this->system_currency)) {
            $this->system_currency = $this->system->get_system_currency();
        }

        return $amount->get_amount() . ' ' . $this->system_currency;
    }

    private function get_code_time_type_options(): array
    {
        return [
            '' => $this->translator->translate('service_editor.sections.discount_code.code_type.option.duration'),
            'days' => $this->translator->translate('service_editor.sections.discount_code.code_type.option.days'),
            'weeks' => $this->translator->translate('service_editor.sections.discount_code.code_type.option.weeks'),
            'months' => $this->translator->translate('service_editor.sections.discount_code.code_type.option.months')

        ];
    }

	private function get_non_auto_generated_discount_codes(): Discount_Collection
	{
		$criteria = new Discount_Query_Criteria();
		$per_page = 100;
		$page = 1;
		$sort_by = (new Sort_By_Clause())->sort_by('id', true);

		$criteria->set_type_filter(Discount_Query_Criteria::TYPE_FILTER_EXCLUDE_AUTO_GENERATED);

		return $this->discount_repository->find_by_criteria($criteria, $per_page, $page, $sort_by, true);
	}
}