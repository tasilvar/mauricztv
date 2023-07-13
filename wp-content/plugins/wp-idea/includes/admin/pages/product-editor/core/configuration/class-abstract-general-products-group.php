<?php

namespace bpmj\wpidea\admin\pages\product_editor\core\configuration;

use bpmj\wpidea\admin\pages\product_editor\core\fields\{Checkbox_Categories_Field,
	Recurring_Payments_Interval_Field,
	Special_Offer_Dates_Field,
	Tags_Field};
use bpmj\wpidea\admin\settings\core\collections\{Additional_Fields_Collection, Fields_Collection};
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\{Abstract_Setting_Field,
	Button_Setting_Field,
	Configure_Popup_Setting_Field,
	Media_Setting_Field,
	Number_Setting_Field,
	Text_Area_Setting_Field,
	Text_Setting_Field,
	Toggle_Setting_Field};
use bpmj\wpidea\admin\settings\core\entities\Setting_Field_Validation_Result;
use bpmj\wpidea\Packages;
use bpmj\wpidea\packages\Interface_Packages_API;

abstract class Abstract_General_Products_Group extends Abstract_Settings_Group
{
    private const NAME = 'name';
    private const TAGS_POPUP = 'tags_popup';
    private const SLUG = 'slug';
    private const TAGS = 'tags';
    private const PRICE = 'price';
    private const SPECIAL_OFFER = 'special_offer';
    private const SALE_PRICE = 'sale_price';
    private const SALE_PRICE_DATE_FROM = 'sale_price_date_from';
    private const SALE_PRICE_DATE_TO = 'sale_price_date_to';
    private const BANNER = 'banner';
    private const FEATURED_IMAGE = 'featured_image';
    public const SALES_DISABLED = 'sales_disabled';
    public const HIDE_FROM_LIST = 'hide_from_list';
    public const HIDE_PURCHASE_BUTTON = 'hide_purchase_button';
    public const PROMOTE_COURSE = 'promote_curse';
    public const RECURRING_PAYMENTS = 'recurring_payments_enabled';
    private const RECURRING_PAYMENTS_INTERVAL = 'recurring_payments_interval';
    private const PURCHASE_LIMIT = 'purchase_limit';
    private const PURCHASE_LIMIT_ITEMS_LEFT = 'purchase_limit_items_left';
    private const SHORT_DESCRIPTION_POPUP = 'short_description_popup';
    private const CATEGORIES_POPUP = 'categories_popup';
    private const CATEGORIES = 'categories';
    private const SHORT_DESCRIPTION = 'short_description';
    private const DESCRIPTION_BUTTON = 'description_button';
    protected Interface_Packages_API $packages_api;
    public function __construct(Interface_Packages_API $packages_api)
    {
        $this->packages_api = $packages_api;
    }

    public function get_name(): string
    {
        return 'general';
    }

    abstract protected function get_translate_prefix();

    abstract protected function get_id_query_arg_name();

    public function register_fields(): void
    {
        $this->add_fieldset(
            $this->translate_with_prefix('sections.general.fieldset.name'),
            (new Fields_Collection())
                ->add($this->get_name_field())
                ->add($this->get_description_popup_field())
                ->add($this->get_short_description_popup_field())
        );

        $this->add_location_fieldset();

        $this->add_price_fieldset();

        $this->add_quantities_fieldset();

        $this->add_fieldset(
            $this->translate_with_prefix('sections.general.fieldset.graphics'),
            (new Fields_Collection())
                ->add($this->get_banner_field())
                ->add($this->get_featured_image_field())
        );
        $this->add_fieldset(
            $this->translate_with_prefix('sections.general.fieldset.sale'),
            (new Fields_Collection())
                ->add($this->get_turn_sale_field())
                ->add($this->get_hide_from_list_field())
                ->add($this->get_hide_purchase_button_field())
                ->add($this->get_promote_course_field())
                ->add($this->get_recurring_payments_field())
        );
    }

    protected function get_name_field(): Abstract_Setting_Field
    {
        return (new Text_Setting_Field(
            self::NAME,
            $this->translate_with_prefix('sections.general.name')
        ))->set_max_length(200);
    }

    private function get_short_description_popup_field(): Abstract_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::SHORT_DESCRIPTION_POPUP,
            $this->translate_with_prefix('sections.general.short_description'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_short_description_field())
        ))->set_popup(
            $this->settings_popup,
            $this->translate_with_prefix('sections.general.short_description')
        );
    }

    private function get_description_popup_field(): Abstract_Setting_Field
    {
        return (new Button_Setting_Field(
            self::DESCRIPTION_BUTTON,
            $this->translate_with_prefix('sections.general.description')
        ))->set_url(
            $this->url_generator->generate_admin_page_url('post.php', [
                'post' => $this->current_request->get_query_arg($this->get_id_query_arg_name()),
                'action' => 'edit'
            ])
        );
    }

    private function get_short_description_field(): Abstract_Setting_Field
    {
        return new Text_Area_Setting_Field(
            self::SHORT_DESCRIPTION,
            $this->translate_with_prefix('sections.general.short_description')
        );
    }

    private function get_categories_popup_field(): Abstract_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::CATEGORIES_POPUP,
            $this->translate_with_prefix('sections.general.categories'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_categories_field())
        ))->set_popup(
            $this->settings_popup,
            $this->translate_with_prefix('sections.general.categories')
        );
    }

    private function get_categories_field(): Abstract_Setting_Field
    {
        return (new Checkbox_Categories_Field(
            self::CATEGORIES,
            $this->translate_with_prefix('sections.general.select_categories')
        ))->set_categories_list(
            $this->get_all_categories()
        );
    }

    private function get_url_field(): Abstract_Setting_Field
    {
        return (new Text_Setting_Field(
            self::SLUG,
            $this->translate_with_prefix('sections.general.url')
        ))->set_sanitize_callback(function ($value) {
            return sanitize_title($value);
        });
    }

    private function get_tags_popup_field(): Abstract_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::TAGS_POPUP,
            $this->translate_with_prefix('sections.general.tags'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_tags_field())
        ))->set_popup(
            $this->settings_popup,
            $this->translate_with_prefix('sections.general.tags')
        );
    }

    private function get_tags_field(): Abstract_Setting_Field
    {
        return new Tags_Field(
            self::TAGS,
            $this->translate_with_prefix('sections.general.add_tags'),
            $this->translate_with_prefix('sections.general.add_tags.desc')
        );
    }

    protected function get_price_field(): Abstract_Setting_Field
    {
        $field = new Number_Setting_Field(
            self::PRICE,
            $this->translate_with_prefix('sections.general.price'),
            null,
            $this->translate_with_prefix('sections.general.price.tooltip')
        );
        $field->set_sanitize_callback(function ($value) {
            return trim($value);
        });
        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();

            if (!is_numeric($value) || (float)$value < 0) {
                $results->add_error_message('product_editor.sections.general.price.validation');
            }
            return $results;
        });

        return $field;
    }

    protected function get_special_offer_field(): Configure_Popup_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::SPECIAL_OFFER,
            $this->translate_with_prefix('sections.general.special_offer'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_sale_price_field())
                ->add($this->get_sale_price_date_from_field())
                ->add($this->get_sale_price_date_to_field())
        ))->set_popup(
            $this->settings_popup,
            $this->translate_with_prefix('sections.general.special_offer')
        );
    }

    protected function add_price_fieldset(): void
    {
        $this->add_fieldset(
            $this->translate_with_prefix('sections.general.fieldset.price'),
            (new Fields_Collection())
                ->add($this->get_price_field())
                ->add($this->get_special_offer_field())
        );
    }

    protected function get_sale_price_date_from_field(): Abstract_Setting_Field
    {
        return (new Special_Offer_Dates_Field(
            self::SALE_PRICE_DATE_FROM,
            $this->translate_with_prefix('sections.general.sale_price_date_from'),
            null,
            $this->translate_with_prefix('sections.general.sale_price_date_from.tooltip'),
            $this->get_hour()
        ))->set_related_feature(Packages::FEAT_SALE_PRICE_DATES);
    }

    protected function get_sale_price_date_to_field(): Abstract_Setting_Field
    {
        return (new Special_Offer_Dates_Field(
            self::SALE_PRICE_DATE_TO,
            $this->translate_with_prefix('sections.general.sale_price_date_to'),
            null,
            $this->translate_with_prefix('sections.general.sale_price_date_to.tooltip'),
            $this->get_hour()
        ))->set_related_feature(Packages::FEAT_SALE_PRICE_DATES);
    }

    protected function get_sale_price_field(): Abstract_Setting_Field
    {
        $field = new Number_Setting_Field(
            self::SALE_PRICE,
            $this->translate_with_prefix('sections.general.sale_price'),
            null,
            $this->translate_with_prefix('sections.general.sale_price.tooltip'),
            null,
            0
        );
        $field->set_sanitize_callback(function ($value) {
            return number_format((float)$value, 2, '.', '');
        });
        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();

            if (!is_numeric($value) || (float)$value < 0) {
                $results->add_error_message('product_editor.sections.general.sale_price.validation');
            }
            return $results;
        });
        return $field;
    }

    private function get_purchase_limit_field(): Abstract_Setting_Field
    {
        $field = new Number_Setting_Field(
            self::PURCHASE_LIMIT,
            $this->translate_with_prefix('sections.general.purchase_limit'),
            $this->translate_with_prefix('sections.general.purchase_limit.desc')
        );
        $field->set_sanitize_callback(function ($value) {
            return trim($value);
        });
        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();

            if (!is_numeric($value) || (int)$value < 0) {
                $results->add_error_message('product_editor.sections.general.purchase_limit.validation');
            }
            return $results;
        });
        return $field;
    }

    private function get_purchase_limit_items_left_field(): Abstract_Setting_Field
    {
        $field = new Number_Setting_Field(
            self::PURCHASE_LIMIT_ITEMS_LEFT,
            $this->translate_with_prefix('sections.general.purchase_limit_items_left')
        );
        $field->set_sanitize_callback(function ($value) {
            return trim($value);
        });
        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();

            if (!is_numeric($value) || (int)$value < 0) {
                $results->add_error_message('product_editor.sections.general.purchase_limit_items_left.validation');
            }
            return $results;
        });
        return $field;
    }

    protected function get_banner_field(): Abstract_Setting_Field
    {
        return new Media_Setting_Field(
            self::BANNER,
            $this->translate_with_prefix('sections.general.banner')
        );
    }

    protected function get_featured_image_field(): Abstract_Setting_Field
    {
        return (new Media_Setting_Field(
            self::FEATURED_IMAGE,
            $this->translate_with_prefix('sections.general.featured_image')
        ))->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();

            if (empty($value)) {
                return $results;
            }

            if (!attachment_url_to_postid($value)) {
                $results->add_error_message('product_editor.sections.general.featured_image.attachment_must_exist');
            }

            return $results;
        });
    }

    protected function get_turn_sale_field(): Abstract_Setting_Field
    {
        return new Toggle_Setting_Field(
            self::SALES_DISABLED,
            $this->translate_with_prefix('sections.general.sales_disabled'),
            null,
            $this->translate_with_prefix('sections.general.sales_disabled.tooltip')
        );
    }

    protected function get_hide_from_list_field(): Abstract_Setting_Field
    {
        return new Toggle_Setting_Field(
            self::HIDE_FROM_LIST,
            $this->translate_with_prefix('sections.general.hide_from_list')
        );
    }

    protected function get_hide_purchase_button_field(): Abstract_Setting_Field
    {
        return new Toggle_Setting_Field(
            self::HIDE_PURCHASE_BUTTON,
            $this->translate_with_prefix('sections.general.hide_purchase_button'),
            null,
            $this->translate_with_prefix('sections.general.hide_purchase_button.tooltip')
        );
    }

    protected function get_promote_course_field(): Abstract_Setting_Field
    {
        return new Toggle_Setting_Field(
            self::PROMOTE_COURSE,
            $this->translate_with_prefix('sections.general.promote_course')
        );
    }

    protected function get_recurring_payments_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::RECURRING_PAYMENTS,
            $this->translate_with_prefix('sections.general.recurring_payments_enabled'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_recurring_payments_interval_field())
        ))->set_popup(
            $this->settings_popup,
            $this->translate_with_prefix('sections.general.recurring_payments')
        )
            ->set_related_feature(Packages::FEAT_RECURRING_PAYMENTS)
            ->change_visibility($this->any_enabled_gateway_supports_recurring_payments());
    }

    private function get_recurring_payments_interval_field(): Recurring_Payments_Interval_Field
    {
        return new Recurring_Payments_Interval_Field(
            self::RECURRING_PAYMENTS_INTERVAL,
            $this->translate_with_prefix('sections.general.recurring_payments_interval'),
            $this->translate_with_prefix('sections.general.recurring_payments_interval.desc'),
            null,
            $this->get_recurring_payments_interval(),
            $this->get_recurring_payments_unit(),
            '1 months'
        );
    }

    private function get_recurring_payments_interval(): array
    {
        $intervals = [];
        foreach (range(1, 30) as $n) {
            $intervals[$n] = '+' . $n;
        }

        return $intervals;
    }

    private function get_recurring_payments_unit(): array
    {
        return [
            'days' => $this->translate_with_prefix('sections.general.payments_unit.option.days'),
            'weeks' => $this->translate_with_prefix('sections.general.payments_unit.option.weeks'),
            'months' => $this->translate_with_prefix('sections.general.payments_unit.option.months'),
            'years' => $this->translate_with_prefix('sections.general.payments_unit.option.years')
        ];
    }

    protected function get_hour(): array
    {
        $hours = [];

        for ($i = 0; $i <= 23; $i++) {
            $v = sprintf('%02d', $i);
            $hours[$v] = $v;
        }
        return $hours;
    }

    private function get_all_categories(): array
    {
        return get_categories([
            'taxonomy' => 'download_category',
            'hide_empty' => false,
        ]);
    }

    protected function any_enabled_gateway_supports_recurring_payments(): bool
    {
        return function_exists('edd_any_enabled_gateway_supports_recurring_payments') && edd_any_enabled_gateway_supports_recurring_payments();
    }

    protected function translate_with_prefix(string $translation_id): string
    {
        return $this->translator->translate($this->get_translate_prefix() . '.' . $translation_id);
    }

    protected function add_location_fieldset(): void
    {
        $this->add_fieldset(
            $this->translate_with_prefix('sections.general.fieldset.location'),
            (new Fields_Collection())
                ->add($this->get_url_field())
                ->add($this->get_categories_popup_field())
                ->add($this->get_tags_popup_field())
        );
    }

    protected function add_quantities_fieldset(): void
    {
        $this->add_fieldset(

            $this->translate_with_prefix('sections.general.fieldset.quantities_available'),
            (new Fields_Collection())
                ->add($this->get_purchase_limit_field())
                ->add($this->get_purchase_limit_items_left_field())
        );
    }
}