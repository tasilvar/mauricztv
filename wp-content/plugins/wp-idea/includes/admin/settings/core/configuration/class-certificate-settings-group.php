<?php

namespace bpmj\wpidea\admin\settings\core\configuration;

use bpmj\wpidea\admin\settings\core\collections\Fields_Collection;
use bpmj\wpidea\admin\settings\core\entities\Abstract_Settings_Group;
use bpmj\wpidea\admin\settings\core\entities\fields\{Abstract_Setting_Field,
	Certificates_Templates_Table_Field,
	New_Version_Certificates_Popup_Field,
	Toggle_Setting_Field};
use bpmj\wpidea\certificates\Certificate_Template;
use bpmj\wpidea\Packages;
use bpmj\wpidea\packages\Interface_Packages_API;

class Certificate_Settings_Group extends Abstract_Settings_Group
{
    private const ENABLE_CERTIFICATES = 'enable_certificates';
    private const ENABLE_NEW_VERSION_CERTIFICATES_POPUP = 'enable_new_version_certificates_popup';
    private const CERTIFICATE_TEMPLATES = 'certificate_templates';

    private Certificate_Template $certificates;
    private Interface_Packages_API $packages_api;

    public function __construct(
        Certificate_Template $certificates,
        Interface_Packages_API $packages_api
    )
    {
        $this->certificates = $certificates;
        $this->packages_api = $packages_api;
    }

    public function get_name(): string
    {
        return 'certificate';
    }

    public function register_fields(): void
    {
        $this->add_fieldset(
            $this->translator->translate('settings.sections.certificate.fieldset.certificate'),
            (new Fields_Collection())
                ->add($this->get_enable_certificates_field())
                ->add($this->get_enable_new_version_certificates_field())
                ->add($this->get_certificate_templates_field())
        );
    }

    private function get_enable_certificates_field(): Abstract_Setting_Field
    {
        return (new Toggle_Setting_Field(
            self::ENABLE_CERTIFICATES,
            $this->translator->translate('settings.sections.certificate.' . self::ENABLE_CERTIFICATES)
        ))
            ->set_related_feature(Packages::FEAT_CERTIFICATES);
    }

    private function get_enable_new_version_certificates_field(): Abstract_Setting_Field
    {
        return (new New_Version_Certificates_Popup_Field(
            self::ENABLE_NEW_VERSION_CERTIFICATES_POPUP,
            $this->translator->translate('settings.sections.certificate.' . self::ENABLE_NEW_VERSION_CERTIFICATES_POPUP),
            $this->translator->translate('settings.sections.certificate.' . self::ENABLE_NEW_VERSION_CERTIFICATES_POPUP . '.notice'),
        ))
            ->change_visibility(
                $this->has_access_to_feature_and_old_certificates()
            );
    }

    private function get_certificate_templates_field(): Abstract_Setting_Field
    {
        return (new Certificates_Templates_Table_Field(
            self::CERTIFICATE_TEMPLATES,
            $this->translator->translate('settings.sections.certificate.' . self::CERTIFICATE_TEMPLATES),
            $this->certificates->find_all()
        ))
            ->set_related_feature(Packages::FEAT_CERTIFICATES)
            ->change_visibility(
                !$this->has_access_to_feature_and_old_certificates()
            );
    }

    private function is_new_version_of_certificate_templates_enabled(): bool
    {
        return Certificate_Template::check_if_new_version_of_certificate_templates_is_enabled();
    }

    private function has_access_to_feature_and_old_certificates(): bool
    {
        if(!$this->packages_api->has_access_to_feature(Packages::FEAT_CERTIFICATES) || $this->is_new_version_of_certificate_templates_enabled()) {
            return false;
        }

        return true;
    }
}