<?php

declare(strict_types=1);

namespace bpmj\wpidea\sales\order;

use bpmj\wpidea\sales\order\delivery\Delivery;
use bpmj\wpidea\modules\increasing_sales\core\value_objects\Increasing_Sales_Offer_Type;
use bpmj\wpidea\sales\order\invoice\Invoice;
use bpmj\wpidea\sales\order\cart\Cart_Content;
use bpmj\wpidea\sales\order\client\Client;
use bpmj\wpidea\data_types\personal_data\Full_Name;
use bpmj\wpidea\sales\order\additional\Additional_Fields;
use bpmj\wpidea\infrastructure\system\System;
use bpmj\wpidea\sales\order\value_objects\Recurring_Payment_Type;
use bpmj\wpidea\translator\Interface_Translator;
use WP_Query;
use EDD_Payments_Query;

class Orders_Repository implements Interface_Orders_Repository
{
    private const DELIVERY_ADDRESS_PHONE_META = 'edd_delivery_address_phone';
    private const DELIVERY_ADDRESS_STREET_META = 'edd_delivery_address_street';
    private const DELIVERY_ADDRESS_BUILDING_NUMBER_META = 'edd_delivery_address_building_number';
    private const DELIVERY_ADDRESS_APARTMENT_NUMBER_META = 'edd_delivery_address_apartment_number';
    private const DELIVERY_ADDRESS_POSTAL_CODE_META = 'edd_delivery_address_postal_code';
    private const DELIVERY_ADDRESS_CITY_META = 'edd_delivery_address_city';
    private const DELIVERY_ADDRESS_RECEIVER_FIRST_NAME_META = 'edd_delivery_address_first_name';
    private const DELIVERY_ADDRESS_RECEIVER_LAST_NAME_META = 'edd_delivery_address_last_name';
    private const DELIVERY_ADDRESS_RECEIVER_COMPANY_META = 'edd_delivery_address_company';

    private const MAX_VAL = 4294967295;

    private const CHUNK_SIZE = 12000;

    private const ORDER_STATUS_PAYU_RECURRENT = 'payu_recurrent';
    private const ORDER_STATUS_TPAY_RECURRENT = 'tpay_recurrent';

    private const NO_DISCOUNT_CODE_META_VALUE = 'none';

    private $order_status_labels;

    private $system_currency;

    private $unknown_email_translated_string;

    private $translator;

    public function __construct(
        Interface_Translator $translator
    ) {
        $this->translator = $translator;
    }

    public function find_by_id(int $id): ?Order
    {
        if (!$this->check_if_order_exists($id)) {
            return null;
        }

        $this->cache_static_system_data();
        return $this->prepare_orders([$id])->get_first();
    }

    public function find_by_criteria(Order_Query_Criteria $criteria): Order_Collection
    {
        $this->cache_static_system_data();

        $this->apply_pre_query_filters();

        $edd_criteria = $this->parse_criteria_to_edd_criteria($criteria);

        $edd_payment_query = new EDD_Payments_Query($edd_criteria);
        $order_ids = $edd_payment_query->get_payments();

        return $this->prepare_orders($order_ids);
    }

    public function store_meta(Order $order, string $key, string $value): void
    {
        update_post_meta($order->get_id(), $key, $value);
    }

    public function get_meta(Order $order, string $key): string
    {
        return get_post_meta($order->get_id(), $key, true);
    }

    private function check_if_order_exists(int $id): bool
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'posts';

        $wpdb->get_results("SELECT ID FROM $table_name WHERE ID = $id AND post_type='edd_payment' LIMIT 1");

        return ($wpdb->num_rows > 0);
    }

    private function cache_static_system_data(): void
    {
        $this->order_status_labels = $this->get_order_status_labels();
        $this->system_currency = System::get_currency();
        $this->unknown_email_translated_string = $this->translator->translate('orders.unknown_email');
    }

    private function apply_pre_query_filters(): void
    {
        add_action('edd_pre_get_payments', [$this, 'search_user_full_name']);
        add_action('edd_pre_get_payments', [$this, 'search_total_range']);
        add_action('edd_pre_get_payments', [$this, 'search_by_email']);
        add_action('edd_pre_get_payments', [$this, 'search_by_phone_no']);
        add_action('edd_pre_get_payments', [$this, 'search_meta_query']);
        add_action('edd_pre_get_payments', [$this, 'search_by_products']);
        add_action('edd_pre_get_payments', [$this, 'search_by_increasing_sales_offer_type']);
        add_action('edd_pre_get_payments', [$this, 'search_by_first_checkbox']);
        add_action('edd_pre_get_payments', [$this, 'search_by_second_checkbox']);
        add_action('edd_pre_get_payments', [$this, 'search_by_payment_method']);
        add_action('edd_pre_get_payments', [$this, 'search_by_recurring_type']);
    }

    private function prepare_orders(array $order_ids): Order_Collection
    {
        $orders_array = [];
        $order_ids_chunks = array_chunk($order_ids, self::CHUNK_SIZE);

        foreach ($order_ids_chunks as $chunk) {
            $orders_array = array_merge($orders_array, $this->prepare_orders_chunk($chunk));
        }

        return new Order_Collection($orders_array);
    }

    private function prepare_orders_chunk(array $order_ids): array
    {
        $payments_meta = $this->get_parsed_orders_meta($order_ids);

        $orders_array = [];

        foreach ($order_ids as $id) {
            $orders_array[] = $this->prepare_order($id, $payments_meta[$id]);
        }

        return $orders_array;
    }

    private function get_parsed_orders_meta(array $payment_ids): array
    {
        $parsed_meta = [];

        $chunk_meta = $this->query_meta_for_orders($payment_ids);
        $statuses = $this->query_statuses_and_dates_for_orders($payment_ids);

        foreach ($chunk_meta as $i => $item) {
            if (isset($item->meta_key)) {
                $parsed_meta[(int)$item->post_id][$item->meta_key] = $item->meta_value;
            }
        }

        foreach ($statuses as $i => $item) {
            $parsed_meta[(int)$item->ID]['status'] = $item->post_status;
            $parsed_meta[(int)$item->ID]['post_date'] = $item->post_date;
        }

        return $parsed_meta;
    }

    private function query_meta_for_orders(array $payment_ids): array
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'postmeta';
        $ids = implode(',', $payment_ids);
        $meta_keys_to_include = [
            "'_edd_payment_user_email'",
            "'_edd_payment_user_id'",
            "'_edd_payment_customer_id'",
            "'_edd_payment_meta'",
            "'_edd_payment_gateway'",
            "'_edd_payment_tax'",
            "'_edd_payment_total'",
            "'_payu_payment_subtype'",
            "'_tpay_payment_subtype'",
            "'bpmj_eddcm_purchase_data'",
            "'bpmj_eddcm_buy_as_gift_vouchers'",
            "'publigo_increasing_sales_offer_type'",
            "'_payu_payment_subtype'",
            "'_payu_recurrent_payment_token'",
            "'_tpay_payment_subtype'",
            "'_tpay_cli_auth'",
        ];
        $meta_keys_to_include_string = implode(',', $meta_keys_to_include);
        $limit = self::CHUNK_SIZE * count($meta_keys_to_include);

        return $wpdb->get_results(
            "SELECT post_id, meta_key, meta_value
            FROM $table_name       
            WHERE post_id IN ($ids) AND
            meta_key IN ({$meta_keys_to_include_string})
            ORDER BY meta_id ASC LIMIT $limit"
        );
    }

    private function query_statuses_and_dates_for_orders(array $payment_ids): array
    {
        global $wpdb;

        $posts_table_name = $wpdb->prefix . 'posts';
        $statuses_query_limit = self::CHUNK_SIZE;
        $ids = implode(',', $payment_ids);

        return $wpdb->get_results(
            "SELECT ID, post_status, post_date
            FROM {$posts_table_name}       
            WHERE ID IN ($ids)
            LIMIT $statuses_query_limit"
        );
    }

    private function prepare_order($order_id, array $post_meta): Order
    {
        $order_meta = $this->get_order_meta($post_meta, $order_id);

        $user_id = (int)($post_meta['_edd_payment_user_id'] ?? 0);

        $cart_details = isset($order_meta['cart_details']) ? maybe_unserialize($order_meta['cart_details']) : [];
        $cart_details = is_array($cart_details) ? $cart_details : [];

        $total = $this->get_order_total($order_meta, $post_meta['_edd_payment_total']);
        $subtotal = $this->get_order_subtotal_from_cart_details(
            $cart_details,
            $total,
            $post_meta['_edd_payment_tax'] ?? 0
        );
        $currency = $order_meta['currency'] ?: ($this->system_currency ?? '');
        $gateway = $post_meta['_edd_payment_gateway'] ?? '';
        $status = $post_meta['status'] ?? '';
        $status_label = $this->get_payment_status_label_from_meta($status, $post_meta);
        $date = $this->is_order_recurring($post_meta) ? $post_meta['post_date'] : $order_meta['date'];

        $cart = new Cart_Content();
        $cart->set_item_names($this->get_item_names_from_cart_details($cart_details));
        $cart->set_item_details($cart_details);

        $user_meta_info = isset($order_meta['user_info']) ? maybe_unserialize($order_meta['user_info']) : [];

        $purchase_data = maybe_unserialize($post_meta['bpmj_eddcm_purchase_data'] ?? []);
        $voucher_data = maybe_unserialize($post_meta['bpmj_eddcm_buy_as_gift_vouchers'] ?? []);

        $discount_code_meta_value = $user_meta_info['discount'] ?? self::NO_DISCOUNT_CODE_META_VALUE;
        $discount_code = ($discount_code_meta_value === self::NO_DISCOUNT_CODE_META_VALUE) ? null : Discount_Code::from_string(
            $discount_code_meta_value
        );

        $full_name = new Full_Name(($user_meta_info['first_name'] ?? ''), ($user_meta_info['last_name'] ?? ''));

        $client = new Client(
            (int)$post_meta['_edd_payment_customer_id'],
            $order_meta['email'], $full_name,
            $purchase_data['bpmj_eddcm_phone_no'] ?? ''
        );

        $invoice_apartment_number = $order_meta['bpmj_edd_invoice_apartment_number'] ?? null;

        $invoice = new Invoice();
        $invoice->set_invoice_type($order_meta['bpmj_edd_invoice_type'] ?? '');
        $invoice->set_invoice_company_name($order_meta['bpmj_edd_invoice_company_name'] ?? '');
        $invoice->set_invoice_country($order_meta['bpmj_edd_invoice_country'] ?? '');
        $invoice->set_invoice_city($order_meta['bpmj_edd_invoice_city'] ?? '');
        $invoice->set_invoice_street($order_meta['bpmj_edd_invoice_street'] ?? '');
        $invoice->set_invoice_building_number($order_meta['bpmj_edd_invoice_building_number'] ?? '');
        $invoice->set_invoice_apartment_number($invoice_apartment_number ? (int)$invoice_apartment_number : null);
        $invoice->set_invoice_nip($order_meta['bpmj_edd_invoice_nip'] ?? '');
        $invoice->set_invoice_person_name($order_meta['bpmj_edd_invoice_person_name'] ?? '');
        $invoice->set_invoice_postcode($order_meta['bpmj_edd_invoice_postcode'] ?? '');

        $delivery = $this->prepare_delivery_from_order_meta($order_meta);

        $additional_fields = new Additional_Fields();
        $additional_fields->set_buy_as_gift($purchase_data['bpmj_eddcm_buy_as_gift'] ?? false);
        $additional_fields->set_voucher_codes($this->voucher_data_array_to_string($voucher_data) ?? '');
        $additional_fields->set_checkbox_checked($purchase_data['bpmj_eddcm_additional_checkbox_checked'] ?? false);
        $additional_fields->set_checkbox_description(
            $purchase_data['bpmj_eddcm_additional_checkbox_description'] ?? ''
        );
        $additional_fields->set_checkbox2_checked($purchase_data['bpmj_eddcm_additional_checkbox2_checked'] ?? false);
        $additional_fields->set_checkbox2_description(
            $purchase_data['bpmj_eddcm_additional_checkbox2_description'] ?? ''
        );
        $additional_fields->set_order_comment($purchase_data['bpmj_eddcm_order_comment'] ?? '');

        $increasing_sales_offer_type = $post_meta['publigo_increasing_sales_offer_type'] ?? null;
        $increasing_sales_offer_type = $increasing_sales_offer_type ? new Increasing_Sales_Offer_Type($increasing_sales_offer_type) : null;

        return new Order(
            $order_id,
            $user_id,
            $date ?: null,
            $subtotal,
            $total,
            $currency,
            $gateway,
            $status,
            $status_label,
            $cart,
            $client,
            $invoice,
            $additional_fields,
            $discount_code,
            $increasing_sales_offer_type,
            new Recurring_Payment_Type($this->determine_payment_recurring_type($post_meta)),
            $delivery
        );
    }

    private function exists_delivery_address(array $order_meta): bool
    {
        return (!empty($order_meta[self::DELIVERY_ADDRESS_PHONE_META]) &&
                !empty($order_meta[self::DELIVERY_ADDRESS_STREET_META]) &&
                !empty($order_meta[self::DELIVERY_ADDRESS_BUILDING_NUMBER_META]) &&
                !empty($order_meta[self::DELIVERY_ADDRESS_POSTAL_CODE_META]) &&
                !empty($order_meta[self::DELIVERY_ADDRESS_CITY_META])
            );
    }

    private function voucher_data_array_to_string(array $voucher_data): string
    {
        return implode(',', $voucher_data);
    }

    public function get_payment_status_label_from_meta(string $status, array $meta): string
    {
        if (($meta['_payu_payment_subtype'] ?? '') === 'recurrent' && $meta['status'] === 'pending') {
            return $this->order_status_labels[self::ORDER_STATUS_PAYU_RECURRENT];
        }

        if (($meta['_tpay_payment_subtype'] ?? '') === 'recurrent' && $meta['status'] === 'pending') {
            return $this->order_status_labels[self::ORDER_STATUS_TPAY_RECURRENT];
        }

        return $this->order_status_labels[$status] ?? $status;
    }

    public function count_by_criteria(Order_Query_Criteria $criteria): int
    {
        $this->apply_pre_query_filters();

        $edd_payment_query = new EDD_Payments_Query($criteria->get_query_criteria_for_total_items());

        do_action('edd_pre_get_payments', $edd_payment_query);

        return (new WP_Query($edd_payment_query->args))->found_posts;
    }

    public function remove(int $id): void
    {
        edd_delete_purchase($id);
    }

    public function search_meta_query($query): void
    {
        $serialization_variants = [
            's:1000' => 's:([1-9]|[1-9][0-9]|[1-9][0-9][0-9]|1000):"', //1000 sings string
            's:100' => 's:([0-9]|[1-9][0-9]|100):"',//100 sings string
            'd' => 'd:' //100 sings  decimal
        ];

        $fields_to_be_searched = [
            [
                'field_name' => 'invoice_company_name',
                'pre' => 'bpmj_edd_invoice_company_name',
                'variant' => $serialization_variants['s:1000']
            ],
            [
                'field_name' => 'invoice_country',
                'pre' => 'bpmj_edd_invoice_country',
                'variant' => $serialization_variants['s:100']
            ],
            [
                'field_name' => 'invoice_type',
                'pre' => 'bpmj_edd_invoice_type',
                'variant' => $serialization_variants['s:100']
            ],
            [
                'field_name' => 'invoice_city',
                'pre' => 'bpmj_edd_invoice_city',
                'variant' => $serialization_variants['s:100']
            ],
            [
                'field_name' => 'invoice_street',
                'pre' => 'bpmj_edd_invoice_street',
                'variant' => $serialization_variants['s:100']
            ],
            [
                'field_name' => 'invoice_building_number',
                'pre' => 'bpmj_edd_invoice_building_number',
                'variant' => $serialization_variants['s:100']
            ],
            [
                'field_name' => 'invoice_apartment_number',
                'pre' => 'bpmj_edd_invoice_apartment_number',
                'variant' => $serialization_variants['s:100']
            ],
            [
                'field_name' => 'invoice_nip',
                'pre' => 'bpmj_edd_invoice_nip',
                'variant' => $serialization_variants['s:100']
            ],
            [
                'field_name' => 'invoice_person_name',
                'pre' => 'bpmj_edd_invoice_person_name',
                'variant' => $serialization_variants['s:100']
            ],
            [
                'field_name' => 'invoice_postcode',
                'pre' => 'bpmj_edd_invoice_postcode',
                'variant' => $serialization_variants['s:100']
            ]
        ];

        foreach ($fields_to_be_searched as $field_to_be_searched) {
            if (!array_key_exists($field_to_be_searched['field_name'], $query->args)) {
                continue;
            }

            $field_value = $query->args[$field_to_be_searched['field_name']];

            $variant = $field_to_be_searched['variant'];

            $query->__set('meta_query', [
                'key' => '_edd_payment_meta',
                'value' => $field_to_be_searched['pre'] . '";' . $variant . '[a-zA-Z0-9 ]*' . $field_value . '[a-zA-Z0-9 ]*',
                'compare' => 'REGEXP'
            ]);
        }
    }

    public function search_by_phone_no($query): void
    {
        $fields_to_be_searched = [
            [
                'field_name' => 'phone_no',
                'pre' => 'bpmj_eddcm_phone_no',
                'variant' => 's:([0-9]|[1-9][0-9]|100):"',
            ],
        ];

        foreach ($fields_to_be_searched as $field_to_be_searched) {
            if (!array_key_exists($field_to_be_searched['field_name'], $query->args)) {
                continue;
            }

            $field_value = $query->args[$field_to_be_searched['field_name']];

            $variant = $field_to_be_searched['variant'];

            $query->__set('meta_query', [
                'key' => 'bpmj_eddcm_purchase_data',
                'value' => $field_to_be_searched['pre'] . '";' . $variant . '[a-zA-Z0-9 ]*' . $field_value . '[a-zA-Z0-9 ]*',
                'compare' => 'REGEXP',
            ]);
        }
    }

    public function search_total_range($query): void
    {
        if (!array_key_exists('total', $query->args)) {
            return;
        }

        $min = $query->args['total'][0] ?? 0;
        $max = $query->args['total'][1] ?? self::MAX_VAL;

        $meta_query_array = [

            [
                'key' => '_edd_payment_total',
                'value' => [$min, $max],
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC'
            ]
        ];

        $query->__set('meta_query', $meta_query_array);
    }

    public function search_by_increasing_sales_offer_type($query): void
    {
        if (!array_key_exists('increasing_sales_offer_type', $query->args)) {
            return;
        }

        $value = $query->args['increasing_sales_offer_type'];

        $key = 'publigo_increasing_sales_offer_type';
        $search_meta = [
            'key' => $key,
            'value' => $value,
            'compare' => '='
        ];

        $query->__set('meta_query', $search_meta);
    }

    public function search_by_email($query): void
    {
        if (!array_key_exists('email', $query->args)) {
            return;
        }

        $value = $query->args['email'];

        $key = '_edd_payment_user_email';
        $search_meta = [
            'key' => $key,
            'value' => $value,
            'compare' => 'LIKE'
        ];

        $query->__set('meta_query', $search_meta);
    }

    public function search_by_first_checkbox($query): void
    {
        if (!array_key_exists('first_checkbox', $query->args)) {
            return;
        }

        $relation = 'OR';

        $compare = $query->args['first_checkbox'] ? 'REGEXP' : 'NOT REGEXP';

        $meta_query_array = [
            'relation' => $relation,
            [
                'key' => 'bpmj_eddcm_purchase_data',
                'value' => "bpmj_eddcm_additional_checkbox_checked\";b:1;s:42:\"bpmj_eddcm_additional_checkbox_description",
                'compare' => $compare
            ]
        ];

        $query->__set('meta_query', $meta_query_array);
    }

    public function search_by_second_checkbox($query): void
    {
        if (!array_key_exists('second_checkbox', $query->args)) {
            return;
        }

        $relation = 'OR';

        $compare = $query->args['second_checkbox'] ? 'REGEXP' : 'NOT REGEXP';

        $meta_query_array = [
            'relation' => $relation,
            [
                'key' => 'bpmj_eddcm_purchase_data',
                'value' => "bpmj_eddcm_additional_checkbox2_checked\";b:1;s:43:\"bpmj_eddcm_additional_checkbox2_description",
                'compare' => $compare
            ]
        ];

        $query->__set('meta_query', $meta_query_array);
    }


    public function search_by_products($query): void
    {
        if (!array_key_exists('products', $query->args)) {
            return;
        }

        $array_product = $query->args['products'];

        $meta_query_array = ['relation' => 'OR'];
        foreach ($array_product as $product) {
            $meta_query_array[] = [
                'key' => '_edd_payment_meta',
                'value' => "\"name\";s:[0-9]{1,}:.*;s:2:\"id\";i:$product",
                'compare' => 'REGEXP'
            ];
        }

        $query->__set('meta_query', $meta_query_array);
    }

    public function search_user_full_name($query): void
    {
        if (!array_key_exists('full_name', $query->args)) {
            return;
        }

        $name_array = explode(' ', $query->args['full_name']);

        $relation = (count($name_array) > 1) ? 'AND' : 'OR';

        $first_name = (count($name_array) > 1) ? $name_array[0] : $query->args['full_name'];
        $last_name = (count($name_array) > 1) ? $name_array[1] : $query->args['full_name'];

        $meta_query_array = [
            'relation' => $relation,
            [
                'key' => '_edd_payment_meta',
                'value' => "first_name\";s:[0-9]{1,}:\".*$first_name.*\";s:9:\"last_name",
                'compare' => 'REGEXP'
            ],
            [
                'key' => '_edd_payment_meta',
                'value' => "last_name\";s:[0-9]{1,}:\".*$last_name.*\";s:8:\"discount",
                'compare' => 'REGEXP'
            ]
        ];

        $query->__set('meta_query', $meta_query_array);
    }

    private function get_order_subtotal_from_cart_details(array $cart_details, $total, $tax = null)
    {
        $subtotal = 0;

        if (is_array($cart_details)) {
            foreach ($cart_details as $item) {
                if (isset($item['subtotal'])) {
                    $subtotal += $item['subtotal'];
                }
            }
        } else {
            $subtotal = $total;
            $tax = edd_use_taxes() ? $tax : 0;
            $subtotal -= $tax;
        }

        return (float)$subtotal;
    }

    private function get_order_meta(array $post_meta, int $payment_id): array
    {
        $meta = maybe_unserialize($post_meta['_edd_payment_meta']);

        if (empty($meta)) {
            $meta = [];
        }

        if (empty($meta['email'])) {
            $customer_id = $post_meta['_edd_payment_customer_id'];
            $meta['email'] = $this->get_payment_email($post_meta, $customer_id);
        }

        if (empty($meta['date'])) {
            $meta['date'] = get_post_field('post_date', $payment_id);
        }

        return $meta;
    }

    private function get_payment_email(array $post_meta, $customer_id = null): string
    {
        $email = $post_meta['_edd_payment_user_email'] ?? null;

        if (empty($email)) {
            $email = EDD()->customers->get_column('email', $customer_id) ?:
                $this->unknown_email_translated_string;
        }

        return $email;
    }

    private function get_order_total(array $payment_meta, $payment_total_meta)
    {
        $amount = $payment_total_meta;

        if (empty($amount) && '0.00' !== $amount) {
            $meta = $payment_meta;
            $meta = maybe_unserialize($meta);

            if (isset($meta['amount'])) {
                $amount = $meta['amount'];
            }
        }

        return (float)$amount;
    }

    private function get_item_names_from_cart_details(array $cart_details): array
    {
        $names = [];

        foreach ($cart_details as $item) {
            $names[] = $item['name'];
        }

        return $names;
    }

    private function get_order_status_labels(): array
    {
        $edd_statuses = edd_get_payment_statuses();
        return array_merge($edd_statuses, [
            self::ORDER_STATUS_PAYU_RECURRENT => $this->translator->translate('orders.status.payu_recurrent'),
            self::ORDER_STATUS_TPAY_RECURRENT => $this->translator->translate('orders.status.tpay_recurrent')
        ]);
    }

    private function is_order_recurring(array $post_meta): bool
    {
        return ($post_meta['_tpay_payment_subtype'] ?? '') === 'recurrent'
            || ($post_meta['_payu_payment_subtype'] ?? '') === 'recurrent';
    }

    private function parse_criteria_to_edd_criteria(Order_Query_Criteria $criteria): array
    {
        $edd_criteria = $criteria->get_query_criteria();

        if (isset($edd_criteria['discount'])) {
            $edd_criteria['s'] = 'discount:' . $edd_criteria['discount'];
        }

        return $edd_criteria;
    }

    public function search_by_payment_method($query): void
    {
        if (!array_key_exists('payment_method', $query->args)) {
            return;
        }

        $meta_query_array = [
            'key' => '_edd_payment_gateway',
            'value' => $query->args['payment_method'],
            'compare' => 'IN',
        ];

        $query->__set('meta_query', $meta_query_array);
    }
    public function search_by_recurring_type($query): void
    {
        if (!array_key_exists('recurring_payment', $query->args)) {
            return;
        }

        $recurring_payments_values = [
            Recurring_Payment_Type::RECURRING_PAYMENT_NO => 1,
            Recurring_Payment_Type::RECURRING_PAYMENT_MANUAL => 10,
            Recurring_Payment_Type::RECURRING_PAYMENT_AUTOMATIC => 100,
        ];

        $query_criteria = [
            // no query
            1 => [
                'relation' => 'AND',
                [
                    'key' => '_tpay_payment_subtype',
                    'compare' => 'NOT EXISTS',
                ],
                [
                    'key' => '_payu_payment_subtype',
                    'compare' => 'NOT EXISTS',
                ],
            ],
            // manual query
            10 => [
                'relation' => 'AND',
                [
                    'relation' => 'OR',
                    [
                        'key' => '_tpay_payment_subtype',
                        'compare' => 'EXISTS',
                    ],
                    [
                        'key' => '_payu_payment_subtype',
                        'compare' => 'EXISTS',
                    ],
                ],
                [
                    'key' => '_payu_recurrent_payment_token',
                    'compare' => 'NOT EXISTS',
                ],
                [
                    'key' => '_tpay_cli_auth',
                    'compare' => 'NOT EXISTS',
                ],
            ],
            // automatic query
            100 => [
                'relation' => 'AND',
                [
                    'relation' => 'OR',
                    [
                        'key' => '_tpay_payment_subtype',
                        'compare' => 'EXISTS',
                    ],
                    [
                        'key' => '_payu_payment_subtype',
                        'compare' => 'EXISTS',
                    ],
                ],
                [
                    'relation' => 'OR',
                    [
                        'key' => '_payu_recurrent_payment_token',
                        'compare' => 'EXISTS',
                    ],
                    [
                        'key' => '_tpay_cli_auth',
                        'compare' => 'EXISTS',
                    ],
                ],
            ],
            // no + manual query
            11 => [
                'relation' => 'OR',
                [
                    'relation' => 'AND',
                    [
                        'key' => '_payu_recurrent_payment_token',
                        'compare' => 'NOT EXISTS',
                    ],
                    [
                        'key' => '_tpay_cli_auth',
                        'compare' => 'NOT EXISTS',
                    ],
                ],
                [
                    'relation' => 'AND',
                    [
                        'key' => '_tpay_payment_subtype',
                        'compare' => 'NOT EXISTS',
                    ],
                    [
                        'key' => '_payu_payment_subtype',
                        'compare' => 'NOT EXISTS',
                    ],
                ],
            ],
            // no + automatic query
            101 => [
                'relation' => 'OR',
                [
                    'relation' => 'AND',
                    [
                        'key' => '_tpay_payment_subtype',
                        'compare' => 'NOT EXISTS',
                    ],
                    [
                        'key' => '_payu_payment_subtype',
                        'compare' => 'NOT EXISTS',
                    ],
                ],
                [
                    'key' => '_payu_recurrent_payment_token',
                    'compare' => 'EXISTS',
                ],
                [
                    'key' => '_tpay_cli_auth',
                    'compare' => 'EXISTS',
                ],
            ],
            // manual + automatic query
            110 =>   [
                'relation' => 'OR',
                [
                    'key' => '_tpay_payment_subtype',
                    'compare' => ' EXISTS',
                ],
                [
                    'key' => '_payu_payment_subtype',
                    'compare' => 'EXISTS',
                ],
            ],
            // no + manual + automatic query
            111 => [],
        ];

        $query_criteria_value = 0;
        foreach ($query->args['recurring_payment'] as $type) {
            if (!isset($recurring_payments_values[$type])) {
                continue;
            }

            $query_criteria_value += $recurring_payments_values[$type];
        }

        $meta_query = $query_criteria[$query_criteria_value] ?? [];

        if (empty($meta_query)) {
            return;
        }

        $query->__set('meta_query', $meta_query);
    }

    private function determine_payment_recurring_type(array $post_meta): string
    {
        if ($this->is_order_recurring($post_meta)) {
            if (!empty($post_meta['_payu_recurrent_payment_token'])
                || !empty($post_meta['_tpay_cli_auth'])) {
            return Recurring_Payment_Type::RECURRING_PAYMENT_AUTOMATIC;
            }

        return Recurring_Payment_Type::RECURRING_PAYMENT_MANUAL;
        }

        return Recurring_Payment_Type::RECURRING_PAYMENT_NO;
    }

    private function prepare_delivery_from_order_meta(array $order_meta): ?Delivery
    {
        if (!$this->exists_delivery_address($order_meta)) {
            return null;
        }

        $delivery_apartment_number = $order_meta[self::DELIVERY_ADDRESS_APARTMENT_NUMBER_META] ?? null;

        return new Delivery(
            $order_meta[self::DELIVERY_ADDRESS_PHONE_META] ?? '',
            $order_meta[self::DELIVERY_ADDRESS_STREET_META] ?? '',
            $order_meta[self::DELIVERY_ADDRESS_BUILDING_NUMBER_META] ?? '',
            $delivery_apartment_number ? (int)$delivery_apartment_number : null,
            $order_meta[self::DELIVERY_ADDRESS_POSTAL_CODE_META] ?? '',
            $order_meta[self::DELIVERY_ADDRESS_CITY_META] ?? '',
            $order_meta[self::DELIVERY_ADDRESS_RECEIVER_FIRST_NAME_META] ?? '',
            $order_meta[self::DELIVERY_ADDRESS_RECEIVER_LAST_NAME_META] ?? '',
            $order_meta[self::DELIVERY_ADDRESS_RECEIVER_COMPANY_META] ?? ''
        );
    }
}