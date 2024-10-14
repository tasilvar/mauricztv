<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\increasing_sales;

use bpmj\wpidea\helpers\Price_Formatting;
use bpmj\wpidea\modules\increasing_sales\infrastructure\persistence\Interface_Offers_Persistence;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\model\Variant_ID;
use bpmj\wpidea\sales\product\service\Variant_IDs_Parser;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\modules\increasing_sales\core\dto\Offer_Data_DTO;

class Increasing_Sales_Presenter
{
    private Interface_Translator $translator;
    private Interface_Offers_Persistence $offers_persistence;
    private Variant_IDs_Parser $variant_ids_parser;

    public function __construct(
        Interface_Translator $translator,
        Interface_Offers_Persistence $offers_persistence,
        Variant_IDs_Parser $variant_ids_parser
    ) {
        $this->translator = $translator;
        $this->offers_persistence = $offers_persistence;
        $this->variant_ids_parser = $variant_ids_parser;
    }

    public function get_page_offer_header(?int $id_offer): string
    {
        return ($id_offer) ? $this->translator->translate('increasing_sales.form.edit') : $this->translator->translate('increasing_sales.form.add');
    }

    public function get_offer_data_by_id(int $id_offer): ?Offer_Data_DTO
    {
        $offer = $this->offers_persistence->find_by_id($id_offer);

        if (!$offer) {
            return null;
        }

        $product_variant_id = $offer->get_product_variant_id() ? $offer->get_product_variant_id()->to_int() : null;
        $offered_product_variant_id = $offer->get_offered_product_variant_id() ? $offer->get_offered_product_variant_id()->to_int() : null;

        $offer_data = new Offer_Data_DTO();
        $offer_data->id_offer = $id_offer;
        $offer_data->product_id = $this->get_formated_product_id($offer->get_product_id()->to_int(), $product_variant_id);
        $offer_data->offer_type = $offer->get_offer_type()->get_value();
        $offer_data->offered_product_id = $this->get_formated_product_id($offer->get_offered_product_id()->to_int(), $offered_product_variant_id);
        $offer_data->title = $offer->get_title() ?? '';
        $offer_data->description = $offer->get_description() ?? '';
        $offer_data->image_url = $offer->get_image() ?? '';
        $offer_data->discount = $this->amount_in_fractions_to_float($offer->get_discount_in_fractions());

        return $offer_data;
    }

    private function amount_in_fractions_to_float(?int $amount): ?float
    {
        if (!$amount) {
            return null;
        }
        return Price_Formatting::format_to_float($amount, Price_Formatting::DIVIDE_BY_100);
    }

    private function get_formated_product_id(int $product_id, ?int $product_variant_id): string
    {
        $variant_id = $product_variant_id ? new Variant_ID($product_variant_id) : null;

        return $this->variant_ids_parser->parse_product_id_to_string_id(new Product_ID($product_id), $variant_id);
    }
}
