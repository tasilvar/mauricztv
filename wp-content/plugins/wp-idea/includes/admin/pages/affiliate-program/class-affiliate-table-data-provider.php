<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\affiliate_program;

use bpmj\wpidea\admin\tables\dynamic\data\Dynamic_Table_Data_Usage_Context;
use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\caps\Access_Filter_Name;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\helpers\Price_Formatting;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\infrastructure\system\System;
use bpmj\wpidea\modules\affiliate_program\api\controllers\Admin_Affiliate_Ajax_Controller;
use bpmj\wpidea\modules\affiliate_program\core\entities\Commission;
use bpmj\wpidea\modules\affiliate_program\core\entities\Commission_Collection;
use bpmj\wpidea\modules\affiliate_program\core\repositories\Interface_Commission_Repository;
use bpmj\wpidea\modules\affiliate_program\infrastructure\persistence\Commission_Query_Criteria;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_User_Repository;

class Affiliate_Table_Data_Provider implements Interface_Dynamic_Table_Data_Provider
{
    private const PERCENT = '%';

    private Interface_Url_Generator $url_generator;
    private Interface_Translator $translator;
    private System $system;
    private Interface_Filters $filters;
    private Interface_Commission_Repository $commission_repository;
    private Interface_Product_Repository $product_repository;
    private Interface_User_Repository $user_repository;

    private string $system_currency;

    public function __construct(
        Interface_Url_Generator $url_generator,
        Interface_Translator $translator,
        System $system,
        Interface_Filters $filters,
        Interface_Commission_Repository $commission_repository,
        Interface_Product_Repository $product_repository,
        Interface_User_Repository $user_repository
    ) {
        $this->url_generator = $url_generator;
        $this->translator = $translator;
        $this->system = $system;
        $this->filters = $filters;
        $this->commission_repository = $commission_repository;
        $this->product_repository = $product_repository;
        $this->user_repository = $user_repository;
    }

    public function get_rows(
        array $filters,
        Sort_By_Clause $sort_by,
        int $per_page,
        int $page,
        Dynamic_Table_Data_Usage_Context $context
    ): array {

        $entities = $this->commission_repository->find_by_criteria(
            $this->get_criteria_from_filters($filters),
            $per_page,
            $page,
            $sort_by
        );

        return $this->parse_entities_collection_to_array($entities);
    }

    public function get_total(array $filters): int
    {
        return $this->commission_repository->count_by_criteria(
            $this->get_criteria_from_filters($filters)
        );
    }

    private function get_status_label(string $status): string
    {
        return $this->translator->translate('affiliate_program.status.' . $status);
    }

    private function get_criteria_from_filters(array $filters): Commission_Query_Criteria
    {
        $criteria = new Commission_Query_Criteria();

        $criteria->set_partner_id($this->get_filter_value_if_present($filters, 'partner_id'));
        $criteria->set_partner_email($this->get_filter_value_if_present($filters, 'partner_email'));
        $criteria->set_name($this->get_filter_value_if_present($filters, 'name'));
        $criteria->set_email($this->get_filter_value_if_present($filters, 'email'));
        $criteria->set_products($this->get_filter_value_if_present($filters, 'products'));
        $criteria->set_sale_amount($this->get_filter_value_if_present($filters, 'sale_amount'));
        $criteria->set_commission_percentage($this->get_filter_value_if_present($filters, 'percentage'));
        $criteria->set_commission_amount($this->get_filter_value_if_present($filters, 'amount'));
        $criteria->set_date($this->get_filter_value_if_present($filters, 'created_at'));
        $criteria->set_status($this->get_filter_value_if_present($filters, 'status'));

        return $criteria;
    }

    private function parse_entities_collection_to_array(
        Commission_Collection $entities
    ): array {

        return array_map(fn(Commission $entity) => [
            'id' => $entity->get_id()->to_int(),
            'partner_id' => $entity->get_partner_id()->to_int(),
            'partner_affiliate_id' => $entity->get_partner_affiliate_id(),
            'partner_email' => $this->filters->apply(Access_Filter_Name::PARTNER_EMAIL, $entity->get_partner_email(),
                $entity->get_id()->to_int()),
            'name' => $this->filters->apply(Access_Filter_Name::PARTNER_NAME, $entity->get_client_name(),
                $entity->get_id()->to_int()),
            'email' => $this->filters->apply(Access_Filter_Name::PARTNER_EMAIL, $entity->get_client_email(),
                $entity->get_id()->to_int()),
            'created_at' => $entity->get_date()->format('Y-m-d H:i:s'),
            'products' => $this->get_product_name_from_ids($entity->get_purchased_product_ids()),
            'sale_amount' => $this->amount_in_fractions_to_float($entity->get_sale_amount_in_fractions()),
            'currency' => $this->get_currency(),
            'percentage' => $entity->get_commission_percentage(),
            'percent' => self::PERCENT,
            'amount' => $this->amount_in_fractions_to_float($entity->get_commission_amount_in_fractions()),
            'status' => $entity->get_status()->get_value(),
            'status_label' => $this->get_status_label($entity->get_status()->get_value()),
            'status_url' => $this->get_change_status_url($entity->get_id()->to_int(),
                $entity->get_status()->get_value()),
            'delete_url' => $this->get_delete_partner_url($entity->get_id()->to_int()),
            'partner_profile_url' => $this->get_partner_profile_url($entity->get_partner_email())
        ], iterator_to_array($entities));

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

    private function get_product_name_from_ids(array $product_ids): string
    {
        $product_names = [];

        foreach ($product_ids as $product_id) {
            $product = $this->product_repository->find(new Product_ID((int)$product_id));
            if(!is_null( $product )) {
                $product_names[] = $product->get_name()->get_value();
            }
            else {
                $product_names[] = 'n/a';
            }
        }

        return implode(', ', $product_names);
    }

    private function get_partner_profile_url(string $email): string
    {
        $user = $this->user_repository->find_by_email($email);

        if (!$user) {
            return "javascript:alert('".$this->translator->translate("alert.account_deleted")."');";
        }

        return $this->url_generator->generate_admin_page_url('user-edit.php', [
            'user_id' => $user->get_id()->to_int()
        ]);

    }

    private function get_change_status_url(int $partner_id, string $status): string
    {
        return $this->url_generator->generate(Admin_Affiliate_Ajax_Controller::class, 'change_status', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $partner_id,
            'status' => $status
        ]);
    }

    private function get_delete_partner_url(int $partner_id): string
    {
        return $this->url_generator->generate(Admin_Affiliate_Ajax_Controller::class, 'delete', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $partner_id
        ]);

    }

    private function get_currency(): string
    {
        if (!isset($this->system_currency)) {
            $this->system_currency = $this->system->get_system_currency();
        }

        return $this->system_currency;
    }
}