<?php

namespace bpmj\wpidea\modules\increasing_sales\api\controllers;

use bpmj\wpidea\Caps;
use bpmj\wpidea\controllers\Access_Control;
use bpmj\wpidea\controllers\Base_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\helpers\Price_Formatting;
use bpmj\wpidea\Interface_Redirector;
use bpmj\wpidea\modules\increasing_sales\core\entities\Offer;
use bpmj\wpidea\modules\increasing_sales\core\value_objects\Increasing_Sales_Offer_Type;
use bpmj\wpidea\modules\increasing_sales\core\value_objects\Offer_ID;
use bpmj\wpidea\modules\increasing_sales\core\value_objects\Offered_Product_ID;
use bpmj\wpidea\modules\increasing_sales\core\value_objects\Offered_Variant_ID;
use bpmj\wpidea\modules\increasing_sales\core\value_objects\Product_ID;
use bpmj\wpidea\modules\increasing_sales\core\value_objects\Variant_ID;
use bpmj\wpidea\modules\increasing_sales\infrastructure\persistence\Interface_Offers_Persistence;
use bpmj\wpidea\Request_Method;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\sales\product\service\Variant_IDs_Parser;

class Admin_Increasing_Sales_Controller extends Base_Controller
{
    private Interface_Offers_Persistence $offers_persistence;
    private const WPI_INCREASING_SALES_OFFERS = 'wpi_increasing_sales_offers';
    private Variant_IDs_Parser  $variant_ids_parser;

    public function __construct(
        Access_Control $access_control,
        Interface_Translator $translator,
        Interface_Redirector $redirector,
        Interface_Offers_Persistence $offers_persistence,
        Variant_IDs_Parser  $variant_ids_parser
    ) {
        $this->offers_persistence = $offers_persistence;
        $this->variant_ids_parser = $variant_ids_parser;

        parent::__construct($access_control, $translator, $redirector);
    }

    public function behaviors(): array
    {
        return [
            'roles' => Caps::ROLES_ADMINS_SUPPORT,
            'caps' => [Caps::CAP_MANAGE_SETTINGS],
            'allowed_methods' => [Request_Method::POST]
        ];
    }

    public function add_offer_action(Current_Request $current_request): void
    {
        $offer_form_data = $current_request->get_request_arg(self::WPI_INCREASING_SALES_OFFERS);

        if (empty($offer_form_data)) {
            $this->redirector->redirect_back();
        }

        $model = $this->create_offer_model_from_form_data($offer_form_data);

        if (!$model) {
            $this->redirector->redirect_back();
        }

        $this->offers_persistence->insert($model);

        $this->redirector->redirect($offer_form_data['redirect_increasing_sales_page']);
    }

    public function edit_offer_action(Current_Request $current_request): void
    {
        $offer_form_data = $current_request->get_request_arg(self::WPI_INCREASING_SALES_OFFERS);

        if (empty($offer_form_data)) {
            $this->redirector->redirect_back();
        }

        $model = $this->create_offer_model_from_form_data($offer_form_data);

        if (!$model) {
            $this->redirector->redirect_back();
        }

        $this->offers_persistence->update($model);

        $this->redirector->redirect($offer_form_data['redirect_increasing_sales_page']);
    }

    private function create_offer_model_from_form_data(array $offer_form_data): ?Offer
    {
        $id = $offer_form_data['id'] ?? null;
        $product_id = $offer_form_data['product_id'] ?? null;
        $offer_type = $offer_form_data['offer_type'] ?? null;
        $offered_product = $offer_form_data['offered_product_id'] ?? null;
        $title = $offer_form_data['title'] ?? null;
        $description = $offer_form_data['description'] ?? null;
        $image_url = $offer_form_data['image_url'] ?? null;
        $discount = $offer_form_data['discount'] ?? null;

        if (!$product_id || !$offer_type || !$offered_product) {
            return null;
        }

        $product_and_variant_id = $this->variant_ids_parser->parse_string_id_to_product_and_variant_id($product_id);
        $offered_product_and_variant_id = $this->variant_ids_parser->parse_string_id_to_product_and_variant_id($offered_product);

        return Offer::create(
            $id? new Offer_ID((int)$id) : null,
            new Product_ID($product_and_variant_id->get_product_id()->to_int()),
            $product_and_variant_id->get_variant_id() ? new Variant_ID($product_and_variant_id->get_variant_id()->to_int()) : null,
            new Increasing_Sales_Offer_Type($offer_type),
            new Offered_Product_ID($offered_product_and_variant_id->get_product_id()->to_int()),
            $offered_product_and_variant_id->get_variant_id() ? new Offered_Variant_ID($offered_product_and_variant_id->get_variant_id()->to_int()) : null,
            $title ?: null,
            $description ?: null,
            $image_url ?: null,
            $discount ? $this->get_discount_in_fractions($discount) : null
        );
    }

    private function get_discount_in_fractions(float $discount): int
    {
        return Price_Formatting::round_and_format_to_int($discount, Price_Formatting::MULTIPLY_BY_100);
    }
}
