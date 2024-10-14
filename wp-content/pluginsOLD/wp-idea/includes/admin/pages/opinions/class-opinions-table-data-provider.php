<?php

namespace bpmj\wpidea\admin\pages\opinions;

use bpmj\wpidea\admin\tables\dynamic\data\Dynamic_Table_Data_Usage_Context;
use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\controllers\admin\Admin_Opinions_Controller;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\modules\opinions\core\entities\Opinion;
use bpmj\wpidea\modules\opinions\core\repositories\Interface_Opinion_Repository;
use bpmj\wpidea\modules\opinions\core\value_objects\Opinion_Status;
use bpmj\wpidea\modules\opinions\infrastructure\persistence\Opinions_Query_Criteria;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;
use bpmj\wpidea\translator\Interface_Translator;

class Opinions_Table_Data_Provider implements Interface_Dynamic_Table_Data_Provider
{
    private Interface_Opinion_Repository $opinion_repository;
    private Interface_Translator $translator;
    private Interface_Url_Generator $url_generator;

    public function __construct(
        Interface_Opinion_Repository $opinion_repository,
        Interface_Translator $translator,
        Interface_Url_Generator $url_generator
    )
    {
        $this->opinion_repository = $opinion_repository;
        $this->translator = $translator;
        $this->url_generator = $url_generator;
    }

    public function get_rows(array $filters, Sort_By_Clause $sort_by, int $per_page, int $page, Dynamic_Table_Data_Usage_Context $context): array
    {
	    $criteria = $this->get_criteria_from_filters($filters);
        $models = $this->opinion_repository->find_by_criteria(
            $criteria,
            $per_page,
            $page,
            $sort_by
        );
        return $this->models_to_rows($models->to_array());
    }

    public function get_total(array $filters): int
    {
        return $this->opinion_repository->count_by_criteria($this->get_criteria_from_filters($filters));
    }

    private function models_to_rows(array $models): array
    {
        $rows = [];

        foreach ($models as $model) {
            /* @var Opinion $model */

            $rows[] = [
                'product_name' => $model->get_product_name(),
                'user_name' => $model->get_user_full_name(),
                'user_email' => $model->get_user_email(),
                'opinion_rating' => $model->get_rating()->get_value() . '⭐',
                'opinion_content' => $model->get_opinion_content()->get_value(),
                'date_of_issue' => $model->get_date_of_issue()->format('Y-m-d H:i:s'),
                'status' => $model->get_status()->get_value(),
                'status_label' => $this->translator->translate("opinions.status.{$model->get_status()->get_value()}"),
                'accept_opinion' => $this->get_accept_opinion_url($model),
                'discard_opinion' => $this->get_discard_opinion_url($model),
            ];
        }

        return $rows;
    }

	private function get_criteria_from_filters(array $filters): Opinions_Query_Criteria
	{
		$criteria = new Opinions_Query_Criteria();

		$criteria->set_product_id_in($this->get_filter_value_if_present($filters, 'product_name'));
		$criteria->set_user_full_name_like($this->get_filter_value_if_present($filters, 'user_name'));
		$criteria->set_user_email_like($this->get_filter_value_if_present($filters, 'user_email'));
		$criteria->set_opinion_rating_in($this->get_filter_value_if_present($filters, 'opinion_rating'));
		$criteria->set_opinion_content_like($this->get_filter_value_if_present($filters, 'opinion_content'));
		$criteria->set_statuses($this->get_filter_value_if_present($filters, 'status'));
		$criteria->set_date_of_issue_from($this->get_filter_value_if_present($filters, 'date_of_issue')['startDate'] ?? null);
		$criteria->set_date_of_issue_to($this->get_filter_value_if_present($filters, 'date_of_issue')['endDate'] ?? null);

		return $criteria;
	}

	private function get_filter_value_if_present(array $filters, string $filter_name)
	{
		return array_values(
			array_filter($filters, static function ($filter, $key) use ($filter_name) {
			   return $filter['id'] === $filter_name;
			}, ARRAY_FILTER_USE_BOTH)
        )[0]['value'] ?? null;
	}

    private function get_accept_opinion_url(Opinion $opinion): string
    {
        return $this->url_generator->generate(Admin_Opinions_Controller::class, 'change_status', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $opinion->get_id()->to_int(),
            'new_status' => Opinion_Status::ACCEPTED,
        ]);
    }

    private function get_discard_opinion_url(Opinion $opinion): string
    {
        return $this->url_generator->generate(Admin_Opinions_Controller::class, 'change_status', [
            Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
            'id' => $opinion->get_id()->to_int(),
            'new_status' => Opinion_Status::DISCARDED,
        ]);
    }
}