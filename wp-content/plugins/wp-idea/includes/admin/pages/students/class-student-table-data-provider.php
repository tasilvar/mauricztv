<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\students;

use bpmj\wpidea\admin\tables\dynamic\data\Dynamic_Table_Data_Usage_Context;
use bpmj\wpidea\admin\tables\dynamic\data\Interface_Dynamic_Table_Data_Provider;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;
use bpmj\wpidea\students\persistence\Student_Query_Criteria;
use bpmj\wpidea\students\repository\Interface_Student_Repository;
use bpmj\wpidea\learning\course\Course_Collection;
use bpmj\wpidea\routing\Interface_Url_Generator;

class Student_Table_Data_Provider implements Interface_Dynamic_Table_Data_Provider
{
    private Interface_Student_Repository $student_repository;
    private Interface_Readable_Course_Repository $course_repository;
    private Student_Presenter_Filter $student_filter;
    private Interface_Url_Generator $url_generator;

    public function __construct(
        Interface_Readable_Course_Repository $course_repository,
        Student_Presenter_Filter $student_filter,
        Interface_Student_Repository $student_repository,
        Interface_Url_Generator $url_generator
    ) {
        $this->course_repository = $course_repository;
        $this->student_filter = $student_filter;
        $this->student_repository = $student_repository;
        $this->url_generator = $url_generator;
    }

    public function get_rows(array $filters, Sort_By_Clause $sort_by, int $per_page, int $page, Dynamic_Table_Data_Usage_Context $context): array
    {
        $rows = [];

        foreach ($this->student_repository->find_by_criteria($this->get_criteria_from_query_filters($filters), $page, $per_page, $sort_by) as $student) {
            $row = $this->student_filter->filtered_array($student->to_array());

            $row['courses'] = $this->get_courses_string(
                $this->course_repository->find_by_user_id($student->get_id()->to_int())
            );

            if ($context->get_value() === Dynamic_Table_Data_Usage_Context::DISPLAY_DATA) {
                $row['edit_url'] = $this->url_generator->generate_admin_page_url('user-edit.php', [
                    'user_id' => $student->get_id()->to_int()
                ]);
            }

            $rows[] = $row;
        }

        return $rows;
    }

    private function get_courses_string(Course_Collection $collection): string
    {
        $titles = array_map(function ($course) {
            return $course['title'];
        }, $collection->to_array());

        return implode(', ', $titles);
    }

    public function get_total(array $filters): int
    {
        $criteria = $this->get_criteria_from_query_filters($filters);

        return $this->student_repository->count_by_criteria($criteria);
    }

    private function get_criteria_from_query_filters(array $filters): Student_Query_Criteria
    {
        return (new Student_Query_Criteria())->get_from_query_filters($filters);
    }
}