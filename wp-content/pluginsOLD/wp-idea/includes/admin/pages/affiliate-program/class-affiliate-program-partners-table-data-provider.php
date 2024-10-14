<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\affiliate_program;

use bpmj\wpidea\admin\tables\dynamic\data\Dynamic_Table_Data_Usage_Context;
use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\helpers\Price_Formatting;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\infrastructure\system\System;
use bpmj\wpidea\modules\affiliate_program\core\repositories\Interface_Partner_Repository;
use bpmj\wpidea\modules\affiliate_program\core\repositories\Partner_Query_Criteria;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\modules\affiliate_program\core\entities\Partner;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\modules\affiliate_program\core\services\Affiliate_Link_Generator;
use bpmj\wpidea\user\Interface_User_Repository;

class Affiliate_Program_Partners_Table_Data_Provider implements Interface_Dynamic_Table_Data_Provider
{
    private string $system_currency;

    private Interface_Translator $translator;
    private System $system;
    private Interface_Partner_Repository $partner_repository;
    private Interface_Url_Generator $url_generator;
    private Affiliate_Link_Generator $affiliate_link_generator;
    private Interface_User_Repository $user_repository;

    public function __construct(
        Interface_Translator $translator,
        System $system,
        Interface_Partner_Repository $partner_repository,
        Interface_Url_Generator $url_generator,
        Affiliate_Link_Generator $affiliate_link_generator,
        Interface_User_Repository $user_repository
    ) {
        $this->translator = $translator;
        $this->system = $system;
        $this->partner_repository = $partner_repository;
        $this->url_generator = $url_generator;
        $this->affiliate_link_generator = $affiliate_link_generator;
        $this->user_repository = $user_repository;
    }

    public function get_rows(
        array $filters,
        Sort_By_Clause $sort_by,
        int $per_page,
        int $page,
        Dynamic_Table_Data_Usage_Context $context
    ): array {
        $result = [];
        $criteria = $this->get_criteria_from_filters($filters);
        foreach ($this->partner_repository->find_by_criteria($criteria, $page, $per_page, $sort_by) as $partner) {
            $result[] = $this->partner_to_array($partner);
        }
        return $result;
    }

    public function get_total(array $filters): int
    {
        return $this->partner_repository->count_by_criteria(
            $this->get_criteria_from_filters($filters)
        );
    }

    private function get_criteria_from_filters(array $filters): Partner_Query_Criteria
    {
        $criteria = new Partner_Query_Criteria();

        $criteria->set_full_name_like($this->get_filter_value_if_present($filters, 'name'));
        $criteria->set_email_like($this->get_filter_value_if_present($filters, 'partner_email'));
        $criteria->set_partner_link_like($this->get_filter_value_if_present($filters, 'partner_link'));

        $sales_sum_range = $this->get_filter_value_if_present($filters, 'sale_amount_sum');
        $criteria->set_sales_sum_range($sales_sum_range[0] ?? null, $sales_sum_range[1] ?? null);

        $amount_sum = $this->get_filter_value_if_present($filters, 'amount_sum');
        $criteria->set_commissions_sum_range($amount_sum[0] ?? null, $amount_sum[1] ?? null);

        $unsettled_amount_sum = $this->get_filter_value_if_present($filters, 'unsettled_amount_sum');
        $criteria->set_unsettled_commissions_sum_range($unsettled_amount_sum[0] ?? null, $unsettled_amount_sum[1] ?? null);

        $status = $this->get_filter_value_if_present($filters, 'status');
        $criteria->set_status($status);

        return $criteria;
    }

    private function amount_in_fractions_to_float(int $amount): float
    {
        return Price_Formatting::format_to_float($amount, Price_Formatting::DIVIDE_BY_100);
    }

    private function get_filter_value_if_present(array $filters, string $filter_name)
    {
        return array_values(
            array_filter($filters, static function ($filter, $key) use ($filter_name) {
                return $filter['id'] === $filter_name;
            }, ARRAY_FILTER_USE_BOTH)
        )[0]['value'] ?? null;
    }

    private function get_currency(): string
    {
        if (!isset($this->system_currency)) {
            $this->system_currency = $this->system->get_system_currency();
        }

        return $this->system_currency;
    }

    private function partner_to_array(Partner $partner): array
    {
        $user = $this->user_repository->find_by_id($partner->get_user_id());
        $edit_url = $user ? $this->url_generator->generate_admin_page_url('user-edit.php', [
            'user_id' => $partner->get_user_id()->to_int()
        ]) : null;

        return [
            'id' => $partner->get_id()->to_int(),
            'name' => $partner->get_full_name()->get_full_name(),
            'partner_email' => $partner->get_email()->get_value(),
            'partner_link' => $this->affiliate_link_generator->get_partner_affiliate_link($partner)->get_value(),
            'amount_sum' => $this->amount_in_fractions_to_float($partner->get_stats()->get_commissions_sum_in_fractions()),
            'unsettled_amount_sum' => $this->amount_in_fractions_to_float($partner->get_stats()->get_unsettled_commissions_sum_in_fractions()),
            'sale_amount_sum' => $this->amount_in_fractions_to_float($partner->get_stats()->get_sales_sum_in_fractions()),
            'status' => $partner->is_active() ? 'active' : 'inactive',
            'currency' => $this->get_currency(),
            'status_label' => $partner->is_active() ?
                $this->translator->translate('partners.status.active') :
                $this->translator->translate('partners.status.inactive'),
            'edit_url' => $edit_url
        ];
    }
}
