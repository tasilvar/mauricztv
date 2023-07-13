<?php

namespace bpmj\wpidea\modules\affiliate_program\api;

use bpmj\wpidea\helpers\Price_Formatting;
use bpmj\wpidea\infrastructure\system\System;
use bpmj\wpidea\modules\affiliate_program\api\dto\{Commission_DTO, Commission_DTO_Collection};
use bpmj\wpidea\modules\affiliate_program\core\exceptions\Object_Uninitialized_Exception;
use bpmj\wpidea\modules\affiliate_program\core\services\Affiliate_Program_Data_Getter;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Partner_Info;
use bpmj\wpidea\modules\affiliate_program\api\dto\collections\External_Landing_Link_DTO_Collection;
use bpmj\wpidea\modules\affiliate_program\core\services\Interface_External_Landing_Link_Service;
use bpmj\wpidea\modules\affiliate_program\api\dto\mappers\External_Landing_Link_Collection_To_DTO_Collection_Mapper;
use bpmj\wpidea\translator\Interface_Translator;

class Affiliate_Program_API
{
    private static ?Affiliate_Program_API $instance = null;
    private Affiliate_Program_Data_Getter $affiliate_program_data_getter;
    private Interface_External_Landing_Link_Service $external_landing_link_service;
    private External_Landing_Link_Collection_To_DTO_Collection_Mapper $landing_link_collection_to_DTO_collection_mapper;
    private Interface_Translator $translator;
    private System $system;

    public function __construct(
        Affiliate_Program_Data_Getter $affiliate_program_data_getter,
        Interface_External_Landing_Link_Service $external_landing_link_service,
        External_Landing_Link_Collection_To_DTO_Collection_Mapper $landing_link_collection_to_DTO_collection_mapper,
        Interface_Translator $translator,
        System $system
    ) {
        $this->affiliate_program_data_getter = $affiliate_program_data_getter;
        $this->external_landing_link_service = $external_landing_link_service;
        $this->landing_link_collection_to_DTO_collection_mapper = $landing_link_collection_to_DTO_collection_mapper;
        $this->translator = $translator;
        $this->system = $system;

        self::$instance = $this;
    }

    /**
     * @throws Object_Uninitialized_Exception
     */

    public static function get_instance(): Affiliate_Program_API
    {
        if (!isset(self::$instance)) {
            throw new Object_Uninitialized_Exception();
        }

        return self::$instance;
    }

    public function get_partner_info(): ?Partner_Info
    {
        return $this->affiliate_program_data_getter->get_partner_info();
    }

    public function get_external_landing_links(): External_Landing_Link_DTO_Collection
    {
        $links = $this->external_landing_link_service->find_all();

        return $this->landing_link_collection_to_DTO_collection_mapper->map($links);
    }

    public function get_partner_commissions(): Commission_DTO_Collection
    {
        $partner_commissions = $this->affiliate_program_data_getter->get_partner_commissions();

        $commissions_dto = new Commission_DTO_Collection();

        if (!$partner_commissions) {
            return $commissions_dto;
        }

        foreach ($partner_commissions as $partner_commission) {
            $commissions_dto->add(
                new Commission_DTO(
                    $partner_commission->get_id()->to_int(),
                    $this->amount_in_fractions_to_float($partner_commission->get_sale_amount_in_fractions()) . ' ' . $this->get_currency(),
                    $this->amount_in_fractions_to_float($partner_commission->get_commission_amount_in_fractions()) . ' ' . $this->get_currency(),
                    $partner_commission->get_date()->format('Y-m-d H:i:s'),
                    $this->get_status_label($partner_commission->get_status()->get_value()),
                    $partner_commission->get_campaign()
                )
            );
        }

        return $commissions_dto;
    }

    private function amount_in_fractions_to_float(int $amount): float
    {
        return Price_Formatting::format_to_float($amount, Price_Formatting::DIVIDE_BY_100);
    }

    private function get_status_label(string $status): string
    {
        return $this->translator->translate('affiliate_program.status.' . $status);
    }

    private function get_currency(): string
    {
        return $this->system->get_system_currency();
    }
}