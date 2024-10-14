<?php

namespace bpmj\wpidea\admin\pages\course_editor\core\configuration;

use bpmj\wpidea\admin\pages\course_editor\core\fields\Course_Start_Date_Field;
use bpmj\wpidea\admin\pages\course_editor\core\fields\Radio_View_Field;
use bpmj\wpidea\admin\pages\course_editor\Course_Editor_Page_Renderer;
use bpmj\wpidea\admin\pages\product_editor\core\configuration\Abstract_General_Products_Group;
use bpmj\wpidea\admin\pages\product_editor\core\fields\Special_Offer_Dates_Field;
use bpmj\wpidea\admin\pages\product_editor\core\fields\Variable_Prices_Field;
use bpmj\wpidea\admin\settings\core\collections\Additional_Fields_Collection;
use bpmj\wpidea\admin\settings\core\collections\Fields_Collection;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Button_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Configure_Popup_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Media_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Message;
use bpmj\wpidea\admin\settings\core\entities\fields\Navigation_Label_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Number_And_Select_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Number_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\relation\Field_Relation;
use bpmj\wpidea\admin\settings\core\entities\fields\Select_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Text_Area_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Text_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\fields\Toggle_Setting_Field;
use bpmj\wpidea\admin\settings\core\entities\Setting_Field_Validation_Result;
use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\admin\settings\web\Settings_Info_Box;
use bpmj\wpidea\app\courses\Courses_App_Service;
use bpmj\wpidea\certificates\Certificate_Template;
use bpmj\wpidea\controllers\admin\Admin_Redirect_Controller;
use bpmj\wpidea\Courses;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\options\Interface_Options;
use bpmj\wpidea\Packages;
use bpmj\wpidea\packages\Interface_Packages_API;
use bpmj\wpidea\sales\product\api\Interface_Product_API;
use bpmj\wpidea\sales\product\Custom_Purchase_Links_Helper;

class General_Course_Group extends Abstract_General_Products_Group
{
    private const RESTRICTED_TO = '_bpmj_eddpc_restricted_to';
    public const SHORT_DESCRIPTION_POPUP = 'short_description_popup';
    public const SHORT_DESCRIPTION = 'short_description';
    public const DESCRIPTION_BUTTON = 'description_button';
    public const WELCOME_BUTTON = 'welcome_button';
    public const PRICE = 'price';
    public const VARIABLE_PRICING = 'variable_pricing_enabled';
    public const VARIABLE_PRICES = 'variable_prices';
    public const VARIABLE_PRICES_SPECIAL_OFFER = 'variable_prices_special_offer';
    private const VARIABLE_SALE_PRICE_DATE_FROM = 'variable_sale_price_date_from';
    private const VARIABLE_SALE_PRICE_DATE_TO = 'variable_sale_price_date_to';
    public const SPECIAL_OFFER = 'special_offer';
    public const PURCHASE_LIMIT = 'purchase_limit';
    public const PURCHASE_LIMIT_ITEMS_LEFT = 'purchase_limit_items_left';
    public const ACCESS_START = 'access_start';
    public const ACCESS_TIME_AND_UNIT = 'access_time_and_unit';
    public const LOGO = 'logo';
    public const DISABLE_CERTIFICATES = 'disable_certificates';
    public const SALES_DISABLED = 'sales_disabled';
    public const HIDE_FROM_LIST = 'hide_from_list';
    public const HIDE_PURCHASE_BUTTON = 'hide_purchase_button';
    public const PROMOTE_COURSE = 'promote_curse';
    public const REDIRECT_PAGE = 'redirect_page';
    public const REDIRECT_URL = 'redirect_url';
    public const CERTIFICATE_TEMPLATE_ID = 'certificate_template_id';
    public const ENABLE_CERTIFICATE_NUMBERING = 'enable_certificate_numbering';
    public const CERTIFICATE_NUMBERING_PATTERN = 'certificate_numbering_pattern';
    public const DISABLE_EMAIL_SUBSCRIPTION = 'disable_email_subscription';
    public const RECURRING_PAYMENTS = 'recurring_payments_enabled';
    public const CUSTOM_PURCHASE_LINKS = 'custom_purchase_link';
    public const VIEW_POPUP = 'view_popup';
    public const NAVIGATION_NEXT_LESSON_LABEL = 'navigation_next_lesson_label';
    public const NAVIGATION_PREVIOUS_LESSON_LABEL = 'navigation_previous_lesson_label';
    public const PROGRESS_TRACKING = 'progress_tracking';
    public const INACCESSIBLE_LESSON_DISPLAY = 'inaccessible_lesson_display';
    public const PROGRESS_FORCED = 'progress_forced';

    private Courses $courses;
    private Custom_Purchase_Links_Helper $custom_purchase_links_helper;
    private Interface_Product_API $product_api;
    private Courses_App_Service $courses_app_service;
    private Interface_Options $options;
    private Certificate_Template $certificates;

    public function __construct(
        Courses $courses,
        Custom_Purchase_Links_Helper $custom_purchase_links_helper,
        Interface_Product_API $product_api,
        Courses_App_Service $courses_app_service,
        Interface_Options $options,
        Certificate_Template $certificates,
        Interface_Packages_API $packages_api
    )
    {
        $this->courses = $courses;
        $this->custom_purchase_links_helper = $custom_purchase_links_helper;
        $this->product_api = $product_api;
        $this->courses_app_service = $courses_app_service;
        $this->options = $options;
        $this->certificates = $certificates;

        parent::__construct($packages_api);
    }

    protected function get_translate_prefix(): string
    {
        return 'course_editor';
    }

    protected function get_id_query_arg_name(): string
    {
        return Course_Editor_Page_Renderer::COURSE_ID_QUERY_ARG_NAME;
    }

    public function register_fields(): void
    {
        $this->add_fieldset(
            $this->translate_with_prefix('sections.general.fieldset.name'),
            (new Fields_Collection())
                ->add($this->get_name_field())
                ->add($this->get_description_popup_field())
                ->add($this->get_short_description_popup_field())
                ->add($this->get_welcome_field())
        );

        $this->add_location_fieldset();

        $this->add_fieldset(
            $this->translate_with_prefix('sections.general.fieldset.price'),
            (new Fields_Collection())
                ->add($this->get_variable_pricing_field())
                ->add($this->get_variable_prices_field())
                ->add($this->get_price_field())
                ->add($this->get_special_offer_field())
                ->add($this->get_variable_prices_special_offer_field())
        );

        $this->add_fieldset(
            $this->translate_with_prefix('sections.general.fieldset.quantities_available'),
            (new Fields_Collection())
                ->add($this->get_purchase_limit_field())
                ->add($this->get_purchase_limit_items_left_field()),
            true,
            $this->get_field_relation_for_fields_hidden_when_variable_pricing_enabled()

        );

        $this->add_fieldset(
            $this->translate_with_prefix('sections.general.fieldset.course_start'),
            (new Fields_Collection())
                 ->add(
                     new Message(
                         $this->translate_with_prefix('sections.general.access_start.desc'),
                         Settings_Info_Box::INFO_BOX_TYPE_WARNING
                     )
                 )
                ->add($this->get_access_start_field())
                ->add($this->get_access_time_field())
        );

        $this->add_fieldset(
            $this->translate_with_prefix('sections.general.fieldset.graphics'),
            (new Fields_Collection())
                ->add($this->get_featured_image_field())
                ->add($this->get_course_logo_field())
                ->add($this->get_banner_field())
        );

        $this->add_fieldset(
            $this->translate_with_prefix('sections.general.fieldset.view'),
            (new Fields_Collection())
                ->add($this->get_view_popup_field())
        );

        $this->add_fieldset(
            $this->translate_with_prefix('sections.general.fieldset.certification'),
            (new Fields_Collection())
                ->add($this->get_disable_certificates_field()),
            $this->app_settings->get(Settings_Const::ENABLE_CERTIFICATES) ?? false
        );

        $this->add_fieldset(
            $this->translate_with_prefix('sections.general.fieldset.no_authorization'),
            (new Fields_Collection())
                ->add($this->get_redirect_page_field())
                ->add($this->get_redirect_url_field())
        );

        $this->add_fieldset(
            $this->translate_with_prefix('sections.general.fieldset.sale'),
            (new Fields_Collection())
                ->add($this->get_turn_sale_field())
                ->add($this->get_hide_from_list_field())
                ->add($this->get_hide_purchase_button_field())
                ->add($this->get_promote_course_field())
                ->add($this->get_disable_email_subscription_field())
                ->add($this->get_recurring_payments_field())
                ->add($this->get_custom_purchase_links_field())
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
                'course_id' => $this->current_request->get_query_arg(Course_Editor_Page_Renderer::COURSE_ID_QUERY_ARG_NAME)
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

    private function get_welcome_field(): Abstract_Setting_Field
    {
        $page_id = $this->courses->get_page_id_by_course_id($this->get_course_id());

        return (new Button_Setting_Field(
            self::WELCOME_BUTTON,
            $this->translate_with_prefix('sections.general.welcome')
        ))->set_url(
            $this->url_generator->generate_admin_page_url('post.php', [
                'post' => $page_id,
                'action' => 'edit',
                'edit_description' => 1
            ])
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
        return (new Variable_Prices_Field(
            self::VARIABLE_PRICES,
            $this->translate_with_prefix('sections.general.variable_prices'),
        ))->set_id($this->get_course_id())
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
                ->add($this->get_sale_price_date_from_field())
                ->add($this->get_sale_price_date_to_field())
        ))
            ->set_popup(
                $this->settings_popup,
                $this->translate_with_prefix('sections.general.special_offer')
            )
            ->set_relation($this->get_field_relation_for_fields_hidden_when_variable_pricing_enabled());
    }

    private function get_variable_prices_special_offer_field(): Configure_Popup_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::VARIABLE_PRICES_SPECIAL_OFFER,
            $this->translate_with_prefix('sections.general.variable_prices_special_offer'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_variable_sale_price_date_from_field())
                ->add($this->get_variable_sale_price_date_to_field())
        ))
            ->set_popup(
                $this->settings_popup,
                $this->translate_with_prefix('sections.general.variable_prices_special_offer')
            )
            ->change_visibility($this->packages_api->has_access_to_feature(Packages::FEAT_VARIABLE_PRICES))
            ->set_relation($this->get_field_relation_for_fields_visible_when_variable_pricing_enabled());
    }

    private function get_variable_sale_price_date_from_field(): Abstract_Setting_Field
    {
        return new Special_Offer_Dates_Field(
            self::VARIABLE_SALE_PRICE_DATE_FROM,
            $this->translate_with_prefix('sections.general.sale_price_date_from'),
            null,
            $this->translate_with_prefix('sections.general.sale_price_date_from.tooltip'),
            $this->get_hour()
        );
    }

    private function get_variable_sale_price_date_to_field(): Abstract_Setting_Field
    {
        return new Special_Offer_Dates_Field(
            self::VARIABLE_SALE_PRICE_DATE_TO,
            $this->translate_with_prefix('sections.general.sale_price_date_to'),
            null,
            $this->translate_with_prefix('sections.general.sale_price_date_to.tooltip'),
            $this->get_hour()
        );
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

    private function get_access_start_field(): Abstract_Setting_Field
    {
        return (new Course_Start_Date_Field(
            self::ACCESS_START,
            $this->translate_with_prefix('sections.general.access_start'),
            null,
            $this->translate_with_prefix('sections.general.access_start.tooltip'),
            $this->get_hour_options(),
            $this->get_minute_options(),
        ))->set_related_feature(Packages::FEAT_COURSE_ACCESS_START);
    }

    private function get_access_time_field(): Abstract_Setting_Field
    {
        $separator = '-';
        $field = (new Number_And_Select_Field(
            self::ACCESS_TIME_AND_UNIT,
            $this->translate_with_prefix('sections.general.access_time'),
            null,
            $this->translate_with_prefix('sections.general.access_time.tooltip'),
            $this->get_access_time_unit_type_options()
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
                $results->add_error_message('course_editor.sections.general.code_time.validation.must_be_a_number');
            }

            if(empty($select_value)) {
                $results->add_error_message('course_editor.sections.general.code_time.validation.must_not_be_empty');
            }

            if ((int)$number_value < 0) {
                $results->add_error_message('course_editor.sections.general.code_time.validation');
            }

            return $results;
        });

        $field->set_relation($this->get_field_relation_for_fields_hidden_when_variable_pricing_enabled());

        $field->set_related_feature(Packages::FEAT_COURSE_ACCESS_TIME);

        return $field;
    }


    private function get_course_logo_field(): Abstract_Setting_Field
    {
        return new Media_Setting_Field(
            self::LOGO,
            $this->translate_with_prefix('sections.general.logo')
        );
    }

    private function get_view_popup_field(): Configure_Popup_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::VIEW_POPUP,
            $this->translate_with_prefix('sections.general.view_options'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_next_lesson_label_field())
                ->add($this->get_previous_label_field())
                ->add($this->get_inaccessible_lesson_display_field())
                ->add($this->get_progress_tracking_field())
                ->add($this->get_progress_forced_field())
            ))->set_popup(
                $this->settings_popup,
                $this->translate_with_prefix('sections.general.view_options')
            );
    }

    private function get_next_lesson_label_field(): Abstract_Setting_Field
    {
        return new Navigation_Label_Setting_Field(
            self::NAVIGATION_NEXT_LESSON_LABEL,
            $this->translate_with_prefix('sections.general.navigation_next_lesson_label.label'),
            null,
            $this->translate_with_prefix('sections.general.navigation_next_lesson_label.label.tooltip'),
            null,
            null,
            $this->get_default_navigation_next_lesson_label(),
            $this->translate_with_prefix('sections.general.navigation_next_lesson_label.lesson'),
            $this->translate_with_prefix('sections.general.navigation_next_lesson_label.lesson_title'),
        );
    }

    private function get_default_navigation_next_lesson_label(): string
    {
        $navigation_next_lesson_label = $this->app_settings->get(self::NAVIGATION_NEXT_LESSON_LABEL);

        if($navigation_next_lesson_label === 'lesson' || $navigation_next_lesson_label === 'lesson_title'){
            $navigation_next_lesson_label = $this->translate_with_prefix('sections.general.navigation_next_lesson_label.'.$navigation_next_lesson_label);
        }

        return sprintf( $this->translator->translate('course_editor.sections.general.view_options.default'), $navigation_next_lesson_label);
    }

    private function get_previous_label_field(): Abstract_Setting_Field
    {
        return new Navigation_Label_Setting_Field(
            self::NAVIGATION_PREVIOUS_LESSON_LABEL,
            $this->translate_with_prefix('sections.general.navigation_previous_lesson_label.label'),
            null,
            $this->translate_with_prefix('sections.general.navigation_previous_lesson_label.label.tooltip'),
            null,
            null,
            $this->get_default_previous_label(),
            $this->translate_with_prefix('sections.general.navigation_previous_lesson_label.lesson'),
            $this->translate_with_prefix('sections.general.navigation_previous_lesson_label.lesson_title'),
        );
    }

    private function get_default_previous_label(): string
    {
        $navigation_previous_lesson_label = $this->app_settings->get(self::NAVIGATION_PREVIOUS_LESSON_LABEL);

        if($navigation_previous_lesson_label === 'lesson' || $navigation_previous_lesson_label === 'lesson_title'){
            $navigation_previous_lesson_label = $this->translate_with_prefix('sections.general.navigation_previous_lesson_label.'.$navigation_previous_lesson_label);
        }

        return sprintf( $this->translator->translate('course_editor.sections.general.view_options.default'), $navigation_previous_lesson_label);
    }

    private function get_inaccessible_lesson_display_field(): Abstract_Setting_Field
    {
        return new Radio_View_Field(
            self::INACCESSIBLE_LESSON_DISPLAY,
            $this->translate_with_prefix('sections.general.inaccessible_lesson_display.label'),
            null,
            null,
            null,
            null,
            $this->get_default_inaccessible_lesson_display(),
            $this->get_inaccessible_lesson_display_options()
        );
    }

    private function get_default_inaccessible_lesson_display(): string
    {
        $inaccessible_lesson_display = $this->app_settings->get(self::INACCESSIBLE_LESSON_DISPLAY) ?? 'visible';

        $inaccessible_lesson_display = $this->translate_with_prefix('sections.general.inaccessible_lesson_display.'.$inaccessible_lesson_display);

        return sprintf( $this->translator->translate('course_editor.sections.general.view_options.default'), $inaccessible_lesson_display);
    }

    private function get_inaccessible_lesson_display_options(): array
    {
        return [
            'visible' => $this->translate_with_prefix('sections.general.inaccessible_lesson_display.visible'),
            'grayed' => $this->translate_with_prefix('sections.general.inaccessible_lesson_display.grayed'),
            'hidden' => $this->translate_with_prefix('sections.general.inaccessible_lesson_display.hidden')
        ];
    }

    private function get_progress_tracking_field(): Abstract_Setting_Field
    {
        return (new Select_Setting_Field(
            self::PROGRESS_TRACKING,
            $this->translate_with_prefix('sections.general.progress_tracking'),
            null,
            null,
            null,
            $this->get_progress_tracking_options()
        ))->set_related_feature(Packages::FEAT_PROGRESS_TRACKING);
    }

    private function get_progress_forced_field(): Abstract_Setting_Field
    {
        return (new Radio_View_Field(
            self::PROGRESS_FORCED,
            $this->translate_with_prefix('sections.general.progress_forced.label'),
            null,
            null,
            null,
            null,
            $this->get_default_progress_forced(),
            $this->get_progress_forced_options()
        ))->set_related_feature(Packages::FEAT_PROGRESS_TRACKING);
    }

    private function get_default_progress_forced(): string
    {
        $progress_forced = $this->app_settings->get(self::PROGRESS_FORCED) ? 'enabled' : 'disabled';

        return sprintf( $this->translator->translate('course_editor.sections.general.view_options.default'), $this->translate_with_prefix('sections.general.progress_forced.'.$progress_forced));
    }

    private function get_progress_forced_options(): array
    {
        return [
            'enabled' => $this->translate_with_prefix('sections.general.progress_forced.enabled'),
            'disabled' => $this->translate_with_prefix('sections.general.progress_forced.disabled')
        ];
    }

    private function get_disable_certificates_field(): Toggle_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::DISABLE_CERTIFICATES,
            $this->translate_with_prefix('sections.general.disable_certificates'),
            null,
            $this->translate_with_prefix('sections.general.disable_certificates.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_certificate_template_id_field())
                ->add($this->get_enable_certificate_numbering_field())
                ->add($this->get_certificate_numbering_pattern_field())
                ->add(
                    new Message($this->translator->translate('course.settings.certificate_number.explanation.text'))
                )
        ))->set_popup($this->settings_popup,
                $this->translate_with_prefix('sections.general.disable_certificates.popup.title')
        );
    }

    private function get_disable_email_subscription_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::DISABLE_EMAIL_SUBSCRIPTION,
            $this->translate_with_prefix('sections.general.disable_email_subscription')
        ))->change_visibility($this->are_renewals_enabled());
    }

    protected function get_recurring_payments_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::RECURRING_PAYMENTS,
            $this->translate_with_prefix('sections.general.recurring_payments_enabled')
        ))->change_visibility($this->any_enabled_gateway_supports_recurring_payments())
            ->set_related_feature(Packages::FEAT_RECURRING_PAYMENTS);
    }

    private function get_custom_purchase_links_field(): Abstract_Setting_Field
    {
        $field =  (new Text_Setting_Field(
            self::CUSTOM_PURCHASE_LINKS,
            $this->translate_with_prefix('sections.general.custom_purchase_link'),
            null,
            $this->translate_with_prefix('sections.general.custom_purchase_link.tooltip')
        ));

        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();

            if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                $results->add_error_message('settings.field.validation.invalid_url');
            }

            return $results;
        });

        $field->change_visibility($this->custom_purchase_links_helper->feature_is_active());

        return $field;

    }

    private function get_redirect_page_field(): Abstract_Setting_Field
    {
        return new Select_Setting_Field(
            self::REDIRECT_PAGE,
            $this->translate_with_prefix('sections.general.redirect_page'),
            null,
            $this->translate_with_prefix('sections.general.redirect_page.tooltip'),
            null,
            $this->get_all_pages()
        );
    }

    private function get_certificate_template_id_field(): Abstract_Setting_Field
    {
        return new Select_Setting_Field(
            self::CERTIFICATE_TEMPLATE_ID,
            $this->translate_with_prefix('sections.general.certificate_template_id'),
            null,
            $this->translate_with_prefix('sections.general.certificate_template_id.tooltip'),
            null,
            $this->get_all_certificates()
        );
    }

    private function get_enable_certificate_numbering_field(): Abstract_Setting_Field
    {
        return new Toggle_Setting_Field(
            self::ENABLE_CERTIFICATE_NUMBERING,
            $this->translate_with_prefix('sections.general.enable_certificate_numbering')
        );
    }

    private function get_certificate_numbering_pattern_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::CERTIFICATE_NUMBERING_PATTERN,
            $this->translate_with_prefix('sections.general.certificate_numbering_pattern')
        );
    }

    private function get_redirect_url_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::REDIRECT_URL,
            $this->translate_with_prefix('sections.general.redirect_url')
        );
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
        $course_id = $this->current_request->get_query_arg(Course_Editor_Page_Renderer::COURSE_ID_QUERY_ARG_NAME);

        $course_with_product = $this->courses_app_service->find_course(new Course_ID($course_id));

        if (!$course_with_product) {
            return false;
        }

        $product_id = $course_with_product->get_product_id()->to_int();

        $price_variants = $this->product_api->get_price_variants($product_id);

        if($price_variants->has_pricing_variants && !$price_variants->variable_prices){
            return true;
        }

        return false;
    }

    private function get_all_pages(): array
    {
        $all_pages = get_pages([
            'hierarchical' => false,
            'post_type' => 'page'
        ]);

        $all_pages_new = [$this->translate_with_prefix('sections.general.redirect_page.select.choose')];

        foreach ($all_pages as $page) {
            if ('publish' !== $page->post_status || $this->is_course_page($page->ID)) {
                continue;
            }
            $all_pages_new[$page->ID] = $page->post_title;
        }

        return $all_pages_new;
    }

    private function is_course_page(int $post_id): bool
    {
        return !empty(get_post_meta($post_id, self::RESTRICTED_TO, true));
    }

    private function get_all_certificates(): array
    {
        $all_certificates = [$this->translate_with_prefix('sections.general.certificate_template_id.select.default')];

        foreach($this->certificates->find_all() as $certificate){
            $all_certificates[$certificate->get_id()] = $certificate->get_name();
        }

        return $all_certificates;
    }

    private function are_renewals_enabled(): bool
    {
        $renewal = $this->options->get('bmpj_eddpc_renewal');

        if(!$renewal){
          return false;
        }

        return true;
    }

    private function get_hour_options(): array
    {
        $hours = [];

        for ($i = 0; $i <= 23; $i++) {
            $v = sprintf('%02d', $i);
            $hours[$v] = $v;
        }
        return $hours;
    }

    private function get_minute_options(): array
    {
        $hours = [];

        for ($i = 0; $i <= 59; $i++) {
            $v = sprintf('%02d', $i);
            $hours[$v] = $v;
        }
        return $hours;
    }

    private function get_progress_tracking_options(): array
    {
        return [
            'on' => $this->translate_with_prefix('sections.general.progress_tracking.option.on'),
            'off' => $this->translate_with_prefix('sections.general.progress_tracking.option.off'),
        ];
    }

    private function get_access_time_unit_type_options(): array
    {
        return [
            'minutes' => $this->translate_with_prefix('sections.general.access_time_unit.option.minutes'),
            'hours' => $this->translate_with_prefix('sections.general.access_time_unit.option.hours'),
            'days' => $this->translate_with_prefix('sections.general.access_time_unit.option.days'),
            'months' => $this->translate_with_prefix('sections.general.access_time_unit.option.months'),
            'years' => $this->translate_with_prefix('sections.general.access_time_unit.option.years')
        ];
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

    private function get_course_id(): int
    {
        return (int)$this->current_request->get_query_arg(Course_Editor_Page_Renderer::COURSE_ID_QUERY_ARG_NAME);
    }
}