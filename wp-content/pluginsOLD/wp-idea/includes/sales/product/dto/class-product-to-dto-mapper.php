<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\dto;

use bpmj\wpidea\data_types\ID;
use DateTime;
use bpmj\wpidea\sales\product\model\{collection\Product_Variant_Collection,
    Product_Discount_Code_Settings,
    Product_Mailers_Settings};
use bpmj\wpidea\sales\product\model\Gtu;
use bpmj\wpidea\sales\product\model\Product;
use bpmj\wpidea\sales\product\model\Product_Description;
use bpmj\wpidea\sales\product\model\Product_Flat_Rate_Tax_Symbol;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\model\Product_Name;
use bpmj\wpidea\sales\product\model\Product_Price;
use bpmj\wpidea\sales\product\model\Product_Short_Description;
use bpmj\wpidea\sales\product\model\Product_Tags;

class Product_To_DTO_Mapper
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';
    public const ACCESS_TIME_AND_UNIT_SEPARATOR = '-';

    public function map_product_to_dto(Product $product): Product_DTO
    {
        $dto = new Product_DTO();

        $dto->id = $product->get_id()->to_int();
        $dto->name = $product->get_name()->get_value();
        $dto->description = $product->get_description()->get_value();
        $dto->short_description = $product->get_short_description() ? $product->get_short_description()->get_value() : null;
        $dto->price = $product->get_price()->get_value();
        $dto->linked_resource_id = $product->get_linked_resource_id() ? $product->get_linked_resource_id()->to_int() : null;
        $dto->slug = $product->get_slug();
        $dto->banner = $product->get_banner();
        $dto->featured_image = $product->get_featured_image();
        $dto->sales_disabled = $product->sales_disabled();
        $dto->categories = $product->get_categories();
        $dto->hide_from_list = $product->hide_from_list();
        $dto->hide_purchase_button = $product->hide_purchase_button();
        $dto->flat_rate_tax_symbol = $product->get_flat_rate_tax_symbol() ? $product->get_flat_rate_tax_symbol()->get_value() : null;
        $dto->vat_rate = $product->get_vat_rate();
        $dto->gtu = $product->get_gtu()->get_code();
        $dto->sale_price = $product->get_sale_price() ? $product->get_sale_price()->get_value() : null;
        $dto->sale_price_date_from = $product->get_sale_price_date_from() ? $product->get_sale_price_date_from()->format(
            self::DATE_FORMAT
        ) : null;
        $dto->sale_price_date_to = $product->get_sale_price_date_to() ? $product->get_sale_price_date_to()->format(
            self::DATE_FORMAT
        ) : null;
        $dto->purchase_limit = $product->get_purchase_limit();
        $dto->purchase_limit_items_left = $product->get_purchase_limit_items_left();
        $dto->promote_curse = $product->get_promote_course();
        $dto->recurring_payments_enabled = $product->get_recurring_payments_enabled();
        $dto->recurring_payments_interval = $product->get_recurring_payments_interval();
        $dto->tags = $product->get_tags()->to_string();
        $dto->mailchimp = $product->get_mailers_settings()->get_mailchimp_lists();
        $dto->sell_discount_code = $product->get_discount_code_settings() ? $product->get_discount_code_settings()->get_sell_discount_code() : null;
        $dto->discount_code_period_validity = $product->get_discount_code_settings() ? $product->get_discount_code_settings()->get_discount_code_period_validity(): null;
        $dto->mailchimp = $product->get_mailers_settings()->get_mailchimp_lists();
        $dto->mailerlite = $product->get_mailers_settings()->get_mailerlite_lists();
        $dto->freshmail = $product->get_mailers_settings()->get_freshmail_lists();
        $dto->ipresso_tags = $product->get_mailers_settings()->get_ipresso_tags();
        $dto->ipresso_tags_unsubscribe = $product->get_mailers_settings()->get_ipresso_tags_unsubscribe();
        $dto->activecampaign = $product->get_mailers_settings()->get_activecampaign_lists();
        $dto->activecampaign_unsubscribe = $product->get_mailers_settings()->get_activecampaign_lists_unsubscribe();
        $dto->activecampaign_tags = $product->get_mailers_settings()->get_activecampaign_tags();
        $dto->activecampaign_tags_unsubscribe = $product->get_mailers_settings()->get_activecampaign_tags_unsubscribe();
        $dto->getresponse = $product->get_mailers_settings()->get_getresponse_lists();
        $dto->getresponse_unsubscribe = $product->get_mailers_settings()->get_getresponse_lists_unsubscribe();
        $dto->getresponse_tags = $product->get_mailers_settings()->get_getresponse_tags();
        $dto->salesmanago_tags = $product->get_mailers_settings()->get_salesmanago_tags();
        $dto->interspire = $product->get_mailers_settings()->get_interspire_lists();
        $dto->convertkit = $product->get_mailers_settings()->get_convertkit_lists();
        $dto->convertkit_tags = $product->get_mailers_settings()->get_convertkit_tags();
        $dto->convertkit_tags_unsubscribe = $product->get_mailers_settings()->get_convertkit_tags_unsubscribe();
        $dto->variable_prices = $this->get_variable_prices_from_model_product_variants($product->get_product_variants());

        $dto->variable_pricing_enabled = $product->get_variable_pricing_enabled();
        $dto->tmp_sale_price = $product->get_tmp_sale_price() ? $product->get_tmp_sale_price()->get_value() : null;
        $dto->effective_sale_price = $product->get_effective_sale_price() ? $product->get_effective_sale_price()->get_value() : null;
        $dto->variable_sale_price_date_from = $product->get_variable_sale_price_date_from() ? $product->get_variable_sale_price_date_from()->format(self::DATE_FORMAT) : null;
        $dto->variable_sale_price_date_to = $product->get_variable_sale_price_date_to() ? $product->get_variable_sale_price_date_to()->format(self::DATE_FORMAT) : null;

        $dto->access_time_and_unit = $this->get_access_time_with_unit($product);

        $dto->access_start_enabled = $product->get_access_start_enabled();
        $dto->access_start = $product->get_access_start() ? $product->get_access_start()->format('Y-m-d H:i') : null;
        $dto->custom_purchase_link = $product->get_custom_purchase_link();
        $dto->thumbnail_id = $product->get_thumbnail_id();
        $dto->disable_certificates = $product->get_disable_certificates();
        $dto->enable_certificate_numbering = $product->get_enable_certificate_numbering();
        $dto->certificate_numbering_pattern = $product->get_certificate_numbering_pattern();
        $dto->disable_email_subscription = $product->get_disable_email_subscription();
        $dto->logo = $product->get_logo();
        $dto->gtu_variable_prices = $product->get_gtu_variable_prices();
        $dto->flat_rate_tax_symbol_variable_prices = $product->get_flat_rate_tax_symbol_variable_prices();
        $dto->navigation_next_lesson_label = $product->get_navigation_next_lesson_label();
        $dto->navigation_previous_lesson_label = $product->get_navigation_previous_lesson_label();
        $dto->progress_tracking = $product->get_progress_tracking();
        $dto->inaccessible_lesson_display = $product->get_inaccessible_lesson_display();
        $dto->progress_forced = $product->get_progress_forced();
        $dto->bundled_products = $product->get_bundled_products();
        $dto->is_bundle = $product->is_bundle();

        return $dto;
    }

    private function has_date_part(?string $date):bool {
        if(empty($date)) {
            return false;
        }
        $date_parts = explode(' ', $date);
        return !empty($date_parts[0]);
    }

    public function map_dto_to_product(Product_DTO $product_DTO): Product
    {
        $name = new Product_Name($product_DTO->name);
        $description = new Product_Description($product_DTO->description);
        $short_description = $product_DTO->short_description ? new Product_Short_Description($product_DTO->short_description) : null;
        $price = new Product_Price($product_DTO->price);

        $flat_rate_tax = $product_DTO->flat_rate_tax_symbol ? new Product_Flat_Rate_Tax_Symbol($product_DTO->flat_rate_tax_symbol) : null;
        $gtu = new Gtu($product_DTO->gtu ?: Gtu::NO_GTU);

        $variable_pricing_enabled = $product_DTO->variable_pricing_enabled;
        $tmp_sale_price = $product_DTO->tmp_sale_price ? new Product_Price((float)$product_DTO->tmp_sale_price) : null;
        $effective_sale_price = $product_DTO->effective_sale_price ? new Product_Price((float)$product_DTO->effective_sale_price) : null;
        $variable_sale_price_date_from = $product_DTO->variable_sale_price_date_from ? new DateTime($product_DTO->variable_sale_price_date_from) : null;
        $variable_sale_price_date_to = $product_DTO->variable_sale_price_date_to ? new DateTime($product_DTO->variable_sale_price_date_to) : null;

        $access_time =  $this->get_access_time($product_DTO);
        $access_time_unit = $this->get_access_time_unit($product_DTO);

        $access_start_enabled = $product_DTO->access_start_enabled;
        $access_start = $product_DTO->access_start ? new DateTime($product_DTO->access_start) : null;

        $custom_purchase_link = $product_DTO->custom_purchase_link;
        $thumbnail_id = $product_DTO->thumbnail_id;

        $mailers = new Product_Mailers_Settings(
            $product_DTO->mailchimp,
            $product_DTO->mailerlite,
            $product_DTO->freshmail,
            $product_DTO->ipresso_tags,
            $product_DTO->ipresso_tags_unsubscribe,
            $product_DTO->activecampaign,
            $product_DTO->activecampaign_unsubscribe,
            $product_DTO->activecampaign_tags,
            $product_DTO->activecampaign_tags_unsubscribe,
            $product_DTO->getresponse,
            $product_DTO->getresponse_unsubscribe,
            $product_DTO->getresponse_tags,
            $product_DTO->salesmanago_tags,
            $product_DTO->interspire,
            $product_DTO->convertkit,
            $product_DTO->convertkit_tags,
            $product_DTO->convertkit_tags_unsubscribe,
        );

        $tags = Product_Tags::from_string($product_DTO->tags);

        $discount_code = new Product_Discount_Code_Settings(
            $product_DTO->sell_discount_code,
            $product_DTO->discount_code_period_validity
        );
        
        return Product::create(
            $product_DTO->id ? new Product_ID($product_DTO->id) : null,
            $name,
            $description,
            $short_description,
            $price,
            $product_DTO->linked_resource_id ? new ID($product_DTO->linked_resource_id) : null,
            $product_DTO->slug,
            $product_DTO->is_bundle,
            $gtu,
            $product_DTO->banner,
            $product_DTO->featured_image,
            $product_DTO->sales_disabled,
            $product_DTO->categories,
            $product_DTO->hide_from_list,
            $product_DTO->hide_purchase_button,
            $flat_rate_tax,
            $product_DTO->vat_rate,
            $product_DTO->sale_price ? new Product_Price($product_DTO->sale_price) : null,
            $this->has_date_part($product_DTO->sale_price_date_from) ? new DateTime($product_DTO->sale_price_date_from) : null,
            $this->has_date_part($product_DTO->sale_price_date_to) ? new DateTime($product_DTO->sale_price_date_to) : null,
            $product_DTO->purchase_limit,
            $product_DTO->purchase_limit_items_left,
            $product_DTO->promote_curse,
            $product_DTO->recurring_payments_enabled,
            $product_DTO->recurring_payments_interval,
            $mailers,
            $tags,
            $discount_code,
            null,
            null,
            $variable_pricing_enabled,
            $tmp_sale_price,
            $effective_sale_price,
            $variable_sale_price_date_from,
            $variable_sale_price_date_to,
            $access_time,
            $access_time_unit,
            $access_start_enabled,
            $access_start,
            $custom_purchase_link,
            $thumbnail_id,
            $product_DTO->disable_certificates,
            $product_DTO->enable_certificate_numbering,
            $product_DTO->disable_email_subscription,
            $product_DTO->certificate_numbering_pattern,
            $product_DTO->logo,
            $product_DTO->gtu_variable_prices,
            $product_DTO->flat_rate_tax_symbol_variable_prices,
            $product_DTO->navigation_next_lesson_label,
            $product_DTO->navigation_previous_lesson_label,
            $product_DTO->progress_tracking,
            $product_DTO->inaccessible_lesson_display,
            $product_DTO->progress_forced,
            $product_DTO->bundled_products
        );
    }

    private function get_access_time_unit(Product_DTO $product_DTO): ?string
    {
        $access_time_and_unit = $product_DTO->access_time_and_unit
            ? explode(self::ACCESS_TIME_AND_UNIT_SEPARATOR, $product_DTO->access_time_and_unit)
            : null;

        return !empty($access_time_and_unit[1]) ? $access_time_and_unit[1] : null;
    }

    private function get_access_time_with_unit(Product $product): string
    {
        return $product->get_access_time() . self::ACCESS_TIME_AND_UNIT_SEPARATOR . $product->get_access_time_unit();
    }

    private function get_access_time(Product_DTO $product_DTO): ?int
    {
        $access_time_and_unit = $product_DTO->access_time_and_unit
            ? explode(self::ACCESS_TIME_AND_UNIT_SEPARATOR, $product_DTO->access_time_and_unit)
            : null;

        return !empty($access_time_and_unit[0]) ? (int)$access_time_and_unit[0] : null;
    }

    private function get_variable_prices_from_model_product_variants(?Product_Variant_Collection $product_variants): ?array
    {
        if(!$product_variants){
            return null;
        }

        $array = [];
        foreach($product_variants as $product_variant){
            $array[$product_variant->get_id()->to_int()] = [
                'name' => $product_variant->get_name(),
                'sale_price' => $product_variant->get_sale_price(),
                'amount' => $product_variant->get_amount(),
                'bpmj_eddcm_purchase_limit_items_left' => $product_variant->get_purchase_limit_items_left(),
                'bpmj_eddcm_purchase_limit' => $product_variant->get_purchase_limit(),
                'access_time' => $product_variant->get_access_time(),
                'access_time_unit' => $product_variant->get_access_time_unit(),
                'recurring_payments_enabled' => $product_variant->get_recurring_payments_enabled()
            ];
        }

        return $array;
    }
}