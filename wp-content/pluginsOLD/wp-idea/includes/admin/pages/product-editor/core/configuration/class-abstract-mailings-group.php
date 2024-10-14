<?php

namespace bpmj\wpidea\admin\pages\product_editor\core\configuration;

use bpmj\wpidea\admin\pages\product_editor\core\fields\{Checkbox_Mailings_Field, Multiselect_Mailings_Field, Tags_Field};
use bpmj\wpidea\admin\settings\core\collections\{Additional_Fields_Collection, Fields_Collection};
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\{Abstract_Setting_Field, Configure_Popup_Setting_Field};

abstract class Abstract_Mailings_Group extends Abstract_Settings_Group
{
    private const MAILCHIMP_POPUP = 'mailchimp-popup';
    private const MAILCHIMP_LISTS_FIELD_NAME = 'mailchimp';
    private const MAILCHIMP_INTEGRATION_NAME = 'edd-mailchimp';

    private const MAILERLITE_POPUP = 'mailerlite-popup';
    private const MAILERLITE_LISTS_FIELD_NAME = 'mailerlite';
    private const MAILERLITE_INTEGRATION_NAME = 'edd-mailerlite';

    private const FRESHMAIL_POPUP = 'freshmail-popup';
    private const FRESHMAIL_LISTS_FIELD_NAME = 'freshmail';
    private const FRESHMAIL_INTEGRATION_NAME = 'edd-freshmail';

    private const IPRESSO_POPUP = 'ipresso-popup';
    private const IPRESSO_INTEGRATION_NAME = 'edd-ipresso';
    private const IPRESSO_TAGS_LISTS_FIELD_NAME = 'ipresso_tags';
    private const IPRESSO_TAGS_UNSUBSCRIBE_LISTS_FIELD_NAME = 'ipresso_tags_unsubscribe';

    private const ACTIVECAMPAIGN_POPUP = 'activecampaign-popup';
    private const ACTIVECAMPAIGN_INTEGRATION_NAME = 'edd-activecampaign';

    private const ACTIVECAMPAIGN_LISTS_FIELD_NAME = 'activecampaign';
    private const ACTIVECAMPAIGN_UNSUBSCRIBE_LISTS_FIELD_NAME = 'activecampaign_unsubscribe';
    private const ACTIVECAMPAIGN_TAGS_LISTS_FIELD_NAME = 'activecampaign_tags';
    private const ACTIVECAMPAIGN_TAGS_UNSUBSCRIBE_LISTS_FIELD_NAME = 'activecampaign_tags_unsubscribe';

    private const GETRESPONSE_POPUP = 'getresponse-popup';
    private const GETRESPONSE_INTEGRATION_NAME = 'edd-getresponse';
    private const GETRESPONSE_LISTS_FIELD_NAME = 'getresponse';
    private const GETRESPONSE_UNSUBSCRIBE_LISTS_FIELD_NAME = 'getresponse_unsubscribe';
    private const GETRESPONSE_TAGS_LISTS_FIELD_NAME = 'getresponse_tags';

    private const SALESMANAGO_POPUP = 'salesmanago-popup';
    private const SALESMANAGO_TAGS_LISTS_FIELD_NAME = 'salesmanago_tags';
    private const SALESMANAGO_INTEGRATION_NAME = 'edd-salesmanago';

    private const INTERSPIRE_POPUP = 'interspire-popup';
    private const INTERSPIRE_LISTS_FIELD_NAME = 'interspire';
    private const INTERSPIRE_INTEGRATION_NAME = 'edd-interspire';

    private const CONVERTKIT_POPUP = 'convertkit-popup';
    private const CONVERTKIT_INTEGRATION_NAME = 'edd-convertkit';
    private const CONVERTKIT_LISTS_FIELD_NAME = 'convertkit';
    private const CONVERTKIT_TAGS_LISTS_FIELD_NAME = 'convertkit_tags';
    private const CONVERTKIT_UNSUBSCRIBE_TAGS_LISTS_FIELD_NAME = 'convertkit_tags_unsubscribe';

    public function get_name(): string
    {
        return 'mailings';
    }

    abstract protected function get_translate_prefix();

    public function register_fields(): void
    {
        $this->add_fieldset(
            $this->translate_with_prefix('sections.mailings.fieldset.mailings'),
            $this->get_fieldset_mailings()
        );
    }

    private function get_fieldset_mailings(): Fields_Collection
    {
        $fieldset_mailings = new Fields_Collection();

        $popup_mailchimp = $this->get_popup_mailchimp_field();
        if ($popup_mailchimp) {
            $fieldset_mailings->add($popup_mailchimp);
        }

        $popup_mailerlite = $this->get_popup_mailerlite_field();
        if ($popup_mailerlite) {
            $fieldset_mailings->add($popup_mailerlite);
        }

        $popup_freshmail = $this->get_popup_freshmail_field();
        if ($popup_freshmail) {
            $fieldset_mailings->add($popup_freshmail);
        }

        $popup_ipresso = $this->get_popup_ipresso_field();
        if ($popup_ipresso) {
            $fieldset_mailings->add($popup_ipresso);
        }

        $popup_activecampaign = $this->get_popup_activecampaign_field();
        if ($popup_activecampaign) {
            $fieldset_mailings->add($popup_activecampaign);
        }

        $popup_getresponse = $this->get_popup_getresponse_field();
        if ($popup_getresponse) {
            $fieldset_mailings->add($popup_getresponse);
        }

        $popup_salesmanago = $this->get_popup_salesmanago_field();
        if ($popup_salesmanago) {
            $fieldset_mailings->add($popup_salesmanago);
        }

        $popup_interspire = $this->get_popup_interspire_field();
        if ($popup_interspire) {
            $fieldset_mailings->add($popup_interspire);
        }

        $popup_convertkit = $this->get_popup_convertkit_field();
        if ($popup_convertkit) {
            $fieldset_mailings->add($popup_convertkit);
        }

        return $fieldset_mailings;
    }

    private function get_popup_mailchimp_field(): ?Abstract_Setting_Field
    {
        if (!$this->is_integration_enabled(self::MAILCHIMP_INTEGRATION_NAME)) {
            return null;
        }

        return (new Configure_Popup_Setting_Field(
            self::MAILCHIMP_POPUP,
            $this->translate_with_prefix('sections.mailings.mailchimp'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_mailchimp_field())
        ))->set_popup(
            $this->settings_popup,
            $this->translate_with_prefix('sections.mailings.mailchimp')
        );
    }

    private function get_mailchimp_field(): Abstract_Setting_Field
    {
        return (new Multiselect_Mailings_Field(
            self::MAILCHIMP_LISTS_FIELD_NAME,
            $this->translate_with_prefix('sections.mailings.popup.mailchimp'),
            $this->translate_with_prefix('sections.mailings.popup.mailchimp.desc')
        ))->set_mailer_lists(
            $this->get_mailer_lists(self::MAILCHIMP_INTEGRATION_NAME, 'list')
        );
    }

    private function get_popup_mailerlite_field(): ?Abstract_Setting_Field
    {
        if (!$this->is_integration_enabled(self::MAILERLITE_INTEGRATION_NAME)) {
            return null;
        }

        return (new Configure_Popup_Setting_Field(
            self::MAILERLITE_POPUP,
            $this->translate_with_prefix('sections.mailings.mailerlite'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_mailerlite_field())
        ))->set_popup(
            $this->settings_popup,
            $this->translate_with_prefix('sections.mailings.mailerlite')
        );
    }

    private function get_mailerlite_field(): Abstract_Setting_Field
    {
        return (new Checkbox_Mailings_Field(
            self::MAILERLITE_LISTS_FIELD_NAME,
            $this->translate_with_prefix('sections.mailings.popup.mailerlite'),
            $this->translate_with_prefix('sections.mailings.popup.mailerlite.desc')
        ))->set_mailer_lists(
            $this->get_mailer_lists(self::MAILERLITE_INTEGRATION_NAME, 'list')
        );
    }

    private function get_popup_freshmail_field(): ?Abstract_Setting_Field
    {
        if (!$this->is_integration_enabled(self::FRESHMAIL_INTEGRATION_NAME)) {
            return null;
        }

        return (new Configure_Popup_Setting_Field(
            self::FRESHMAIL_POPUP,
            $this->translate_with_prefix('sections.mailings.freshmail'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_freshmail_field())
        ))->set_popup(
            $this->settings_popup,
            $this->translate_with_prefix('sections.mailings.freshmail')
        );
    }

    private function get_freshmail_field(): Abstract_Setting_Field
    {
        return (new Checkbox_Mailings_Field(
            self::FRESHMAIL_LISTS_FIELD_NAME,
            $this->translate_with_prefix('sections.mailings.popup.freshmail'),
            $this->translate_with_prefix('sections.mailings.popup.freshmail.desc')
        ))->set_mailer_lists(
            $this->get_mailer_lists(self::FRESHMAIL_INTEGRATION_NAME, 'list')
        );
    }

    private function get_popup_ipresso_field(): ?Abstract_Setting_Field
    {
        if (!$this->is_integration_enabled(self::IPRESSO_INTEGRATION_NAME)) {
            return null;
        }

        return (new Configure_Popup_Setting_Field(
            self::IPRESSO_POPUP,
            $this->translate_with_prefix('sections.mailings.ipresso'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_ipresso_tags_field())
                ->add($this->get_ipresso_tags_unsubscribe_field())
        ))->set_popup(
            $this->settings_popup,
            $this->translate_with_prefix('sections.mailings.ipresso')
        );
    }

    private function get_ipresso_tags_field(): Abstract_Setting_Field
    {
        return new Tags_Field(
            self::IPRESSO_TAGS_LISTS_FIELD_NAME,
            $this->translate_with_prefix('sections.mailings.popup.ipresso_tags'),
            $this->translate_with_prefix('sections.mailings.popup.ipresso_tags.desc')
        );
    }

    private function get_ipresso_tags_unsubscribe_field(): Abstract_Setting_Field
    {
        return new Tags_Field(
            self::IPRESSO_TAGS_UNSUBSCRIBE_LISTS_FIELD_NAME,
            $this->translate_with_prefix('sections.mailings.popup.ipresso_tags_unsubscribe'),
            $this->translate_with_prefix('sections.mailings.popup.ipresso_tags_unsubscribe.desc')
        );
    }

    private function get_popup_activecampaign_field(): ?Abstract_Setting_Field
    {
        if (!$this->is_integration_enabled(self::ACTIVECAMPAIGN_INTEGRATION_NAME)) {
            return null;
        }

        return (new Configure_Popup_Setting_Field(
            self::ACTIVECAMPAIGN_POPUP,
            $this->translate_with_prefix('sections.mailings.activecampaign'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_activecampaign_field())
                ->add($this->get_activecampaign_unsubscribe_field())
                ->add($this->get_activecampaign_tags_field())
                ->add($this->get_activecampaign_tags_unsubscribe_field())
        ))->set_popup(
            $this->settings_popup,
            $this->translate_with_prefix('sections.mailings.activecampaign')
        );
    }

    private function get_activecampaign_field(): Abstract_Setting_Field
    {
        return (new Checkbox_Mailings_Field(
            self::ACTIVECAMPAIGN_LISTS_FIELD_NAME,
            $this->translate_with_prefix('sections.mailings.popup.activecampaign'),
            $this->translate_with_prefix('sections.mailings.popup.activecampaign.desc')
        ))->set_mailer_lists(
            $this->get_mailer_lists(self::ACTIVECAMPAIGN_INTEGRATION_NAME, 'list')
        )->set_pair(self::ACTIVECAMPAIGN_UNSUBSCRIBE_LISTS_FIELD_NAME);
    }

    private function get_activecampaign_unsubscribe_field(): Abstract_Setting_Field
    {
        return (new Checkbox_Mailings_Field(
            self::ACTIVECAMPAIGN_UNSUBSCRIBE_LISTS_FIELD_NAME,
            $this->translate_with_prefix('sections.mailings.popup.activecampaign_unsubscribe'),
            $this->translate_with_prefix('sections.mailings.popup.activecampaign_unsubscribe.desc')
        ))->set_mailer_lists(
            $this->get_mailer_lists(self::ACTIVECAMPAIGN_INTEGRATION_NAME, 'list')
        )->set_pair(self::ACTIVECAMPAIGN_LISTS_FIELD_NAME);
    }

    private function get_activecampaign_tags_field(): Abstract_Setting_Field
    {
        return new Tags_Field(
            self::ACTIVECAMPAIGN_TAGS_LISTS_FIELD_NAME,
            $this->translate_with_prefix('sections.mailings.popup.activecampaign_tags'),
            $this->translate_with_prefix('sections.mailings.popup.activecampaign_tags.desc')
        );
    }

    private function get_activecampaign_tags_unsubscribe_field(): Abstract_Setting_Field
    {
        return new Tags_Field(
            self::ACTIVECAMPAIGN_TAGS_UNSUBSCRIBE_LISTS_FIELD_NAME,
            $this->translate_with_prefix('sections.mailings.popup.activecampaign_tags_unsubscribe'),
            $this->translate_with_prefix('sections.mailings.popup.activecampaign_tags_unsubscribe.desc')
        );
    }

    private function get_popup_getresponse_field(): ?Abstract_Setting_Field
    {
        if (!$this->is_integration_enabled(self::GETRESPONSE_INTEGRATION_NAME)) {
            return null;
        }

        return (new Configure_Popup_Setting_Field(
            self::GETRESPONSE_POPUP,
            $this->translate_with_prefix('sections.mailings.getresponse'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_getresponse_field())
                ->add($this->get_getresponse_unsubscribe_field())
                ->add($this->get_getresponse_tags_field())
        ))->set_popup(
            $this->settings_popup,
            $this->translate_with_prefix('sections.mailings.getresponse')
        );
    }

    private function get_getresponse_field(): Abstract_Setting_Field
    {
        return (new Checkbox_Mailings_Field(
            self::GETRESPONSE_LISTS_FIELD_NAME,
            $this->translate_with_prefix('sections.mailings.popup.getresponse'),
            $this->translate_with_prefix('sections.mailings.popup.getresponse.desc')
        ))->set_mailer_lists(
            $this->get_mailer_lists(self::GETRESPONSE_INTEGRATION_NAME, 'list')
        )->set_pair(self::GETRESPONSE_UNSUBSCRIBE_LISTS_FIELD_NAME);
    }

    private function get_getresponse_unsubscribe_field(): Abstract_Setting_Field
    {
        return (new Checkbox_Mailings_Field(
            self::GETRESPONSE_UNSUBSCRIBE_LISTS_FIELD_NAME,
            $this->translate_with_prefix('sections.mailings.popup.getresponse_unsubscribe'),
            $this->translate_with_prefix('sections.mailings.popup.getresponse_unsubscribe.desc')
        ))->set_mailer_lists(
            $this->get_mailer_lists(self::GETRESPONSE_INTEGRATION_NAME, 'list')
        )->set_pair(self::GETRESPONSE_LISTS_FIELD_NAME);
    }

    private function get_getresponse_tags_field(): Abstract_Setting_Field
    {
        return (new Checkbox_Mailings_Field(
            self::GETRESPONSE_TAGS_LISTS_FIELD_NAME,
            $this->translate_with_prefix('sections.mailings.popup.getresponse_tags'),
            $this->translate_with_prefix('sections.mailings.popup.getresponse_tags.desc')
        ))->set_mailer_lists(
            $this->get_mailer_lists(self::GETRESPONSE_INTEGRATION_NAME, 'tags')
        );
    }

    private function get_popup_salesmanago_field(): ?Abstract_Setting_Field
    {
        if (!$this->is_integration_enabled(self::SALESMANAGO_INTEGRATION_NAME)) {
            return null;
        }

        return (new Configure_Popup_Setting_Field(
            self::SALESMANAGO_POPUP,
            $this->translate_with_prefix('sections.mailings.salesmanago'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_salesmanago_tags_field())
        ))->set_popup(
            $this->settings_popup,
            $this->translate_with_prefix('sections.mailings.salesmanago')
        );
    }

    private function get_salesmanago_tags_field(): Abstract_Setting_Field
    {
        return new Tags_Field(
            self::SALESMANAGO_TAGS_LISTS_FIELD_NAME,
            $this->translate_with_prefix('sections.mailings.popup.salesmanago_tags'),
            $this->translate_with_prefix('sections.mailings.popup.salesmanago_tags.desc')
        );
    }

    private function get_popup_interspire_field(): ?Abstract_Setting_Field
    {
        if (!$this->is_integration_enabled(self::INTERSPIRE_INTEGRATION_NAME)) {
            return null;
        }

        return (new Configure_Popup_Setting_Field(
            self::INTERSPIRE_POPUP,
            $this->translate_with_prefix('sections.mailings.interspire'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_interspire_field())
        ))->set_popup(
            $this->settings_popup,
            $this->translate_with_prefix('sections.mailings.interspire')
        );
    }

    private function get_interspire_field(): Abstract_Setting_Field
    {
        return (new Checkbox_Mailings_Field(
            self::INTERSPIRE_LISTS_FIELD_NAME,
            $this->translate_with_prefix('sections.mailings.popup.interspire'),
            $this->translate_with_prefix('sections.mailings.popup.interspire.desc')
        ))->set_mailer_lists(
            $this->get_mailer_lists(self::INTERSPIRE_INTEGRATION_NAME, 'list')
        );
    }

    private function get_popup_convertkit_field(): ?Abstract_Setting_Field
    {
        if (!$this->is_integration_enabled(self::CONVERTKIT_INTEGRATION_NAME)) {
            return null;
        }

        return (new Configure_Popup_Setting_Field(
            self::CONVERTKIT_POPUP,
            $this->translate_with_prefix('sections.mailings.convertkit'),
            null,
            null,
            (new Additional_Fields_Collection())
                ->add($this->get_convertkit_field())
                ->add($this->get_convertkit_tags_field())
                ->add($this->get_convertkit_tags_unsubscribe_field())
        ))->set_popup(
            $this->settings_popup,
            $this->translate_with_prefix('sections.mailings.convertkit')
        );
    }

    private function get_convertkit_field(): Abstract_Setting_Field
    {
        return (new Checkbox_Mailings_Field(
            self::CONVERTKIT_LISTS_FIELD_NAME,
            $this->translate_with_prefix('sections.mailings.popup.convertkit'),
            $this->translate_with_prefix('sections.mailings.popup.convertkit.desc')
        ))->set_mailer_lists(
            $this->get_mailer_lists(self::CONVERTKIT_INTEGRATION_NAME, 'list')
        );
    }

    private function get_convertkit_tags_field(): Abstract_Setting_Field
    {
        return (new Checkbox_Mailings_Field(
            self::CONVERTKIT_TAGS_LISTS_FIELD_NAME,
            $this->translate_with_prefix('sections.mailings.popup.convertkit_tags'),
            $this->translate_with_prefix('sections.mailings.popup.convertkit_tags.desc')
        ))->set_mailer_lists(
            $this->get_mailer_lists(self::CONVERTKIT_INTEGRATION_NAME, 'tags')
        )->set_pair(self::CONVERTKIT_UNSUBSCRIBE_TAGS_LISTS_FIELD_NAME);
    }

    private function get_convertkit_tags_unsubscribe_field(): Abstract_Setting_Field
    {
        return (new Checkbox_Mailings_Field(
            self::CONVERTKIT_UNSUBSCRIBE_TAGS_LISTS_FIELD_NAME,
            $this->translate_with_prefix('sections.mailings.popup.convertkit_tags_unsubscribe'),
            $this->translate_with_prefix('sections.mailings.popup.convertkit_tags_unsubscribe.desc')
        ))->set_mailer_lists(
            $this->get_mailer_lists(self::CONVERTKIT_INTEGRATION_NAME, 'tags')
        )->set_pair(self::CONVERTKIT_TAGS_LISTS_FIELD_NAME);
    }

    private function get_mailer_lists(string $name, string $list_type): array
    {
        return bpmj_wpid_get_mailer_data($name, $list_type);
    }

    private function is_integration_enabled(string $name): bool
    {
        return WPI()->diagnostic->is_integration_enabled($name);
    }

    private function translate_with_prefix(string $translation_id): string
    {
        return $this->translator->translate($this->get_translate_prefix() . '.' . $translation_id);
    }
}