<?php

namespace bpmj\wpidea\admin\pages\bundle_editor\core\configuration;

use bpmj\wpidea\admin\pages\bundle_editor\Bundle_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\product_editor\core\configuration\Abstract_General_Products_Group;
use bpmj\wpidea\admin\pages\product_editor\core\fields\Product_Variable_Prices_Field;
use bpmj\wpidea\admin\settings\core\collections\Additional_Fields_Collection;
use bpmj\wpidea\admin\settings\core\collections\Fields_Collection;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Button_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Configure_Popup_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Number_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\relation\Field_Relation;
use bpmj\wpidea\admin\settings\core\entities\fields\Text_Area_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Toggle_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\Setting_Field_Validation_Result;
use bpmj\wpidea\admin\settings\web\Settings_Info_Box;
use bpmj\wpidea\controllers\admin\Admin_Redirect_Controller;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\Packages;
use bpmj\wpidea\packages\Interface_Packages_API;
use bpmj\wpidea\sales\product\api\Interface_Product_API;

class General_Bundle_Group extends Abstract_General_Products_Group
{
    public const PRICE = 'price';
    public const SHORT_DESCRIPTION_POPUP = 'short_description_popup';
    public const SHORT_DESCRIPTION = 'short_description';
    public const DESCRIPTION_BUTTON = 'description_button';
    public const VARIABLE_PRICING = 'variable_pricing_enabled';
    public const VARIABLE_PRICES = 'variable_prices';
    public const SPECIAL_OFFER = 'special_offer';
    public const DISABLE_CERTIFICATES = 'disable_certificates';
    public const SALES_DISABLED = 'sales_disabled';
    public const HIDE_FROM_LIST = 'hide_from_list';
    public const HIDE_PURCHASE_BUTTON = 'hide_purchase_button';
    public const PROMOTE_COURSE = 'promote_curse';
    public const REDIRECT_PAGE = 'redirect_page';
    public const RECURRING_PAYMENTS = 'recurring_payments_enabled';

    private Interface_Product_API $product_api;

    public function __construct(
        Interface_Product_API $product_api,
        Interface_Packages_API $packages_api
    )
    {
        $this->product_api = $product_api;

        parent::__construct($packages_api);
    }

    protected function get_translate_prefix(): string
    {
        return 'bundle_editor';
    }

    protected function get_id_query_arg_name(): string
    {
        return Bundle_Editor_Page_Renderer::BUNDLE_ID_QUERY_ARG_NAME;
    }

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

        $this->add_fieldset(
            $this->translate_with_prefix('sections.general.fieldset.price'),
            (new Fields_Collection())
                ->add($this->get_variable_pricing_field())
                ->add($this->get_variable_prices_field())
                ->add($this->get_price_field())
                ->add($this->get_special_offer_field())
        );

        $this->add_fieldset(
            $this->translate_with_prefix('sections.general.fieldset.graphics'),
            (new Fields_Collection())
                ->add($this->get_featured_image_field())
                ->add($this->get_banner_field())
        );

        $this->add_fieldset(
            $this->translate_with_prefix('sections.general.fieldset.sale'),
            (new Fields_Collection())
                ->add($this->get_turn_sale_field())
                ->add($this->get_hide_from_list_field())
                ->add($this->get_hide_purchase_button_field())
                ->add($this->get_recurring_payments_field())
        );
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
            $this->url_generator->generate(Admin_Redirect_Controller::class, 'redirect_to_edit_product_description', [
                Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
                'course_id' => $this->get_bundle_id()
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

    private function get_variable_pricing_field(): Abstract_Setting_Field
    {
        $field = (new Toggle_Setting_Field(
            self::VARIABLE_PRICING,
            $this->translate_with_prefix('sections.general.variable_pricing')
        ))->set_related_feature(Packages::FEAT_VARIABLE_PRICES);

        if($this->packages_api->has_access_to_feature(Packages::FEAT_VARIABLE_PRICES)) {
            $field->add_info_message($this->translate_with_prefix('sections.general.variable_pricing.notice'), Settings_Info_Box::INFO_BOX_TYPE_WARNING);
        }

        return $field;
    }

    private function get_variable_prices_field(): Abstract_Setting_Field
    {
        return (new Product_Variable_Prices_Field(
            self::VARIABLE_PRICES,
            $this->translate_with_prefix('sections.general.variable_prices'),
        ))->set_id($this->get_bundle_id())
            ->change_visibility($this->packages_api->has_access_to_feature(Packages::FEAT_VARIABLE_PRICES))
            ->set_relation($this->get_field_relation_for_fields_visible_when_variable_pricing_enabled());
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

        $field->set_relation($this->get_field_relation_for_fields_hidden_when_variable_pricing_enabled());

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
        ))->set_popup(
            $this->settings_popup,
            $this->translate_with_prefix('sections.general.special_offer')
        )->set_relation($this->get_field_relation_for_fields_hidden_when_variable_pricing_enabled());
    }

    protected function get_turn_sale_field(): Abstract_Setting_Field
    {
        $no_pricing_variants = $this->no_pricing_variants();

        $field = (new Toggle_Setting_Field(
            self::SALES_DISABLED,
            $this->translate_with_prefix('sections.general.sales_disabled'),
            null,
            $this->translate_with_prefix('sections.general.sales_disabled.tooltip')
        ));

        if($no_pricing_variants) {
			$field->disable();
            $field->add_info_message($this->translate_with_prefix('sections.general.sales_disabled.notice'));
        }

        return $field;
    }

    private function no_pricing_variants(): bool
    {
        $product_id = $this->get_bundle_id();

        $price_variants = $this->product_api->get_price_variants($product_id);

        if($price_variants->has_pricing_variants && !$price_variants->variable_prices){
            return true;
        }

        return false;
    }

    private function get_field_relation_for_fields_hidden_when_variable_pricing_enabled(): Field_Relation
    {
        return Field_Relation::create(
            self::VARIABLE_PRICING,
            Field_Relation::TYPE_DEPENDS_ON_RELATED_TOGGLE_NOT_CHECKED
        );
    }

    private function get_field_relation_for_fields_visible_when_variable_pricing_enabled(): Field_Relation
    {
        return Field_Relation::create(
            self::VARIABLE_PRICING,
            Field_Relation::TYPE_DEPENDS_ON_RELATED_TOGGLE_CHECKED
        );
    }

    private function get_bundle_id(): int
    {
        return (int)$this->current_request->get_query_arg(Bundle_Editor_Page_Renderer::BUNDLE_ID_QUERY_ARG_NAME);
    }
}