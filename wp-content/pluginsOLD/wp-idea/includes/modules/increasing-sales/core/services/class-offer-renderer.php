<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\increasing_sales\core\services;

use bpmj\wpidea\helpers\Price_Formatting;
use bpmj\wpidea\infrastructure\system\System;
use bpmj\wpidea\modules\increasing_sales\api\controllers\Increasing_Sales_Ajax_Controller;
use bpmj\wpidea\modules\increasing_sales\core\entities\Offer;
use bpmj\wpidea\modules\increasing_sales\core\value_objects\Increasing_Sales_Offer_Type;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\sales\product\legal\Information_About_Lowest_Price;
use bpmj\wpidea\sales\product\model\Product;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;

class Offer_Renderer
{
    private Interface_Url_Generator $url_generator;
    private Interface_Product_Repository $product_repository;
    private System $system;
    private Interface_View_Provider $view_provider;
    private Offer_To_Cart_Matcher $offer_to_cart_matcher;
    private Active_Offer_Provider $active_offer_provider;
    private Interface_Translator $translator;
    private Information_About_Lowest_Price $information_about_lowest_price;

    public function __construct(
        Interface_Url_Generator $url_generator,
        Interface_Product_Repository $product_repository,
        System $system,
        Interface_View_Provider $view_provider,
        Offer_To_Cart_Matcher $offer_to_cart_matcher,
        Active_Offer_Provider $active_offer_provider,
        Interface_Translator $translator,
        Information_About_Lowest_Price $information_about_lowest_price
    ) {
        $this->url_generator = $url_generator;
        $this->product_repository = $product_repository;
        $this->system = $system;
        $this->view_provider = $view_provider;
        $this->offer_to_cart_matcher = $offer_to_cart_matcher;
        $this->active_offer_provider = $active_offer_provider;
        $this->translator = $translator;
        $this->information_about_lowest_price = $information_about_lowest_price;
    }

    public function get_offers_html(): string
    {
        if ($this->active_offer_provider->get_active_offer()) {
            return '';
        }

        $offer = $this->offer_to_cart_matcher->get_offer_that_can_be_applied_to_current_cart();

        if (!$offer) {
            return '';
        }

        $product = $this->product_repository->find(new Product_ID($offer->get_offered_product_id()->to_int()));

        if (!$product) {
            return '';
        }

        $offer_description = $offer->get_description() ?: null;
        $product_description = $product->get_description()->get_value() ?: null;
        $product_short_description = $product->get_short_description() ? $product->get_short_description()->get_value() : null;

        return $this->view_provider->get('/increasing-sales/view', [
            'offer_id' => $offer->get_id()->to_int(),
            'offer_type' => $offer->get_offer_type()->get_value(),
            'product_price' => $this->get_product_price($product, $offer),
            'product_image_url' => $offer->get_image() ?: $product->get_featured_image(),
            'product_description' => nl2br((($offer_description ?? $product_short_description ?? $product_description) ?? '')),
            'product_name' => $offer->get_title() ?: $this->prepare_default_title($offer, $product),
            'url_action' => $this->get_url_action(),
            'offered_product_lowest_price' => $this->get_offered_product_lowest_price($offer),
        ]);
    }

    private function get_product_name(Product $product, ?int $product_variant_id): string
    {
        $product_variants = $product->get_product_variants();
        $product_variant_name = '';

        if ($product_variants) {
            foreach ($product_variants as $variant) {
                if ($variant->get_id()->to_int() === $product_variant_id) {
                    $product_variant_name = ' - ' . $variant->get_name();
                }
            }
        }

        return $product->get_name()->get_value() . $product_variant_name;
    }

    private function get_product_price(Product $product, Offer $offer): string
    {
        $discount = $this->amount_in_fractions_to_float($offer->get_discount_in_fractions());

        $product_price = $this->get_product_regular_or_sale_price($product, $discount);
        $product_price_for_variant = $this->get_product_regular_or_sale_price_for_variant($product, $offer, $discount);

        return $product_price_for_variant ?? $product_price;
    }

    private function get_product_regular_or_sale_price(Product $product, ?float $discount): ?string
    {
        $sale_price = $product->get_effective_sale_price();
        $regular_product_price = $product->get_price()->get_value();
        $currency = $this->system->get_system_currency();
        $prefix_p_class = '<div class="special-offer-price">';
        $sufix_p_class = '</div>';

        $regular_price_crossed_out = '<div class="special-offer-price-crossed-out">' . $regular_product_price . ' ' . $currency . '</div> ';

        if ($discount) {
            $price_after_discount = $this->get_calculated_price_after_discount($regular_product_price, $discount);
            return $regular_price_crossed_out . $prefix_p_class . $price_after_discount . ' ' . $currency . $sufix_p_class;
        }

        if ($sale_price) {
            return $regular_price_crossed_out . $prefix_p_class . $sale_price->get_value() . ' ' . $currency . $sufix_p_class;
        }

        return $prefix_p_class . $regular_product_price . ' ' . $currency . $sufix_p_class;
    }

    private function get_product_regular_or_sale_price_for_variant(Product $product, Offer $offer, ?float $discount): ?string
    {
        $product_variant_id = $offer->get_offered_product_variant_id() ? $offer->get_offered_product_variant_id()->to_int() : null;
        $product_variants = $product->get_product_variants();

        if (!$product_variants->count() || !$product_variant_id) {
            return null;
        }

        $currency = $this->system->get_system_currency();
        $product_variant_price = '';
        $product_variant_sale_price = '';
        $prefix_p_class = '<div class="special-offer-price">';
        $sufix_p_class = '</div>';

        foreach ($product_variants as $variant) {
            if ($variant->get_id()->to_int() === $product_variant_id) {
                $product_variant_price = $variant->get_amount();
                $product_variant_sale_price = $variant->get_sale_price();
            }
        }

        $regular_price_crossed_out = '<div class="special-offer-price-crossed-out">' . $product_variant_price . ' ' . $currency . '</div> ';

        if ($discount) {
            $price_after_discount = $this->get_calculated_price_after_discount($product_variant_price, $discount);
            return $regular_price_crossed_out . $prefix_p_class . $price_after_discount . ' ' . $currency . $sufix_p_class;
        }

        if ($product_variant_sale_price) {
            return $regular_price_crossed_out . $prefix_p_class . $product_variant_sale_price . ' ' . $currency . $sufix_p_class;
        }

        return $prefix_p_class . $product_variant_price . '  ' . $currency . $sufix_p_class;
    }

    private function get_calculated_price_after_discount(float $price, float $discount): float
    {
        if ($price < $discount) {
            return 0.01;
        }
        return ($price - $discount);
    }

    private function amount_in_fractions_to_float(?int $amount): ?float
    {
        if (!$amount) {
            return null;
        }
        return Price_Formatting::format_to_float($amount, Price_Formatting::DIVIDE_BY_100);
    }

    private function get_url_action(): string
    {
        return $this->url_generator->generate(Increasing_Sales_Ajax_Controller::class, 'update_cart', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create()
        ]);
    }

    private function prepare_default_title(Offer $offer, Product $offered_product): string
    {
        $offered_product_variant_id = $offer->get_offered_product_variant_id() ? $offer->get_offered_product_variant_id()->to_int() : null;
        $offered_product_name = $this->get_product_name($offered_product, $offered_product_variant_id);

        if ($offer->get_offer_type()->get_value() === Increasing_Sales_Offer_Type::BUMP) {
            return $this->translator->translate('increasing_sales.order.bump_title.also_buy') . $offered_product_name;
        }

        $product = $this->product_repository->find(new Product_ID($offer->get_product_id()->to_int()));
        $product_variant_id = $offer->get_product_variant_id() ? $offer->get_product_variant_id()->to_int() : null;

        return $this->translator->translate('increasing_sales.order.upsell_title.buy')
            . $offered_product_name
            . $this->translator->translate('increasing_sales.order.upsell_title.instead')
            . $this->get_product_name($product, $product_variant_id);
    }

    private function get_offered_product_lowest_price(Offer $offer): string
    {
        return $this->information_about_lowest_price->get_product_from_offer_lowest_price_information($offer);
    }
}