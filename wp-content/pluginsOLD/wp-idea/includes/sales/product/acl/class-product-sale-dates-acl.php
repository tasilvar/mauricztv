<?php

namespace bpmj\wpidea\sales\product\acl;

use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use WP_Query;
use DateTime;
use bpmj\wpidea\sales\product\core\event\Event_Name;
use bpmj\wpidea\resources\Resource_Type;

class Product_Sale_Dates_ACL implements Interface_Initiable
{
    private const CRON_ACTION = 'bpmj_wpi_com_cron_check_sales_dates';
    private const SALE_SHOULDNT_START_YET = 'shouldnt_start_yet';
    private const SALE_SHOULD_END_ALREADY = 'should_end_already';
    private const SALE_SHOULD_HAVE_EFFECT_NOW = 'should_have_effect_now';

    private Interface_Actions $actions;

    public function __construct(
        Interface_Actions $actions
    ) {
        $this->actions = $actions;
    }

    public function init(): void
    {
        $this->init_cron();

        $this->actions->add(self::CRON_ACTION, [$this, 'check_sale_dates']);
    }

    public function check_sale_dates(): void
    {
        $this->check_courses_sale_dates();

        $this->check_products_sale_dates();
    }

    private function init_cron(): void
    {
        if ( ! wp_next_scheduled(self::CRON_ACTION) ) {
            wp_schedule_event( time(), 'bpmj_eddcm_5min', self::CRON_ACTION);
        }
    }

    private function check_courses_sale_dates(): void
    {
        $query = new WP_Query( [
            'post_type' => 'courses',
            'post_status' => 'publish',
            'posts_per_page' => -1
        ]);

        foreach ( $query->get_posts() as $post ) {
            $product_id = get_post_meta( $post->ID, 'product_id', true );
            if( !empty($product_id) && 0 != ((int) $product_id)) {
                $this->do_sale_dates_check_on_product($post->ID, (int) $product_id);
            }
        }
    }

    private function check_products_sale_dates(): void
    {
        $query = new WP_Query( [
            'post_type' => 'download',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => 'wpi_resource_type',
                    'value' => implode(',', Resource_Type::SUPPORTED_TYPES),
                    'compare' => 'IN'
                ]
            ]
        ]);

        foreach ( $query->get_posts() as $post ) {
            $this->do_sale_dates_check_on_product($post->ID, $post->ID);
        }
    }

    private function do_sale_dates_check_on_product(int $post_id, int $product_id): void
    {
        if ( edd_has_variable_prices( $product_id ) ) {
            $this->handle_variable_prices_product_sale_dates($post_id, $product_id);

            return;
        }

        $this->handle_singular_price_product_sale_dates($post_id, $product_id);
    }

    private function handle_variable_prices_product_sale_dates(int $post_id, int $product_id): void
    {
        $variable_prices = get_post_meta( $product_id, 'edd_variable_prices', true );

        $sale_date_compare = $this->compare_variable_prices_product_sale_dates($post_id);

        $new_variable_prices = $variable_prices;
        $need_prices_update = false;

        foreach ( $variable_prices as $id => $variable_price ) {
            if ( ! empty( $variable_price['sale_price'] ) && $sale_date_compare === self::SALE_SHOULDNT_START_YET) {
                $new_variable_prices = $this->move_variant_sale_price_to_temporary_price(
                    $new_variable_prices,
                    $id,
                    $variable_price['sale_price']
                );
                $need_prices_update = true;
            }

            if ( ! empty( $variable_price['tmp_sale_price'] ) && $sale_date_compare === self::SALE_SHOULD_HAVE_EFFECT_NOW) {
                $new_variable_prices = $this->move_variant_temporary_price_to_sale_price(
                    $new_variable_prices,
                    $id,
                    $variable_price['tmp_sale_price']
                );
                $need_prices_update = true;
            }

            if ( ! empty( $variable_price['sale_price'] ) && $sale_date_compare === self::SALE_SHOULD_END_ALREADY) {
                $new_variable_prices = $this->clear_variant_sale_price_and_temporary_price($new_variable_prices, $id);
                $need_prices_update = true;
            }
        }

        if($need_prices_update) {
            update_post_meta( $product_id, 'edd_variable_prices', $new_variable_prices );

            $this->trigger_variable_prices_updated_event($product_id, $new_variable_prices);
        }
    }

    private function handle_singular_price_product_sale_dates(int $post_id, int $product_id): void
    {
        $tmp_sale_price       = get_post_meta( $post_id, 'tmp_sale_price', true );
        $current_sale_price       = get_post_meta( $product_id, 'edd_sale_price', true );

        $sale_date_compare = $this->compare_singular_price_product_sale_dates($post_id);

        $is_sale_price_currently_set = !empty($current_sale_price);
        $is_temporary_price_currently_set = !empty($tmp_sale_price);

        if ( $is_sale_price_currently_set && $sale_date_compare === self::SALE_SHOULDNT_START_YET) {
            $this->move_sale_price_to_temporary_price($product_id, $post_id, $current_sale_price);
        }

        if ( $is_temporary_price_currently_set && $sale_date_compare === self::SALE_SHOULD_HAVE_EFFECT_NOW) {
            $this->move_temporary_price_to_sale_price($product_id, $post_id, $tmp_sale_price);
        }

        if ( $is_sale_price_currently_set && $sale_date_compare === self::SALE_SHOULD_END_ALREADY) {
            $this->clear_sale_price_and_temporary_price($product_id, $post_id);
        }
    }

    private function compare_sale_dates(
        $variable_sale_price_from_date,
        $variable_sale_price_from_hour,
        $variable_sale_price_to_date,
        $variable_sale_price_to_hour
    ): string {
        $date_from = !empty($variable_sale_price_from_date) ? date_create_from_format(
            'Y-m-d H',
            $variable_sale_price_from_date . ' ' . $variable_sale_price_from_hour
        ) : null;

        if ($date_from === false) {
            $date_from = null;
        }

        $date_to = !empty($variable_sale_price_to_date) ? date_create_from_format(
            'Y-m-d H',
            $variable_sale_price_to_date . ' ' . $variable_sale_price_to_hour
        ) : null;

        if ($date_to === false) {
            $date_to = null;
        }

        return $this->compare_dates($date_from, $date_to);
    }

    private function compare_dates(?DateTime $date_from, ?DateTime $date_to): string
    {
        $timestamp_now = bpmj_eddpc_adjust_timestamp( time() );

        if ( $date_from != null && $timestamp_now < $date_from->getTimestamp() ) {
            return self::SALE_SHOULDNT_START_YET;
        }
        if ( $date_to != null && $timestamp_now >= $date_to->getTimestamp() ) {
            return self::SALE_SHOULD_END_ALREADY;
        }

        return self::SALE_SHOULD_HAVE_EFFECT_NOW;
    }

    private function move_sale_price_to_temporary_price(int $product_id, int $post_id, $edd_sale_price): void
    {
        update_post_meta($product_id, 'edd_sale_price', '');
        update_post_meta($post_id, 'tmp_sale_price', $edd_sale_price);

        $this->trigger_product_sale_price_updated_event($product_id, null);
    }

    private function clear_sale_price_and_temporary_price(int $product_id, int $post_id): void
    {
        update_post_meta($product_id, 'edd_sale_price', '');
        update_post_meta($post_id, 'tmp_sale_price', '');

        $this->trigger_product_sale_price_updated_event($product_id, null);
    }

    private function move_temporary_price_to_sale_price(int $product_id, int $post_id, $tmp_sale_price): void
    {
        update_post_meta($product_id, 'edd_sale_price', $tmp_sale_price);
        update_post_meta($post_id, 'tmp_sale_price', '');

        $this->trigger_product_sale_price_updated_event($product_id, $tmp_sale_price);
    }

    private function trigger_product_sale_price_updated_event(int $product_id, $new_price): void
    {
        do_action(Event_Name::PROMO_PRICE_UPDATED, $product_id, $new_price);
        do_action(Action_Name::AFTER_PROMO_PRICES_UPDATE);
    }

    private function compare_singular_price_product_sale_dates(int $post_id): string
    {
        $sale_price_from_date = get_post_meta( $post_id, 'sale_price_from_date', true );
        $sale_price_to_date   = get_post_meta( $post_id, 'sale_price_to_date', true );

        $sale_price_from_hour = get_post_meta( $post_id, 'sale_price_from_hour', true );
        $sale_price_to_hour   = get_post_meta( $post_id, 'sale_price_to_hour', true );

        return $this->compare_sale_dates(
            $sale_price_from_date,
            $sale_price_from_hour,
            $sale_price_to_date,
            $sale_price_to_hour
        );
    }

    private function compare_variable_prices_product_sale_dates(int $post_id): string
    {
        $variable_sale_price_from_date = get_post_meta( $post_id, 'variable_sale_price_from_date', true );
        $variable_sale_price_to_date   = get_post_meta( $post_id, 'variable_sale_price_to_date', true );

        $variable_sale_price_from_hour = get_post_meta( $post_id, 'variable_sale_price_from_hour', true );
        $variable_sale_price_to_hour   = get_post_meta( $post_id, 'variable_sale_price_to_hour', true );

        return $this->compare_sale_dates(
            $variable_sale_price_from_date,
            $variable_sale_price_from_hour,
            $variable_sale_price_to_date,
            $variable_sale_price_to_hour
        );
    }

    private function move_variant_sale_price_to_temporary_price($new_variable_prices, $variant_id, $sale_price): array
    {
        $new_variable_prices[$variant_id]['sale_price'] = '';
        $new_variable_prices[$variant_id]['tmp_sale_price'] = $sale_price;

        return $new_variable_prices;
    }

    private function move_variant_temporary_price_to_sale_price($new_variable_prices, $variant_id, $tmp_sale_price): array
    {
        $new_variable_prices[$variant_id]['sale_price'] = $tmp_sale_price;
        unset($new_variable_prices[$variant_id]['tmp_sale_price']);

        return $new_variable_prices;
    }

    private function clear_variant_sale_price_and_temporary_price($new_variable_prices, $id): array
    {
        $new_variable_prices[$id]['sale_price'] = '';
        unset($new_variable_prices[$id]['tmp_sale_price']);

        return $new_variable_prices;
    }

    private function trigger_variable_prices_updated_event(int $product_id, $new_variable_prices): void
    {
        do_action(Event_Name::VARIABLE_PRICES_UPDATED, $product_id, $new_variable_prices);
        do_action(Action_Name::AFTER_PROMO_PRICES_UPDATE);
    }
}