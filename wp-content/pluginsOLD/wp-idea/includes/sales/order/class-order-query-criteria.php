<?php
declare(strict_types = 1);

namespace bpmj\wpidea\sales\order;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use DateTime;

class Order_Query_Criteria
{
    public $per_page;
    public $page;
    public $sort_by;
    public $sort;
    public $filters;
    public $query_array;
    public $query_array_for_total_items;

    public function __construct(array $query)
    {
        $sort_by = $query['sortBy'] ?? new Sort_By_Clause();
        $sort_by_count = count($sort_by->get_all());

        $per_page = $query['perPage'] ?? -1;
        $page = $query['page'] ?? 1;

        $this->per_page = (int)$per_page;
        $this->page = (int)$page;
        $this->sort_by = $this->parse_sort_by($sort_by);
        $this->sort = ($sort_by_count > 0 && $this->sort_by) ? $sort_by->get_all()[0]->desc : null;
        $this->filters = $query['filters'] ?? [];
        $this->query_array = [];

        $this->set_properties();
        $this->query_array_for_total_items = $this->query_array;
    }

    public function set_properties(): void
    {
        foreach ($this->filters as $filter) {
            $property_name = $filter['id'];

            $set_function_name = 'set_query_' . $property_name;

            if ($filter['value']) {
                $this->$set_function_name($filter['value']);
            }
        }  
    }

    public function set_query_products($products): void
    {
        $this->query_array['products'] = $products;
    }

    public function set_query_user_email(string $user_email): void
    {

        $this->query_array['email'] = $user_email;
    }

    public function set_query_full_name(string $full_name): void
    {

        $this->query_array['full_name'] = $full_name;
    }

    public function set_query_invoice_company_name(string $company_name): void
    {

        $this->query_array['invoice_company_name'] = $company_name;
    }

    public function set_query_invoice_country(string $invoice_country): void
    {

        $this->query_array['invoice_country'] = $invoice_country;
    }


    public function set_query_invoice_nip(string $invoice_nip): void
    {

        $this->query_array['invoice_nip'] = $invoice_nip;
    }

    public function set_query_invoice_type(string $invoice_type): void
    {

        $this->query_array['invoice_type'] = $invoice_type;
    }

    public function set_query_phone_no(string $phone_no): void
    {
        $this->query_array['phone_no'] = $phone_no;
    }

    public function set_query_date(array $date): void
    {
        $startDate = new DateTime($date['startDate']);

        $endDate = new DateTime($date['endDate']);

        $this->query_array['start_date'] =  $startDate->format('Y-m-d H:i:s');
        $this->query_array['end_date'] = $endDate->format('Y-m-d H:i:s');
    }

    public function set_query_status(string $status): void
    {
        $this->query_array['status']  = $status;
    }

    public function set_query_subtotal(array $subtotal): void
    {
        $this->query_array['subtotal']  = $subtotal;
    }

    public function set_query_total(array $total): void
    {
        $this->query_array['total']  = $total;
    }

    public function set_query_discount_code(string $code): void
    {
        $this->query_array['discount']  = $code;
    }

    public function set_query_increasing_sales_offer_type(string $increasing_sales_offer_type): void
    {
        $this->query_array['increasing_sales_offer_type']  = $increasing_sales_offer_type;
    }

    public function set_query_first_checkbox(string $first_checkbox): void
    {
        $this->query_array['first_checkbox']  = $first_checkbox === 'true' ? true : false;
    }

    public function set_query_second_checkbox(string $second_checkbox): void
    {
        $this->query_array['second_checkbox']  = $second_checkbox === 'true' ? true : false;
    }

    public function get_query_criteria(): array
    {
        if ($this->per_page) $this->query_array['number'] = $this->per_page;
        if ($this->page) $this->query_array['page'] = $this->page;
        if ($this->sort_by) $this->query_array['orderby'] = $this->sort_by;
        if (!is_null($this->sort) && $this->sort_by){
            if ($this->sort) $this->query_array['order'] = 'DESC'; else $this->query_array['order'] = 'ASC';
        }

        $this->query_array['output'] = 'posts';

        $this->query_array['fields'] = 'ids';

        return $this->query_array;
    }

    public function get_query_criteria_for_total_items(): array
    {
        $this->query_array_for_total_items['number'] = 1;

        $this->query_array_for_total_items['fields'] = 'ids';

        $this->query_array_for_total_items['update_post_meta_cache'] = false;

        $this->query_array_for_total_items['update_post_term_cache'] = false;

        return $this->query_array_for_total_items;
    }

    private function parse_sort_by(Sort_By_Clause $sort_by): ?string
    {
        $property = $sort_by->get_all()[0]->property ?? null;

        if(is_null($property)) {
            return null;
        }

        if($property === 'total') {
            return 'amount';
        }

        return $property;
    }

    public function set_query_payment_method(array $payment_method): void
    {
        $this->query_array['payment_method'] = $payment_method;
    }

    public function set_query_recurring_payment(array $recurring_payment): void
    {
        $this->query_array['recurring_payment'] = $recurring_payment;
    }
}
