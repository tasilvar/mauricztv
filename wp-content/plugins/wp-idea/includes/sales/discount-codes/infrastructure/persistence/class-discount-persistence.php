<?php

declare(strict_types=1);

namespace bpmj\wpidea\sales\discount_codes\infrastructure\persistence;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\sales\discount_codes\core\repositories\Discount_Query_Criteria;
use WP_Query;

class Discount_Persistence
{
    public function count_by_criteria(Discount_Query_Criteria $criteria): int
    {
        $args = array_merge(
            $this->get_default_args_from_criteria($criteria),
            [
                'fields' => 'ids'
            ]
        );

        return (new WP_Query($args))->found_posts;
    }

    public function find_by_criteria(
        Discount_Query_Criteria $criteria,
        int $per_page,
        int $page,
        ?Sort_By_Clause $sort_by,
        $exclude_slow_data = false
    ): array {
        $discount_codes_data = [];

        $args = $this->get_default_args_from_criteria($criteria, $per_page, $page, $sort_by);

        $discounts_query = new WP_Query($args);
        $discounts = $discounts_query->get_posts();
        
        if (!$discounts) {
            return $discount_codes_data;
        }

        foreach ($discounts as $discount) {
            $discount_id = $discount->ID;

            if( !$exclude_slow_data ) {
                $uses_max = edd_get_discount_max_uses($discount_id);
                $uses = edd_get_discount_uses($discount_id);
                $start_date = edd_get_discount_start_date($discount_id);
                $expiration = edd_get_discount_expiration($discount_id);
            }
            else {
                $uses_max = 0;
                $uses = 0;
                $start_date = null;
                $expiration = null;
            }

            $discount_codes_data[] = [
                'ID' => $discount_id,
                'name' => $discount->post_title,
                'code' => $exclude_slow_data ? '' : edd_get_discount_code($discount_id),
                'amount' => edd_get_discount_amount($discount_id),
                'amount_type' => edd_get_discount_type($discount_id),
                'uses' => $uses,
                'uses_max' => $uses_max,
                'start_date' => $start_date,
                'expiration' => $expiration,
                'status' => $exclude_slow_data || edd_is_discount_expired($discount_id) ? 'expired' : $discount->post_status,
            ];
        }

        return $discount_codes_data;
    }

    private function get_default_args_from_criteria(
        Discount_Query_Criteria $criteria,
        int $per_page = -1,
        int $page = 1,
        ?Sort_By_Clause $sort_by = null
    ): array {
        $args = [
            'post_type' => 'edd_discount',
            'post_status' => ['active', 'inactive', 'expired'],
            'posts_per_page' => $per_page,
            'paged' => $page,
            'meta_query' => [
                'relation' => 'AND'
            ]
        ];

        $args = $this->add_sort_by_to_args($args, $sort_by);
        $args = $this->add_filters_to_args($args, $criteria);

        return $args;
    }

    private function add_sort_by_to_args(array $args, ?Sort_By_Clause $sort_by): array
    {
        $args = array_merge($args, [
            'orderby' => 'ID',
            'order' => 'DESC',
        ]);

        $first_sort_by = $sort_by ? $sort_by->get_first() : null;

        if(!$first_sort_by) {
            return $args;
        }

        $desc = $first_sort_by->desc;
        $property = $first_sort_by->property;

        $args['order'] = $desc ? 'DESC' : 'ASC';

        if (!in_array($property, ['id', 'name'])) {
            if($property === 'uses') {
                $args['orderby'] = 'meta_value_num';
            } else {
                $args['orderby'] = 'meta_value';
            }

            $args['meta_key'] = '_edd_discount_' . $property;

            return $args;
        }

        if ($property === 'name') {
            $args['orderby'] = 'post_title';
        }

        if ($property === 'id') {
            $args['orderby'] = 'ID';
        }

        return $args;
    }

    private function add_filters_to_args(array $args, Discount_Query_Criteria $criteria): array
    {
        if($criteria->get_name_contains()) {
            $args['s'] = $criteria->get_name_contains();
        }

        if($criteria->get_code_contains()) {
            $args['meta_query'][] = [
                'key' => '_edd_discount_code',
                'value' => $criteria->get_code_contains(),
                'compare' => 'LIKE'
            ];
        }

        if($criteria->get_type_filter() === Discount_Query_Criteria::TYPE_FILTER_EXCLUDE_AUTO_GENERATED) {
            $args['meta_query'][] = [
                'relation' => 'AND',
                [
                    'key' => '_edd_discount_name',
                    'value' => 'Voucher - [A-Z0-9]*',
                    'compare' => 'NOT REGEXP'
                ],
                [
                    'key' => '_edd_discount_name',
                    'value' => '.* \[\d+\]',
                    'compare' => 'NOT REGEXP'
                ]
            ];
        }

        if($criteria->get_status()) {
            $args['post_status'] = $criteria->get_status();
        }
        
        return $args;
    }

    public function delete(int $id): void
    {
        edd_remove_discount($id);
    }
}