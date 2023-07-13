<?php

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\repository;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\sales\product\acl\Product_Sale_Dates_ACL;
use bpmj\wpidea\sales\product\core\event\Event_Name as Product_Event_Name;
use bpmj\wpidea\sales\product\Flat_Rate_Tax_Symbol_Helper;
use bpmj\wpidea\sales\product\Meta_Helper as Product_Meta_Helper;
use bpmj\wpidea\sales\product\model\collection\Product_Variant_Collection;
use bpmj\wpidea\sales\product\model\Gtu;
use bpmj\wpidea\sales\product\model\Product;
use bpmj\wpidea\sales\product\model\Product_Description;
use bpmj\wpidea\sales\product\model\Product_Discount_Code_Settings;
use bpmj\wpidea\sales\product\model\Product_Flat_Rate_Tax_Symbol;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\model\Product_Mailers_Settings;
use bpmj\wpidea\sales\product\model\Product_Name;
use bpmj\wpidea\sales\product\model\Product_Price;
use bpmj\wpidea\sales\product\model\Product_Short_Description;
use bpmj\wpidea\sales\product\model\Product_Tags;
use bpmj\wpidea\sales\product\model\Product_Variant;
use bpmj\wpidea\sales\product\model\Variant_ID;
use bpmj\wpidea\sales\product\Product_Collection;
use bpmj\wpidea\user\User_ID;
use DateTime;
use WP_Term;

class Product_Wp_Repository implements Interface_Product_Repository
{
    private const POST_TYPE_PRODUCT = 'download';
    private const EDD_VARIABLE_PRICES = 'edd_variable_prices';
    private const GTU = 'gtu';
    private const FLAT_RATE_TAX_SYMBOL = 'flat_rate_tax_symbol';

    private const MAILCHIMP_META_NAME = '_edd_mailchimp';
    private const MAILERLITE_META_NAME = '_edd_mailerlite';
    private const FRESHMAIL_META_NAME = '_edd_freshmail';
    private const IPRESSO_TAGS_META_NAME = '_edd_ipresso';
    private const IPRESSO_TAGS_UNSUB_META_NAME = '_edd_ipresso_unsubscribe';
    private const ACTIVECAMPAIGN_META_NAME = '_edd_activecampaign';
    private const ACTIVECAMPAIGN_UNSUB_META_NAME = '_edd_activecampaign_unsubscribe';
    private const ACTIVECAMPAIGN_TAGS_META_NAME = '_edd_activecampaign_tags';
    private const ACTIVECAMPAIGN_TAGS_UNSUB_META_NAME = '_edd_activecampaign_tags_unsubscribe';
    private const GETRESPONSE_META_NAME = '_edd_getresponse';
    private const GETRESPONSE_UNSUB_META_NAME = '_edd_getresponse_unsubscribe';
    private const GETRESPONSE_TAGS_META_NAME = '_edd_getresponse_tags';
    private const SALESMANAGO_TAGS_META_NAME = '_bpmj_edd_sm_tags';
    private const INTERSPIRE_META_NAME = '_edd_interspire';
    private const CONVERTKIT_META_NAME = '_edd_convertkit';
    private const CONVERTKIT_TAGS_META_NAME = '_edd_convertkit_tags';
    private const CONVERTKIT_TAGS_UNSUB_META_NAME = '_edd_convertkit_tags_unsubscribe';
    private const ACCESS_META_KEY = '_bpmj_eddpc_access';

    private Interface_Events $events;
    private Product_Sale_Dates_ACL $sale_dates_ACL;


    public function __construct(
        Interface_Events $events,
        Product_Sale_Dates_ACL $sale_dates_ACL
    )
    {
        $this->events = $events;
        $this->sale_dates_ACL = $sale_dates_ACL;
    }

    public function find(Product_ID $id): ?Product
    {
        $int_id = $id->to_int();

        $post = get_posts([
            'post_type' => self::POST_TYPE_PRODUCT,
            'posts_per_page' => 1,
            'post__in' => [$int_id]
        ]);


        if (!$post) {
            return null;
        }

        $slug = $post[0]->post_name;
        $price = (float)get_post_meta($int_id, 'edd_price', true);
        $banner = get_post_meta($int_id, 'banner', true);
        $featured_image = $this->get_thumbnail_url_from_post_id($int_id);
        $sales_disabled = get_post_meta($int_id, 'sales_disabled', true) === 'on';
        $categories = get_the_terms($int_id, 'download_category');
        $hide_from_list = get_post_meta($int_id, 'hide_from_lists', true) === 'on';
        $hide_purchase_button = get_post_meta($int_id, 'purchase_button_hidden', true) === 'on';
        $is_bundle = get_post_meta($int_id, '_edd_product_type', true) === 'bundle';
        $flat_rate_tax_symbol = get_post_meta($int_id, Flat_Rate_Tax_Symbol_Helper::META_NAME, true);
        $vat_rate = get_post_meta($int_id, 'invoices_vat_rate', true);
        $gtu = Product_Meta_Helper::get_gtu_as_string($int_id);
        $sale_price = get_post_meta($int_id, 'sale_price', true);
        $sale_price = $sale_price ? new Product_Price((float)$sale_price) : null;
        $sale_price_date_from = get_post_meta($int_id, 'sale_price_from_date', true);
        $sale_price_hour_from = get_post_meta($int_id, 'sale_price_from_hour', true);
        $sale_price_from_datetime = $this->get_date_time_object($sale_price_date_from,$sale_price_hour_from);
        $sale_price_date_to = get_post_meta($int_id, 'sale_price_to_date', true);
        $sale_price_hour_to = get_post_meta($int_id, 'sale_price_to_hour', true);
        $sale_price_to_datetime = $this->get_date_time_object($sale_price_date_to, $sale_price_hour_to);
        $purchase_limit = (int)get_post_meta($int_id, '_bpmj_eddcm_purchase_limit', true);
        $purchase_limit_items_left = (int)get_post_meta($int_id, '_bpmj_eddcm_purchase_limit_items_left', true);
        $promote_course = get_post_meta($int_id, 'promote_curse', true) === 'on';
        $recurring_payments_enabled = get_post_meta($int_id, '_edd_recurring_payments_enabled', true) === '1';
        $recurring_payments_interval = get_post_meta($int_id, '_edd_recurring_payments_interval', true);
        $sell_discount_code = get_post_meta($int_id, '_edd-sell-discount-code', true);
        $sell_discount_time = get_post_meta($int_id, '_edd-sell-discount-time', true);
        $sell_discount_time_type = get_post_meta($int_id, '_edd-sell-discount-time-type', true);
        $variable_pricing_enabled = get_post_meta($int_id, '_variable_pricing', true) === '1';
        $edd_variable_prices = get_post_meta($int_id, 'edd_variable_prices', true);
        $edd_variable_prices = $edd_variable_prices ? $edd_variable_prices:  null;

        $mailers = $this->get_mailers_settings($int_id);

        $tags = Product_Tags::create_from_array($this->get_product_tags($int_id));

        $discount_code_period_validity = $sell_discount_time . '-' . $sell_discount_time_type;

        $discount_code = new Product_Discount_Code_Settings(
            $sell_discount_code,
            $discount_code_period_validity
        );

        $product_variants = $this->create_product_variant_collection($edd_variable_prices);

        $effective_sale_price = get_post_meta($int_id, 'edd_sale_price', true);
        $effective_sale_price = $effective_sale_price ? new Product_Price((float)$effective_sale_price) : null;

        $tmp_sale_price = get_post_meta($int_id, 'tmp_sale_price', true);
        $tmp_sale_price = $tmp_sale_price ? new Product_Price((float)$tmp_sale_price) : null;
        $variable_sale_price_from_date = get_post_meta($int_id, 'variable_sale_price_from_date', true);
        $variable_sale_price_from_hour = get_post_meta($int_id, 'variable_sale_price_from_hour', true);

        $variable_sale_price_from_datetime = $variable_sale_price_from_date && $variable_sale_price_from_hour ?
            new DateTime($variable_sale_price_from_date . ' ' . $variable_sale_price_from_hour . ':00:00') : null;

        $variable_sale_price_to_date = get_post_meta($int_id, 'variable_sale_price_to_date', true);
        $variable_sale_price_to_hour = get_post_meta($int_id, 'variable_sale_price_to_hour', true);

        $variable_sale_price_to_datetime = $variable_sale_price_to_date && $variable_sale_price_to_hour ?
            new DateTime($variable_sale_price_to_date . ' ' . $variable_sale_price_to_hour . ':00:00') : null;

        $access_time = get_post_meta($int_id, '_bpmj_eddpc_access_time', true);
        $access_time = !empty($access_time) ? (int)$access_time : null;

        $access_time_unit = get_post_meta($int_id, '_bpmj_eddpc_access_time_unit', true);
        $access_start_enabled = get_post_meta($int_id, '_bpmj_eddpc_access_start_enabled', true);
        $access_start_enabled =  !empty($access_start_enabled) ? true : false;
        $access_start = get_post_meta($int_id, '_bpmj_eddpc_access_start', true);
        $access_start = $access_start ? new DateTime($access_start) : null;
        $custom_purchase_link = get_post_meta($int_id, 'custom_purchase_link', true);

        $thumbnail_id = (int)get_post_meta($int_id, '_thumbnail_id', true);
        $disable_certificates = get_post_meta($int_id, 'disable_certificates', true) === 'on';
        $enable_certificate_numbering = get_post_meta($int_id, 'enable_certificate_numbering', true) === 'on';
        $disable_email_subscription = get_post_meta($int_id, 'disable_email_subscription', true) === 'on';
        $certificate_numbering_pattern = get_post_meta($int_id, 'certificate_numbering_pattern', true);
        $logo = get_post_meta($int_id, 'logo', true);
        $bundled_products = get_post_meta($int_id, '_edd_bundled_products', true);
        $bundled_products = !empty($bundled_products) ? $bundled_products : [];

        $navigation_next_lesson_label = get_post_meta($int_id, 'navigation_next_lesson_label', true);

        $navigation_previous_lesson_label = get_post_meta($int_id, 'navigation_previous_lesson_label', true);
        $progress_tracking = get_post_meta($int_id, 'progress_tracking', true);
        $inaccessible_lesson_display = get_post_meta($int_id, 'inaccessible_lesson_display', true);
        $progress_forced = get_post_meta($int_id, 'progress_forced', true);


        $product = Product::create(
            $id,
            new Product_Name($post[0]->post_title),
            new Product_Description($post[0]->post_content),
            !empty($post[0]->post_excerpt) ? new Product_Short_Description($post[0]->post_excerpt) : null,
            new Product_Price($price),
            null,
            $slug,
            $is_bundle,
            new Gtu($gtu),
            $banner,
            $featured_image,
            $sales_disabled,
            is_array($categories) ? $categories : [],
            $hide_from_list,
            $hide_purchase_button,
            new Product_Flat_Rate_Tax_Symbol($flat_rate_tax_symbol),
            $vat_rate,
            $sale_price,
            $sale_price_from_datetime,
            $sale_price_to_datetime,
            $purchase_limit,
            $purchase_limit_items_left,
            $promote_course,
            $recurring_payments_enabled,
            $recurring_payments_interval,
            $mailers,
            $tags,
            $discount_code,
            $product_variants,
            $edd_variable_prices,
            $variable_pricing_enabled,
            $tmp_sale_price,
            $effective_sale_price,
            $variable_sale_price_from_datetime,
            $variable_sale_price_to_datetime,
            $access_time,
            $access_time_unit,
            $access_start_enabled,
            $access_start,
            $custom_purchase_link,
            $thumbnail_id,
            $disable_certificates,
            $enable_certificate_numbering,
            $disable_email_subscription,
            $certificate_numbering_pattern,
            $logo,
            null,
            null,
            $navigation_next_lesson_label,
            $navigation_previous_lesson_label,
            $progress_tracking,
            $inaccessible_lesson_display,
            $progress_forced,
            $bundled_products
        );

        $this->use_product_id_as_resource_id_because_its_the_same_db_record($product);

        return $product;
    }

    public function find_all(): Product_Collection
    {
        $posts = get_posts($this->get_default_query_args());

        return $this->create_collection_from_wp_posts($posts);
    }

    public function find_by_criteria(Product_Query_Criteria $criteria): Product_Collection
    {
        $ids_to_include_in_query = [];
        $meta_query = [];

        $in_bundle = $criteria->get_in_bundle();
        if ($in_bundle) {
            $product_in_bundle_ids = $this->get_product_in_bundle_integer_ids($in_bundle);
            $product_in_bundle_ids = !empty($product_in_bundle_ids) ? $product_in_bundle_ids : [0];
            $ids_to_include_in_query = array_merge($ids_to_include_in_query, $product_in_bundle_ids);
        }

        $linked_resource_id = $criteria->get_linked_resource_id();
        if ($linked_resource_id) {
            $ids_to_include_in_query =
                !empty($ids_to_include_in_query)
                    ? array_filter($ids_to_include_in_query, static fn($id) => $id === $linked_resource_id->to_int())
                    : [$linked_resource_id->to_int()];
        }

        $is_bundle = $criteria->get_is_bundle();
        if ($is_bundle === true) {
            $meta_query[] = [
                'key' => '_edd_product_type',
                'value' => 'bundle'
            ];
        }
        if ($is_bundle === false) {
            $meta_query[] = [
                'key' => '_edd_product_type',
                'compare' => 'NOT EXISTS'
            ];
        }

        $is_visible_in_catalog = $criteria->get_is_visible_in_catalog();
        if ($is_visible_in_catalog === true) {
            $meta_query[] = [
                'relation' => 'OR',
                [
                    'key' => 'hide_from_lists',
                    'value' => 'off'
                ],
                [
                    'key' => 'hide_from_lists',
                    'compare' => 'NOT EXISTS'
                ]
            ];
        }
        if ($is_visible_in_catalog === false) {
            $meta_query[] = [
                'key' => 'hide_from_lists',
                'value' => 'on'
            ];
        }

        if ($criteria->get_product_ids()) {
            $ids_to_include_in_query = array_merge($ids_to_include_in_query, $criteria->get_product_ids());
        }

        foreach ($ids_to_include_in_query as $index => $id) {
            $ids_to_include_in_query[$index] = (int)$id;
        }


        $phrase = $criteria->get_phrase();

        $posts = get_posts(
            array_merge($this->get_default_query_args(), [
                'post__in' => $ids_to_include_in_query,
                'meta_query' => $meta_query,
                's' => $phrase ?? ''
            ])
        );

        return $this->create_collection_from_wp_posts($posts);
    }

    public function save(Product $product): Product_ID
    {
        $this->use_resource_id_as_product_id_because_its_the_same_db_record($product);

        $product_id = $this->is_model_new_entity($product)
            ? $this->create($product)
            : $this->update($product);

        $product->set_id($product_id);

        $this->use_product_id_as_resource_id_because_its_the_same_db_record($product);

        return $product_id;
    }

    public function delete(Product_ID $id): void
    {
        $this->events->emit(Product_Event_Name::PRODUCT_DELETED, $id->to_int());

        wp_delete_post($id->to_int(), true);
    }

    public function get_meta(Product_ID $id, string $key)
    {
        return get_post_meta($id->to_int(), $key, true);
    }

    public function update_meta(Product_ID $id, string $key, $value): void
    {
        update_post_meta($id->to_int(), $key, $value);
    }

    private function is_model_new_entity(Product $product): bool
    {
        return is_null($product->get_id());
    }

    private function get_prepared_categories(Product $product): array
    {
        $array = [];

        foreach ($product->get_categories() as $category){

            if ($category instanceof WP_Term) {
                $array[] = $category->term_taxonomy_id;
                continue;
            }

            if ('on' !== $category && 'off' !== $category) {
                $array[] = $category;
            }
        }

        return $array;
    }

    private function create(Product $product): Product_ID
    {
        $args = $this->get_prepared_insert_args($product);

        $id = wp_insert_post($args);

        $this->update_product_categories($id, $product);

        $this->update_product_tags($id, $product);

        return new Product_ID($id);
    }

    private function update(Product $product): Product_ID
    {
        $this->emit_price_updated_events($product);

        $args = $this->get_prepared_insert_args($product);

        $id = wp_update_post($args);

        $this->save_gtu_from_string_for_variant($product);

        $this->save_flat_rate_tax_symbol_for_variant($product);

        $this->update_product_categories($id, $product);

        $this->update_product_tags($id, $product);

        $this->sale_dates_ACL->check_sale_dates();
        
        return new Product_ID($id);
    }

    private function get_prepared_insert_args(Product $product): array
    {

        $sale_price_from_date = $product->get_sale_price_date_from() ? $product->get_sale_price_date_from()->format('Y-m-d') : null;
        $sale_price = $product->get_sale_price() ? $product->get_sale_price()->get_value() : null;
        $effective_sale_price = $sale_price;
        $tmp_sale_price = $product->get_tmp_sale_price() ? $product->get_tmp_sale_price()->get_value() : null;

        if ( $sale_price && $sale_price_from_date ) {
            $effective_sale_price = '';
            $tmp_sale_price = $sale_price;
        }

        $product_id = $product->get_id() ? $product->get_id()->to_int() : null;

        $variable_prices_array = [];

        if(!$product_id){
            $variable_prices_array = ['edd_variable_prices' => []];
        }

        $meta_bundle_input = $product->is_bundle() ? [
            '_eddcm_subtype' => 'bundle',
            '_edd_product_type' => 'bundle',
            '_edd_bundled_products' => $product->get_bundled_products(),
        ] : [];

        return array_filter([
            'ID' => $product->get_id() ? $product->get_id()->to_int() : null,
            'post_type' => 'download',
            'post_title' => $product->get_name()->get_value(),
            'post_name' => $product->get_slug(),
            'post_status' => 'publish',
            'post_content' => $product->get_description()->get_value(),
            'post_excerpt' => $product->get_short_description() ? $product->get_short_description()->get_value() : '&nbsp;',
            'comment_status' => 'closed',
                'meta_input' => array_merge($variable_prices_array, $meta_bundle_input, [
                'edd_price' => $product->get_price()->get_value(),
                'sale_price' => $sale_price,
                'edd_sale_price' => $effective_sale_price,
                'tmp_sale_price' => $tmp_sale_price,
                'sale_price_from_date' => $sale_price_from_date,
                'sale_price_from_hour' => $product->get_sale_price_date_from() ?
                    $product->get_sale_price_date_from()->format('H') : null,
                'sale_price_to_date' => $product->get_sale_price_date_to() ?
                    $product->get_sale_price_date_to()->format('Y-m-d') : null,
                'sale_price_to_hour' => $product->get_sale_price_date_to() ?
                    $product->get_sale_price_date_to()->format('H') : null,
                'banner' => $product->get_banner(),
                '_thumbnail_id' => $this->get_thumbnail_id_for_product($product),
                'purchase_limit' => $product->get_purchase_limit(),
                '_bpmj_eddcm_purchase_limit' => $product->get_purchase_limit(),
                'purchase_limit_items_left' => $product->get_purchase_limit_items_left(),
                '_bpmj_eddcm_purchase_limit_items_left' => $product->get_purchase_limit_items_left(),
                'sales_disabled' => $product->sales_disabled() ? 'on' : 'off',
                'hide_from_lists' => $product->hide_from_list() ? 'on' : 'off',
                'purchase_button_hidden' => $product->hide_purchase_button() ? 'on' : 'off',
                'gtu' => $product->get_gtu()->get_code(),
                'promote_curse' => $product->get_promote_course() ? 'on' : 'off',
                '_edd_recurring_payments_enabled' => $product->get_recurring_payments_enabled() ? '1' : '0',
                '_edd_recurring_payments_interval' => $product->get_recurring_payments_interval(),
                '_edd-sell-discount-code' => $product->get_discount_code_settings()->get_sell_discount_code(),
                '_edd-sell-discount-time' => $product->get_discount_code_settings()->get_sell_discount_time(),
                '_edd-sell-discount-time-type' => $product->get_discount_code_settings()->get_sell_discount_time_type(),
                self::MAILCHIMP_META_NAME => $product->get_mailers_settings()->get_mailchimp_lists(),
                self::MAILERLITE_META_NAME => $product->get_mailers_settings()->get_mailerlite_lists(),
                self::FRESHMAIL_META_NAME => $product->get_mailers_settings()->get_freshmail_lists(),
                self::IPRESSO_TAGS_META_NAME => $product->get_mailers_settings()->get_ipresso_tags(),
                self::IPRESSO_TAGS_UNSUB_META_NAME => $product->get_mailers_settings()->get_ipresso_tags_unsubscribe(),
                self::ACTIVECAMPAIGN_META_NAME => $product->get_mailers_settings()->get_activecampaign_lists(),
                self::ACTIVECAMPAIGN_UNSUB_META_NAME => $product->get_mailers_settings()->get_activecampaign_lists_unsubscribe(),
                self::ACTIVECAMPAIGN_TAGS_META_NAME => $product->get_mailers_settings()->get_activecampaign_tags(),
                self::ACTIVECAMPAIGN_TAGS_UNSUB_META_NAME => $product->get_mailers_settings()->get_activecampaign_tags_unsubscribe(),
                self::GETRESPONSE_META_NAME => $product->get_mailers_settings()->get_getresponse_lists(),
                self::GETRESPONSE_UNSUB_META_NAME => $product->get_mailers_settings()->get_getresponse_lists_unsubscribe(),
                self::GETRESPONSE_TAGS_META_NAME => $product->get_mailers_settings()->get_getresponse_tags(),
                self::SALESMANAGO_TAGS_META_NAME => $product->get_mailers_settings()->get_salesmanago_tags(),
                self::INTERSPIRE_META_NAME => $product->get_mailers_settings()->get_interspire_lists(),
                self::CONVERTKIT_META_NAME => $product->get_mailers_settings()->get_convertkit_lists(),
                self::CONVERTKIT_TAGS_META_NAME => $product->get_mailers_settings()->get_convertkit_tags(),
                self::CONVERTKIT_TAGS_UNSUB_META_NAME => $product->get_mailers_settings()->get_convertkit_tags_unsubscribe(),
                Flat_Rate_Tax_Symbol_Helper::META_NAME => $product->get_flat_rate_tax_symbol() ? $product->get_flat_rate_tax_symbol()->get_value(
                ) : Flat_Rate_Tax_Symbol_Helper::NO_TAX_SYMBOL,
                'invoices_vat_rate' => $product->get_vat_rate() ?? '',
                '_variable_pricing' => $product->get_variable_pricing_enabled(),
                'variable_sale_price_from_date' => $product->get_variable_sale_price_date_from() ? $product->get_variable_sale_price_date_from()->format('Y-m-d') : null,
                'variable_sale_price_from_hour' => $product->get_variable_sale_price_date_from() ? $product->get_variable_sale_price_date_from()->format('H') : null,
                'variable_sale_price_to_date' => $product->get_variable_sale_price_date_to() ? $product->get_variable_sale_price_date_to()->format('Y-m-d') : null,
                'variable_sale_price_to_hour' => $product->get_variable_sale_price_date_to() ? $product->get_variable_sale_price_date_to()->format('H') : null,
                '_bpmj_eddpc_access_time' => $product->get_access_time(),
                '_bpmj_eddpc_access_time_unit' => $product->get_access_time_unit(),
                '_bpmj_eddpc_access_start_enabled' => $product->get_access_start_enabled(),
                '_bpmj_eddpc_access_start' => $product->get_access_start() ? $product->get_access_start()->format('Y-m-d H:i') : null,
                'custom_purchase_link' => $product->get_custom_purchase_link(),
                'disable_certificates' => $product->get_disable_certificates() ? 'on' : 'off',
                'enable_certificate_numbering' => $product->get_enable_certificate_numbering() ? 'on' : 'off',
                'disable_email_subscription' => $product->get_disable_email_subscription() ? 'on' : 'off',
                'certificate_numbering_pattern' => $product-> get_certificate_numbering_pattern(),
                'logo' => $product-> get_logo(),
                'navigation_next_lesson_label' => $product->get_navigation_next_lesson_label(),
                'navigation_previous_lesson_label' => $product->get_navigation_previous_lesson_label(),
                'progress_tracking' => $product->get_progress_tracking(),
                'inaccessible_lesson_display' => $product->get_inaccessible_lesson_display(),
                'progress_forced' => $product->get_progress_forced()
               ])
        ]);
    }

    private function save_gtu_from_string_for_variant(Product $product): void
    {
        if(!$product->get_gtu_variable_prices()){
            return;
        }

        $variable_prices = get_post_meta( $product->get_id()->to_int(), self::EDD_VARIABLE_PRICES, true );

        $gtu_variable_prices = explode('-', $product->get_gtu_variable_prices());

        $price_id = $gtu_variable_prices[0] ?? null;
        $gtu = $gtu_variable_prices[1] ?? null;

        if(!$price_id){
            return;
        }

        if($gtu) {
            $variable_prices[$price_id][self::GTU] = $gtu;
        }else{
            unset($variable_prices[$price_id][self::GTU]);
        }

        update_post_meta( $product->get_id()->to_int(), self::EDD_VARIABLE_PRICES, $variable_prices );
    }

    private function save_flat_rate_tax_symbol_for_variant(Product $product): void
    {
        if(!$product->get_flat_rate_tax_symbol_variable_prices()){
            return;
        }

        $variable_prices = get_post_meta( $product->get_id()->to_int(), self::EDD_VARIABLE_PRICES, true );

        $flat_rate_tax_symbol_variable_prices = explode('-', $product->get_flat_rate_tax_symbol_variable_prices());

        $price_id = $flat_rate_tax_symbol_variable_prices[0] ?? null;
        $flat_rate_tax_symbol = $flat_rate_tax_symbol_variable_prices[1] ?? null;

        if(!$price_id){
            return;
        }

        if($flat_rate_tax_symbol) {
            $variable_prices[$price_id][self::FLAT_RATE_TAX_SYMBOL] = $flat_rate_tax_symbol;
        }else{
            unset($variable_prices[$price_id][self::FLAT_RATE_TAX_SYMBOL]);
        }

        update_post_meta( $product->get_id()->to_int(), self::EDD_VARIABLE_PRICES, $variable_prices );
    }

    private function get_thumbnail_id_for_product(Product $product): ?int
    {
        if (empty($product->get_featured_image())) {
            return null;
        }

        $attachment_id = attachment_url_to_postid($product->get_featured_image());

        return $attachment_id ?: null;
    }

    private function get_thumbnail_url_from_post_id(int $post_id): ?string
    {
        $url = get_the_post_thumbnail_url($post_id);

        return $url ?: null;
    }

    private function use_resource_id_as_product_id_because_its_the_same_db_record(Product $product): void
    {
        if(!$product->get_linked_resource_id()) {
            return;
        }

        $product_id = new Product_ID($product->get_linked_resource_id()->to_int());

        $product->set_id($product_id);
    }

    private function use_product_id_as_resource_id_because_its_the_same_db_record(Product $product): void
    {
        if ($product->is_bundle()) {
            return;
        }

        $resource_id = new ID($product->get_id()->to_int());

        $product->set_linked_resource_id($resource_id);
    }

    private function create_collection_from_wp_posts(array $posts): Product_Collection
    {
        $collection = new Product_Collection();

        foreach ($posts as $post_id) {
            $collection->add(
                $this->find(new Product_ID((int)$post_id))
            );
        }

        return $collection;
    }

    private function get_default_query_args(): array
    {
        return [
            'post_type' => self::POST_TYPE_PRODUCT,
            'status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids'
        ];
    }

    private function get_product_in_bundle_integer_ids(Product_ID $bundle_id): array
    {
        return get_post_meta($bundle_id->to_int(), '_edd_bundled_products', true) ?: [];
    }

    private function get_mailers_settings(int $int_id): Product_Mailers_Settings
    {
        $mailchimp = get_post_meta($int_id, self::MAILCHIMP_META_NAME, true);
        $mailerlite = get_post_meta($int_id, self::MAILERLITE_META_NAME, true);
        $freshmail = get_post_meta($int_id, self::FRESHMAIL_META_NAME, true);
        $ipresso_tags = get_post_meta($int_id, self::IPRESSO_TAGS_META_NAME, true);
        $ipresso_tags_unsubscribe = get_post_meta($int_id, self::IPRESSO_TAGS_UNSUB_META_NAME, true);
        $activecampaign = get_post_meta($int_id, self::ACTIVECAMPAIGN_META_NAME, true);
        $activecampaign_unsubscribe = get_post_meta($int_id, self::ACTIVECAMPAIGN_UNSUB_META_NAME, true);
        $activecampaign_tags = get_post_meta($int_id, self::ACTIVECAMPAIGN_TAGS_META_NAME, true);
        $activecampaign_tags_unsubscribe = get_post_meta($int_id, self::ACTIVECAMPAIGN_TAGS_UNSUB_META_NAME, true);
        $getresponse = get_post_meta($int_id, self::GETRESPONSE_META_NAME, true);
        $getresponse_unsubscribe = get_post_meta($int_id, self::GETRESPONSE_UNSUB_META_NAME, true);
        $getresponse_tags = get_post_meta($int_id, self::GETRESPONSE_TAGS_META_NAME, true);
        $salesmanago_tags = get_post_meta($int_id, self::SALESMANAGO_TAGS_META_NAME, true);
        $interspire = get_post_meta($int_id, self::INTERSPIRE_META_NAME, true);
        $convertkit = get_post_meta($int_id, self::CONVERTKIT_META_NAME, true);
        $convertkit_tags = get_post_meta($int_id, self::CONVERTKIT_TAGS_META_NAME, true);
        $convertkit_tags_unsubscribe = get_post_meta($int_id, self::CONVERTKIT_TAGS_UNSUB_META_NAME, true);

        return new Product_Mailers_Settings(
            is_array($mailchimp) ? $mailchimp : [],
            is_array($mailerlite) ? $mailerlite : [],
            is_array($freshmail) ? $freshmail : [],
            $ipresso_tags ?? '',
            $ipresso_tags_unsubscribe ?? '',
            is_array($activecampaign) ? $activecampaign : [],
            is_array($activecampaign_unsubscribe) ? $activecampaign_unsubscribe : [],
            $activecampaign_tags ?? '',
            $activecampaign_tags_unsubscribe ?? '',
            is_array($getresponse) ? $getresponse : [],
            is_array($getresponse_unsubscribe) ? $getresponse_unsubscribe : [],
            is_array($getresponse_tags) ? $getresponse_tags : [],
            $salesmanago_tags ?? '',
            is_array($interspire) ? $interspire : [],
            is_array($convertkit) ? $convertkit : [],
            is_array($convertkit_tags) ? $convertkit_tags : [],
            is_array($convertkit_tags_unsubscribe) ? $convertkit_tags_unsubscribe : [],
        );
    }

    private function get_product_tags(int $int_id): array
    {
        $wp_tags = get_the_terms($int_id, 'download_tag');

        $tags = [];

        if (empty($wp_tags)) {
            return $tags;
        }

        foreach ($wp_tags as $wp_tag) {
            if (!($wp_tag instanceof WP_Term)) {
                continue;
            }

            $tags[] = $wp_tag->name;
        }

        return $tags;
    }

    private function update_product_tags($id, Product $product): void
    {
        wp_set_object_terms($id, $product->get_tags()->to_array(), 'download_tag', false);
    }

    private function update_product_categories($id, Product $product): void
    {
        wp_set_post_terms($id, $this->get_prepared_categories($product), 'download_category');
    }

    private function create_product_variant_collection(?array $variants): Product_Variant_Collection
    {
        $collection = new Product_Variant_Collection();

        if(!$variants){
            return $collection;
        }

        foreach ($variants as $key => $value) {
            $collection->add(
                new Product_Variant(
                    new Variant_ID((int)$key),
                    $value['name'],
                    (float)$value['amount'],
                    isset($value['sale_price']) ? (float)$value['sale_price'] : null,
                    !empty($value['recurring_payments_enabled']) ? true : false,
                    isset($value['bpmj_eddcm_purchase_limit']) ? (int)$value['bpmj_eddcm_purchase_limit'] : null,
                    isset($value['bpmj_eddcm_purchase_limit_items_left']) ? (int)$value['bpmj_eddcm_purchase_limit_items_left'] : null,
                    isset($value['access_time']) ? (int)$value['access_time'] : null,
                    $value['access_time_unit'] ?? null
                )
            );
        }

        return $collection;
    }

    private function get_date_time_object(?string $date, ?string $hour): ?DateTime
    {
        if (!empty($date) && (!empty($hour) || $hour == 0)) {
            return new DateTime($date . ' ' . $hour . ':00:00');
        }
        return null;
    }

    private function emit_price_updated_events(Product $product): void
    {
        if($product->get_variable_pricing_enabled()) {
            return;
        }

        $this->events->emit(
            Product_Event_Name::REGULAR_PRICE_UPDATED,
            $product->get_id()->to_int(),
            $product->get_price()->get_value()
        );

        $sale_price = $product->get_sale_price();
        if (!$product->get_sale_price_date_from() || !$product->get_sale_price_date_to()) {
            $this->events->emit(
                Product_Event_Name::PROMO_PRICE_UPDATED,
                $product->get_id()->to_int(),
                $sale_price ? $sale_price->get_value() : null
            );
        }

    }

	public function find_products_user_has_or_had_access_to( User_ID $user_id ): Product_Collection
	{
		$product_collection = new Product_Collection();

		$access = get_user_meta($user_id->to_int(), self::ACCESS_META_KEY, true);

		if (!is_array($access)) {
			return $product_collection;
		}

		foreach ($access as $id => $product) {
			$model = $this->find(new Product_ID((int)$id));

			if(!$model) {
				continue;
			}

			$product_collection->add($model);
		}

		return $product_collection;
	}

    public function user_has_or_had_access_to_product(User_ID $user_id, Product_ID $product_id): bool
    {
        $access = get_user_meta($user_id->to_int(), self::ACCESS_META_KEY, true);

        return in_array($product_id->to_int(), array_keys($access));
    }
}
