<?php

namespace bpmj\wpidea\admin\settings\core\configuration;

use bpmj\wpidea\admin\settings\core\collections\{
    Additional_Fields_Collection,
    Fields_Collection
};
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\{Configure_Popup_Setting_Field,
    Select_Setting_Field,
    Text_Setting_Field,
    Toggle_Setting_Field,
    Wysiwyg_Setting_Field
};

class Cart_Settings_Group extends Abstract_Settings_Group
{
    public const NAME = 'cart';
    public const SHOW_EMAIL_2_ON_CHECKOUT = 'show_email2_on_checkout';
    public const SHOW_COMMENT_FIELD_ON_CHECKOUT = 'show_comment_field_on_checkout';
    public const AGREE_LABEL = 'agree_label';
    public const SCARLET_CART_ADDITIONAL_INFO_1_TITLE = 'scarlet_cart_additional_info_1_title';
    public const SCARLET_CART_ADDITIONAL_INFO_1_DESC = 'scarlet_cart_additional_info_1_desc';
    public const CART_POPUP_2 = 'cart_popup_2';
    public const SCARLET_CART_ADDITIONAL_INFO_2_TITLE = 'scarlet_cart_additional_info_2_title';
    public const SCARLET_CART_ADDITIONAL_INFO_2_DESC = 'scarlet_cart_additional_info_2_desc';
    public const CART_POPUP_3 = 'cart_popup_3';
    public const ADDITIONAL_CHECKBOX_DESCRIPTION = 'additional_checkbox_description';
    public const ADDITIONAL_CHECKBOX_REQUIRED = 'additional_checkbox_required';
    public const SHOW_ADDITIONAL_CHECKBOX_ON_CHECKOUT = 'show_additional_checkbox_on_checkout';
    public const ADDITIONAL_CHECKBOX_2_DESCRIPTION = 'additional_checkbox2_description';
    public const ADDITIONAL_CHECKBOX_2_REQUIRED = 'additional_checkbox2_required';
    public const SHOW_ADDITIONAL_CHECKBOX_2_ON_CHECKOUT = 'show_additional_checkbox2_on_checkout';
    public const EDD_ID_HIDE_FNAME = 'edd_id_hide_fname';
    public const LAST_NAME_REQUIRED = 'last_name_required';
    public const EDD_ID_HIDE_LNAME = 'edd_id_hide_lname';
    public const SHOW_PHONE_NUMBER_FIELD_ON_CHECKOUT = 'show_phone_number_field_on_checkout';
    public const USE_EXPERIMENTAL_CART_VIEW = 'use_experimental_cart_view';
    public const EXPERIMENTAL_CART_BUTTON_TEXT = 'experimental_cart_button_text';

    public function get_name(): string
    {
        return self::NAME;
    }

    public function register_fields(): void
    {
        $this->add_fieldset(
            $this->translator->translate('settings.sections.cart.cart_view'),
            (new Fields_Collection())
                ->add($this->get_cart_view_select_field())
                ->add($this->get_cart_view_go_back_button_toggle_field())
        );

        $this->add_fieldset(
            $this->translator->translate('settings.sections.cart.data_in_form'),
            (new Fields_Collection())
                ->add(new Toggle_Setting_Field(
                    self::SHOW_EMAIL_2_ON_CHECKOUT,
                    $this->translator->translate('settings.sections.cart.show_email2_on_checkout'),
                    null,
                    $this->translator->translate('settings.sections.cart.show_email2_on_checkout.tooltip')))
                ->add($this->get_hide_name_field())
                ->add($this->get_hide_last_name_field())
                ->add($this->get_phone_field())
                ->add(new Toggle_Setting_Field(
                    self::SHOW_COMMENT_FIELD_ON_CHECKOUT,
                    $this->translator->translate('settings.sections.cart.show_comment_field'),
                    null,
                    $this->translator->translate('settings.sections.cart.show_comment_field.tooltip')
                ))
        );

        $this->add_fieldset(
            $this->translator->translate('settings.sections.cart.additional_checkboxes'),
            (new Fields_Collection())
                ->add($this->get_first_checkbox_field())
                ->add($this->get_second_checkbox_field())
        );

        $this->add_fieldset(
            $this->translator->translate('settings.sections.cart.fieldset.statute'),
            (new Fields_Collection())
                ->add($this->get_additional_agree_label_field())
        );


        $this->add_fieldset(
            $this->translator->translate('settings.sections.cart.sidebar'),
            (new Fields_Collection())
                ->add($this->get_additional_informations_1_field())
                ->add($this->get_additional_informations_2_field())
        );
    }

    private function get_cart_view_select_field(): Select_Setting_Field
    {
        return new Select_Setting_Field(
            self::USE_EXPERIMENTAL_CART_VIEW,
            $this->translator->translate('settings.sections.cart.cart_view'),
            null,
            null,
            null,
            [
                'standard' => $this->translator->translate('settings.sections.cart.cart_view.standard'),
                'experimental' => $this->translator->translate('settings.sections.cart.cart_view.experimental')
            ]
        );
    }

    private function get_additional_agree_label_field(): Text_Setting_Field
    {
        return new Text_Setting_Field(
            self::AGREE_LABEL,
            $this->translator->translate('settings.sections.cart.agree_label'),
            null,
            $this->translator->translate('settings.sections.cart.agree_label.tooltip')
        );
    }

    private function get_additional_informations_1_field(): Configure_Popup_Setting_Field
    {
        $additional_fields = new Additional_Fields_Collection();
        $additional_fields->add(
            new Text_Setting_Field(
                self::SCARLET_CART_ADDITIONAL_INFO_1_TITLE,
                $this->translator->translate('settings.sections.cart.info_1_title'),
                null,
                $this->translator->translate('settings.sections.cart.info_1_title.tooltip')
            )
        )->add(
            new Wysiwyg_Setting_Field(
                self::SCARLET_CART_ADDITIONAL_INFO_1_DESC,
                $this->translator->translate('settings.sections.cart.info_1_desc'),
                null,
                $this->translator->translate('settings.sections.cart.info_1_desc.tooltip')
            )
        );
        $field = new Configure_Popup_Setting_Field(
            self::CART_POPUP_2,
            $this->translator->translate('settings.sections.cart.cart_popup_2'),
            null,
            $this->translator->translate('settings.sections.cart.cart_popup_2.tooltip'),
            $additional_fields
        );
        $field->set_popup($this->settings_popup, $this->translator->translate('settings'));
        return $field;
    }

    private function get_additional_informations_2_field(): Configure_Popup_Setting_Field
    {
        $additional_fields = new Additional_Fields_Collection();
        $additional_fields->add(
            new Text_Setting_Field(
                self::SCARLET_CART_ADDITIONAL_INFO_2_TITLE,
                $this->translator->translate('settings.sections.cart.info_2_title'),
                null,
                $this->translator->translate('settings.sections.cart.info_2_title.tooltip')
            )
        )->add(
            new Wysiwyg_Setting_Field(
                self::SCARLET_CART_ADDITIONAL_INFO_2_DESC,
                $this->translator->translate('settings.sections.cart.info_2_desc'),
                null,
                $this->translator->translate('settings.sections.cart.info_2_desc.tooltip')
            )
        );
        $field = new Configure_Popup_Setting_Field(
            self::CART_POPUP_3,
            $this->translator->translate('settings.sections.cart.cart_popup_3'),
            null,
            $this->translator->translate('settings.sections.cart.cart_popup_3.tooltip'),
            $additional_fields
        );
        $field->set_popup($this->settings_popup, $this->translator->translate('settings'));
        return $field;
    }

    private function get_first_checkbox_field(): Toggle_Setting_Field
    {
        $additional_fields = new Additional_Fields_Collection();
        $additional_fields->add(
            new Text_Setting_Field(
                self::ADDITIONAL_CHECKBOX_DESCRIPTION,
                $this->translator->translate('settings.sections.cart.acd'),
                null,
                $this->translator->translate('settings.sections.cart.acd.tooltip')
            )
        )->add(new Toggle_Setting_Field(
            self::ADDITIONAL_CHECKBOX_REQUIRED,
            $this->translator->translate('settings.sections.cart.acdr'),
            null,
            $this->translator->translate('settings.sections.cart.acdr.tooltip')
        ));
        $field = new Toggle_Setting_Field(
            self::SHOW_ADDITIONAL_CHECKBOX_ON_CHECKOUT,
            $this->translator->translate('settings.sections.cart.ac'),
            null,
            $this->translator->translate('settings.sections.cart.ac.tooltip'),
            $additional_fields
        );
        $field->set_popup($this->settings_popup, $this->translator->translate('settings'));
        return $field;
    }

    private function get_second_checkbox_field(): Toggle_Setting_Field
    {
        $additional_fields = new Additional_Fields_Collection();
        $additional_fields->add(
            new Text_Setting_Field(
                self::ADDITIONAL_CHECKBOX_2_DESCRIPTION,
                $this->translator->translate('settings.sections.cart.acd2'),
                null,
                $this->translator->translate('settings.sections.cart.acd2.tooltip')
            )
        )->add(new Toggle_Setting_Field(
            self::ADDITIONAL_CHECKBOX_2_REQUIRED,
            $this->translator->translate('settings.sections.cart.acdr2'),
            null,
            $this->translator->translate('settings.sections.cart.acdr2.tooltip')
        ));
        $field = new Toggle_Setting_Field(
            self::SHOW_ADDITIONAL_CHECKBOX_2_ON_CHECKOUT,
            $this->translator->translate('settings.sections.cart.ac2'),
            null,
            $this->translator->translate('settings.sections.cart.ac2.tooltip'),
            $additional_fields
        );
        $field->set_popup($this->settings_popup, $this->translator->translate('settings'));
        return $field;
    }

    private function get_hide_name_field(): Toggle_Setting_Field
    {
        return new Toggle_Setting_Field(
            self::EDD_ID_HIDE_FNAME,
            $this->translator->translate('settings.sections.cart.hide_fname'),
            null,
            $this->translator->translate('settings.sections.cart.hide_fname.tooltip')
        );
    }

    private function get_hide_last_name_field(): Toggle_Setting_Field
    {
        $additional_fields = new Additional_Fields_Collection();
        $additional_fields->add(
            new Toggle_Setting_Field(
                self::LAST_NAME_REQUIRED,
                $this->translator->translate('settings.sections.cart.last_name_required')
            )
        );
        $field = new Toggle_Setting_Field(
            self::EDD_ID_HIDE_LNAME,
            $this->translator->translate('settings.sections.cart.hide_lname'),
            null,
            $this->translator->translate('settings.sections.cart.hide_lname.tooltip'),
            $additional_fields
        );
        $field->set_popup($this->settings_popup, $this->translator->translate('settings'));
        return $field;
    }

    private function get_phone_field(): Toggle_Setting_Field
    {
        $additional_fields = new Additional_Fields_Collection();
        $additional_fields->add(
            new Toggle_Setting_Field(
                'phone_number_required_on_checkout',
                $this->translator->translate('settings.sections.cart.phone_required')
            )
        );
        $field = new Toggle_Setting_Field(
            self::SHOW_PHONE_NUMBER_FIELD_ON_CHECKOUT,
            $this->translator->translate('settings.sections.cart.enable_field_phone'),
            null,
            $this->translator->translate('settings.sections.cart.enable_field_phone.tooltip'),
            $additional_fields
        );
        $field->set_popup($this->settings_popup, $this->translator->translate('settings'));
        return $field;
    }

    private function get_cart_view_go_back_button_toggle_field(): Toggle_Setting_Field
    {
        $additional_fields = new Additional_Fields_Collection();
        $additional_fields->add(
            new Text_Setting_Field(
                self::EXPERIMENTAL_CART_BUTTON_TEXT,
                $this->translator->translate('settings.sections.cart.cart_view.go_back_button_text')
            )
        )->add(
            new Text_Setting_Field(
                'experimental_cart_button_url',
                $this->translator->translate('settings.sections.cart.cart_view.go_back_button_url')
            )
        );

        $field = new Toggle_Setting_Field(
            'experimental_cart_go_back_button_enabled',
            $this->translator->translate('settings.sections.cart.cart_view.go_back_button'),
            null,
            null,
            $additional_fields
        );
        $field->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.cart.cart_view.go_back_button'));

        return $field;
    }
}