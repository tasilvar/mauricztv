<?php

namespace bpmj\wpidea\admin\settings\infrastructure\persistence\storage_place;

class Default_Storage_Places_Registry
{

    private Edd_Settings_Storage_Place $edd_settings_storage_place;
    private Fakturownia_Storage_Place $fakturownia_storage_place;
    private Ifirma_Storage_Place $ifirma_storage_place;
    private Infakt_Storage_Place $infakt_storage_place;
    private Publigo_Settings_Storage_Place $publigo_settings_storage_place;
    private Taxe_Storage_Place $taxe_storage_place;
    private Wfirma_Storage_Place $wfirma_storage_place;
    private WP_Settings_Storage_Place $wp_settings_storage_place;
    private Payment_Reminders_Module_Storage_Place $payment_reminders_module_storage_place;

    public function __construct(
        Edd_Settings_Storage_Place $edd_settings_storage_place,
        Fakturownia_Storage_Place $fakturownia_storage_place,
        Ifirma_Storage_Place $ifirma_storage_place,
        Infakt_Storage_Place $infakt_storage_place,
        Publigo_Settings_Storage_Place $publigo_settings_storage_place,
        Taxe_Storage_Place $taxe_storage_place,
        Wfirma_Storage_Place $wfirma_storage_place,
        WP_Settings_Storage_Place $wp_settings_storage_place,
        Payment_Reminders_Module_Storage_Place $payment_reminders_module_storage_place //@todo: ta klasa powinna być w module payment reminders i on powinien rejestrować pola (prawdopodobnie)
    )
    {
        $this->edd_settings_storage_place = $edd_settings_storage_place;
        $this->fakturownia_storage_place = $fakturownia_storage_place;
        $this->ifirma_storage_place = $ifirma_storage_place;
        $this->infakt_storage_place = $infakt_storage_place;
        $this->publigo_settings_storage_place = $publigo_settings_storage_place;
        $this->taxe_storage_place = $taxe_storage_place;
        $this->wfirma_storage_place = $wfirma_storage_place;
        $this->wp_settings_storage_place = $wp_settings_storage_place;
        $this->payment_reminders_module_storage_place = $payment_reminders_module_storage_place;
    }

    public function get_edd_settings_storage_place(): Edd_Settings_Storage_Place
    {
        return $this->edd_settings_storage_place;
    }

    public function get_fakturownia_storage_place(): Fakturownia_Storage_Place
    {
        return $this->fakturownia_storage_place;
    }

    public function get_ifirma_storage_place(): Ifirma_Storage_Place
    {
        return $this->ifirma_storage_place;
    }

    public function get_infakt_storage_place(): Infakt_Storage_Place
    {
        return $this->infakt_storage_place;
    }

    public function get_publigo_settings_storage_place(): Publigo_Settings_Storage_Place
    {
        return $this->publigo_settings_storage_place;
    }

    public function get_taxe_storage_place(): Taxe_Storage_Place
    {
        return $this->taxe_storage_place;
    }

    public function get_wfirma_storage_place(): Wfirma_Storage_Place
    {
        return $this->wfirma_storage_place;
    }

    public function get_wp_settings_storage_place(): WP_Settings_Storage_Place
    {
        return $this->wp_settings_storage_place;
    }

    public function get_payment_reminders_module_storage_place(): Payment_Reminders_Module_Storage_Place
    {
        return $this->payment_reminders_module_storage_place;
    }
}