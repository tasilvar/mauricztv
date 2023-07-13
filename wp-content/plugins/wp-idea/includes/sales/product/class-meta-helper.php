<?php
namespace bpmj\wpidea\sales\product;

use bpmj\wpidea\sales\product\Flat_Rate_Tax_Symbol_Helper;
use bpmj\wpidea\sales\product\model\Gtu;
use bpmj\wpidea\wolverine\product\Repository as ProductRepository;

class Meta_Helper
{
    public static function get_gtu_as_string(int $product_id): string
    {
        $gtu = get_post_meta($product_id, ProductRepository::GTU_META_NAME, true);
        return $gtu ? $gtu : Gtu::NO_GTU;
    }

    public static function get_gtu_as_string_for_variant(int $product_id, int $price_id): string
    {
        $variable_prices = get_post_meta( $product_id, ProductRepository::VARIABLE_PRICES_META_NAME, true );
        return $variable_prices[$price_id][ProductRepository::GTU_META_NAME] ?? Gtu::NO_GTU;
    }

    public static function save_gtu_from_string_for_variant(int $product_id, int $price_id, string $gtu): void
    {
        $variable_prices = get_post_meta( $product_id, ProductRepository::VARIABLE_PRICES_META_NAME, true );
        if($gtu) {
            $variable_prices[$price_id][ProductRepository::GTU_META_NAME] = $gtu;
        }
        else {
            unset($variable_prices[$price_id][ProductRepository::GTU_META_NAME]);
        }

        update_post_meta( $product_id, ProductRepository::VARIABLE_PRICES_META_NAME, $variable_prices );
    }

    public static function get_flat_rate_tax_symbol(int $product_id): string
    {
        $flat_rate_tax_symbol = get_post_meta($product_id, Flat_Rate_Tax_Symbol_Helper::META_NAME, true);

        return $flat_rate_tax_symbol ?: Flat_Rate_Tax_Symbol_Helper::NO_TAX_SYMBOL;
    }

    public static function save_flat_rate_tax_symbol_for_variant(int $product_id, int $price_id, string $flat_rate_tax_symbol): void
    {
        $variable_prices = get_post_meta( $product_id, ProductRepository::VARIABLE_PRICES_META_NAME, true );
        if($flat_rate_tax_symbol) {
            $variable_prices[$price_id][Flat_Rate_Tax_Symbol_Helper::META_NAME] = $flat_rate_tax_symbol;
        }
        else {
            unset($variable_prices[$price_id][Flat_Rate_Tax_Symbol_Helper::META_NAME]);
        }

        update_post_meta( $product_id, ProductRepository::VARIABLE_PRICES_META_NAME, $variable_prices );
    }


    public static function get_flat_rate_tax_symbol_for_variant(int $product_id, int $price_id): string
    {
        $variable_prices = get_post_meta( $product_id, ProductRepository::VARIABLE_PRICES_META_NAME, true );
        return $variable_prices[$price_id][Flat_Rate_Tax_Symbol_Helper::META_NAME] ?? Flat_Rate_Tax_Symbol_Helper::NO_TAX_SYMBOL;
    }


    public static function save_invoices_vat_rate(int $product_id, string $vat_rate ): void
    {
        update_post_meta( $product_id, ProductRepository::INVOICES_VAT_RATE_META_NAME, $vat_rate );
    }

    public static function get_invoices_vat_rate(int $product_id): string
    {
        return get_post_meta($product_id, ProductRepository::INVOICES_VAT_RATE_META_NAME, true);
    }
}

