<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\discount_codes;

use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\admin\tables\dynamic\data\Dynamic_Table_Data_Usage_Context;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\sales\discount_codes\core\repositories\Interface_Discount_Repository;
use bpmj\wpidea\sales\discount_codes\core\repositories\Discount_Query_Criteria;
use bpmj\wpidea\sales\discount_codes\core\collections\Discount_Collection;
use bpmj\wpidea\sales\discount_codes\core\entities\Discount;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Amount;
use DateTimeImmutable;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Uses;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Status;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\controllers\admin\Admin_Discounts_Ajax_Controller;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\infrastructure\system\System;
use bpmj\wpidea\sales\discount_codes\core\value_objects\Max_Uses;

class Discounts_Table_Data_Provider implements Interface_Dynamic_Table_Data_Provider
{
    private const EMPTY_VALUE_PLACEHOLDER = '-';
    private Interface_Discount_Repository $repository;
    private Interface_Url_Generator $url_generator;
    private Interface_Translator $translator;
    private System $system;

    private string $system_currency;

    public function __construct(
        Interface_Discount_Repository $repository,
        Interface_Url_Generator $url_generator,
        Interface_Translator $translator,
        System $system
    ) {
        $this->repository = $repository;
        $this->url_generator = $url_generator;
        $this->translator = $translator;
        $this->system = $system;
    }

    public function get_rows(
        array $filters,
        Sort_By_Clause $sort_by,
        int $per_page,
        int $page,
        Dynamic_Table_Data_Usage_Context $context
    ): array {
        $sort_by->remove('created_at');

        $entities = $this->repository->find_by_criteria(
            $this->get_criteria_from_filters($filters),
            $per_page,
            $page,
            $sort_by
        );

        return $this->parse_entities_collection_to_array($entities, $context);
    }

    public function get_total(array $filters): int
    {
        return $this->repository->count_by_criteria(
            $this->get_criteria_from_filters($filters)
        );
    }

    private function get_criteria_from_filters(array $filters): Discount_Query_Criteria
    {
        $criteria = Discount_Query_Criteria::create();

        $criteria->set_name_contains($this->get_filter_value_if_present($filters, 'name'));
        $criteria->set_code_contains($this->get_filter_value_if_present($filters, 'code'));
        $criteria->set_status_equals($this->get_filter_value_if_present($filters, 'status'));

        return $criteria;
    }

    private function parse_entities_collection_to_array(
        Discount_Collection $entities,
        Dynamic_Table_Data_Usage_Context $context
    ): array {
        $is_export_context = $context->equals(
            Dynamic_Table_Data_Usage_Context::from_value(Dynamic_Table_Data_Usage_Context::EXPORT_DATA)
        );

        return array_map(fn(Discount $entity) => [
            'id' => $entity->get_id()->to_int(),
            'code' => $this->get_code($entity->get_code()->get_value()),
            'name' => $entity->get_name()->get_value(),
            'amount' => $this->format_amount($entity->get_amount()),
            'uses' => $this->format_uses($entity->get_uses(), $entity->get_max_uses()),
            'start' => $this->format_date($entity->get_time_limit()->get_start_date()),
            'expiration' => $this->format_date($entity->get_time_limit()->get_end_date()),
            'status' => $entity->get_status()->get_value(),
            'status_label' => $this->get_status_label($entity->get_status()),
            'edit_url' => $is_export_context ? null : $this->get_edit_url($entity),
            'delete_url' => $is_export_context ? null : $this->get_delete_url($entity)
        ], iterator_to_array($entities));
    }

    private function get_code(string $code): string
    {
        if(empty($code)){
            return 'n/a';
        }

        return $code;
    }

    private function format_amount(Amount $amount): string
    {
        if ($amount->get_type() === Amount::TYPE_PERCENTAGE) {
            return $amount->get_amount() . '%';
        }

        if (!isset($this->system_currency)) {
            $this->system_currency = $this->system->get_system_currency();
        }

        return $amount->get_amount() . ' ' . $this->system_currency;
    }

    private function format_uses(Uses $uses, ?Max_Uses $max_uses): string
    {
        $max_uses_string = $max_uses ? $max_uses->get() : self::EMPTY_VALUE_PLACEHOLDER;

        return $uses->get() . '/' . $max_uses_string;
    }

    private function format_date(?DateTimeImmutable $date): string
    {
        return $date ? $date->format('Y-m-d H:i:s') : self::EMPTY_VALUE_PLACEHOLDER;
    }

    private function get_edit_url(Discount $entity): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => 'wp-idea-discounts',
            'wp-idea-action' => 'edit_discount',
            'discount' => $entity->get_id()->to_int()
        ]);
    }

    private function get_status_label(Status $status): string
    {
        return $this->translator->translate('discount_codes.status.' . $status->get_value());
    }

    /**
     * @return mixed|null
     */
    private function get_filter_value_if_present(array $filters, string $filter_name)
    {
        return array_values(
                array_filter($filters, static function ($filter, $key) use ($filter_name) {
                    return $filter['id'] === $filter_name;
                },           ARRAY_FILTER_USE_BOTH)
            )[0]['value'] ?? null;
    }

    private function get_delete_url(Discount $entity): string
    {
        return $this->url_generator->generate(Admin_Discounts_Ajax_Controller::class, 'delete', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $entity->get_id()->to_int()
        ]);
    }
}