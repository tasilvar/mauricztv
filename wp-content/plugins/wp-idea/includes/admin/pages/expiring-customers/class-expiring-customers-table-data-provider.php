<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\expiring_customers;

use bpmj\wpidea\admin\pages\students\Student_Presenter_Filter;
use bpmj\wpidea\admin\tables\dynamic\data\Dynamic_Table_Data_Usage_Context;
use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\learning\course\Course_Collection;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\students\persistence\Interface_Student_Persistence;
use bpmj\wpidea\students\persistence\Student_Query_Criteria;
use bpmj\wpidea\students\repository\Interface_Student_Repository;
use bpmj\wpidea\students\vo\Student_ID;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\infrastructure\system\date\Interface_System_Datetime_Info;
use DateTimeImmutable;
use bpmj\wpidea\students\service\Student_Access_Time_To_Course_Provider;

class Expiring_Customers_Table_Data_Provider implements Interface_Dynamic_Table_Data_Provider
{
    private Student_Presenter_Filter $student_filter;
    private Interface_Product_Repository $product_repository;
    private Interface_Translator $translator;
    private Interface_System_Datetime_Info $datetime_info;
    private Interface_Student_Repository $student_repository;
    private Interface_Readable_Course_Repository $course_repository;
    private Student_Access_Time_To_Course_Provider $access_time_to_course_provider;

    public function __construct(
        Interface_Translator $translator,
        Interface_System_Datetime_Info $datetime_info,
        Interface_Student_Repository $student_repository,
        Interface_Readable_Course_Repository $course_repository,
        Interface_Product_Repository $product_repository,
        Student_Presenter_Filter $student_filter,
        Student_Access_Time_To_Course_Provider $access_time_to_course_provider
    ) {
        $this->translator = $translator;
        $this->datetime_info = $datetime_info;
        $this->student_repository = $student_repository;
        $this->course_repository = $course_repository;
        $this->product_repository = $product_repository;
        $this->student_filter = $student_filter;
        $this->access_time_to_course_provider = $access_time_to_course_provider;
    }

    public function get_rows(
        array $filters,
        Sort_By_Clause $sort_by,
        int $per_page,
        int $page,
        Dynamic_Table_Data_Usage_Context $context
    ): array {
        $data = [];
        foreach ($this->get_courses_with_access_time($filters) as $course) {
            $product_id = new Product_ID($course->get_product_id()->to_int());
            foreach ($this->student_repository->get_students_with_access_to_course($product_id, $sort_by, $per_page, $page) as $student) {
                $student_array = $student->to_array();
                $data[] = [
                    'name' => $this->student_filter->get_filtered_full_name($student_array),
                    'email' => $this->student_filter->get_filtered_email($student_array),
                    'access_to' => $this->get_student_access_to($student->get_id(), $product_id),
                    'course' => $course->get_title(),
                ];
            }
        }

        return $data;
    }

    private function get_courses_with_access_time(array $filters): Course_Collection
    {
        $course_collection = new Course_Collection();
        foreach ($this->get_courses($filters) as $course) {
            $product = $this->product_repository->find(new Product_ID($course->get_product_id()->to_int()));

            if(!$product) {
                continue;
            }

            if ($product->has_access_time()) {
                $course_collection->add($course);
            }
        }

        return $course_collection;
    }

    private function get_filter_value_if_present(array $filters, string $filter_name)
    {
        return array_values(
            array_filter($filters, static function ($filter, $key) use ($filter_name) {
                return $filter['id'] === $filter_name;
            }, ARRAY_FILTER_USE_BOTH)
        )[0]['value'] ?? null;
    }


    private function get_date_from_timestamp(int $access_time): string
    {
        $date = new DateTimeImmutable(
            date('Y-m-d H:i:s', $access_time)
        );

        $local_date = $date->setTimezone(
            $this->datetime_info->get_current_timezone()
        );

        return $local_date->format('Y-m-d H:i:s');
    }

    public function get_total(array $filters): int
    {
        $courses_ids = [];
        foreach ($this->get_courses_with_access_time($filters) as $course) {
            $courses_ids[] = $course->get_id()->to_int();
        }

        $criteria = new Student_Query_Criteria();
        $criteria->set_courses_ids($courses_ids);

        return $this->student_repository->count_by_criteria($criteria);
    }

    private function get_student_access_to(Student_ID $student_id, $product_id): string
    {
        $access_time = $this->access_time_to_course_provider->get_student_access_time_to_course($student_id, $product_id);

        return $access_time === null ? $this->translator->translate(
            'expiring_customers.access_to.unlimited'
        ) : $this->get_date_from_timestamp(
            $access_time
        );
    }

    private function get_courses(array $filters): Course_Collection
    {
        $course_id_filter_value = $this->get_filter_value_if_present($filters, 'course');

        if (is_null($course_id_filter_value)) {
            return $this->course_repository->find_all();
        }

        return (new Course_Collection())->add($this->course_repository->find_by_id(new Course_ID((int)$course_id_filter_value)));
    }
}