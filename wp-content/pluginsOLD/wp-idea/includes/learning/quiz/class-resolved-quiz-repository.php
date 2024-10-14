<?php
declare(strict_types=1);

namespace bpmj\wpidea\learning\quiz;

use bpmj\wpidea\data_types\personal_data\Full_Name;
use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use WP_Query;

class Resolved_Quiz_Repository implements Interface_Resolved_Quiz_Repository
{
    private const QUESTIONS = 'questions';
    private const TIME_IS_UP = 'time_is_up';

    public function find_by_criteria(Resolved_Quiz_Query_Criteria $criteria, int $per_page = 0, int $page = 1, ?Sort_By_Clause $sort_by = null): array
    {
        return $this->get_quizzes($criteria, $per_page, $page, $sort_by);
    }

    public function count_by_criteria(Resolved_Quiz_Query_Criteria $criteria): int
    {
        return $this->query_quizzes_by_criteria($criteria, -1, 1)->found_posts;
    }

    public function count_not_rated(): int
    {
        global $wpdb;
        
        $table_name_posts = $wpdb->prefix . 'posts';
        $table_name_postmeta = $wpdb->prefix . 'postmeta';

        $count = $wpdb->get_var( "SELECT COUNT(p.ID)
            FROM $table_name_posts p
            WHERE p.post_type = 'tests' AND p.post_status = 'publish' AND
                (ID not in (select post_id from $table_name_postmeta where meta_key = 'is_passed') OR
                ID in (select post_id from $table_name_postmeta where meta_key = 'is_passed' and meta_value = ''))" );
        return absint($count);
    }
    
    public function find_by_id(int $id_quiz): ?Resolved_Quiz
    {
        $ids = [$id_quiz];
        $meta_for_all_quizzes = $this->fetch_quizzes_meta($ids);
        $parsed_quizzes_data  = $this->parsed_quizzes_data($meta_for_all_quizzes, $ids);

        $array_with_quizzes_objects = $this->create_object_from_quizzes_data($ids, $parsed_quizzes_data);

        return $array_with_quizzes_objects[0] ?? null;
    }

    private function get_quizzes(Resolved_Quiz_Query_Criteria $criteria, int $per_page, int $page, ?Sort_By_Clause $sort_by): array
    {
        $ids = $this->find_filtered_quiz_ids($criteria, $per_page, $page, $sort_by);

        if(empty($ids)) {
            return [];
        }

        $meta_for_all_quizzes = $this->fetch_quizzes_meta($ids);
        $parsed_quizzes_data  = $this->parsed_quizzes_data($meta_for_all_quizzes, $ids);

        return $this->create_object_from_quizzes_data($ids, $parsed_quizzes_data);
    }

    private function parsed_quizzes_data(array $meta_for_all_quizzes, array $ids): array
    {
        $parsed_quizzes_data = [];

        foreach ($meta_for_all_quizzes as $i => $item) {
            $parsed_quizzes_data[(int)$item->post_id]['id'] = (int)$item->post_id;
            if(isset($item->meta_key)) {
                $parsed_quizzes_data[(int)$item->post_id][$item->meta_key] = $item->meta_value;
            }
        }

        $course_ids_for_all_quizzes = $this->fetch_course_ids($parsed_quizzes_data);
        foreach ($course_ids_for_all_quizzes as $i => $course_meta) {
            $filtered_data = array_filter($parsed_quizzes_data, function ($value, $key) use ($course_meta) {
                return (int)$value['quiz_id'] === (int)$course_meta->post_id;
            }, ARRAY_FILTER_USE_BOTH);

            foreach ($filtered_data as $key => $data) {
                $parsed_quizzes_data[$key]['course_id'] = $course_meta->meta_value;
            }
        }

        $course_titles_for_all_quizzes = $this->fetch_course_data($course_ids_for_all_quizzes);
        foreach ($course_titles_for_all_quizzes as $i => $course_data) {
            $filtered_data = array_filter($parsed_quizzes_data, function ($value, $key) use ($course_data) {
                return (int)$value['course_id'] === (int)$course_data->ID;
            }, ARRAY_FILTER_USE_BOTH);

            foreach ($filtered_data as $key => $data) {
                $parsed_quizzes_data[$key]['course_title'] = $course_data->post_title;
            }
        }

        $basic_data_for_all_quizzes = $this->fetch_quizzes_basic_data($ids);
        foreach ($basic_data_for_all_quizzes as $i => $basic_data) {
            $parsed_quizzes_data[$basic_data->ID]['post_title'] = $basic_data->post_title;
            $parsed_quizzes_data[$basic_data->ID]['post_date_gmt'] = $basic_data->post_date_gmt;
            $parsed_quizzes_data[$basic_data->ID]['post_status'] = $basic_data->post_status;
        }

        return $parsed_quizzes_data;
    }

    private function find_filtered_quiz_ids(Resolved_Quiz_Query_Criteria $criteria, int $per_page, int $page, ?Sort_By_Clause $sort_by): array
    {
        $query = $this->query_quizzes_by_criteria($criteria, $per_page, $page, $sort_by);

        return $query->get_posts();
    }

    private function fetch_quizzes_basic_data(array $ids, ?int $limit = null): array
    {
        global $wpdb;

        $posts_table_name = $wpdb->prefix . 'posts';
        $ids_string = implode(',', $ids);
        $query_limit = $limit ? " LIMIT $limit" : '';

        return $wpdb->get_results( "SELECT ID, post_status, post_title, post_date_gmt
            FROM {$posts_table_name}       
            WHERE ID IN ($ids_string)
            $query_limit" );
    }

    private function fetch_quizzes_meta(array $ids, ?int $limit = null): array
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'postmeta';
        $ids_string = implode(',', $ids);

        $meta_keys_to_include = [
            "'is_passed'",
            "'user_email'",
            "'test_questions_points_all'",
            "'points'",
            "'quiz_id'",
            "'user_first_name'",
            "'user_last_name'",
            "'user_email'",
        ];

        $meta_keys_to_include_string = implode(',', $meta_keys_to_include);
        $calculated_limit = count($meta_keys_to_include) * ($limit ?? 0);
        $query_limit = $limit ? " LIMIT $calculated_limit" : '';

        return $wpdb->get_results( "SELECT post_id, meta_key, meta_value
            FROM $table_name       
            WHERE post_id IN ($ids_string) AND
            meta_key IN ({$meta_keys_to_include_string})
            ORDER BY meta_id ASC $query_limit" );
    }

    private function fetch_course_ids(array $parsed_quizzes_data, ?int $limit = null): array
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'postmeta';

        $quiz_page_ids = [];
        foreach ($parsed_quizzes_data as $data) {
            $quiz_page_ids[] = $data['quiz_id'];
        }

        $ids_string = implode(',', $quiz_page_ids);

        $meta_keys_to_include = [
            "'_bpmj_eddcm'",
        ];

        $meta_keys_to_include_string = implode(',', $meta_keys_to_include);
        $calculated_limit = count($meta_keys_to_include) * ($limit ?? 0);
        $query_limit = $limit ? " LIMIT $calculated_limit" : '';

        return $wpdb->get_results( "SELECT post_id, meta_key, meta_value
            FROM $table_name       
            WHERE post_id IN ($ids_string) AND
            meta_key IN ({$meta_keys_to_include_string})
            ORDER BY meta_id ASC $query_limit" );
    }

    private function fetch_course_data(array $course_ids_for_all_quizzes, ?int $limit = null): array
    {
        global $wpdb;

        $posts_table_name = $wpdb->prefix . 'posts';

        $ids = [];
        foreach ($course_ids_for_all_quizzes as $course_meta) {
            $ids[] = $course_meta->meta_value;
        }

        $ids_string = implode(',', $ids);

        $query_limit = $limit ? " LIMIT $limit" : '';

        return $wpdb->get_results( "SELECT ID, post_title
            FROM {$posts_table_name}       
            WHERE ID IN ($ids_string)
            $query_limit" );
    }

    private function create_object_from_quizzes_data(array $ids, array $quizzes_data): array
    {
        $tests = [];

        foreach ($ids as $id) {
            $data = $quizzes_data[$id];

            $is_passed_value = $data['is_passed'] ?? null;

            $user_full_name = new Full_Name(($data['user_first_name'] ?? ''), ($data['user_last_name'] ?? ''));

            $tests[] = new Resolved_Quiz(
                (int)$id,
                (int)$data['quiz_id'],
                (int)$data['course_id'],
                $data['post_title'],
                $data['course_title'] ?? '',
                new \DateTimeImmutable($data['post_date_gmt']),
                (int)$data['points'],
                (int)$data['test_questions_points_all'],
                $this->get_quiz_result_from_is_passed_value($is_passed_value),
                $user_full_name,
                $data['user_email']
            );
        }

        return $tests;
    }

    private function get_quiz_result_from_is_passed_value(?string $is_passed_value): string
    {
        switch ($is_passed_value) {
            case 'yes':
                return Resolved_Quiz::RESULT_PASSED;
            case 'no':
                return Resolved_Quiz::RESULT_FAILED;
            default:
                return Resolved_Quiz::RESULT_NOT_RATED_YET;
        }
    }

    private function query_quizzes_by_criteria(Resolved_Quiz_Query_Criteria $criteria, int $per_page, int $page, ?Sort_By_Clause $sort_by = null): WP_Query
    {
        $args = [
            'post_type' => 'tests',
            'post_status' => 'publish',
            'posts_per_page' => $per_page,
            'paged' => $page,
            'fields' => 'ids'
        ];

        $meta_query = [];

        $args = $this->add_order_by_to_args($args, $sort_by);

        if ($criteria->course) {
            $meta_query = $this->add_course_name_filter_to_meta_query($meta_query, $criteria->course);
        }

        if ($criteria->title) {
            $args['search_prod_title'] = $criteria->title;
        }

        if ($criteria->user_full_name) {
            $meta_query = $this->add_full_name_filter_to_meta_query($meta_query, $criteria->user_full_name);
        }

        if ($criteria->result) {
            $meta_query = $this->add_result_filter_to_meta_query($meta_query, $criteria->result);
        }

        if ($criteria->datetime_from || $criteria->datetime_to) {
            $args['date_query']['inclusive'] = true;
        }

        if ($criteria->datetime_from) {
            $args['date_query']['after'] = $criteria->datetime_from->format('Y-m-d H:i:s');
        }

        if ($criteria->datetime_to) {
            $args['date_query']['before'] = $criteria->datetime_to->format('Y-m-d H:i:s');
        }

        if($criteria->user_email) {
            $meta_query = $this->add_user_email_filter_to_meta_query($meta_query, $criteria->user_email);
        }

        if($criteria->quiz_id) {
            $meta_query = $this->add_quiz_id_filter_to_meta_query($meta_query, $criteria->quiz_id);
        }

        if (!empty($meta_query)) {
            $args['meta_query'] = $meta_query;
        }

        return new WP_Query($args);
    }

    private function add_order_by_to_args(array $args, ?Sort_By_Clause $sort_by): array
    {
        $order_by = !is_null($sort_by) ? $sort_by->get_first() : null;

        if(!$order_by) {
            return $args;
        }

        if(in_array($order_by->property, ['date', 'title'])) {
            $args['orderby'][$order_by->property] = ($order_by->desc ? 'DESC' : 'ASC');
        }

        if($order_by->property === 'id') {
            $args['orderby']['ID'] = ($order_by->desc ? 'DESC' : 'ASC');
        }

        if($order_by->property === 'result') {
            $args['orderby'] = 'meta_value meta_value_num';
            $args['order'] = ($order_by->desc ? 'DESC' : 'ASC');
            $args['meta_key'] = 'is_passed';
        }

        if(in_array($order_by->property, ['points', 'user_email'])) {
            $args['orderby'] = 'meta_value meta_value_num';
            $args['order'] = ($order_by->desc ? 'DESC' : 'ASC');
            $args['meta_key'] = $order_by->property;
        }

        return $args;
    }

    private function add_course_name_filter_to_meta_query(array $meta_query, string $course_name): array
    {
        $query = new WP_Query([
            'post_type' => 'page',
            'posts_per_page' => -1,
            'search_prod_title' => $course_name,
            'fields' => 'ids'
        ]);

        $meta_sub_query = [];

        $meta_sub_query['relation'] = 'OR';

        if ($query->post_count > 0) {
            foreach ($query->get_posts() as $post_id) {
                $meta_sub_query[] = [
                    'key' => 'course_id',
                    'value' => $post_id,
                ];
            }

            $meta_query[] = $meta_sub_query;
        } else {
            $meta_query[] = [
                'key' => 'course_id',
                'value' => 0,
            ];
        }

        return $meta_query;
    }

    private function add_full_name_filter_to_meta_query(array $meta_query, ?string $user_full_name): array
    {
        $name_array = explode(' ', $user_full_name);

        $relation   = (count($name_array) > 1) ? 'AND' : 'OR';

        $first_name = (count($name_array) > 1) ? $name_array[0] : $user_full_name;
        $last_name  = (count($name_array) > 1) ? $name_array[1] : $user_full_name;

        $meta_query[] = [
            'relation' => $relation,
            [
                'key' => 'user_first_name',
                'value' => $first_name,
                'compare' => 'LIKE'
            ],
            [
                'key' => 'user_last_name',
                'value' => $last_name,
                'compare' => 'LIKE'
            ]
        ];

        return $meta_query;
    }

    private function add_result_filter_to_meta_query(array $meta_query, ?string $result): array
    {
        if (Resolved_Quiz::RESULT_PASSED === $result) {
            $meta_query[] = [
                'key' => 'is_passed',
                'compare' => 'EXISTS'
            ];
            $meta_query[] = [
                'key' => 'is_passed',
                'value' => 'yes',
            ];
        }

        if (Resolved_Quiz::RESULT_FAILED === $result) {
            $meta_query[] = [
                [
                    'key' => 'is_passed',
                    'value' => 'no',
                ],
            ];
        }

        if (Resolved_Quiz::RESULT_NOT_RATED_YET === $result) {
            $meta_query[] = [
                'relation' => 'OR',
                [
                    'key' => 'is_passed',
                    'compare' => 'NOT EXISTS',
                ],
                [
                    'key' => 'is_passed',
                    'value' => '',
                ],
            ];
        }

        return $meta_query;
    }

    private function add_user_email_filter_to_meta_query(array $meta_query, ?string $user_email): array
    {
        $meta_query[] = [
            'key' => 'user_email',
            'value' => $user_email,
            'compare' => 'LIKE'
        ];

        return $meta_query;
    }

    private function add_quiz_id_filter_to_meta_query(array $meta_query, ?int $quiz_id): array
    {
        $meta_query[] = [
            'key' => 'quiz_id',
            'value' => $quiz_id
        ];

        return $meta_query;
    }

    public function save_configuration_of_questions(Quiz_ID $quiz_id, array $configuration_of_questions): void
    {
        update_post_meta($quiz_id->to_int(), self::QUESTIONS, $configuration_of_questions);
    }

    public function get_configuration_of_questions(Quiz_ID $quiz_id): array
    {
        $configuration_of_questions = get_post_meta($quiz_id->to_int(), self::QUESTIONS, true);
        return !empty($configuration_of_questions) ? $configuration_of_questions : [];
    }

    public function save_time_is_up(Quiz_ID $quiz_id): void
    {
        update_post_meta($quiz_id->to_int(), self::TIME_IS_UP, true);
    }

    public function get_time_is_up(Quiz_ID $quiz_id): bool
    {
        $time_is_up = get_post_meta($quiz_id->to_int(), self::TIME_IS_UP, true);
        return $time_is_up == '1';
    }
}