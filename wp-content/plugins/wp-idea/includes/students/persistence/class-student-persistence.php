<?php

namespace bpmj\wpidea\students\persistence;

use bpmj\wpidea\infrastructure\database\Interface_Database;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\students\vo\Student_ID;
use WP_User_Query;

class Student_Persistence implements Interface_Student_Persistence
{
    private const ACCESS_TO_DOWNLOAD = '_bpmj_eddpc_access_to_download';

    private Interface_Readable_Course_Repository $course_repository;

    public function __construct(
        Interface_Readable_Course_Repository $course_repository
    ) {
        $this->course_repository = $course_repository;
    }

    public function get_students_with_access_to_course(Product_ID $product_id,
                                                       Sort_By_Clause $sort_by,
                                                       int $per_page,
                                                       int $page): array
    {
        $sort_by_access_to_desc = $sort_by->get('access_to')->desc ?? false;

        $product_id = $product_id->to_int();

        $query = new WP_User_Query([
            'number' => $per_page,
            'offset' => $per_page * ($page - 1),
            'meta_query' => [
                [
                    'key' => '_bpmj_eddpc_access_to_download',
                    'value' => $product_id
                ],
            ],
            'meta_key' => '_bpmj_eddpc_' . $product_id . '_access_time',
            'orderby' => 'meta_value_num',
            'order' => $sort_by_access_to_desc ? 'DESC' : 'ASC'
        ]);

        return $query->get_results();
    }

    public function find_by_criteria(
        Student_Query_Criteria $criteria,
        int $page,
        int $per_page,
        ?Sort_By_Clause $sort_by = null
    ): array
    {
        $query_args = $this->get_query_args_with_criteria($per_page, $page, $sort_by, $criteria);
        return (new WP_User_Query($query_args))->get_results();
    }

    private function get_query_args_with_criteria(
        int $per_page,
        int $page,
        ?Sort_By_Clause $sort_by,
        Student_Query_Criteria $criteria,
        array $args = []
    ): array {
        $default_args = [
            'number' => $per_page,
            'paged' => $page,
            'order_by' => [],
            'exclude' => $this->get_non_student_user_ids(),
        ];

        $args = array_merge($default_args, $args);

        $this->apply_email_criteria($args, $criteria);
        $this->apply_login_criteria($args, $criteria);
        $this->apply_name_criteria($args, $criteria);
        $this->apply_course_criteria($args, $criteria);
        $this->apply_sort_by($args, $sort_by);

        return wp_parse_args($args);
    }

    public function get_all(){
        $args = [
            'count_total' => true,
            'order_by' => [],
            'orderby' => ['ID' => 'desc'],
        ];

        $args['exclude'] = $this->get_non_student_user_ids();

        $query = new WP_User_Query($args);

        return $query->get_results();
    }

    private function get_non_student_user_ids(): array
    {
        global $wpdb;

        $key = self::ACCESS_TO_DOWNLOAD;
        $query = "SELECT ID FROM {$wpdb->users} WHERE ID NOT IN (SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key='{$key}')";

        return array_map(function ($row) {
            return (int)$row['ID'];
        }, $wpdb->get_results($query, ARRAY_A));
    }

    private function apply_email_criteria(array &$args, Student_Query_Criteria $criteria): void
    {
        if ($criteria->get_email()) {
            $args['search_columns'] = [
                'user_email'
            ];
            $args['search'] = '*' . $criteria->get_email() . '*';
        }
    }

    private function apply_login_criteria(array &$args, Student_Query_Criteria $criteria): void
    {
        if ($criteria->get_login()) {
            $args['search_columns'] = [
                'user_login'
            ];
            $args['search'] = '*' . $criteria->get_login() . '*';
        }
    }

    private function apply_course_criteria(array &$args, Student_Query_Criteria $criteria): void
    {
        if (!$criteria->get_courses_ids()) {
            return;
        }

        $product_ids = [];

        foreach ($criteria->get_courses_ids() as $course_id) {
            $course = $this->course_repository->find_by_id(new Course_ID($course_id));
            $product_ids[] = $course->get_product_id()->to_int();
        }

        if (empty($product_ids)) {
            return;
        }

        $meta_conditions[] = [
            'key' => self::ACCESS_TO_DOWNLOAD,
            'value' => $product_ids,
            'compare' => 'IN'
        ];
        $args['meta_query'] = $meta_conditions;
    }

    private function apply_name_criteria(array &$args, Student_Query_Criteria $criteria): void
    {
        if ($criteria->get_name()) {
            $args['meta_query'] = [
                'relation' => 'OR',
            ];

            foreach (explode(' ', $criteria->get_name()) as $value) {
                $args['meta_query']['first_name_clause'] = [
                    'key' => 'first_name',
                    'value' => $value,
                    'operator' => 'LIKE',
                    'compare' => 'LIKE',
                    'compare_key' => 'LIKE'
                ];
                $args['meta_query']['last_name_clause'] = [
                    'key' => 'last_name',
                    'value' => $value,
                    'operator' => 'LIKE',
                    'compare' => 'LIKE',
                    'compare_key' => 'LIKE'
                ];
            }
        } else {
            // this is needed for sorting
            $args['meta_query']['first_name_clause'] = [
                'key' => 'first_name',
            ];
            $args['meta_query']['last_name_clause'] = [
                'key' => 'last_name',
            ];
        }
    }

    private function apply_sort_by(array &$args, ?Sort_By_Clause $sort_by): void
    {
        if (!$sort_by) {
            return;
        }

        foreach ($sort_by->get_all() as $sort) {
            $order_by = $sort->desc ? "DESC" : "ASC";

            switch ($sort->property) {
                case 'id':
                    $args['orderby']['ID'] = $order_by;
                    break;
                case 'login':
                    $args['orderby']['login'] = $order_by;
                    break;
                case 'full_name':
                    $args['orderby']['first_name_clause'] = $order_by;
                    $args['orderby']['last_name_clause'] = $order_by;
                    break;
                case 'email':
                    $args['orderby']['user_email'] = $order_by;
                    break;
                default:
                    break;
            }
        }
    }

    public function get_student_access_time_to_course(Student_ID $student_id, Product_ID $product_id): ?int
    {
        $access_time_value = get_user_meta(
            $student_id->to_int(),
            '_bpmj_eddpc_' . $product_id->to_int() . '_access_time',
            true
        );

        return $access_time_value ?: null;
    }

    public function count_by_criteria(Student_Query_Criteria $criteria): int
    {
        $args = $this->get_query_args_with_criteria(-1, 1, new Sort_By_Clause(), $criteria, [
            'fields' => 'ids'
        ]);

        return (new WP_User_Query($args))->get_total();
    }
}