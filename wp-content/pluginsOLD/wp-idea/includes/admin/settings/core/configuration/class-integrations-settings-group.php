<?php

namespace bpmj\wpidea\admin\settings\core\configuration;

use bpmj\wpidea\admin\settings\core\collections\{Additional_Fields_Collection, Fields_Collection};
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\{Abstract_Setting_Field,
	Button_Reload_Api_Cache,
	Checkbox_Setting_Field,
	Message,
	Number_Setting_Field,
	Section_Heading,
	Select_Setting_Field,
	Text_Area_Setting_Field,
	Text_Setting_Field,
	Toggle_Setting_Field,
	WFirma_Redirect_Setting_Field};
use bpmj\wpidea\admin\settings\core\entities\Setting_Field_Validation_Result;
use bpmj\wpidea\admin\settings\infrastructure\persistence\storage_place\Default_Storage_Places_Registry;
use bpmj\wpidea\nonce\Nonce_Handler;

class Integrations_Settings_Group extends Abstract_Settings_Group
{
    private const EDD_MAILCHIMP = 'edd-mailchimp';
    private const EDD_FRESHMAIL = 'edd-freshmail';
    private const EDD_MAILERLITE = 'edd-mailerlite';
    private const EDD_GETRESPONSE = 'edd-getresponse';
    private const EDD_INTERSPIRE = 'edd-interspire';
    private const EDD_ACTIVECAMPAIGN = 'edd-activecampaign';
    private const EDD_CONVERKIT = 'edd-convertkit';

    public const MAILCHIMP_INTEGRATION = 'integration_edd-mailchimp';
    public const MAILCHIMP_API_KEY = 'eddmc_api';
    public const MAILCHIMP_LIST = 'eddmc_list';
    public const MAILCHIMP_SHOW_CHECKOUT_SIGNUP = 'eddmc_show_checkout_signup';
    public const MAILCHIMP_LABEL = 'eddmc_label';
    public const MAILCHIMP_DOUBLE_OPT_IN = 'eddmc_double_opt_in';

    public const GETRESPONSE_INTEGRATION = 'integration_edd-getresponse';
    public const GETRESPONSE_EDDRES_TOKEN = 'bpmj_eddres_token';
    public const GETRESPONSE_EDDRES_SHOW_CHECKOUT_SIGNUP = 'bpmj_eddres_show_checkout_signup';
    public const GETRESPONSE_EDDRES_LIST = 'bpmj_eddres_list';
    public const GETRESPONSE_EDDRES_LIST_UNSUBSCRIBE = 'bpmj_eddres_list_unsubscribe';
    public const GETRESPONSE_EDDRES_LABEL = 'bpmj_eddres_label';

    public const FRESHMAIL_INTEGRATION = 'integration_edd-freshmail';
    public const FRESHMAIL_API_KEY = 'bpmj_eddfm_api';
    public const FRESHMAIL_API_SECRET = 'bpmj_eddfm_api_secret';
    public const FRESHMAIL_SHOW_CHECKOUT_SIGNUP = 'bpmj_eddfm_show_checkout_signup';
    public const FRESHMAIL_GROUP = 'bpmj_eddfm_group';
    public const FRESHMAIL_GROUP_UNSUBSCRIBE = 'bpmj_eddfm_group_unsubscribe';
    public const FRESHMAIL_LABEL = 'bpmj_eddfm_label';
    public const FRESHMAIL_DOUBLE_OPT_IN = 'bpmj_eddfm_double_opt_in';

    public const SALESMANAGO_INTEGRATION = 'integration_edd-salesmanago';
    public const SALESMANAGO_OWNER = 'salesmanago_owner';
    public const SALESMANAGO_ENDPOINT = 'salesmanago_endpoint';
    public const SALESMANAGO_CLIENT_ID = 'salesmanago_client_id';
    public const SALESMANAGO_API_SECRET = 'salesmanago_api_secret';
    public const SALESMANAGO_TRACKING_CODE = 'salesmanago_tracking_code';
    public const SALESMANAGO_CHECKOUT_MODE = 'salesmanago_checkout_mode';
    public const SALESMANAGO_CHECKOUT_LABEL = 'salesmanago_checkout_label';
    public const SALESMANAGO_TAGS = 'bpmj_eddsm_salesmanago_tags';

    public const IPRESSO_INTEGRATION = 'integration_edd-ipresso';
    public const IPRESSO_API_ENDPOINT = 'bpmj_eddip_api_endpoint';
    public const IPRESSO_API = 'bpmj_eddip_api';
    public const IPRESSO_API_LOGIN = 'bpmj_eddip_api_login';
    public const IPRESSO_API_PASSWORD = 'bpmj_eddip_api_password';
    public const IPRESSO_SHOW_CHECKOUT_SIGNUP = 'bpmj_eddip_show_checkout_signup';
    public const IPRESSO_TRACKING_CODE = 'bpmj_eddip_tracking_code';

    public const MAILERLITE_INTEGRATION = 'integration_edd-mailerlite';
    public const MAILERLITE_API = 'bpmj_edd_ml_api';
    public const MAILERLITE_GROUP = 'bpmj_edd_ml_group';
    public const MAILERLITE_SHOW_CHECKOUT_SIGNUP = 'bpmj_edd_ml_show_checkout_signup';
    public const MAILERLITE_LABEL = 'bpmj_edd_ml_label';
    public const MAILERLITE_DOUBLE_OPT_IN = 'bpmj_edd_ml_double_opt_in';

    public const INTERSPIRE_INTEGRATION = 'integration_edd-interspire';
    public const INTERSPIRE_USERNAME = 'bpmj_edd_in_username';
    public const INTERSPIRE_TOKEN = 'bpmj_edd_in_token';
    public const INTERSPIRE_XML_ENDPOINT = 'bpmj_edd_in_xmlEndpoint';
    public const INTERSPIRE_CONTACT_LIST = 'bpmj_edd_in_contact_list';
    public const INTERSPIRE_SHOW_CHECKOUT = 'bpmj_edd_in_show_checkout_signup';
    public const INTERSPIRE_LABEL = 'bpmj_edd_in_label';
    public const INTERSPIRE_DOUBLE_OPT_IN = 'bpmj_edd_in_double_opt_in';

    public const ACTIVECAMPAIGN_INTEGRATION = 'integration_edd-activecampaign';
    public const ACTIVECAMPAIGN_API_URL = 'bpmj_eddact_api_url';
    public const ACTIVECAMPAIGN_API_TOKEN = 'bpmj_eddact_api_token';
    public const ACTIVECAMPAIGN_SHOW_CHECKOUT_SIGNUP= 'bpmj_eddact_show_checkout_signup';
    public const ACTIVECAMPAIGN_LIST = 'bpmj_eddact_list';
    public const ACTIVECAMPAIGN_LIST_UNSUBSCRIBE = 'bpmj_eddact_list_unsubscribe';
    public const ACTIVECAMPAIGN_TAG = 'bpmj_eddact_tag';
    public const ACTIVECAMPAIGN_TAG_UNSUBSCRIBE = 'bpmj_eddact_tag_unsubscribe';
    public const ACTIVECAMPAIGN_LABEL = 'bpmj_eddact_label';
    public const ACTIVECAMPAIGN_FORM_ID = 'bpmj_eddact_form_id';
    
    public const CONVERTKIT_INTEGRATION = 'integration_edd-convertkit';
    public const CONVERTKIT_API = 'edd_convertkit_api';
    public const CONVERTKIT_API_SECRET = 'edd_convertkit_api_secret';
    public const CONVERTKIT_SHOW_CHECKOUT_SIGNUP = 'edd_convertkit_show_checkout_signup';
    public const CONVERTKIT_LIST = 'edd_convertkit_list';
    public const CONVERTKIT_LABEL = 'edd_convertkit_label';

    public const FAKTUROWNIA_INTEGRATION = 'integration_wp-fakturownia';
    public const FAKTUROWNIA_API_KEY = 'apikey';
    public const FAKTUROWNIA_DEPARTMETNS_ID = 'departments_id';
    public const FAKTUROWNIA_AUTO_SENT = 'auto_sent';
    public const FAKTUROWNIA_AUTO_SENT_RECEIPT = 'auto_sent_receipt';
    public const FAKTUROWNIA_RECEIPT = 'receipt';
    public const FAKTUROWNIA_VAT_EXEMPTION = 'vat_exemption';

    public const IFIRMA_INTEGRATION = 'integration_wp-ifirma';
    public const IFIRMA_EMAIL = 'ifirma_ifirma_email';
    public const IFIRMA_INVOICE_KEY = 'ifirma_ifirma_invoice_key';
    public const IFIRMA_SUBSCRIBER_KEY = 'ifirma_ifirma_subscriber_key';
    public const IFIRMA_VAT_EXEMPTION = 'ifirma_vat_exemption';
    public const IFIRMA_AUTO_SENT = 'ifirma_auto_sent';

    public const WFIRMA_INTEGRATION = 'integration_wp-wfirma';
    public const WFIRMA_AUTH_TYPE = 'wfirma_auth_type';
    public const WFIRMA_LOGIN = 'wfirma_wf_login';
    public const WFIRMA_PASS = 'wfirma_wf_pass';
    public const WFIRMA_OAUTH2_CLIENT_ID = 'wfirma_wf_oauth2_client_id';
    public const WFIRMA_OAUTH2_CLIENT_SECRET = 'wfirma_wf_oauth2_client_secret';
    public const WFIRMA_OAUTH_BUTTON_REDIR = 'wfirma_wf_oauth2_button_redir';
    public const WFIRMA_OAUTH2_AUTHORIZATION_CODE = 'wfirma_wf_oauth2_authorization_code';
    public const WFIRMA_COMPANY_ID = 'wfirma_wf_company_id';
    public const WFIRMA_RECEIPT = 'wfirma_receipt';
    public const WFIRMA_AUTO_SENT = 'wfirma_auto_sent';
    public const WFIRMA_AUTO_SENT_RECEIPT = 'wfirma_auto_sent_receipt';
    public const WFIRMA_OAUTH_REDIRECT_URL = 'https://wfirma.pl/oauth2/auth?response_type=code&client_id={client_id}&scope=contractors-read contractors-write invoices-read invoices-write&redirect_uri={return_url}';
    public const WFIRMA_OAUTH_RETURN_PATH = '/?pbg-listener=wfirma';
    public const WFIRMA_CONFIGURATION_PATH = 'admin.php?page=wp-idea-settings&autofocus=integrations';
    public const WFIRMA_IPECHO_SERVICE = 'http://mb.waw.pl/pbg/ipecho.php';
    
    public const TAXE_INTEGRATION = 'integration_wp-taxe';
    public const TAXE_LOGIN = 'taxe_taxe_login';
    public const TAXE_API_KEY = 'taxe_taxe_api_key';
    public const TAXE_VAT_EXEMPTION = 'taxe_vat_exemption';
    public const TAXE_RECEIPT = 'taxe_receipt';
    public const TAXE_AUTO_SENT = 'taxe_auto_sent';
    public const TAXE_AUTO_SENT_RECEIPT = 'taxe_auto_sent_receipt';

    public const INFAKT_INTEGRATION = 'integration_wp-infakt';
    public const INFAKT_API_KEY = 'infakt_infakt_api_key';
    public const INFAKT_VAT_EXEMPTION = 'infakt_vat_exemption';
    public const INFAKT_AUTO_SENT = 'infakt_auto_sent';

    private const ACTION_SYNC = 'action_sync';

    private Default_Storage_Places_Registry $default_storage_places_registry;

    public function __construct(
        Default_Storage_Places_Registry $default_storage_places_registry
    )
    {
        $this->default_storage_places_registry = $default_storage_places_registry;
    }

    public function get_name(): string
    {
        return 'integrations';
    }

    public function register_fields(): void
    {
        // invoicing systems

        $this->add_field(new Section_Heading(
            $this->translator->translate('settings.sections.integrations.fieldset.invoicing_systems')
        ));
        $this->add_fieldset(
            $this->translator->translate('settings.sections.integrations.fieldset.invoicing_systems'),
            (new Fields_Collection())
                ->add($this->get_fakturownia_integration_field())
                ->add($this->get_ifirma_integration_field())
                ->add($this->get_wfirma_integration_field())
                ->add($this->get_taxe_integration_field())
                ->add($this->get_infakt_integration_field())
        );

        // mailing systems

        $this->add_field(new Section_Heading(
            $this->translator->translate('settings.sections.integrations.fieldset.mailing_systems')
        ));

        $this->add_fieldset(                                                                                    
            $this->translator->translate('settings.sections.integrations.fieldset.mailing_systems.action'),
            (new Fields_Collection())
                ->add($this->get_action_mailing_field())
        );

        $this->add_fieldset(
            $this->translator->translate('settings.sections.integrations.fieldset.mailing_systems.pl'),
            (new Fields_Collection())
                ->add($this->get_getresponse_integration_field())
                ->add($this->get_freshmail_integration_field())
                ->add($this->get_salesmanago_integration_field())
                ->add($this->get_ipresso_integration_field())
        );
        $this->add_fieldset(
            $this->translator->translate('settings.sections.integrations.fieldset.mailing_systems.en'),
            (new Fields_Collection())
                ->add($this->get_mailchimp_integration_field())
                ->add($this->get_mailerlite_integration_field())
                ->add($this->get_interspire_integration_field())
                ->add($this->get_activecampaign_integration_field())
                ->add($this->get_convertkit_integration_field())
        );
    }

    private function get_action_mailing_field(): Abstract_Setting_Field
    {
        return (new Button_Reload_Api_Cache(
            self::ACTION_SYNC,
            $this->translator->translate('settings.sections.integrations.fieldset.mailing_systems.sync'),
            null,
            null,
            null,
            $this->translator->translate('settings.sections.integrations.fieldset.mailing_systems.sync.button')
        ))->set_url($this->get_reload_cache_url());

    }

    private function get_reload_cache_url() : string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => 'wp-idea-settings',
             'bpmj_eddcm_reload_cache' => 'mailers',
             'autofocus' => 'integrations',
             Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create('bpmj_eddcm_reload_cache')
        ]);
    }
    
    private function get_getresponse_integration_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::GETRESPONSE_INTEGRATION,
            $this->translator->translate('settings.sections.integrations.getresponse_integration'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_getresponse_eddres_token_field())
                ->add($this->get_getresponse_eddres_show_checkout_signup_field())
                ->add($this->get_getresponse_eddres_list_field())
                ->add($this->get_getresponse_eddres_list_unsubscribe_field())
                ->add($this->get_getresponse_eddres_label_field())
            ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.integrations.getresponse_integration'));
    }

    private function get_getresponse_eddres_token_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::GETRESPONSE_EDDRES_TOKEN,
            $this->translator->translate('settings.sections.integrations.getresponse.' . self::GETRESPONSE_EDDRES_TOKEN),
            $this->translator->translate('settings.sections.integrations.getresponse.' . self::GETRESPONSE_EDDRES_TOKEN . '.desc')
        );
    }

    private function get_getresponse_eddres_show_checkout_signup_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::GETRESPONSE_EDDRES_SHOW_CHECKOUT_SIGNUP,
            $this->translator->translate('settings.sections.integrations.getresponse.' . self::GETRESPONSE_EDDRES_SHOW_CHECKOUT_SIGNUP),
            null,
            $this->translator->translate('settings.sections.integrations.getresponse.' . self::GETRESPONSE_EDDRES_SHOW_CHECKOUT_SIGNUP . '.tooltip')
        );
    }
    
    private function get_getresponse_eddres_list_field(): Select_Setting_Field
    {
        return new Select_Setting_Field(
            self::GETRESPONSE_EDDRES_LIST,
            $this->translator->translate('settings.sections.integrations.getresponse.' . self::GETRESPONSE_EDDRES_LIST),
            $this->translator->translate('settings.sections.integrations.getresponse.' . self::GETRESPONSE_EDDRES_LIST . '.desc'),
            null,
            null,
            $this->get_mailer_lists(self::EDD_GETRESPONSE)
        );
    }
    
    private function get_getresponse_eddres_list_unsubscribe_field(): Select_Setting_Field
    {
        return new Select_Setting_Field(
            self::GETRESPONSE_EDDRES_LIST_UNSUBSCRIBE,
            $this->translator->translate('settings.sections.integrations.getresponse.' . self::GETRESPONSE_EDDRES_LIST_UNSUBSCRIBE),
            $this->translator->translate('settings.sections.integrations.getresponse.' . self::GETRESPONSE_EDDRES_LIST_UNSUBSCRIBE . '.desc'),
            null,
            null,
            $this->get_mailer_lists(self::EDD_GETRESPONSE)
        );
    }
    
    private function get_getresponse_eddres_label_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::GETRESPONSE_EDDRES_LABEL,
            $this->translator->translate('settings.sections.integrations.getresponse.' . self::GETRESPONSE_EDDRES_LABEL),
            $this->translator->translate('settings.sections.integrations.getresponse.' . self::GETRESPONSE_EDDRES_LABEL . '.desc')
        );
    }

    private function get_freshmail_integration_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::FRESHMAIL_INTEGRATION,
            $this->translator->translate('settings.sections.integrations.freshmail_integration'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_freshmail_api_key_field())
                ->add($this->get_freshmail_api_secret_field())
                ->add($this->get_freshmail_show_checkout_signup_field())
                ->add($this->get_freshmail_group_field())
                ->add($this->get_freshmail_group_unsubscribe_field())
                ->add($this->get_freshmail_label_field())
                ->add($this->get_freshmail_double_opt_in_field())
            ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.integrations.freshmail_integration'));
    }

    private function get_freshmail_api_key_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::FRESHMAIL_API_KEY,
            $this->translator->translate('settings.sections.integrations.freshmail.' . self::FRESHMAIL_API_KEY),
            $this->translator->translate('settings.sections.integrations.freshmail.' . self::FRESHMAIL_API_KEY . '.desc')
        );
    }

    private function get_freshmail_api_secret_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::FRESHMAIL_API_SECRET,
            $this->translator->translate('settings.sections.integrations.freshmail.' . self::FRESHMAIL_API_SECRET),
            $this->translator->translate('settings.sections.integrations.freshmail.' . self::FRESHMAIL_API_SECRET . '.desc')
        );
    }

    private function get_freshmail_show_checkout_signup_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::FRESHMAIL_SHOW_CHECKOUT_SIGNUP,
            $this->translator->translate('settings.sections.integrations.freshmail.' . self::FRESHMAIL_SHOW_CHECKOUT_SIGNUP),
            null,
            $this->translator->translate('settings.sections.integrations.freshmail.' . self::FRESHMAIL_SHOW_CHECKOUT_SIGNUP . '.tooltip')
            );
    }

    private function get_freshmail_group_field(): Select_Setting_Field
    {
        return new Select_Setting_Field(
            self::FRESHMAIL_GROUP,
            $this->translator->translate('settings.sections.integrations.freshmail.' . self::FRESHMAIL_GROUP),
            $this->translator->translate('settings.sections.integrations.freshmail.' . self::FRESHMAIL_GROUP . '.desc'),
            null,
            null,
            $this->get_mailer_lists(self::EDD_FRESHMAIL)
            );
    }
    
    private function get_freshmail_group_unsubscribe_field(): Select_Setting_Field
    {
        return new Select_Setting_Field(
            self::FRESHMAIL_GROUP_UNSUBSCRIBE,
            $this->translator->translate('settings.sections.integrations.freshmail.' . self::FRESHMAIL_GROUP_UNSUBSCRIBE),
            $this->translator->translate('settings.sections.integrations.freshmail.' . self::FRESHMAIL_GROUP_UNSUBSCRIBE . '.desc'),
            null,
            null,
            $this->get_mailer_lists(self::EDD_FRESHMAIL)
            );
    }
    
    private function get_freshmail_label_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::FRESHMAIL_LABEL,
            $this->translator->translate('settings.sections.integrations.freshmail.' . self::FRESHMAIL_LABEL),
            $this->translator->translate('settings.sections.integrations.freshmail.' . self::FRESHMAIL_LABEL . '.desc')
            );
    }
    
    private function get_freshmail_double_opt_in_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::FRESHMAIL_DOUBLE_OPT_IN,
            $this->translator->translate('settings.sections.integrations.freshmail.' . self::FRESHMAIL_DOUBLE_OPT_IN),
            null,
            $this->translator->translate('settings.sections.integrations.freshmail.' . self::FRESHMAIL_DOUBLE_OPT_IN . '.tooltip')
            );
    }
    
    private function get_salesmanago_integration_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::SALESMANAGO_INTEGRATION,
            $this->translator->translate('settings.sections.integrations.salesmanago_integration'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_salesmanago_owner_field())
                ->add($this->get_salesmanago_endpoint_field())
                ->add($this->get_salesmanago_client_id_field())
                ->add($this->get_salesmanago_api_secret_field())
                ->add($this->get_salesmanago_tracking_code_field())
                ->add($this->get_salesmanago_checkout_mode_field())
                ->add($this->get_salesmanago_checkout_label_field())
                ->add($this->get_salesmanago_tags_field())
        ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.integrations.salesmanago_integration'));
    }  

    private function get_salesmanago_owner_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::SALESMANAGO_OWNER,
            $this->translator->translate('settings.sections.integrations.salesmanago.' . self::SALESMANAGO_OWNER),
            $this->translator->translate('settings.sections.integrations.salesmanago.' . self::SALESMANAGO_OWNER . '.desc')
        );
    }

    private function get_salesmanago_endpoint_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::SALESMANAGO_ENDPOINT,
            $this->translator->translate('settings.sections.integrations.salesmanago.' . self::SALESMANAGO_ENDPOINT),
            $this->translator->translate('settings.sections.integrations.salesmanago.' . self::SALESMANAGO_ENDPOINT . '.desc')
        );
    }

    private function get_salesmanago_client_id_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::SALESMANAGO_CLIENT_ID,
            $this->translator->translate('settings.sections.integrations.salesmanago.' . self::SALESMANAGO_CLIENT_ID),
            $this->translator->translate('settings.sections.integrations.salesmanago.' . self::SALESMANAGO_CLIENT_ID . '.desc')
        );
    }

    private function get_salesmanago_api_secret_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::SALESMANAGO_API_SECRET,
            $this->translator->translate('settings.sections.integrations.salesmanago.' . self::SALESMANAGO_API_SECRET),
            $this->translator->translate('settings.sections.integrations.salesmanago.' . self::SALESMANAGO_API_SECRET . '.desc')
        );
    }

    private function get_salesmanago_tracking_code_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::SALESMANAGO_TRACKING_CODE,
            $this->translator->translate('settings.sections.integrations.salesmanago.' . self::SALESMANAGO_TRACKING_CODE),
            null,
            $this->translator->translate('settings.sections.integrations.salesmanago.' . self::SALESMANAGO_TRACKING_CODE . '.tooltip')
        );
    }

    private function get_salesmanago_checkout_mode_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::SALESMANAGO_CHECKOUT_MODE,
            $this->translator->translate('settings.sections.integrations.salesmanago.' . self::SALESMANAGO_CHECKOUT_MODE),
            null,
            $this->translator->translate('settings.sections.integrations.salesmanago.' . self::SALESMANAGO_CHECKOUT_MODE . '.tooltip')
        );
    }

    private function get_salesmanago_checkout_label_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::SALESMANAGO_CHECKOUT_LABEL,
            $this->translator->translate('settings.sections.integrations.salesmanago.' . self::SALESMANAGO_CHECKOUT_LABEL),
            $this->translator->translate('settings.sections.integrations.salesmanago.' . self::SALESMANAGO_CHECKOUT_LABEL . '.desc')
        );
    }

    private function get_salesmanago_tags_field(): Abstract_Setting_Field
    {
        return (new Text_Area_Setting_Field(
            self::SALESMANAGO_TAGS,
            $this->translator->translate('settings.sections.integrations.salesmanago.' . self::SALESMANAGO_TAGS),
            $this->translator->translate('settings.sections.integrations.salesmanago.' . self::SALESMANAGO_TAGS . '.desc')
        ))->set_placeholder($this->translator->translate('settings.sections.integrations.salesmanago.' . self::SALESMANAGO_TAGS . '.placeholder'));
    }

    private function get_ipresso_integration_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::IPRESSO_INTEGRATION,
            $this->translator->translate('settings.sections.integrations.ipresso_integration'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_ipresso_api_endpoint_field())
                ->add($this->get_ipresso_api_field())
                ->add($this->get_ipresso_api_login_field())
                ->add($this->get_ipresso_api_password_field())
                ->add($this->get_ipresso_show_checkout_signup_field())
                ->add($this->get_ipresso_tracking_code_field())
        ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.integrations.ipresso_integration'));
    }

    private function get_ipresso_api_endpoint_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::IPRESSO_API_ENDPOINT,
            $this->translator->translate('settings.sections.integrations.ipresso.' . self::IPRESSO_API_ENDPOINT),
            $this->translator->translate('settings.sections.integrations.ipresso.' . self::IPRESSO_API_ENDPOINT . '.desc')
        );
    }

    private function get_ipresso_api_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::IPRESSO_API,
            $this->translator->translate('settings.sections.integrations.ipresso.' . self::IPRESSO_API),
            $this->translator->translate('settings.sections.integrations.ipresso.' . self::IPRESSO_API . '.desc')
        );
    }

    private function get_ipresso_api_login_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::IPRESSO_API_LOGIN,
            $this->translator->translate('settings.sections.integrations.ipresso.' . self::IPRESSO_API_LOGIN),
            $this->translator->translate('settings.sections.integrations.ipresso.' . self::IPRESSO_API_LOGIN . '.desc')
        );
    }

    private function get_ipresso_api_password_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::IPRESSO_API_PASSWORD,
            $this->translator->translate('settings.sections.integrations.ipresso.' . self::IPRESSO_API_PASSWORD),
            $this->translator->translate('settings.sections.integrations.ipresso.' . self::IPRESSO_API_PASSWORD . '.desc')
        );
    }

    private function get_ipresso_show_checkout_signup_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::IPRESSO_SHOW_CHECKOUT_SIGNUP,
            $this->translator->translate('settings.sections.integrations.ipresso.' . self::IPRESSO_SHOW_CHECKOUT_SIGNUP),
            null,
            $this->translator->translate('settings.sections.integrations.ipresso.' . self::IPRESSO_SHOW_CHECKOUT_SIGNUP . '.tooltip')
        );
    }

    private function get_ipresso_tracking_code_field(): Abstract_Setting_Field
    {
        return new Text_Area_Setting_Field(
            self::IPRESSO_TRACKING_CODE,
            $this->translator->translate('settings.sections.integrations.ipresso.' . self::IPRESSO_TRACKING_CODE),
            $this->translator->translate('settings.sections.integrations.ipresso.' . self::IPRESSO_TRACKING_CODE . '.desc')
        );
    }

    private function get_mailchimp_integration_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::MAILCHIMP_INTEGRATION,
            $this->translator->translate('settings.sections.integrations.mailchimp_integration'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_mailchimp_api_field())
                ->add($this->get_mailchimp_list_field())
                ->add($this->get_mailchimp_show_checkout_signup_field())
                ->add($this->get_mailchimp_label_field())
                ->add($this->get_mailchimp_double_opt_in_field())
        ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.integrations.mailchimp_integration'));
    }

    private function get_mailchimp_api_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::MAILCHIMP_API_KEY,
            $this->translator->translate('settings.sections.integrations.mailchimp.' . self::MAILCHIMP_API_KEY),
            $this->translator->translate('settings.sections.integrations.mailchimp.' . self::MAILCHIMP_API_KEY . '.desc')
        );
    }

    private function get_mailchimp_list_field(): Select_Setting_Field
    {
        return new Select_Setting_Field(
            self::MAILCHIMP_LIST,
            $this->translator->translate('settings.sections.integrations.mailchimp.' . self::MAILCHIMP_LIST),
            $this->translator->translate('settings.sections.integrations.mailchimp.' . self::MAILCHIMP_LIST . '.desc'),
            null,
            null,
            $this->get_mailer_lists(self::EDD_MAILCHIMP)
        );
    }

    private function get_mailchimp_show_checkout_signup_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::MAILCHIMP_SHOW_CHECKOUT_SIGNUP,
            $this->translator->translate('settings.sections.integrations.mailchimp.' . self::MAILCHIMP_SHOW_CHECKOUT_SIGNUP),
            null,
            $this->translator->translate('settings.sections.integrations.mailchimp.' . self::MAILCHIMP_SHOW_CHECKOUT_SIGNUP . '.tooltip')
        );
    }

    private function get_mailchimp_label_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::MAILCHIMP_LABEL,
            $this->translator->translate('settings.sections.integrations.mailchimp.' . self::MAILCHIMP_LABEL),
            $this->translator->translate('settings.sections.integrations.mailchimp.' . self::MAILCHIMP_LABEL . '.desc')
        );
    }

    private function get_mailchimp_double_opt_in_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::MAILCHIMP_DOUBLE_OPT_IN,
            $this->translator->translate('settings.sections.integrations.mailchimp.' . self::MAILCHIMP_DOUBLE_OPT_IN),
            null,
            $this->translator->translate('settings.sections.integrations.mailchimp.' . self::MAILCHIMP_DOUBLE_OPT_IN . '.tooltip')
        );
    }

    private function get_mailerlite_integration_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::MAILERLITE_INTEGRATION,
            $this->translator->translate('settings.sections.integrations.mailerlite_integration'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_mailerlite_api_field())
                ->add($this->get_mailerlite_group_field())
                ->add($this->get_mailerlite_show_checkout_signup_field())
                ->add($this->get_mailerlite_label_field())
                ->add($this->get_mailerlite_double_opt_in_field())
        ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.integrations.mailerlite_integration'));
    }

    private function get_mailerlite_api_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::MAILERLITE_API,
            $this->translator->translate('settings.sections.integrations.mailerlite.' . self::MAILERLITE_API),
            $this->translator->translate('settings.sections.integrations.mailerlite.' . self::MAILERLITE_API . '.desc')
        );
    }

    private function get_mailerlite_group_field(): Abstract_Setting_Field
    {
        return new Select_Setting_Field(
            self::MAILERLITE_GROUP,
            $this->translator->translate('settings.sections.integrations.mailerlite.' . self::MAILERLITE_GROUP),
            $this->translator->translate('settings.sections.integrations.mailerlite.' . self::MAILERLITE_GROUP . '.desc'),
            null,
            null,
            $this->get_mailer_lists(self::EDD_MAILERLITE)
        );
    }

    private function get_mailerlite_show_checkout_signup_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::MAILERLITE_SHOW_CHECKOUT_SIGNUP,
            $this->translator->translate('settings.sections.integrations.mailerlite.' . self::MAILERLITE_SHOW_CHECKOUT_SIGNUP),
            null,
            $this->translator->translate('settings.sections.integrations.mailerlite.' . self::MAILERLITE_SHOW_CHECKOUT_SIGNUP . '.tooltip')
        );
    }

    private function get_mailerlite_label_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::MAILERLITE_LABEL,
            $this->translator->translate('settings.sections.integrations.mailerlite.' . self::MAILERLITE_LABEL),
            null,
            $this->translator->translate('settings.sections.integrations.mailerlite.' . self::MAILERLITE_LABEL . '.tooltip')
        );
    }

    private function get_mailerlite_double_opt_in_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::MAILERLITE_DOUBLE_OPT_IN,
            $this->translator->translate('settings.sections.integrations.mailerlite.' . self::MAILERLITE_DOUBLE_OPT_IN),
            null,
            $this->translator->translate('settings.sections.integrations.mailerlite.' . self::MAILERLITE_DOUBLE_OPT_IN . '.tooltip')
        );
    }

    private function get_interspire_integration_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::INTERSPIRE_INTEGRATION,
            $this->translator->translate('settings.sections.integrations.interspire_integration'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_interspire_username_field())
                ->add($this->get_interspire_token_field())
                ->add($this->get_interspire_xml_endpoint_field())
                ->add($this->get_interspire_contact_list_field())
                ->add($this->get_interspire_show_checkout_signup_field())
                ->add($this->get_interspire_label_field())
                ->add($this->get_interspire_double_opt_in_field())
        ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.integrations.interspire_integration'));
    }

    private function get_interspire_username_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::INTERSPIRE_USERNAME,
            $this->translator->translate('settings.sections.integrations.interspire.' . self::INTERSPIRE_USERNAME),
            $this->translator->translate('settings.sections.integrations.interspire.' . self::INTERSPIRE_USERNAME . '.desc')
        );
    }

    private function get_interspire_token_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::INTERSPIRE_TOKEN,
            $this->translator->translate('settings.sections.integrations.interspire.' . self::INTERSPIRE_TOKEN),
            $this->translator->translate('settings.sections.integrations.interspire.' . self::INTERSPIRE_TOKEN . '.desc')
        );
    }

    private function get_interspire_xml_endpoint_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::INTERSPIRE_XML_ENDPOINT,
            $this->translator->translate('settings.sections.integrations.interspire.' . self::INTERSPIRE_XML_ENDPOINT),
            $this->translator->translate('settings.sections.integrations.interspire.' . self::INTERSPIRE_XML_ENDPOINT . '.desc')
        );
    }

    private function get_interspire_contact_list_field(): Abstract_Setting_Field
    {
        return new Select_Setting_Field(
            self::INTERSPIRE_CONTACT_LIST,
            $this->translator->translate('settings.sections.integrations.interspire.' . self::INTERSPIRE_CONTACT_LIST),
            $this->translator->translate('settings.sections.integrations.interspire.' . self::INTERSPIRE_CONTACT_LIST . '.desc'),
            null,
            null,
            $this->get_mailer_lists(self::EDD_INTERSPIRE)
        );
    }

    private function get_interspire_show_checkout_signup_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::INTERSPIRE_SHOW_CHECKOUT,
            $this->translator->translate('settings.sections.integrations.interspire.' . self::INTERSPIRE_SHOW_CHECKOUT),
            null,
            $this->translator->translate('settings.sections.integrations.interspire.' . self::INTERSPIRE_SHOW_CHECKOUT . '.tooltip')
        );
    }

    private function get_interspire_label_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::INTERSPIRE_LABEL,
            $this->translator->translate('settings.sections.integrations.interspire.' . self::INTERSPIRE_LABEL),
            $this->translator->translate('settings.sections.integrations.interspire.' . self::INTERSPIRE_LABEL . '.desc')
        );
    }

    private function get_interspire_double_opt_in_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::INTERSPIRE_DOUBLE_OPT_IN,
            $this->translator->translate('settings.sections.integrations.interspire.' . self::INTERSPIRE_DOUBLE_OPT_IN),
            null,
            $this->translator->translate('settings.sections.integrations.interspire.' . self::INTERSPIRE_DOUBLE_OPT_IN . '.tooltip')
        );
    }

    private function get_activecampaign_integration_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::ACTIVECAMPAIGN_INTEGRATION,
            $this->translator->translate('settings.sections.integrations.activecampaign_integration'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_activecampaign_api_url_field())
                ->add($this->get_activecampaign_api_token_field())
                ->add($this->get_activecampaign_show_checkout_signup_field())
                ->add($this->get_activecampaign_list_field())
                ->add($this->get_activecampaign_list_unsubscribe_field())
                ->add($this->get_activecampaign_tag_field())
                ->add($this->get_activecampaign_tag_unsubscribe_field())
                ->add($this->get_activecampaign_label_field())
                ->add($this->get_activecampaign_form_id_field())
            ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.integrations.activecampaign_integration'));
    }

    private function get_activecampaign_api_url_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::ACTIVECAMPAIGN_API_URL,
            $this->translator->translate('settings.sections.integrations.activecampaign.' . self::ACTIVECAMPAIGN_API_URL),
            $this->translator->translate('settings.sections.integrations.activecampaign.' . self::ACTIVECAMPAIGN_API_URL . '.desc')
        );
    }

    private function get_activecampaign_api_token_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::ACTIVECAMPAIGN_API_TOKEN,
            $this->translator->translate('settings.sections.integrations.activecampaign.' . self::ACTIVECAMPAIGN_API_TOKEN),
            $this->translator->translate('settings.sections.integrations.activecampaign.' . self::ACTIVECAMPAIGN_API_TOKEN . '.desc')
        );
    }

    private function get_activecampaign_show_checkout_signup_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::ACTIVECAMPAIGN_SHOW_CHECKOUT_SIGNUP,
            $this->translator->translate('settings.sections.integrations.activecampaign.' . self::ACTIVECAMPAIGN_SHOW_CHECKOUT_SIGNUP),
            null,
            $this->translator->translate('settings.sections.integrations.activecampaign.' . self::ACTIVECAMPAIGN_SHOW_CHECKOUT_SIGNUP . '.tooltip')
        );
    }
    
    private function get_activecampaign_list_field(): Abstract_Setting_Field
    {
        return new Select_Setting_Field(
            self::ACTIVECAMPAIGN_LIST,
            $this->translator->translate('settings.sections.integrations.activecampaign.' . self::ACTIVECAMPAIGN_LIST),
            $this->translator->translate('settings.sections.integrations.activecampaign.' . self::ACTIVECAMPAIGN_LIST . '.desc'),
            null,
            null,
            $this->get_mailer_lists(self::EDD_ACTIVECAMPAIGN)
        );
    }
    
    private function get_activecampaign_list_unsubscribe_field(): Abstract_Setting_Field
    {
        return new Select_Setting_Field(
            self::ACTIVECAMPAIGN_LIST_UNSUBSCRIBE,
            $this->translator->translate('settings.sections.integrations.activecampaign.' . self::ACTIVECAMPAIGN_LIST_UNSUBSCRIBE),
            $this->translator->translate('settings.sections.integrations.activecampaign.' . self::ACTIVECAMPAIGN_LIST_UNSUBSCRIBE . '.desc'),
            null,
            null,
            $this->get_mailer_lists(self::EDD_ACTIVECAMPAIGN)
        );
    }

    private function get_activecampaign_tag_field(): Abstract_Setting_Field
    {
        return (new Text_Area_Setting_Field(
            self::ACTIVECAMPAIGN_TAG,
            $this->translator->translate('settings.sections.integrations.activecampaign.' . self::ACTIVECAMPAIGN_TAG),
            $this->translator->translate('settings.sections.integrations.activecampaign.' . self::ACTIVECAMPAIGN_TAG . '.desc')
        ))->set_placeholder($this->translator->translate('settings.sections.integrations.activecampaign.' . self::ACTIVECAMPAIGN_TAG . '.placeholder'));
    }
    
    
    private function get_activecampaign_tag_unsubscribe_field(): Abstract_Setting_Field
    {
        return (new Text_Area_Setting_Field(
            self::ACTIVECAMPAIGN_TAG_UNSUBSCRIBE,
            $this->translator->translate('settings.sections.integrations.activecampaign.' . self::ACTIVECAMPAIGN_TAG_UNSUBSCRIBE),
            $this->translator->translate('settings.sections.integrations.activecampaign.' . self::ACTIVECAMPAIGN_TAG_UNSUBSCRIBE . '.desc')
        ))->set_placeholder($this->translator->translate('settings.sections.integrations.activecampaign.' . self::ACTIVECAMPAIGN_TAG_UNSUBSCRIBE . '.placeholder'));
    }
    
    private function get_activecampaign_label_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::ACTIVECAMPAIGN_LABEL,
            $this->translator->translate('settings.sections.integrations.activecampaign.' . self::ACTIVECAMPAIGN_LABEL),
            $this->translator->translate('settings.sections.integrations.activecampaign.' . self::ACTIVECAMPAIGN_LABEL . '.desc')
        );
    }
    
    private function get_activecampaign_form_id_field(): Abstract_Setting_Field
    {
        return new Select_Setting_Field(
            self::ACTIVECAMPAIGN_FORM_ID,
            $this->translator->translate('settings.sections.integrations.activecampaign.' . self::ACTIVECAMPAIGN_FORM_ID),
            $this->translator->translate('settings.sections.integrations.activecampaign.' . self::ACTIVECAMPAIGN_FORM_ID . '.desc'),
            null,
            null,
            $this->get_mailer_forms(self::EDD_ACTIVECAMPAIGN)
        );
    }

    private function get_convertkit_integration_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::CONVERTKIT_INTEGRATION,
            $this->translator->translate('settings.sections.integrations.convertkit_integration'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_convertkit_api_field())
                ->add($this->get_convertkit_api_secret_field())
                ->add($this->get_convertkit_show_checkout_signup_field())
                ->add($this->get_convertkit_list_field())
                ->add($this->get_convertkit_label_field())
        ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.integrations.convertkit_integration'));
    }

    private function get_convertkit_api_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::CONVERTKIT_API,
            $this->translator->translate('settings.sections.integrations.convertkit.' . self::CONVERTKIT_API),
            $this->translator->translate('settings.sections.integrations.convertkit.' . self::CONVERTKIT_API . '.desc')
        );
    }

    private function get_convertkit_api_secret_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::CONVERTKIT_API_SECRET,
            $this->translator->translate('settings.sections.integrations.convertkit.' . self::CONVERTKIT_API_SECRET),
            $this->translator->translate('settings.sections.integrations.convertkit.' . self::CONVERTKIT_API_SECRET . '.desc')
        );
    }

    private function get_convertkit_show_checkout_signup_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::CONVERTKIT_SHOW_CHECKOUT_SIGNUP,
            $this->translator->translate('settings.sections.integrations.convertkit.' . self::CONVERTKIT_SHOW_CHECKOUT_SIGNUP),
            null,
            $this->translator->translate('settings.sections.integrations.convertkit.' . self::CONVERTKIT_SHOW_CHECKOUT_SIGNUP . '.tooltip')
        );
    }

    private function get_convertkit_list_field(): Abstract_Setting_Field
    {
        return new Select_Setting_Field(
            self::CONVERTKIT_LIST,
            $this->translator->translate('settings.sections.integrations.convertkit.' . self::CONVERTKIT_LIST),
            $this->translator->translate('settings.sections.integrations.convertkit.' . self::CONVERTKIT_LIST . '.desc'),
            null,
            null,
            $this->get_mailer_lists(self::EDD_CONVERKIT)
        );
    }

    private function get_convertkit_label_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::CONVERTKIT_LABEL,
            $this->translator->translate('settings.sections.integrations.convertkit.' . self::CONVERTKIT_LABEL),
            $this->translator->translate('settings.sections.integrations.convertkit.' . self::CONVERTKIT_LABEL . '.desc')
        );
    }

    private function get_fakturownia_integration_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::FAKTUROWNIA_INTEGRATION,
            $this->translator->translate('settings.sections.integrations.fakturownia_integration'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_fakturownia_api_key_field())
                ->add($this->get_fakturownia_departments_id_field())
                ->add($this->get_fakturownia_auto_sent_field())
                ->add($this->get_fakturownia_auto_sent_receipt_field())
                ->add($this->get_fakturownia_receipt_field())
                ->add($this->get_fakturownia_vat_exemption_field())
        ))->set_popup($this->settings_popup,
                      $this->translator->translate('settings.sections.integrations.fakturownia_integration'));
    }

    private function get_fakturownia_api_key_field(): Abstract_Setting_Field
    {
        $field = (new Text_Setting_Field(
            self::FAKTUROWNIA_API_KEY,
            $this->translator->translate('settings.sections.integrations.fakturownia.' . self::FAKTUROWNIA_API_KEY),
            $this->translator->translate('settings.sections.integrations.fakturownia.' . self::FAKTUROWNIA_API_KEY . '.desc')
        ))->set_storage_place($this->default_storage_places_registry->get_fakturownia_storage_place());

        $field->set_validation_callback(function ($value) {
            $results = new Setting_Field_Validation_Result();

            if (!empty($value) && !preg_match( '/[a-zA-Z0-9]{10,}\/[a-z0-9]/', $value)) {
                $results->add_error_message('settings.field.validation.fakturownia.invalid_apikey');
            }

            return $results;
        });

        return $field;
    }

    private function get_fakturownia_departments_id_field(): Abstract_Setting_Field
    {
        return (new Number_Setting_Field(
            self::FAKTUROWNIA_DEPARTMETNS_ID,
            $this->translator->translate('settings.sections.integrations.fakturownia.' . self::FAKTUROWNIA_DEPARTMETNS_ID),
            $this->translator->translate('settings.sections.integrations.fakturownia.' . self::FAKTUROWNIA_DEPARTMETNS_ID . '.desc')
        ))->set_storage_place($this->default_storage_places_registry->get_fakturownia_storage_place());
    }

    private function get_fakturownia_auto_sent_field(): Abstract_Setting_Field
    {
        return (new Checkbox_Setting_Field(
            self::FAKTUROWNIA_AUTO_SENT,
            $this->translator->translate('settings.sections.integrations.fakturownia.' . self::FAKTUROWNIA_AUTO_SENT),
            $this->translator->translate('settings.sections.integrations.fakturownia.' . self::FAKTUROWNIA_AUTO_SENT . '.desc')

        ))->set_storage_place($this->default_storage_places_registry->get_fakturownia_storage_place());
    }

    private function get_fakturownia_auto_sent_receipt_field(): Abstract_Setting_Field
    {
        return (new Checkbox_Setting_Field(
            self::FAKTUROWNIA_AUTO_SENT_RECEIPT,
            $this->translator->translate('settings.sections.integrations.fakturownia.' . self::FAKTUROWNIA_AUTO_SENT_RECEIPT),
            $this->translator->translate('settings.sections.integrations.fakturownia.' . self::FAKTUROWNIA_AUTO_SENT_RECEIPT . '.desc')
        ))->set_storage_place($this->default_storage_places_registry->get_fakturownia_storage_place());
    }

    private function get_fakturownia_receipt_field(): Abstract_Setting_Field
    {
        return (new Checkbox_Setting_Field(
            self::FAKTUROWNIA_RECEIPT,
            $this->translator->translate('settings.sections.integrations.fakturownia.' . self::FAKTUROWNIA_RECEIPT)
        ))->set_storage_place($this->default_storage_places_registry->get_fakturownia_storage_place());
    }

    private function get_fakturownia_vat_exemption_field(): Abstract_Setting_Field
    {
        return (new Text_Area_Setting_Field(
            self::FAKTUROWNIA_VAT_EXEMPTION,
            $this->translator->translate('settings.sections.integrations.fakturownia.' . self::FAKTUROWNIA_VAT_EXEMPTION),
            null,
            null,
            null,
            'SPRZEDAWCA ZWOLNIONY PODMIOTOWO Z PODATKU OD TOWARU I USŁUG (dostawa towarów lub świadczenie usług zwolnione na podstawie art. 113 ust 1 (albo ust. 9) ustawy z dnia 11 marca 2004 r. o podatku od towarów i usług (Dz. U. z 2011 r Nr 177, poz. 1054 z późn. zm.)).'
        ))->set_storage_place($this->default_storage_places_registry->get_fakturownia_storage_place());
    }

    private function get_ifirma_integration_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::IFIRMA_INTEGRATION,
            $this->translator->translate('settings.sections.integrations.ifirma_integration'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_ifirma_email_field())
                ->add($this->get_ifirma_invoice_key_field())
                ->add($this->get_ifirma_subscriber_key_field())
                ->add($this->get_ifirma_vat_exemption_field())
                ->add($this->get_ifirma_auto_sent_field())
        ))->set_popup($this->settings_popup,
            $this->translator->translate('settings.sections.integrations.ifirma_integration'));
    }

    private function get_ifirma_email_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::IFIRMA_EMAIL,
            $this->translator->translate('settings.sections.integrations.ifirma.' . self::IFIRMA_EMAIL),
            $this->translator->translate('settings.sections.integrations.ifirma.' . self::IFIRMA_EMAIL . '.desc')
        );
    }

    private function get_ifirma_invoice_key_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::IFIRMA_INVOICE_KEY,
            $this->translator->translate('settings.sections.integrations.ifirma.' . self::IFIRMA_INVOICE_KEY),
            $this->translator->translate('settings.sections.integrations.ifirma.' . self::IFIRMA_INVOICE_KEY . '.desc')
        );
    }

    private function get_ifirma_subscriber_key_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::IFIRMA_SUBSCRIBER_KEY,
            $this->translator->translate('settings.sections.integrations.ifirma.' . self::IFIRMA_SUBSCRIBER_KEY),
            $this->translator->translate('settings.sections.integrations.ifirma.' . self::IFIRMA_SUBSCRIBER_KEY . '.desc')
        );
    }

    private function get_ifirma_vat_exemption_field(): Abstract_Setting_Field
    {
        return (new Text_Setting_Field(
            self::IFIRMA_VAT_EXEMPTION,
            $this->translator->translate('settings.sections.integrations.ifirma.' . self::IFIRMA_VAT_EXEMPTION),
            null,
            null,
            null,
            $this->translator->translate('settings.sections.integrations.ifirma.' . self::IFIRMA_VAT_EXEMPTION . '.value')
        ))->set_max_length(30);
    }

    private function get_ifirma_auto_sent_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::IFIRMA_AUTO_SENT,
            $this->translator->translate('settings.sections.integrations.ifirma.' . self::IFIRMA_AUTO_SENT),
            $this->translator->translate('settings.sections.integrations.ifirma.' . self::IFIRMA_AUTO_SENT . '.desc')
        );
    }

    private function get_mailer_lists(string $name, string $list_type = 'list'): array
    {
        return bpmj_wpid_get_mailer_data($name);
    }

    private function get_mailer_forms(string $name): array
    {
        return bpmj_wpid_get_mailer_data($name, 'forms');
    }
    
    private function get_wfirma_integration_field(): Abstract_Setting_Field
    {
        $bpmj_wpwf_settings = get_option( 'bpmj_wpwf_settings' );

        $additional_fields = new Additional_Fields_Collection();
        if(isset($bpmj_wpwf_settings[ 'auth_type' ]) || (isset($bpmj_wpwf_settings[ 'wf_login' ]) && $bpmj_wpwf_settings[ 'wf_login' ] != '')) {
            $additional_fields->add($this->get_wfirma_auth_type_field());
        }
        $additional_fields->add($this->get_wfirma_login_field())
            ->add($this->get_wfirma_pass_field())
            ->add($this->get_wfirma_oauth2_message1())
            ->add($this->get_wfirma_oauth2_client_id_field())
            ->add($this->get_wfirma_oauth2_client_secret_field())
            ->add($this->get_wfirma_oauth2_message2())
            ->add($this->get_wfirma_oauth2_redir_field())
            ->add($this->get_wfirma_oauth2_authorization_code_field())
            ->add($this->get_wfirma_company_id_field())
            ->add($this->get_wfirma_receipt_field())
            ->add($this->get_wfirma_auto_sent_field())
            ->add($this->get_wfirma_auto_sent_receipt_field());

        return (new Toggle_Setting_Field(
            self::WFIRMA_INTEGRATION,
            $this->translator->translate('settings.sections.integrations.wfirma_integration'),
            null,
            null,
            $additional_fields
        ))->set_popup($this->settings_popup,
                      $this->translator->translate('settings.sections.integrations.wfirma_integration'));
    }

    private function get_wfirma_auth_type_field(): Select_Setting_Field
    {
        $options = [
            'basic' => $this->translator->translate('settings.sections.integrations.wfirma.option.' . self::WFIRMA_AUTH_TYPE . '.basic'),
            'oauth2' => $this->translator->translate('settings.sections.integrations.wfirma.option.' . self::WFIRMA_AUTH_TYPE . '.oauth2')
        ];
        
        return new Select_Setting_Field(
            self::WFIRMA_AUTH_TYPE,
            $this->translator->translate('settings.sections.integrations.wfirma.' . self::WFIRMA_AUTH_TYPE),
            $this->translator->translate('settings.sections.integrations.wfirma.' . self::WFIRMA_AUTH_TYPE . '.desc'),
            $this->translator->translate('settings.sections.integrations.wfirma.' . self::WFIRMA_AUTH_TYPE . '.tooltip'),
            null,
            $options
        );
    }
    
    private function get_wfirma_login_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::WFIRMA_LOGIN,
            $this->translator->translate('settings.sections.integrations.wfirma.' . self::WFIRMA_LOGIN),
            $this->translator->translate('settings.sections.integrations.wfirma.' . self::WFIRMA_LOGIN . '.desc')
        );
    }

    private function get_wfirma_pass_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::WFIRMA_PASS,
            $this->translator->translate('settings.sections.integrations.wfirma.' . self::WFIRMA_PASS),
            $this->translator->translate('settings.sections.integrations.wfirma.' . self::WFIRMA_PASS . '.desc')
        );
    }

    private function get_wfirma_oauth2_message1(): Abstract_Setting_Field
    {
        $ip = file_get_contents(self::WFIRMA_IPECHO_SERVICE);
        if( !filter_var($ip, FILTER_VALIDATE_IP) ) {
            $ip = $this->translator->translate('settings.sections.integrations.wfirma.oauth2_message1.ip_error');
        }
        
        return new Message(
            str_replace(['{url}', '{ip}'], [get_site_url() . self::WFIRMA_OAUTH_RETURN_PATH, $ip], $this->translator->translate('settings.sections.integrations.wfirma.oauth2_message1'))
        );
    }
    
    private function get_wfirma_oauth2_client_id_field(): Abstract_Setting_Field
    {
    	return new Text_Setting_Field(
    		self::WFIRMA_OAUTH2_CLIENT_ID,
    		$this->translator->translate('settings.sections.integrations.wfirma.' . self::WFIRMA_OAUTH2_CLIENT_ID),
    		$this->translator->translate('settings.sections.integrations.wfirma.' . self::WFIRMA_OAUTH2_CLIENT_ID . '.desc')
    	);
    }
    
    private function get_wfirma_oauth2_client_secret_field(): Abstract_Setting_Field
    {
    	return new Text_Setting_Field(
    		self::WFIRMA_OAUTH2_CLIENT_SECRET,
    		$this->translator->translate('settings.sections.integrations.wfirma.' . self::WFIRMA_OAUTH2_CLIENT_SECRET),
    		$this->translator->translate('settings.sections.integrations.wfirma.' . self::WFIRMA_OAUTH2_CLIENT_SECRET . '.desc')
    	);
    }

    private function get_wfirma_oauth2_message2(): Abstract_Setting_Field
    {
    	return new Message(
    	    $this->translator->translate('settings.sections.integrations.wfirma.oauth2_message2')
    	);
    }

    private function get_wfirma_oauth2_redir_field(): Abstract_Setting_Field
    {
        $return_url = get_site_url() . self::WFIRMA_OAUTH_RETURN_PATH;
        $url = str_replace(['{return_url}'], [urlencode($return_url)], self::WFIRMA_OAUTH_REDIRECT_URL);
        
        return (new WFirma_Redirect_Setting_Field(
            self::WFIRMA_OAUTH_BUTTON_REDIR,
            $this->translator->translate('settings.sections.integrations.wfirma.oauth2_button_redir'),
            null,
            null,
            null,
            $this->translator->translate('settings.sections.integrations.wfirma.oauth2_button_redir.value'),
        ))->set_data($url);
    }
    
    private function get_wfirma_oauth2_authorization_code_field(): Abstract_Setting_Field
    {
    	return new Text_Setting_Field(
    		self::WFIRMA_OAUTH2_AUTHORIZATION_CODE,
    		$this->translator->translate('settings.sections.integrations.wfirma.' . self::WFIRMA_OAUTH2_AUTHORIZATION_CODE),
    		$this->translator->translate('settings.sections.integrations.wfirma.' . self::WFIRMA_OAUTH2_AUTHORIZATION_CODE . '.desc')
    	);
    }
    
    private function get_wfirma_company_id_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::WFIRMA_COMPANY_ID,
            $this->translator->translate('settings.sections.integrations.wfirma.' . self::WFIRMA_COMPANY_ID),
            $this->translator->translate('settings.sections.integrations.wfirma.' . self::WFIRMA_COMPANY_ID . '.desc')
        );
    }

    private function get_wfirma_receipt_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::WFIRMA_RECEIPT,
            $this->translator->translate('settings.sections.integrations.wfirma.' . self::WFIRMA_RECEIPT)
        );
    }

    private function get_wfirma_auto_sent_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::WFIRMA_AUTO_SENT,
            $this->translator->translate('settings.sections.integrations.wfirma.' . self::WFIRMA_AUTO_SENT),
            $this->translator->translate('settings.sections.integrations.wfirma.' . self::WFIRMA_AUTO_SENT . '.desc')
        );
    }

    private function get_wfirma_auto_sent_receipt_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::WFIRMA_AUTO_SENT_RECEIPT,
            $this->translator->translate('settings.sections.integrations.wfirma.' . self::WFIRMA_AUTO_SENT_RECEIPT),
            $this->translator->translate('settings.sections.integrations.wfirma.' . self::WFIRMA_AUTO_SENT_RECEIPT . '.desc')
        );
    }

    private function get_taxe_integration_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::TAXE_INTEGRATION,
            $this->translator->translate('settings.sections.integrations.taxe_integration'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_taxe_login_field())
                ->add($this->get_taxe_api_key_field())
                ->add($this->get_taxe_vat_exemption_field())
                ->add($this->get_taxe_receipt_field())
                ->add($this->get_taxe_auto_sent_field())
                ->add($this->get_taxe_auto_sent_receipt_field())
        ))->set_popup($this->settings_popup,
                      $this->translator->translate('settings.sections.integrations.taxe_integration'));
    }

    private function get_taxe_login_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::TAXE_LOGIN,
            $this->translator->translate('settings.sections.integrations.taxe.' . self::TAXE_LOGIN)
        );
    }

    private function get_taxe_api_key_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::TAXE_API_KEY,
            $this->translator->translate('settings.sections.integrations.taxe.' . self::TAXE_API_KEY),
            $this->translator->translate('settings.sections.integrations.taxe.' . self::TAXE_API_KEY . '.desc')
        );
    }

    private function get_taxe_vat_exemption_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::TAXE_VAT_EXEMPTION,
            $this->translator->translate('settings.sections.integrations.taxe.' . self::TAXE_VAT_EXEMPTION),
            null,
            null,
            null,
            $this->translator->translate('settings.sections.integrations.taxe.' . self::TAXE_VAT_EXEMPTION . '.value')
            );
    }
    
    private function get_taxe_receipt_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::TAXE_RECEIPT,
            $this->translator->translate('settings.sections.integrations.taxe.' . self::TAXE_RECEIPT)
        );
    }

    private function get_taxe_auto_sent_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::TAXE_AUTO_SENT,
            $this->translator->translate('settings.sections.integrations.taxe.' . self::TAXE_AUTO_SENT),
            $this->translator->translate('settings.sections.integrations.taxe.' . self::TAXE_AUTO_SENT . '.desc')
        );
    }

    private function get_taxe_auto_sent_receipt_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::TAXE_AUTO_SENT_RECEIPT,
            $this->translator->translate('settings.sections.integrations.taxe.' . self::TAXE_AUTO_SENT_RECEIPT),
            $this->translator->translate('settings.sections.integrations.taxe.' . self::TAXE_AUTO_SENT_RECEIPT . '.desc')
        );
    }

    private function get_infakt_integration_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::INFAKT_INTEGRATION,
            $this->translator->translate('settings.sections.integrations.infakt_integration'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_infakt_api_key_field())
                ->add($this->get_infakt_vat_exemption_field())
                ->add($this->get_infakt_auto_sent_field())
        ))->set_popup($this->settings_popup,
                      $this->translator->translate('settings.sections.integrations.infakt_integration'));
    }

    private function get_infakt_api_key_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::INFAKT_API_KEY,
            $this->translator->translate('settings.sections.integrations.infakt.' . self::INFAKT_API_KEY),
            $this->translator->translate('settings.sections.integrations.infakt.' . self::INFAKT_API_KEY . '.desc')
        );
    }

    private function get_infakt_vat_exemption_field(): Abstract_Setting_Field
    {
        return new Text_Setting_Field(
            self::INFAKT_VAT_EXEMPTION,
            $this->translator->translate('settings.sections.integrations.infakt.' . self::INFAKT_VAT_EXEMPTION),
            null,
            null,
            null,
            $this->translator->translate('settings.sections.integrations.infakt.' . self::INFAKT_VAT_EXEMPTION . '.value')
        );
    }

    private function get_infakt_auto_sent_field(): Abstract_Setting_Field
    {
        return new Checkbox_Setting_Field(
            self::INFAKT_AUTO_SENT,
            $this->translator->translate('settings.sections.integrations.infakt.' . self::INFAKT_AUTO_SENT),
            $this->translator->translate('settings.sections.integrations.infakt.' . self::INFAKT_AUTO_SENT . '.desc')
        );
    }
}