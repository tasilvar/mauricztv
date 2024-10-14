<?php
declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\certificates;

use bpmj\wpidea\admin\tables\dynamic\data\Dynamic_Table_Data_Usage_Context;
use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\caps\Access_Filter_Name;
use bpmj\wpidea\certificates\Certificate_Display_Model_Collection;
use bpmj\wpidea\certificates\Certificate_Display_Service;
use bpmj\wpidea\certificates\Certificate_Query_Criteria;
use bpmj\wpidea\certificates\Interface_Certificate_Repository;
use bpmj\wpidea\controllers\admin\Admin_Certificates_Ajax_Controller;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\nonce\Nonce_Handler;
use bpmj\wpidea\routing\Interface_Url_Generator;

class Certificates_Table_Data_Provider implements Interface_Dynamic_Table_Data_Provider
{
    private const CERTIFICATE_NONCE = 'certificate_nonce';

    private Interface_Certificate_Repository $certificate_repository;
    private Certificate_Display_Service $certificate_service;
    private Interface_Url_Generator $url_generator;
    private Interface_Filters $filters;

    public function __construct(
        Interface_Certificate_Repository $certificate_repository,
        Certificate_Display_Service $certificate_service,
        Interface_Url_Generator $url_generator,
        Interface_Filters $filters
    ) {
        $this->certificate_repository = $certificate_repository;
        $this->certificate_service = $certificate_service;
        $this->url_generator = $url_generator;
        $this->filters = $filters;
    }

    public function get_rows(
        array $filters,
        Sort_By_Clause $sort_by,
        int $per_page,
        int $page,
        Dynamic_Table_Data_Usage_Context $context
    ): array {
        $criteria = $this->get_criteria_from_filters_and_sort_by($filters, $sort_by);
        $certificates = $this->certificate_repository->find_by_criteria($criteria, $page, $per_page);

        return $this->get_prepared_rows(
            $this->certificate_service->get_display_model_collection($certificates)
        );
    }

    private function get_criteria_from_filters_and_sort_by(
        array $filters,
        Sort_By_Clause $sort_by_clause
    ): Certificate_Query_Criteria {
        $criteria = new Certificate_Query_Criteria();

        $criteria->set_name_query($this->get_filter_value($filters, 'full_name'));
        $criteria->set_email_query($this->get_filter_value($filters, 'email'));

        if ($this->get_filter_value($filters, 'certificate_number')) {
            $criteria->set_certificate_number_query($this->get_filter_value($filters, 'certificate_number'));
        }

        if ($this->get_filter_value($filters, 'course')) {
            $criteria->set_course_id(new Course_ID((int) $this->get_filter_value($filters, 'course')));
        }

        $sort_by = $sort_by_clause->get_first();
        if ($sort_by !== null) {
            $criteria->set_sort_by_column($sort_by->property);
            $criteria->set_sort_direction_ascending(!$sort_by->desc);
        }

        return $criteria;
    }

    private function get_filter_value(array $filters, string $filter_name)
    {
        foreach ($filters as $filter) {
            if ($filter['id'] === $filter_name) {
                return $filter['value'];
            }
        }

        return null;
    }

    private function get_prepared_rows(Certificate_Display_Model_Collection $models): array
    {
        $rows = [];
        foreach ($models as $model) {
            $nonce = wp_create_nonce(self::CERTIFICATE_NONCE);
            $download_url = $this->url_generator->generate_admin_page_url(
                "admin.php?page=wp-idea-generate-certificate-template&id={$model->get_id()}&certificate-nonce={$nonce}");
            $regenerate_url = $this->url_generator->generate(
                Admin_Certificates_Ajax_Controller::class,
                'regenerate', [
                Nonce_Handler::DEFAULT_REQUEST_VARIABLE_NAME => Nonce_Handler::create(),
                'id' => $model->get_id()
            ]);

            $rows[] = [
                'id' => $model->get_id(),
                'course' => $model->get_course_name(),
                'full_name' => $this->filters->apply(Access_Filter_Name::CUSTOMER_NAME, $model->get_user_full_name()),
                'email' => $this->filters->apply(Access_Filter_Name::CUSTOMER_EMAIL, $model->get_user_email()),
                'certificate_number' => $model->get_certificate_number(),
                'created' => $model->get_created_at(),
                'regenerate_url' => $regenerate_url,
                'download_url' => $download_url
            ];
        }

        return $rows;
    }

    public function get_total(array $filters): int
    {
        $criteria = $this->get_criteria_from_filters_and_sort_by($filters, new Sort_By_Clause());

        return $this->certificate_repository->count_by_criteria($criteria);
    }
}