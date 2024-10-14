<?php

namespace bpmj\wpidea\admin\settings\core\configuration;

use bpmj\wpidea\admin\settings\core\collections\{
    Additional_Fields_Collection,
    Fields_Collection
};
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\{Abstract_Setting_Field,
    Message,
    Section_Heading,
    Text_Setting_Field,
    Toggle_Setting_Field,
    Checkbox_Setting_Field,
    Select_Setting_Field,
    Configure_Popup_Setting_Field};

class Payments_Settings_Group extends Abstract_Settings_Group
{
    public const TEST_MODE = 'test_mode';
    public const TEST_MODE_OPTIONS_GROUP = 'test_mode_options_group';
    public const DEFAULT_GATEWAY = 'default_gateway';
    public const DISPLAY_PAYMENT_METHODS_AS_ICONS = 'display_payment_methods_as_icons';
    public const TEST_PAYMENT_GATE = 'gateway_manual';
    public const TPAY_PAYMENT_GATE = 'gateway_tpay_gateway';
    public const TPAY_ID = 'tpay_id';
    public const TPAY_PIN = 'tpay_pin';
    public const TPAY_CARDS_API_KEY = 'tpay_cards_api_key';
    public const TPAY_CARDS_API_PASSWORD = 'tpay_cards_api_password';
    public const TPAY_CARDS_VERIFICATION_CODE = 'tpay_cards_verification_code';
    public const TPAY_RECURRENCE_ALLOW_STANDARD_PAYMENTS = 'tpay_recurrence_allow_standard_payments';
    public const TPAY_CHECKOUT_LABEL = 'tpay_checkout_label';

    public const PAYU_PAYMENT_GATE = 'gateway_payu';
    public const PAYU_API_TYPE = 'payu_api_type';
    public const PAYU_POS_ID = 'payu_pos_id';
    public const PAYU_POS_AUTH_KEY = 'payu_pos_auth_key';
    public const PAYU_KEY1 = 'payu_key1';
    public const PAYU_KEY2 = 'payu_key2';
    public const PAYU_RETURN_URL_FAILURE = 'payu_return_url_failure';
    public const PAYU_RETURN_URL_SUCCESS = 'payu_return_url_success';
    public const PAYU_RETURN_URL_REPORTS = 'payu_return_url_reports';

    public const PAYU_API_ENVIRONMENT = 'payu_api_environment';
    public const PAYU_RECURRENCE = 'payu_recurrence_allow_standard_payments';
    public const PAYU_ENABLE_DEBUG = 'payu_enable_debug';
    public const PAYU_CHECKOUT_LABEL = 'payu_checkout_label';

    public const PRZELEWY24_PAYMENT_GATE = 'gateway_przelewy24_gateway';
    public const PRZELEWY24_ID = 'przelewy24_id';
    public const PRZELEWY24_PIN = 'przelewy24_pin';
    public const PRZELEWY24_CHECKOUT_LABEL = 'przelewy24_checkout_label';

    public const DOTPAY_PAYMENT_GATE = 'gateway_dotpay_gateway';
    public const DOTPAY_ID = 'dotpay_id';
    public const DOTPAY_PIN = 'dotpay_pin';
    public const DOTPAY_ONLINE_TRANSFER = 'dotpay_onlinetransfer';
    public const DOTPAY_CHECKOUT_LABEL = 'dotpay_checkout_label';

    public const PAYNOW_PAYMENT_GATE = 'gateway_paynow_gateway';
    public const PAYNOW_ACCESS_KEY = 'paynow_access_key';
    public const PAYNOW_SIGNATURE_KEY = 'paynow_signature_key';
    public const PAYNOW_ENVIRONMENT = 'paynow_environment';
    public const PAYNOW_CHECKOUT_LABEL = 'paynow_checkout_label';

    public const STRIPE_PAYMENT_GATE = 'gateway_stripe';
    public const STRIPE_TEST_SECRET_KEY = 'test_secret_key';
    public const STRIPE_TEST_PUBLISHABLE_KEY = 'test_publishable_key';
    public const STRIPE_LIVE_SECRET_KEY = 'live_secret_key';
    public const STRIPE_LIVE_PUBLISHABLE_KEY = 'live_publishable_key';
    public const STRIPE_CHECKOUT_LABEL = 'stripe_checkout_label';

    public const PAYPAL_PAYMENT_GATE = 'gateway_paypal';
    public const PAYPAL_EMAIL = 'paypal_email';
    public const PAYPAL_PAGE_STYLE = 'paypal_page_style';
    public const PAYPAL_DISABLE_VERIFICATION = 'disable_paypal_verification';
    public const PAYPAL_CHECKOUT_LABEL = 'paypal_checkout_label';

    public const COINBASE_PAYMENT_GATE = 'gateway_coinbase';
    public const COINBASE_API_KEY = 'edd_coinbase_api_key';
    public const COINBASE_CHECKOUT_LABEL = 'coinbase_checkout_label';

    public const TRANSFERS_PAYMENT_GATE = 'gateway_przelewy_gateway';
    public const TRANSFERS_NAME = 'edd_przelewy_name';
    public const TRANSFERS_ADDRESS = 'edd_przelewy_address';
    public const TRANSFERS_ACCOUNT_NUMBER = 'edd_przelewy_account_number';
    public const TRANSFERS_CHECKOUT_LABEL = 'przelewy_checkout_label';

    public function get_name(): string
    {
        return 'payments';
    }

    public function register_fields(): void
    {
        $this->add_fieldset(
            $this->translator->translate('settings.sections.payments.fieldset.configuration_and_tests'),
            (new Fields_Collection())
                ->add($this->get_payments_configuration_field())
                ->add($this->get_test_payment_gate_field())
        );
        $this->add_field(new Section_Heading(
                             $this->translator->translate('settings.sections.payments.section.payment_gates')
        ));
        $this->add_fieldset(
            $this->translator->translate('settings.sections.payments.fieldset.bank_and_recurring_payments'),
            (new Fields_Collection())
                ->add($this->get_tpay_gate_field())
                ->add($this->get_payu_gate_field())
        );
        $this->add_fieldset(
            $this->translator->translate('settings.sections.payments.fieldset.bank_payments'),
            (new Fields_Collection())
                ->add($this->get_przelewy24_gate_field())
                ->add($this->get_dotpay_gate_field())
                ->add($this->get_paynow_gate_field())
        );
        $this->add_fieldset(
            $this->translator->translate('settings.sections.payments.fieldset.other_payments'),
            (new Fields_Collection())
                ->add($this->get_stripe_gate_field())
                ->add($this->get_paypal_gate_field())
                ->add($this->get_coinbase_gate_field())
                ->add($this->get_transfers_gate_field())
        );
    }

    public function get_payments_configuration_field():  Configure_Popup_Setting_Field
    {
        return (new Configure_Popup_Setting_Field(
            self::TEST_MODE_OPTIONS_GROUP,
            $this->translator->translate('settings.sections.payments.payment_settings'),
            null,
            $this->translator->translate('settings.sections.payments.payment_settings.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_test_mode_field())
                ->add($this->get_default_gateway_field())
                ->add($this->get_display_payment_methods_as_icons_field())
        ))->set_popup($this->settings_popup,
                      $this->translator->translate('settings.sections.payments.payment_settings'));
    }

    private function get_test_mode_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::TEST_MODE,
            $this->translator->translate('settings.sections.payments.test_mode'),
            null,
            $this->translator->translate('settings.sections.payments.test_mode.tooltip')
        );
    }

    private function get_default_gateway_field(): Abstract_Setting_Field
    {
        $gateways = [];
        if (function_exists('edd_get_payment_gateways')) {
            $gateways = edd_get_payment_gateways();
        }

        $options = [];
        foreach ($gateways as $key => $gateway) {
            $options[edd_sanitize_key( $key )] = esc_html( $gateway['admin_label'] );
        }

        return new Select_Setting_Field(
            self::DEFAULT_GATEWAY,
            $this->translator->translate('settings.sections.payments.default_gateway'),
            null,
            $this->translator->translate('settings.sections.payments.default_gateway.tooltip'),
            null,
            $options
        );
    }

    private function get_display_payment_methods_as_icons_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::DISPLAY_PAYMENT_METHODS_AS_ICONS,
            $this->translator->translate('settings.sections.payments.display_payment_methods_as_icons'),
            null,
            $this->translator->translate('settings.sections.payments.display_payment_methods_as_icons.tooltip')
        );
    }

    private function get_test_payment_gate_field(): Abstract_Setting_Field
    {
        return new Toggle_Setting_Field(
            self::TEST_PAYMENT_GATE,
            $this->translator->translate('settings.sections.payments.test_payment_gate')
        );
    }

    private function get_tpay_gate_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::TPAY_PAYMENT_GATE,
            $this->translator->translate('settings.sections.payments.tpay_payment_gate'),
            null,
            $this->translator->translate('settings.sections.payments.tpay_payment_gate.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_tpay_id_field())
                ->add($this->get_tpay_pin_field())
                ->add($this->get_tpay_cards_api_key_field())
                ->add($this->get_tpay_cards_api_password_field())
                ->add($this->get_tpay_cards_verification_code_field())
                ->add($this->get_tpay_recurrence_allow_standard_payments_field())
                ->add($this->get_tpay_checkout_label_field())
        ))->set_popup($this->settings_popup,
                      $this->translator->translate('settings.sections.payments.tpay_payment_gate'));
    }

    private function get_tpay_id_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::TPAY_ID,
            $this->translator->translate('settings.sections.payments.tpay.tpay_id'),
            $this->translator->translate('settings.sections.payments.tpay.tpay_id.desc')
        );
    }

    private function get_tpay_pin_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::TPAY_PIN,
            $this->translator->translate('settings.sections.payments.tpay.' . self::TPAY_PIN),
            $this->translator->translate('settings.sections.payments.tpay.' . self::TPAY_PIN . '.desc')
        );
    }

    private function get_tpay_cards_api_key_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::TPAY_CARDS_API_KEY,
            $this->translator->translate('settings.sections.payments.tpay.' . self::TPAY_CARDS_API_KEY),
            $this->translator->translate('settings.sections.payments.tpay.' . self::TPAY_CARDS_API_KEY . '.desc'),
            $this->translator->translate('settings.sections.payments.tpay.' . self::TPAY_CARDS_API_KEY . '.tooltip')
        );
    }

    private function get_tpay_cards_api_password_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::TPAY_CARDS_API_PASSWORD,
            $this->translator->translate('settings.sections.payments.tpay.' . self::TPAY_CARDS_API_PASSWORD),
            $this->translator->translate('settings.sections.payments.tpay.' . self::TPAY_CARDS_API_PASSWORD . '.desc'),
            $this->translator->translate('settings.sections.payments.tpay.' . self::TPAY_CARDS_API_PASSWORD . '.tooltip')
        );
    }

    private function get_tpay_cards_verification_code_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::TPAY_CARDS_VERIFICATION_CODE,
            $this->translator->translate('settings.sections.payments.tpay.' . self::TPAY_CARDS_VERIFICATION_CODE),
            $this->translator->translate('settings.sections.payments.tpay.' . self::TPAY_CARDS_VERIFICATION_CODE . '.desc'),
            $this->translator->translate('settings.sections.payments.tpay.' . self::TPAY_CARDS_VERIFICATION_CODE . '.tooltip')
        );
    }

    private function get_tpay_recurrence_allow_standard_payments_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::TPAY_RECURRENCE_ALLOW_STANDARD_PAYMENTS,
            $this->translator->translate('settings.sections.payments.tpay.' . self::TPAY_RECURRENCE_ALLOW_STANDARD_PAYMENTS),
            null,
            $this->translator->translate('settings.sections.payments.tpay.' . self::TPAY_RECURRENCE_ALLOW_STANDARD_PAYMENTS . '.tooltip'),
        );
    }

    private function get_tpay_checkout_label_field(): Abstract_Setting_Field
    {
        return (new Text_Setting_Field(
            self::TPAY_CHECKOUT_LABEL,
            $this->translator->translate('settings.sections.payments.checkout_label'),
            null,
            $this->translator->translate('settings.sections.payments.checkout_label.tooltip'),
        ))->set_placeholder($this->translator->translate('settings.sections.payments.tpay_payment_gate'));
    }

    private function get_payu_gate_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::PAYU_PAYMENT_GATE,
            $this->translator->translate('settings.sections.payments.payu_payment_gate'),
            null,
            $this->translator->translate('settings.sections.payments.payu_payment_gate.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_payu_api_type_field())
                ->add($this->get_payu_pos_id_field())
                ->add($this->get_payu_pos_auth_key_field())
                ->add($this->get_payu_key1_field())
                ->add($this->get_payu_key2_field())
                ->add($this->get_payu_return_url_failure_field())
                ->add($this->get_payu_return_url_success_field())
                ->add($this->get_payu_return_url_reports_field())
                ->add($this->get_payu_api_environment_field())
                ->add($this->get_payu_recurrence_field())
                ->add($this->get_payu_enable_debug_field())
                ->add($this->get_payu_checkout_label_field())
        ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.payments.payu_payment_gate'));
    }

    private function get_payu_api_type_field(): Select_Setting_Field
    {
        $options = [
          'rest' => $this->translator->translate('settings.sections.payments.option.' . self::PAYU_API_TYPE . '.rest'),
          'classic' => $this->translator->translate('settings.sections.payments.option.' . self::PAYU_API_TYPE . '.classic')
        ];

        return new Select_Setting_Field(
            self::PAYU_API_TYPE,
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_API_TYPE),
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_API_TYPE . '.desc'),
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_API_TYPE . '.tooltip'),
            null,
            $options
        );
    }

    private function get_payu_pos_id_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::PAYU_POS_ID,
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_POS_ID),
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_POS_ID . '.desc'),
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_POS_ID . '.tooltip')
        );
    }

    private function get_payu_pos_auth_key_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::PAYU_POS_AUTH_KEY,
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_POS_AUTH_KEY),
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_POS_AUTH_KEY . '.desc')
        );
    }

    private function get_payu_key1_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::PAYU_KEY1,
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_KEY1),
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_KEY1 . '.desc')
        );
    }

    private function get_payu_key2_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::PAYU_KEY2,
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_KEY2),
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_KEY2 . '.desc'),
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_KEY2 . '.tooltip')
        );
    }

    private function get_payu_return_url_failure_field(): Abstract_Setting_Field
    {
        $value = edd_get_failed_transaction_uri() . '?payu_transaction=%transId%&payu_session=%sessionId%&payu_error=%error%';

        $field = new Text_Setting_Field(
            self::PAYU_RETURN_URL_FAILURE,
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_RETURN_URL_FAILURE),
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_RETURN_URL_FAILURE . '.desc'),
            null,
            null,
            $value
        );
        $field->set_readonly(true);

        return $field;
    }

    private function get_payu_return_url_success_field(): Abstract_Setting_Field
    {
        $value = edd_get_success_page_uri() . '?payu_transaction=%transId%&payu_session=%sessionId%';

        $field = new Text_Setting_Field(
            self::PAYU_RETURN_URL_SUCCESS,
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_RETURN_URL_SUCCESS),
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_RETURN_URL_SUCCESS . '.desc'),
            null,
            null,
            $value
        );
        $field->set_readonly(true);

        return $field;
    }

    private function get_payu_return_url_reports_field(): Abstract_Setting_Field
    {
        $value = home_url( '/' );

        $field = new Text_Setting_Field(
            self::PAYU_RETURN_URL_REPORTS,
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_RETURN_URL_REPORTS),
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_RETURN_URL_REPORTS . '.desc'),
            null,
            null,
            $value
        );
        $field->set_readonly(true);

        return $field;
    }


    private function get_payu_api_environment_field(): Select_Setting_Field
    {
        $options = [
            'secure' => $this->translator->translate('settings.sections.payments.option.' . self::PAYU_API_ENVIRONMENT . '.secure'),
            'sandbox' => $this->translator->translate('settings.sections.payments.option.' . self::PAYU_API_ENVIRONMENT . '.sandbox')
        ];

        return new Select_Setting_Field(
            self::PAYU_API_ENVIRONMENT,
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_API_ENVIRONMENT),
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_API_ENVIRONMENT . '.desc'),
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_API_ENVIRONMENT . '.tooltip'),
            null,
            $options
        );
    }

    private function get_payu_recurrence_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::PAYU_RECURRENCE,
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_RECURRENCE),
            null,
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_RECURRENCE . '.tooltip')
        );
    }

    private function get_payu_enable_debug_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::PAYU_ENABLE_DEBUG,
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_ENABLE_DEBUG),
            null,
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_ENABLE_DEBUG . '.tooltip')
        );
    }

    private function get_payu_checkout_label_field(): Abstract_Setting_Field
    {
        return (new Text_Setting_Field(
            self::PAYU_CHECKOUT_LABEL,   
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_CHECKOUT_LABEL),
            null,
            $this->translator->translate('settings.sections.payments.payu.' . self::PAYU_CHECKOUT_LABEL . '.tooltip'),
        ))->set_placeholder($this->translator->translate('settings.sections.payments.payu_payment_gate'));
    }

    private function get_przelewy24_gate_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::PRZELEWY24_PAYMENT_GATE,
            $this->translator->translate('settings.sections.payments.przelewy24_payment_gate'),
            null,
            $this->translator->translate('settings.sections.payments.przelewy24_payment_gate.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_przelewy24_id_field())
                ->add($this->get_przelewy24_pin_field())
                ->add($this->get_przelewy24_checkout_label_field())
        ))->set_popup($this->settings_popup,
                      $this->translator->translate('settings.sections.payments.przelewy24_payment_gate'));
    }

    private function get_przelewy24_id_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::PRZELEWY24_ID,
            $this->translator->translate('settings.sections.payments.przelewy24.' . self::PRZELEWY24_ID),
            $this->translator->translate('settings.sections.payments.przelewy24.' . self::PRZELEWY24_ID . '.desc'),
            $this->translator->translate('settings.sections.payments.przelewy24.' . self::PRZELEWY24_ID . '.tooltip')
        );
    }

    private function get_przelewy24_pin_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::PRZELEWY24_PIN,
            $this->translator->translate('settings.sections.payments.przelewy24.' . self::PRZELEWY24_PIN),
            $this->translator->translate('settings.sections.payments.przelewy24.' . self::PRZELEWY24_PIN . '.desc'),
            $this->translator->translate('settings.sections.payments.przelewy24.' . self::PRZELEWY24_PIN . '.tooltip')
        );
    }

    private function get_przelewy24_checkout_label_field(): Abstract_Setting_Field
    {
        return (new Text_Setting_Field(
            self::PRZELEWY24_CHECKOUT_LABEL,
            $this->translator->translate('settings.sections.payments.przelewy24.' . self::PRZELEWY24_CHECKOUT_LABEL),
            null,
            $this->translator->translate('settings.sections.payments.przelewy24.' . self::PRZELEWY24_CHECKOUT_LABEL . '.tooltip'),
        ))->set_placeholder($this->translator->translate('settings.sections.payments.przelewy24_payment_gate'));
    }

    private function get_dotpay_gate_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::DOTPAY_PAYMENT_GATE,
            $this->translator->translate('settings.sections.payments.dotpay_payment_gate'),
            null,
            $this->translator->translate('settings.sections.payments.dotpay_payment_gate.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_dotpay_info_message())
                ->add($this->get_dotpay_id_field())
                ->add($this->get_dotpay_pin_field())
                ->add($this->get_dotpay_online_transfer_field())
                ->add($this->get_dotpay_checkout_label_field())
        ))->set_popup($this->settings_popup,
                      $this->translator->translate('settings.sections.payments.dotpay_payment_gate'));
    }

    private function get_dotpay_info_message(): Abstract_Setting_Field
    {
        return new Message(
            $this->translator->translate('settings.sections.payments.dotpay.info_message')
        );
    }

    private function get_dotpay_id_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::DOTPAY_ID,
            $this->translator->translate('settings.sections.payments.dotpay.' . self::DOTPAY_ID),
            $this->translator->translate('settings.sections.payments.dotpay.' . self::DOTPAY_ID . '.desc'),
            $this->translator->translate('settings.sections.payments.dotpay.' . self::DOTPAY_ID . '.tooltip')
        );
    }

    private function get_dotpay_pin_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::DOTPAY_PIN,
            $this->translator->translate('settings.sections.payments.dotpay.' . self::DOTPAY_PIN),
            $this->translator->translate('settings.sections.payments.dotpay.' . self::DOTPAY_PIN . '.desc'),
            $this->translator->translate('settings.sections.payments.dotpay.' . self::DOTPAY_PIN . '.tooltip')
        );
    }

    private function get_dotpay_online_transfer_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::DOTPAY_ONLINE_TRANSFER,
            $this->translator->translate('settings.sections.payments.dotpay.' . self::DOTPAY_ONLINE_TRANSFER),
            $this->translator->translate('settings.sections.payments.dotpay.' . self::DOTPAY_ONLINE_TRANSFER . '.desc'),
            $this->translator->translate('settings.sections.payments.dotpay.' . self::DOTPAY_ONLINE_TRANSFER . '.tooltip')
        );
    }

    private function get_dotpay_checkout_label_field(): Abstract_Setting_Field
    {
        return (new Text_Setting_Field(
            self::DOTPAY_CHECKOUT_LABEL,
            $this->translator->translate('settings.sections.payments.dotpay.' . self::DOTPAY_CHECKOUT_LABEL),
            null,
            $this->translator->translate('settings.sections.payments.dotpay.' . self::DOTPAY_CHECKOUT_LABEL . '.tooltip'),
        ))->set_placeholder($this->translator->translate('settings.sections.payments.dotpay_payment_gate'));
    }

    private function get_paynow_gate_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::PAYNOW_PAYMENT_GATE,
            $this->translator->translate('settings.sections.payments.paynow_payment_gate'),
            null,
            $this->translator->translate('settings.sections.payments.paynow_payment_gate.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_paynow_access_key_field())
                ->add($this->get_paynow_signature_key_field())
                ->add($this->get_paynow_environment_field())
                ->add($this->get_paynow_checkout_label_field())
        ))->set_popup($this->settings_popup,
                      $this->translator->translate('settings.sections.payments.paynow_payment_gate'));
    }

    private function get_paynow_access_key_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::PAYNOW_ACCESS_KEY,
            $this->translator->translate('settings.sections.payments.paynow.' . self::PAYNOW_ACCESS_KEY),
            null,
            $this->translator->translate('settings.sections.payments.paynow.' . self::PAYNOW_ACCESS_KEY . '.tooltip')
        );
    }

    private function get_paynow_signature_key_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::PAYNOW_SIGNATURE_KEY,
            $this->translator->translate('settings.sections.payments.paynow.' . self::PAYNOW_SIGNATURE_KEY),
            null,
            $this->translator->translate('settings.sections.payments.paynow.' . self::PAYNOW_SIGNATURE_KEY . '.tooltip')
        );
    }

    private function get_paynow_environment_field(): Abstract_Setting_Field
    {
        $options = [
            'production' => $this->translator->translate('settings.sections.payments.paynow.' . self::PAYNOW_ENVIRONMENT . '.option_production'),
            'sandbox' => $this->translator->translate('settings.sections.payments.paynow.' . self::PAYNOW_ENVIRONMENT . '.option_sandbox')
        ];

        return new Select_Setting_Field(
            self::PAYNOW_ENVIRONMENT,
            $this->translator->translate('settings.sections.payments.paynow.' . self::PAYNOW_ENVIRONMENT),
            $this->translator->translate('settings.sections.payments.paynow.' . self::PAYNOW_ENVIRONMENT . '.desc'),
            $this->translator->translate('settings.sections.payments.paynow.' . self::PAYNOW_ENVIRONMENT . '.tooltip'),
            null,
            $options
        );
    }

    private function get_paynow_checkout_label_field(): Abstract_Setting_Field
    {
        return (new Text_Setting_Field(
            self::PAYNOW_CHECKOUT_LABEL,
            $this->translator->translate('settings.sections.payments.paynow.' . self::PAYNOW_CHECKOUT_LABEL),
            null,
            $this->translator->translate('settings.sections.payments.paynow.' . self::PAYNOW_CHECKOUT_LABEL . '.tooltip'),
        ))->set_placeholder($this->translator->translate('settings.sections.payments.paynow_payment_gate'));
    }

    private function get_stripe_gate_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::STRIPE_PAYMENT_GATE,
            $this->translator->translate('settings.sections.payments.stripe_payment_gate'),
            null,
            $this->translator->translate('settings.sections.payments.stripe_payment_gate.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_test_secret_key_field())
                ->add($this->get_test_publishable_key_field())
                ->add($this->get_live_secret_key_field())
                ->add($this->get_live_publishable_key_field())
                ->add($this->get_stripe_checkout_label_field())
        ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.payments.stripe_payment_gate'));
    }

    private function get_test_secret_key_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::STRIPE_TEST_SECRET_KEY,
            $this->translator->translate('settings.sections.payments.stripe.' . self::STRIPE_TEST_SECRET_KEY),
            $this->translator->translate('settings.sections.payments.stripe.' . self::STRIPE_TEST_SECRET_KEY . '.desc'),
            $this->translator->translate('settings.sections.payments.stripe.' . self::STRIPE_TEST_SECRET_KEY . '.tooltip')
        );
    }

    private function get_test_publishable_key_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::STRIPE_TEST_PUBLISHABLE_KEY,
            $this->translator->translate('settings.sections.payments.stripe.' . self::STRIPE_TEST_PUBLISHABLE_KEY),
            $this->translator->translate('settings.sections.payments.stripe.' . self::STRIPE_TEST_PUBLISHABLE_KEY . '.desc'),
            $this->translator->translate('settings.sections.payments.stripe.' . self::STRIPE_TEST_PUBLISHABLE_KEY . '.tooltip')
        );
    }

    private function get_live_secret_key_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::STRIPE_LIVE_SECRET_KEY,
            $this->translator->translate('settings.sections.payments.stripe.' . self::STRIPE_LIVE_SECRET_KEY),
            $this->translator->translate('settings.sections.payments.stripe.' . self::STRIPE_LIVE_SECRET_KEY . '.desc'),
            $this->translator->translate('settings.sections.payments.stripe.' . self::STRIPE_LIVE_SECRET_KEY . '.tooltip')
        );
    }

    private function get_live_publishable_key_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::STRIPE_LIVE_PUBLISHABLE_KEY,
            $this->translator->translate('settings.sections.payments.stripe.' . self::STRIPE_LIVE_PUBLISHABLE_KEY),
            $this->translator->translate('settings.sections.payments.stripe.' . self::STRIPE_LIVE_PUBLISHABLE_KEY . '.desc'),
            $this->translator->translate('settings.sections.payments.stripe.' . self::STRIPE_LIVE_PUBLISHABLE_KEY . '.tooltip')
        );
    }

    private function get_stripe_checkout_label_field(): Abstract_Setting_Field
    {
        return (new Text_Setting_Field(
            self::STRIPE_CHECKOUT_LABEL,
            $this->translator->translate('settings.sections.payments.stripe.' . self::STRIPE_CHECKOUT_LABEL),
            null,
            $this->translator->translate('settings.sections.payments.stripe.' . self::STRIPE_CHECKOUT_LABEL . '.tooltip'),
        ))->set_placeholder($this->translator->translate('settings.sections.payments.stripe_payment_gate'));
    }


    private function get_paypal_gate_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::PAYPAL_PAYMENT_GATE,
            $this->translator->translate('settings.sections.payments.paypal_payment_gate'),
            null,
            $this->translator->translate('settings.sections.payments.paypal_payment_gate.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_paypal_email_field())
                ->add($this->get_paypal_page_style_field())
                ->add($this->get_dotpay_disable_paypal_verification_field())
                ->add($this->get_paypal_checkout_label_field())
        ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.payments.paypal_payment_gate'));
    }

    private function get_paypal_email_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::PAYPAL_EMAIL,
            $this->translator->translate('settings.sections.payments.paypal.' . self::PAYPAL_EMAIL),
            $this->translator->translate('settings.sections.payments.paypal.' . self::PAYPAL_EMAIL . '.desc'),
            $this->translator->translate('settings.sections.payments.paypal.' . self::PAYPAL_EMAIL . '.tooltip')
        );
    }

    private function get_paypal_page_style_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::PAYPAL_PAGE_STYLE,
            $this->translator->translate('settings.sections.payments.paypal.' . self::PAYPAL_PAGE_STYLE),
            $this->translator->translate('settings.sections.payments.paypal.' . self::PAYPAL_PAGE_STYLE . '.desc'),
            $this->translator->translate('settings.sections.payments.paypal.' . self::PAYPAL_PAGE_STYLE . '.tooltip')
        );
    }

    private function get_dotpay_disable_paypal_verification_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::PAYPAL_DISABLE_VERIFICATION,
            $this->translator->translate('settings.sections.payments.paypal.' . self::PAYPAL_DISABLE_VERIFICATION),
            $this->translator->translate('settings.sections.payments.paypal.' . self::PAYPAL_DISABLE_VERIFICATION . '.desc'),
            $this->translator->translate('settings.sections.payments.paypal.' . self::PAYPAL_DISABLE_VERIFICATION . '.tooltip')
        );
    }

    private function get_paypal_checkout_label_field(): Abstract_Setting_Field
    {
        return (new Text_Setting_Field(
            self::PAYPAL_CHECKOUT_LABEL,
            $this->translator->translate('settings.sections.payments.paypal.' . self::PAYPAL_CHECKOUT_LABEL),
            null,
            $this->translator->translate('settings.sections.payments.paypal.' . self::PAYPAL_CHECKOUT_LABEL . '.tooltip'),
        ))->set_placeholder($this->translator->translate('settings.sections.payments.paypal_payment_gate'));
    }

    private function get_coinbase_gate_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::COINBASE_PAYMENT_GATE,
            $this->translator->translate('settings.sections.payments.coinbase_payment_gate'),
            null,
            $this->translator->translate('settings.sections.payments.coinbase_payment_gate.tooltip'),
            (new Additional_Fields_Collection())
                ->add($this->get_coinbase_info_message())
                ->add($this->get_coinbase_api_key_field())
                ->add($this->get_coinbase_checkout_label_field())
        ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.payments.coinbase_payment_gate'));
    }

    private function get_coinbase_info_message(): Abstract_Setting_Field
    {
        return new Message(
            sprintf($this->translator->translate('settings.sections.payments.coinbase.info_message'), home_url('index.php?edd-listener=coinbase'))
        );
    }

    private function get_coinbase_api_key_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::COINBASE_API_KEY,
            $this->translator->translate('settings.sections.payments.coinbase.' . self::COINBASE_API_KEY),
            $this->translator->translate('settings.sections.payments.coinbase.' . self::COINBASE_API_KEY . '.desc'),
            $this->translator->translate('settings.sections.payments.coinbase.' . self::COINBASE_API_KEY . '.tooltip')
        );
    }

    private function get_coinbase_checkout_label_field(): Abstract_Setting_Field
    {
        return (new Text_Setting_Field(
            self::COINBASE_CHECKOUT_LABEL,
            $this->translator->translate('settings.sections.payments.coinbase.' . self::COINBASE_CHECKOUT_LABEL),
            null,
            $this->translator->translate('settings.sections.payments.coinbase.' . self::COINBASE_CHECKOUT_LABEL . '.tooltip'),
        ))->set_placeholder($this->translator->translate('settings.sections.payments.coinbase_payment_gate'));
    }

    private function get_transfers_gate_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::TRANSFERS_PAYMENT_GATE,
            $this->translator->translate('settings.sections.payments.transfers_payment_gate'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_transfers_name_field())
                ->add($this->get_transfers_address_field())
                ->add($this->get_transfers_account_number_field())
                ->add($this->get_transfers_checkout_label_field())
        ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.payments.transfers_payment_gate'));
    }


    private function get_transfers_name_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::TRANSFERS_NAME,
            $this->translator->translate('settings.sections.payments.transfers.' . self::TRANSFERS_NAME),
            $this->translator->translate('settings.sections.payments.transfers.' . self::TRANSFERS_NAME . '.desc')
        );
    }

    private function get_transfers_address_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::TRANSFERS_ADDRESS,
            $this->translator->translate('settings.sections.payments.transfers.' . self::TRANSFERS_ADDRESS),
            $this->translator->translate('settings.sections.payments.transfers.' . self::TRANSFERS_ADDRESS . '.desc')
        );
    }

    private function get_transfers_account_number_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::TRANSFERS_ACCOUNT_NUMBER,
            $this->translator->translate('settings.sections.payments.transfers.' . self::TRANSFERS_ACCOUNT_NUMBER),
            $this->translator->translate('settings.sections.payments.transfers.' . self::TRANSFERS_ACCOUNT_NUMBER . '.desc')
        );
    }

    private function get_transfers_checkout_label_field(): Abstract_Setting_Field
    {
        return (new Text_Setting_Field(
            self::TRANSFERS_CHECKOUT_LABEL,
            $this->translator->translate('settings.sections.payments.transfers.' . self::TRANSFERS_CHECKOUT_LABEL),
        ))->set_placeholder($this->translator->translate('settings.sections.payments.transfers_payment_gate'));
    }

}