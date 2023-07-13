<?php

namespace bpmj\wpidea\admin\settings\infrastructure\persistence;


use bpmj\wpidea\admin\settings\core\configuration\{Accounting_Settings_Group,
	Advanced_Settings_Group,
	Cart_Settings_Group,
	Design_Settings_Group,
	General_Settings_Group,
	Integrations_Settings_Group,
	Messages_Settings_Group,
	Modules_Settings_Group,
	Payments_Settings_Group};
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\admin\settings\core\persistence\Interface_Settings_Persistence;
use bpmj\wpidea\admin\settings\infrastructure\persistence\storage_place\{Default_Storage_Places_Registry,
	Interface_Settings_Storage_Place};

class Legacy_Settings_Persistence implements Interface_Settings_Persistence
{
    private const SAVE_TO = [
        General_Settings_Group::ADMIN_NOTICE_EMAILS => self::EDD_SETTINGS_STORAGE_PLACE,
        General_Settings_Group::BLOG_NAME => self::WP_SETTINGS_STORAGE_PLACE,
        General_Settings_Group::BLOG_DESCRIPTION => self::WP_SETTINGS_STORAGE_PLACE,
        General_Settings_Group::PAGE_ON_FRONT => self::WP_SETTINGS_STORAGE_PLACE,
        General_Settings_Group::COMMENTS_NOTIFY => self::WP_SETTINGS_STORAGE_PLACE,
        General_Settings_Group::MODERATION_NOTIFY => self::WP_SETTINGS_STORAGE_PLACE,
        General_Settings_Group::COMMENT_MODERATION => self::WP_SETTINGS_STORAGE_PLACE,
        General_Settings_Group::COMMENT_PREVIOUSLY_APPROVED => self::WP_SETTINGS_STORAGE_PLACE,

        Accounting_Settings_Group::CURRENCY => self::EDD_SETTINGS_STORAGE_PLACE,
        Accounting_Settings_Group::THOUSANDS_SEPARATOR => self::EDD_SETTINGS_STORAGE_PLACE,
        Accounting_Settings_Group::DECIMAL_SEPARATOR => self::EDD_SETTINGS_STORAGE_PLACE,
        Accounting_Settings_Group::EDD_ID_FORCE => self::EDD_SETTINGS_STORAGE_PLACE,
        Accounting_Settings_Group::EDD_ID_PERSON => self::EDD_SETTINGS_STORAGE_PLACE,
        Accounting_Settings_Group::EDD_ID_DISABLE_TAX => self::EDD_SETTINGS_STORAGE_PLACE,
        Accounting_Settings_Group::EDD_ID_ENABLE_VAT_MOSS => self::EDD_SETTINGS_STORAGE_PLACE,

        Payments_Settings_Group::TEST_MODE => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::DEFAULT_GATEWAY => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::TEST_PAYMENT_GATE => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::TPAY_PAYMENT_GATE => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PAYU_PAYMENT_GATE => self::EDD_SETTINGS_STORAGE_PLACE,

        Payments_Settings_Group::TPAY_ID => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::TPAY_PIN => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::TPAY_CARDS_API_KEY => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::TPAY_CARDS_API_PASSWORD => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::TPAY_CARDS_VERIFICATION_CODE => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::TPAY_RECURRENCE_ALLOW_STANDARD_PAYMENTS => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::TPAY_CHECKOUT_LABEL => self::EDD_SETTINGS_STORAGE_PLACE,

        Payments_Settings_Group::PAYU_POS_ID => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PAYU_POS_AUTH_KEY => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PAYU_KEY1 => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PAYU_KEY2 => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PAYU_API_TYPE => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PAYU_API_ENVIRONMENT => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PAYU_RECURRENCE => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PAYU_ENABLE_DEBUG => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PAYU_CHECKOUT_LABEL => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PAYU_RETURN_URL_FAILURE => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PAYU_RETURN_URL_SUCCESS => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PAYU_RETURN_URL_REPORTS => self::EDD_SETTINGS_STORAGE_PLACE,

        Payments_Settings_Group::PRZELEWY24_PAYMENT_GATE => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PRZELEWY24_ID => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PRZELEWY24_PIN => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PRZELEWY24_CHECKOUT_LABEL => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::DOTPAY_PAYMENT_GATE => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::DOTPAY_ID => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::DOTPAY_PIN => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::DOTPAY_ONLINE_TRANSFER => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::DOTPAY_CHECKOUT_LABEL => self::EDD_SETTINGS_STORAGE_PLACE,

        Payments_Settings_Group::PAYNOW_PAYMENT_GATE => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PAYNOW_ACCESS_KEY => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PAYNOW_SIGNATURE_KEY => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PAYNOW_ENVIRONMENT => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PAYNOW_CHECKOUT_LABEL => self::EDD_SETTINGS_STORAGE_PLACE,

        Payments_Settings_Group::STRIPE_PAYMENT_GATE => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::STRIPE_TEST_SECRET_KEY => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::STRIPE_TEST_PUBLISHABLE_KEY => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::STRIPE_LIVE_SECRET_KEY => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::STRIPE_LIVE_PUBLISHABLE_KEY => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::STRIPE_CHECKOUT_LABEL => self::EDD_SETTINGS_STORAGE_PLACE,

        Payments_Settings_Group::PAYPAL_PAYMENT_GATE => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PAYPAL_EMAIL => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PAYPAL_PAGE_STYLE => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PAYPAL_DISABLE_VERIFICATION => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::PAYPAL_CHECKOUT_LABEL => self::EDD_SETTINGS_STORAGE_PLACE,

        Payments_Settings_Group::COINBASE_PAYMENT_GATE => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::COINBASE_API_KEY => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::COINBASE_CHECKOUT_LABEL => self::EDD_SETTINGS_STORAGE_PLACE,

        Payments_Settings_Group::TRANSFERS_PAYMENT_GATE => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::TRANSFERS_NAME => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::TRANSFERS_ADDRESS => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::TRANSFERS_ACCOUNT_NUMBER => self::EDD_SETTINGS_STORAGE_PLACE,
        Payments_Settings_Group::TRANSFERS_CHECKOUT_LABEL => self::EDD_SETTINGS_STORAGE_PLACE,

        Integrations_Settings_Group::GETRESPONSE_EDDRES_TOKEN => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::GETRESPONSE_EDDRES_SHOW_CHECKOUT_SIGNUP => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::GETRESPONSE_EDDRES_LIST => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::GETRESPONSE_EDDRES_LIST_UNSUBSCRIBE => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::GETRESPONSE_EDDRES_LABEL => self::EDD_SETTINGS_STORAGE_PLACE,

        Integrations_Settings_Group::MAILCHIMP_API_KEY => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::MAILCHIMP_LIST => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::MAILCHIMP_SHOW_CHECKOUT_SIGNUP => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::MAILCHIMP_LABEL => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::MAILCHIMP_DOUBLE_OPT_IN => self::EDD_SETTINGS_STORAGE_PLACE,

        Integrations_Settings_Group::FRESHMAIL_API_KEY => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::FRESHMAIL_API_SECRET => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::FRESHMAIL_SHOW_CHECKOUT_SIGNUP => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::FRESHMAIL_GROUP => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::FRESHMAIL_GROUP_UNSUBSCRIBE => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::FRESHMAIL_LABEL => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::FRESHMAIL_DOUBLE_OPT_IN => self::EDD_SETTINGS_STORAGE_PLACE,
        
        Integrations_Settings_Group::SALESMANAGO_OWNER => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::SALESMANAGO_ENDPOINT => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::SALESMANAGO_CLIENT_ID => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::SALESMANAGO_API_SECRET => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::SALESMANAGO_TRACKING_CODE => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::SALESMANAGO_CHECKOUT_MODE => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::SALESMANAGO_CHECKOUT_LABEL => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::SALESMANAGO_TAGS => self::EDD_SETTINGS_STORAGE_PLACE,

        Integrations_Settings_Group::IPRESSO_API_ENDPOINT => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::IPRESSO_API => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::IPRESSO_API_LOGIN => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::IPRESSO_API_PASSWORD => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::IPRESSO_SHOW_CHECKOUT_SIGNUP => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::IPRESSO_TRACKING_CODE => self::EDD_SETTINGS_STORAGE_PLACE,

        Integrations_Settings_Group::MAILERLITE_API => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::MAILERLITE_GROUP => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::MAILERLITE_SHOW_CHECKOUT_SIGNUP => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::MAILERLITE_LABEL => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::MAILERLITE_DOUBLE_OPT_IN => self::EDD_SETTINGS_STORAGE_PLACE,

        Integrations_Settings_Group::INTERSPIRE_USERNAME => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::INTERSPIRE_TOKEN => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::INTERSPIRE_XML_ENDPOINT => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::INTERSPIRE_CONTACT_LIST => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::INTERSPIRE_SHOW_CHECKOUT => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::INTERSPIRE_LABEL => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::INTERSPIRE_DOUBLE_OPT_IN => self::EDD_SETTINGS_STORAGE_PLACE,

        Integrations_Settings_Group::ACTIVECAMPAIGN_API_URL => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::ACTIVECAMPAIGN_API_TOKEN => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::ACTIVECAMPAIGN_SHOW_CHECKOUT_SIGNUP => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::ACTIVECAMPAIGN_LIST => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::ACTIVECAMPAIGN_LIST_UNSUBSCRIBE => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::ACTIVECAMPAIGN_TAG => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::ACTIVECAMPAIGN_TAG_UNSUBSCRIBE => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::ACTIVECAMPAIGN_LABEL => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::ACTIVECAMPAIGN_FORM_ID => self::EDD_SETTINGS_STORAGE_PLACE,
        
        Integrations_Settings_Group::CONVERTKIT_API => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::CONVERTKIT_API_SECRET => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::CONVERTKIT_SHOW_CHECKOUT_SIGNUP => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::CONVERTKIT_LIST => self::EDD_SETTINGS_STORAGE_PLACE,
        Integrations_Settings_Group::CONVERTKIT_LABEL => self::EDD_SETTINGS_STORAGE_PLACE,

        Integrations_Settings_Group::IFIRMA_EMAIL => self::IFIRMA_STORAGE_PLACE,
        Integrations_Settings_Group::IFIRMA_INVOICE_KEY => self::IFIRMA_STORAGE_PLACE,
        Integrations_Settings_Group::IFIRMA_SUBSCRIBER_KEY => self::IFIRMA_STORAGE_PLACE,
        Integrations_Settings_Group::IFIRMA_VAT_EXEMPTION => self::IFIRMA_STORAGE_PLACE,
        Integrations_Settings_Group::IFIRMA_AUTO_SENT => self::IFIRMA_STORAGE_PLACE,

        Integrations_Settings_Group::WFIRMA_AUTH_TYPE => self::WFIRMA_STORAGE_PLACE,
        Integrations_Settings_Group::WFIRMA_LOGIN => self::WFIRMA_STORAGE_PLACE,
        Integrations_Settings_Group::WFIRMA_PASS => self::WFIRMA_STORAGE_PLACE,
    	Integrations_Settings_Group::WFIRMA_OAUTH2_CLIENT_ID => self::WFIRMA_STORAGE_PLACE,
    	Integrations_Settings_Group::WFIRMA_OAUTH2_CLIENT_SECRET => self::WFIRMA_STORAGE_PLACE,
    	Integrations_Settings_Group::WFIRMA_OAUTH2_AUTHORIZATION_CODE => self::WFIRMA_STORAGE_PLACE,
   		Integrations_Settings_Group::WFIRMA_COMPANY_ID => self::WFIRMA_STORAGE_PLACE,
        Integrations_Settings_Group::WFIRMA_RECEIPT => self::WFIRMA_STORAGE_PLACE,
        Integrations_Settings_Group::WFIRMA_AUTO_SENT => self::WFIRMA_STORAGE_PLACE,
        Integrations_Settings_Group::WFIRMA_AUTO_SENT_RECEIPT => self::WFIRMA_STORAGE_PLACE,

        Integrations_Settings_Group::TAXE_LOGIN => self::TAXE_STORAGE_PLACE,
        Integrations_Settings_Group::TAXE_API_KEY => self::TAXE_STORAGE_PLACE,
        Integrations_Settings_Group::TAXE_RECEIPT => self::TAXE_STORAGE_PLACE,
        Integrations_Settings_Group::TAXE_VAT_EXEMPTION => self::TAXE_STORAGE_PLACE,
        Integrations_Settings_Group::TAXE_AUTO_SENT => self::TAXE_STORAGE_PLACE,
        Integrations_Settings_Group::TAXE_AUTO_SENT_RECEIPT => self::TAXE_STORAGE_PLACE,

        Integrations_Settings_Group::INFAKT_API_KEY => self::INFAKT_STORAGE_PLACE,
        Integrations_Settings_Group::INFAKT_VAT_EXEMPTION => self::INFAKT_STORAGE_PLACE,
        Integrations_Settings_Group::INFAKT_AUTO_SENT => self::INFAKT_STORAGE_PLACE,

        Cart_Settings_Group::EDD_ID_HIDE_FNAME => self::EDD_SETTINGS_STORAGE_PLACE,
        Cart_Settings_Group::EDD_ID_HIDE_LNAME => self::EDD_SETTINGS_STORAGE_PLACE,
        Cart_Settings_Group::AGREE_LABEL => self::EDD_SETTINGS_STORAGE_PLACE,

        Messages_Settings_Group::FROM_NAME => self::EDD_SETTINGS_STORAGE_PLACE,
        Messages_Settings_Group::FROM_EMAIL => self::EDD_SETTINGS_STORAGE_PLACE,
        Messages_Settings_Group::PURCHASE_SUBJECT => self::EDD_SETTINGS_STORAGE_PLACE,
        Messages_Settings_Group::PURCHASE_HEADING => self::EDD_SETTINGS_STORAGE_PLACE,
        Messages_Settings_Group::PURCHASE_RECEIPT => self::EDD_SETTINGS_STORAGE_PLACE,
        Messages_Settings_Group::ARC_SUBJECT => self::EDD_SETTINGS_STORAGE_PLACE,
        Messages_Settings_Group::ARC_CONTENT => self::EDD_SETTINGS_STORAGE_PLACE,
        Messages_Settings_Group::RENEWAL_DISCOUNT => self::EDD_SETTINGS_STORAGE_PLACE,
        Messages_Settings_Group::RENEWAL_DISCOUNT_VALUE => self::EDD_SETTINGS_STORAGE_PLACE,
        Messages_Settings_Group::RENEWAL_DISCOUNT_TYPE => self::EDD_SETTINGS_STORAGE_PLACE,
        Messages_Settings_Group::RENEWAL_DISCOUNT_TIME => self::EDD_SETTINGS_STORAGE_PLACE,
        Messages_Settings_Group::EXPIRED_ACCESS_REPORT_EMAIL => self::EDD_SETTINGS_STORAGE_PLACE,
        Messages_Settings_Group::RENEWALS_START => self::EDD_SETTINGS_STORAGE_PLACE,
        Messages_Settings_Group::RENEWALS_END => self::EDD_SETTINGS_STORAGE_PLACE,
    ];

    private const CHECKBOXES_TO_CHANGE_TO_YES_OR_NO = [
        Accounting_Settings_Group::INVOICES_IS_VAT_PAYER,
        Design_Settings_Group::LIST_EXCERPT,
        Design_Settings_Group::LIST_PRICE,
        Design_Settings_Group::LIST_BUY_BUTTON,
        Design_Settings_Group::LIST_PAGINATION,
    ];

    private const CHECKBOXES_TO_CHANGE_TO_TRUE_OR_FALSE = [
        Design_Settings_Group::LIST_DETAILS_BUTTON
    ];

    private const CHECKBOXES_TO_CHANGE_TO_ONE_OR_ZERO = [
        Advanced_Settings_Group::ENABLE_ACTIVE_SESSIONS_LIMITER,
        Advanced_Settings_Group::ENABLE_LOGO_IN_COURSE_TO_HOME_PAGE,
        Advanced_Settings_Group::ENABLE_SELL_DISCOUNTS,
        Modules_Settings_Group::ENABLE_DIGITAL_PRODUCTS,
        Modules_Settings_Group::SERVICES_ENABLED,
        Payments_Settings_Group::TEST_MODE
    ];

    private const EDD_SETTINGS_STORAGE_PLACE = 'edd_settings';
    private const WP_SETTINGS_STORAGE_PLACE = 'wp_settings';
    public const IFIRMA_STORAGE_PLACE = 'ifirma_settings';
    public const WFIRMA_STORAGE_PLACE = 'wfirma_settings';
    public const TAXE_STORAGE_PLACE = 'taxe_settings';
    public const INFAKT_STORAGE_PLACE = 'infakt_settings';

    private const ON = 'on';
    private const YES = 'yes';
    private const NO = 'no';
    private const TRUE_STRING = 'true';
    private const FALSE_STRING = 'false';
    private const ONE_STRING = '1';
    private const ZERO_STRING = '0';

    private Default_Storage_Places_Registry $default_storage_places_registry;

    public function __construct(
        Default_Storage_Places_Registry $default_storage_places_registry
    )
    {
        $this->default_storage_places_registry = $default_storage_places_registry;
    }

    public function get_value(Abstract_Setting_Field $field)
    {
        $name = $field->get_name();

        $storage_place = $this->get_storage_place_for_field($field);

        return $storage_place->get_data($name);
    }

    public function save(Abstract_Setting_Field $field): void
    {
        $name = $field->get_name();
        $value = $field->get_value();

        $value = $this->if_checkbox_in_the_table_change_the_value_to_yes_or_no($name, $value);
        $value = $this->if_checkbox_in_the_table_change_the_value_to_true_or_false_string($name, $value);
        $value = $this->if_checkbox_in_the_table_change_the_value_to_one_or_zero_string($name, $value);

        $storage_place = $this->get_storage_place_for_field($field);

        $storage_place->update_data($name, $value);
    }

    private function get_storage_place_for_field(Abstract_Setting_Field $field): Interface_Settings_Storage_Place
    {
        return $field->get_storage_place() ?? $this->get_storage_place_by_setting_name($field->get_name());
    }

    private function get_storage_place_by_setting_name(string $name): Interface_Settings_Storage_Place
    {
        $save_to = self::SAVE_TO[$name] ?? null;

        switch ($save_to) {
            case self::EDD_SETTINGS_STORAGE_PLACE:
                return $this->default_storage_places_registry->get_edd_settings_storage_place();
            case self::WP_SETTINGS_STORAGE_PLACE:
                return $this->default_storage_places_registry->get_wp_settings_storage_place();
            case self::IFIRMA_STORAGE_PLACE:
                return $this->default_storage_places_registry->get_ifirma_storage_place();
            case self::WFIRMA_STORAGE_PLACE:
                return $this->default_storage_places_registry->get_wfirma_storage_place();
            case self::TAXE_STORAGE_PLACE:
                return $this->default_storage_places_registry->get_taxe_storage_place();
            case self::INFAKT_STORAGE_PLACE:
                return $this->default_storage_places_registry->get_infakt_storage_place();
            default:
                return $this->default_storage_places_registry->get_publigo_settings_storage_place();
        }
    }

    private function if_checkbox_in_the_table_change_the_value_to_yes_or_no(string $name, $value)
    {
        if(in_array( $name, self::CHECKBOXES_TO_CHANGE_TO_YES_OR_NO)) {
             if($value !== self::ON){
                 return self::NO;
             }
            return self::YES;
        }

        return $value;
    }

    private function if_checkbox_in_the_table_change_the_value_to_true_or_false_string(string $name, $value)
    {
        if(in_array( $name, self::CHECKBOXES_TO_CHANGE_TO_TRUE_OR_FALSE)) {
            if($value !== self::ON){
                return self::FALSE_STRING;
            }
            return self::TRUE_STRING;
        }

        return $value;
    }

    private function if_checkbox_in_the_table_change_the_value_to_one_or_zero_string(string $name, $value)
    {
        if(in_array( $name, self::CHECKBOXES_TO_CHANGE_TO_ONE_OR_ZERO)) {
            if($value !== self::ON){
                return self::ZERO_STRING;
            }
            return self::ONE_STRING;
        }

        return $value;
    }
}