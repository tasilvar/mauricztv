<?php

namespace bpmj\wpidea\admin\settings\core\configuration;


use bpmj\wpidea\admin\settings\core\collections\{Additional_Fields_Collection, Fields_Collection};
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\{Abstract_Setting_Field,
	Message,
	Select_Setting_Field,
	Text_Setting_Field,
	Toggle_Setting_Field};
use bpmj\wpidea\admin\settings\core\entities\Setting_Field_Validation_Result;
use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\admin\settings\web\Settings_Info_Box;
use bpmj\wpidea\digital_products\repository\Interface_Digital_Product_Repository;
use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;
use bpmj\wpidea\Packages;
use bpmj\wpidea\physical_product\repository\Interface_Physical_Product_Repository;
use bpmj\wpidea\service\repository\Interface_Service_Repository;

class Modules_Settings_Group extends Abstract_Settings_Group
{
    private const COURSES_ENABLE = 'courses_enable';
    public const ENABLE_DIGITAL_PRODUCTS = 'enable_digital_products';
    public const SERVICES_ENABLED = 'services_enabled';
    public const INCREASING_SALES_ENABLED = 'increasing_sales_enabled';
    private const PARTNER_PROGRAM = 'partner-program';
    private const PARTNER_PROGRAM_COMMISSION = 'partner-program-commission';
    public const ENABLE_PHYSICAL_PRODUCTS = 'enable_physical_products';
	private const ENABLE_OPINIONS = 'enable_opinions';
	private const OPINIONS_RULES = 'opinions_rules';

    private Interface_Readable_Course_Repository $course_repository;
    private Interface_Digital_Product_Repository $digital_product_repository;
    private Interface_Physical_Product_Repository $physical_product_repository;
    private Interface_Service_Repository $service_repository;

    public function __construct(
        Interface_Readable_Course_Repository $course_repository,
        Interface_Digital_Product_Repository $digital_product_repository,
        Interface_Physical_Product_Repository $physical_product_repository,
        Interface_Service_Repository $service_repository
    )
    {
        $this->course_repository = $course_repository;
        $this->digital_product_repository = $digital_product_repository;
        $this->physical_product_repository = $physical_product_repository;
        $this->service_repository = $service_repository;
    }


    public function get_name(): string
    {
        return 'modules';
    }

    public function register_fields(): void
    {
        $this->add_fieldset(
            $this->translator->translate('settings.sections.modules.fieldset.product_types'),
            (new Fields_Collection())
                ->add($this->get_modules_courses_enable_field())
                ->add($this->get_modules_enable_digital_products_field())
                ->add($this->get_modules_physical_products_enabled_field())
                ->add($this->get_modules_services_enabled_field())
        );
        $this->add_fieldset(
            $this->translator->translate('settings.sections.modules.fieldset.sales_and_marketing'),
            (new Fields_Collection())
                ->add($this->get_modules_increasing_sales_enable_field())
                ->add($this->get_partner_program_field())
        );
        $this->add_fieldset(
            $this->translator->translate('settings.sections.modules.fieldset.communication'),
            (new Fields_Collection())
                ->add($this->get_opinions_info_field())
                ->add($this->get_modules_opinions_enabled_field())
        );
    }

    private function get_modules_courses_enable_field(): Abstract_Setting_Field
    {
        $field = new Toggle_Setting_Field(
            self::COURSES_ENABLE,
            $this->translator->translate('settings.sections.modules.' . self::COURSES_ENABLE),
            null,
            $this->translator->translate('settings.sections.modules.' . self::COURSES_ENABLE . '.tooltip')
        );

        if($this->has_any_courses()) {
            $field->disable_with_reason($this->translator->translate('settings.sections.modules.' . self::COURSES_ENABLE . '.notice'));
        }

        return $field;
    }

    private function get_modules_enable_digital_products_field(): Abstract_Setting_Field
    {
        $field = new Toggle_Setting_Field(
            self::ENABLE_DIGITAL_PRODUCTS,
            $this->translator->translate('settings.sections.modules.' . self::ENABLE_DIGITAL_PRODUCTS),
            null,
            $this->translator->translate('settings.sections.modules.' . self::ENABLE_DIGITAL_PRODUCTS . '.tooltip')
        );

        if($this->has_any_digital_products()) {
            $field->disable_with_reason($this->translator->translate('settings.sections.modules.' . self::ENABLE_DIGITAL_PRODUCTS . '.disable_notice'));
        }

        return $field;
    }

    private function get_modules_services_enabled_field(): Abstract_Setting_Field
    {
        $field = new Toggle_Setting_Field(
            self::SERVICES_ENABLED,
            $this->translator->translate('settings.sections.modules.' . self::SERVICES_ENABLED),
            null,
            $this->translator->translate('settings.sections.modules.' . self::SERVICES_ENABLED . '.tooltip')
        );

        if($this->has_any_services()) {
            $field->disable_with_reason($this->translator->translate('settings.sections.modules.' . self::SERVICES_ENABLED . '.disable_notice'));
        }

        return $field;
    }

    private function get_modules_physical_products_enabled_field(): Abstract_Setting_Field
    {
        $field = new Toggle_Setting_Field(
            self::ENABLE_PHYSICAL_PRODUCTS,
            $this->translator->translate('settings.sections.modules.' . self::ENABLE_PHYSICAL_PRODUCTS),
            null,
            $this->translator->translate('settings.sections.modules.' . self::ENABLE_PHYSICAL_PRODUCTS . '.tooltip')
        );

        $field->set_related_feature(Packages::FEAT_PHYSICAL_PRODUCTS);

        if($this->has_any_physical_products()) {
            $field->disable_with_reason($this->translator->translate('settings.sections.modules.' . self::ENABLE_PHYSICAL_PRODUCTS . '.disable_notice'));
        }

        return $field;
    }

    private function get_modules_increasing_sales_enable_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::INCREASING_SALES_ENABLED,
            $this->translator->translate('settings.sections.modules.' . self::INCREASING_SALES_ENABLED),
            null,
            $this->translator->translate('settings.sections.modules.' . self::INCREASING_SALES_ENABLED . '.tooltip')
        ))
            ->set_related_feature(Packages::FEAT_INCREASING_SALES);
    }

    private function get_partner_program_field(): Toggle_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::PARTNER_PROGRAM,
            $this->translator->translate('settings.sections.advanced.partner_program'),
            $this->translator->translate('settings.sections.advanced.partner_program.desc'),
            $this->translator->translate('settings.sections.advanced.partner_program.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_partner_program_commission_field())
        ))
            ->set_popup(
                $this->settings_popup,
                $this->translator->translate('settings.sections.advanced.partner_program')
            )
            ->set_related_feature(Packages::FEAT_PARTNER_PROGRAM);
    }

    private function get_modules_opinions_enabled_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::ENABLE_OPINIONS,
            $this->translator->translate('settings.sections.modules.' . self::ENABLE_OPINIONS),
            null,
            $this->translator->translate('settings.sections.modules.' . self::ENABLE_OPINIONS . '.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_opinions_rules_info_field())
                ->add($this->get_opinions_rules_field())
        ))
            ->set_popup(
                $this->settings_popup,
                $this->translator->translate('settings.sections.modules.' . self::ENABLE_OPINIONS)
            )
            ->set_related_feature(Packages::FEAT_OPINIONS);
    }

    private function get_opinions_info_field(): Abstract_Setting_Field
    {
        $visibility = $this->app_settings->get(Settings_Const::OPINIONS_RULES) ? false : true;

        return (new Message(
            $this->translator->translate('settings.sections.modules.' . self::OPINIONS_RULES . '.attention'),
            Settings_Info_Box::INFO_BOX_TYPE_WARNING
        ))->change_visibility($visibility);
    }

    private function get_opinions_rules_info_field(): Abstract_Setting_Field
    {
        return new Message(
            $this->translator->translate('settings.sections.modules.' . self::OPINIONS_RULES . '.info')
        );
    }

    private function get_opinions_rules_field(): Text_Setting_Field
    {
        $field = new Text_Setting_Field(
            self::OPINIONS_RULES,
            $this->translator->translate('settings.sections.modules.' . self::OPINIONS_RULES)
        );
        $field->set_sanitize_callback(function ($value) {
            return trim($value);
        });
        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();

            if (!filter_var($value, FILTER_VALIDATE_URL)) {
                $results->add_error_message('settings.field.validation.invalid_url');
            }

            return $results;
        });
        return $field;
    }

    private function get_partner_program_commission_field(): Select_Setting_Field
    {
        return new Select_Setting_Field(
            self::PARTNER_PROGRAM_COMMISSION,
            $this->translator->translate('settings.sections.advanced.partner_program_commission'),
            $this->translator->translate('settings.sections.advanced.partner_program_commission.desc'),
            $this->translator->translate('settings.sections.advanced.partner_program_commission.tooltip'),
            null,
            $this->commission_option()
        );
    }

    private function commission_option(): array
    {
        $commission_options= [];
        foreach(range(1,100) as $n) {
            $commission_options[$n] = $n.' %';
        }

        return $commission_options;
    }

    private function has_any_courses(): bool
    {
        if(!$this->course_repository->count()){
            return false;
        }

        return true;
    }

    private function has_any_digital_products(): bool
    {
        return $this->digital_product_repository->count_all() > 0;
    }

    private function has_any_physical_products(): bool
    {
        return $this->physical_product_repository->count_all() > 0;
    }

    private function has_any_services(): bool
    {
        return $this->service_repository->count_all() > 0;
    }
}