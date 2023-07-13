<?php

namespace bpmj\wpidea\admin\settings\core\configuration;

use bpmj\wpidea\admin\settings\core\collections\{Additional_Fields_Collection, Fields_Collection};
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\{Abstract_Setting_Field,
	Configure_Popup_Setting_Field,
	Custom_Sorting_Field,
	Navigation_Label_Setting_Field,
	relation\Field_Relation,
	Select_Setting_Field,
	Toggle_Setting_Field};
use bpmj\wpidea\controllers\admin\Admin_Settings_Fields_Ajax_Controller;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\Packages;
use bpmj\wpidea\packages\Interface_Packages_API;
use bpmj\wpidea\sales\product\api\Interface_Product_API;
use bpmj\wpidea\sales\product\service\Information_About_Available_Quantities;

class Design_Settings_Group extends Abstract_Settings_Group
{
    public const NAME = 'design';
    public const LIST_EXCERPT = 'list_excerpt';
    public const LIST_BUY_BUTTON = 'list_buy_button';
    public const LIST_PAGINATION = 'list_pagination';
    public const LIST_DETAILS_BUTTON = 'list_details_button';
    public const DISPLAY_CATEGORIES = 'display_categories';
    public const DISPLAY_TAGS = 'display_tags';
    public const PROGRESS_TRACKING = 'progress_tracking';
    public const AUTO_PROGRESS = 'auto_progress';

    public const DISPLAY_AUTHOR_INFO = 'display_author_info';
    public const ENABLE_RESPONSIVE_VIDEOS = 'enable_responsive_videos';
    public const PROGRESS_FORCED = 'progress_forced';
    public const INACCESSIBLE_LESSON_DISPLAY = 'inaccessible_lesson_display';
    public const NAVIGATION_NEXT_LESSON_LABEL = 'navigation_next_lesson_label';
    public const NAVIGATION_PREVIOUS_LESSON_LABEL = 'navigation_previous_lesson_label';
    public const LIST_ORDER = 'list_order';
    public const LIST_PRICE = 'list_price';
    public const LIST_ORDERBY = 'list_orderby';
	private const CUSTOM_SORTING = 'custom_sorting_order';
	private const LIST_ORDERBY_VALUE_CUSTOM = 'custom_order';
    public const SHOW_AVAILABLE_QUANTITIES = 'show_available_quantities';
    public const AVAILABLE_QUANTITIES_FORMAT = 'available_quantities_format';

	private Interface_Packages_API $packages_api;
	private Interface_Product_API $product_api;

	public function __construct(
		Interface_Packages_API $packages_api,
		Interface_Product_API $product_api
	)
	{

		$this->packages_api = $packages_api;
		$this->product_api = $product_api;
	}


	public function get_name(): string
    {
        return self::NAME;
    }

    public function register_fields(): void
    {
        $this->add_fieldset(
            $this->translator->translate('settings.sections.design.fieldset.course_view_settings'),
            (new Fields_Collection())
                ->add($this->get_previous_and_next_lesson_label_fields())
                ->add($this->get_inaccessible_lesson_display_field())
                ->add((new Toggle_Setting_Field(self::PROGRESS_TRACKING,
                    $this->translator->translate('settings.sections.design.progress_tracking.label'),
                    null,
                    $this->translator->translate('settings.sections.design.progress_tracking.label.tooltip')))
	                ->set_related_feature(Packages::FEAT_PROGRESS_TRACKING)
                )
                ->add((new Toggle_Setting_Field(self::PROGRESS_FORCED,
                    $this->translator->translate('settings.sections.design.progress_forced.label'),
                    null,
                    $this->translator->translate('settings.sections.design.progress_forced.label.tooltip')))
	                ->set_related_feature(Packages::FEAT_PROGRESS_TRACKING)
                )
                ->add((new Toggle_Setting_Field(self::AUTO_PROGRESS,
                    $this->translator->translate('settings.sections.design.auto_progress.label'),
                    null,
                    $this->translator->translate('settings.sections.design.auto_progress.label.tooltip')))
	                ->set_related_feature(Packages::FEAT_PROGRESS_TRACKING)
                )
                ->add(new Toggle_Setting_Field(self::DISPLAY_AUTHOR_INFO,
                    $this->translator->translate('settings.sections.design.display_author_info.label'),
                    null,
                    $this->translator->translate('settings.sections.design.display_author_info.label.tooltip')))
                ->add(new Toggle_Setting_Field(self::ENABLE_RESPONSIVE_VIDEOS,
                        $this->translator->translate('settings.sections.design.responsive_video.label'),
                        null,
                        $this->translator->translate('settings.sections.design.responsive_video.label.tooltip'))
                ));

        $this->add_fieldset(
            $this->translator->translate('settings.sections.design.fieldset.directory_settings'),
                (new Fields_Collection())
                ->add(new Toggle_Setting_Field(self::LIST_PRICE,
                    $this->translator->translate('settings.sections.design.list_price.label'),
                    null,
                    $this->translator->translate('settings.sections.design.list_price.label.tooltip')))
                ->add(new Toggle_Setting_Field(self::LIST_EXCERPT,
                    $this->translator->translate('settings.sections.design.list_excerpt.label'),
                    null,
                    $this->translator->translate('settings.sections.design.list_excerpt.label.tooltip')))
                ->add(new Toggle_Setting_Field(self::LIST_BUY_BUTTON,
                    $this->translator->translate('settings.sections.design.list_buy_button.label'),
                    null,
                    $this->translator->translate('settings.sections.design.list_buy_button.label.tooltip')))
                ->add(new Toggle_Setting_Field(self::LIST_PAGINATION,
                    $this->translator->translate('settings.sections.design.list_pagination.label'),
                    null,
                    $this->translator->translate('settings.sections.design.list_pagination.label.tooltip')))
                ->add(new Toggle_Setting_Field(self::LIST_DETAILS_BUTTON,
                    $this->translator->translate('settings.sections.design.list_details_button.label'),
                    null,
                    $this->translator->translate('settings.sections.design.list_details_button.label.tooltip')))
                ->add($this->get_show_available_quantities_field())
                ->add(new Toggle_Setting_Field(self::DISPLAY_CATEGORIES,
                    $this->translator->translate('settings.sections.design.display_categories.label'),
                    null,
                    $this->translator->translate('settings.sections.design.display_categories.label.tooltip')))
                ->add(new Toggle_Setting_Field(self::DISPLAY_TAGS,
                    $this->translator->translate('settings.sections.design.display_tags.label'),
                    null,
                    $this->translator->translate('settings.sections.design.display_tags.label.tooltip')))
                ->add($this->get_default_view_field())
                ->add($this->get_list_orderby_options())
                ->add($this->get_custom_sorting_field())
                ->add($this->get_list_order_field())
            );
    }

    private function get_show_available_quantities_field(): Abstract_Setting_Field
    {
	    $field = new Toggle_Setting_Field(
		    self::SHOW_AVAILABLE_QUANTITIES,
		    $this->translator->translate( 'settings.sections.design.show_available_quantities.label' ),
		    null,
		    $this->translator->translate( 'settings.sections.design.show_available_quantities.tooltip' ),
		    ( new Additional_Fields_Collection() )
			    ->add( $this->get_available_quantities_format_field() )
	    );

		$field->set_popup(
			$this->settings_popup,
		    $this->translator->translate('settings.sections.design.show_available_quantities.label')
	    );

		$field->set_related_feature(Packages::FEAT_AVAILABLE_QUANTITIES);

	    return $field;
    }

    private function get_available_quantities_format_field(): Select_Setting_Field
    {
        return new Select_Setting_Field(
            self::AVAILABLE_QUANTITIES_FORMAT,
            $this->translator->translate('settings.sections.design.available_quantities_format.label'),
            null,
            null,
            null,
            [
                Information_About_Available_Quantities::AVAILABLE_QUANTITIES_FORMAT_X_OF_Y => $this->translator->translate('settings.sections.design.available_quantities_format.' . Information_About_Available_Quantities::AVAILABLE_QUANTITIES_FORMAT_X_OF_Y),
                Information_About_Available_Quantities::AVAILABLE_QUANTITIES_FORMAT_X => $this->translator->translate('settings.sections.design.available_quantities_format.' . Information_About_Available_Quantities::AVAILABLE_QUANTITIES_FORMAT_X)
            ]
        );
    }

    private function get_inaccessible_lesson_display_field(): Select_Setting_Field
    {
        return new Select_Setting_Field(
            self::INACCESSIBLE_LESSON_DISPLAY,
            $this->translator->translate('settings.sections.design.inaccessible_lesson_display.label'),
            null,
            $this->translator->translate('settings.sections.design.inaccessible_lesson_display.label.tooltip'),
            null,
            [
                'visible' => $this->translator->translate('settings.sections.design.inaccessible_lesson_display.visible'),
                'grayed' => $this->translator->translate('settings.sections.design.inaccessible_lesson_display.grayed'),
                'hidden' => $this->translator->translate('settings.sections.design.inaccessible_lesson_display.hidden')
            ]
        );
    }

    private function get_previous_and_next_lesson_label_fields(): Configure_Popup_Setting_Field
    {
        $additional_fields = new Additional_Fields_Collection();
        $additional_fields->add(new Navigation_Label_Setting_Field(
            self::NAVIGATION_NEXT_LESSON_LABEL,
            $this->translator->translate('settings.sections.design.navigation_next_lesson_label.label'),
            null,
            $this->translator->translate('settings.sections.design.navigation_next_lesson_label.label.tooltip'),
            null,
            null,
            null,
            $this->translator->translate('settings.sections.design.navigation_next_lesson_label.text_previous'),
            $this->translator->translate('settings.sections.design.navigation_next_lesson_label.title_previous'),
        ));
        $additional_fields->add(new Navigation_Label_Setting_Field(
            self::NAVIGATION_PREVIOUS_LESSON_LABEL,
            $this->translator->translate('settings.sections.design.navigation_previous_lesson_label.label'),
            null,
            $this->translator->translate('settings.sections.design.navigation_previous_lesson_label.label.tooltip'),
            null,
            null,
            null,
            $this->translator->translate('settings.sections.design.navigation_next_lesson_label.text_next'),
            $this->translator->translate('settings.sections.design.navigation_next_lesson_label.title_next'),
        ));
        $field = new Configure_Popup_Setting_Field(
            'design_labels_popup',
            $this->translator->translate('settings.sections.design.popup.label'),
            null,
            $this->translator->translate('settings.sections.design.popup.label.tooltip'),
            $additional_fields
        );
        $field->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.design.course_view_settings.label'));
        return $field;
    }

	private function get_list_orderby_options(): Select_Setting_Field
    {
	    $has_access_to_feature = $this->packages_api->has_access_to_feature( Packages::FEAT_PRODUCTS_CUSTOM_SORTING_ORDER );
	    $options = [
            'post_date' => $this->translator->translate('settings.sections.design.list_orderby.post_date'),
            'id' => $this->translator->translate('settings.sections.design.list_orderby.id'),
            'title' => $this->translator->translate('settings.sections.design.list_orderby.title'),
            'price' => $this->translator->translate('settings.sections.design.list_orderby.price'),
            'random' => $this->translator->translate('settings.sections.design.list_orderby.random'),
            self::LIST_ORDERBY_VALUE_CUSTOM => $this->translator->translate($has_access_to_feature ? 'settings.sections.design.list_orderby.custom' : 'settings.sections.design.list_orderby.custom.no_access'),
        ];

	    $field = new Select_Setting_Field(
		    self::LIST_ORDERBY,
		    $this->translator->translate('settings.sections.design.list_orderby.label'),
		    null,
		    $this->translator->translate( 'settings.sections.design.list_orderby.label.tooltip' ),
		    null,
		    $options
	    );

	    if(!$has_access_to_feature) {
			$field->disable_option_by_value(self::LIST_ORDERBY_VALUE_CUSTOM);
		}

	    return $field;
    }

	private function get_list_order_field(): Select_Setting_Field
    {
        $options = [
            'DESC' => $this->translator->translate('desc'),
            'ASC' => $this->translator->translate('asc')
        ];

        $relation = Field_Relation::create(self::LIST_ORDERBY, Field_Relation::TYPE_DEPENDS_ON_SELECT_VALUE_NOT_EQUALS, self::LIST_ORDERBY_VALUE_CUSTOM);

        return (new Select_Setting_Field(
            self::LIST_ORDER,
            $this->translator->translate('settings.sections.design.list_sort_type.label'),
            null,
            $this->translator->translate('settings.sections.design.list_sort_type.label.tooltip'),
            null,
            $options
        ))->set_relation($relation);
    }

	private function get_custom_sorting_field(): Custom_Sorting_Field
	{
		$relation = Field_Relation::create(self::LIST_ORDERBY, Field_Relation::TYPE_DEPENDS_ON_SELECT_VALUE_EQUALS, self::LIST_ORDERBY_VALUE_CUSTOM);
		return (new Custom_Sorting_Field(self::CUSTOM_SORTING, $this->get_save_custom_order_endpoint(), $this->get_products()))
			->set_relation($relation);
	}

	private function get_save_custom_order_endpoint(): string
	{
		return $this->url_generator->generate(Admin_Settings_Fields_Ajax_Controller::class, 'save_field_value', [
			Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
		]);
	}

	private function get_default_view_field(): Select_Setting_Field
    {
        $options = [
            'grid' => $this->translator->translate('settings.sections.design.default_view.grid'),
            'grid_small' => $this->translator->translate('settings.sections.design.default_view.grid_small'),
            'list' => $this->translator->translate('settings.sections.design.default_view.list')
        ];
        return (new Select_Setting_Field(
            'default_view',
            $this->translator->translate('settings.sections.design.default_view.label'),
            null,
            $this->translator->translate('settings.sections.design.default_view.label.tooltip'),
            null,
            $options
        ));
    }

    private function get_products(): array
    {
        $result = [];

        foreach ($this->product_api->find_all() as $product) {
            $result[] = [
                'id' => $product->get_id(),
                'product' => htmlspecialchars($product->get_name(), ENT_QUOTES),
            ];
        }

        return $result;
    }
}