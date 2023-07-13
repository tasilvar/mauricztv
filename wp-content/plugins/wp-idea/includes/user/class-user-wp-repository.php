<?php

namespace bpmj\wpidea\user;

use bpmj\wpidea\infrastructure\database\Sort_By_Clause;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;
use WP_User;
use WP_User_Query;

class User_Wp_Repository implements Interface_User_Repository
{
    private const ACCESS_TO_DOWNLOAD = '_bpmj_eddpc_access_to_download';

    private Interface_Readable_Course_Repository $course_repository;

    public function __construct(
        Interface_Readable_Course_Repository $course_repository
    ) {
        $this->course_repository = $course_repository;
    }

    public function find_by_id(User_ID $id): ?Interface_User
    {
        $wp_user = get_userdata($id->to_int());

        if (!$wp_user) {
            return null;
        }

        return $this->create_user_from_wp_user($wp_user);
    }

    public function find_by_email(string $email): ?Interface_User
    {
        $wp_user = get_user_by('email', $email);

        if (!$wp_user) {
            return null;
        }

        return $this->create_user_from_wp_user($wp_user);
    }

    public function save(Interface_User $user): void
    {
        wp_update_user([
            'ID' => $user->get_id()->to_int(),
            'user_login' => $user->get_login()
        ]);

        update_user_meta($user->get_id()->to_int(), 'first_name', $user->get_first_name());
        update_user_meta($user->get_id()->to_int(), 'last_name', $user->get_last_name());
    }

    private function fill_model_from_wp_object(User $user, object $wp_user): User
    {
        $wp_user_meta = get_user_meta($wp_user->ID);

        $user
            ->set_login($wp_user->user_login)
            ->set_email($wp_user->user_email)
            ->set_first_name($wp_user_meta['first_name'][0])
            ->set_last_name($wp_user_meta['last_name'][0]);

        return $user;
    }

    private function create_user_from_wp_user(WP_User $wp_user): Interface_User
    {
        $user = new User(new User_ID($wp_user->ID));

        $this->fill_model_from_wp_object($user, $wp_user);

        return $user;
    }

    public function delete(Interface_User $user): void
    {
        wp_delete_user($user->get_id()->to_int());
    }

    public function find_by_criteria(
        User_Query_Criteria $criteria,
        int $page = 1,
        int $per_page = 25,
        ?Sort_By_Clause $sort_by = null
    ): User_Collection {
        $query = $this->get_wp_query_from_criteria($per_page, $page, $sort_by, $criteria);

        $users = $query->get_results();

        $collection = new User_Collection();

        foreach ($users as $user) {
            $user_model = new User(new User_ID($user->ID));
            $this->fill_model_from_wp_object($user_model, $user);
            $collection->add($user_model);
        }

        return $collection;
    }

    public function count_by_criteria(User_Query_Criteria $criteria): int
    {
        $query = $this->get_wp_query_from_criteria(-1, 1, new Sort_By_Clause(), $criteria, [
            'fields' => 'ids'
        ]);

        return $query->get_total();
    }

    private function apply_email_criteria(array &$args, User_Query_Criteria $criteria): void
    {
        if ($criteria->get_email()) {
            $args['search_columns'] = [
                'user_email'
            ];
            $args['search'] = '*' . $criteria->get_email() . '*';
        }
    }

    private function apply_login_criteria(array &$args, User_Query_Criteria $criteria): void
    {
        if ($criteria->get_login()) {
            $args['search_columns'] = [
                'user_login'
            ];
            $args['search'] = '*' . $criteria->get_login() . '*';
        }
    }

    private function apply_roles_criteria(array &$args, User_Query_Criteria $criteria): void
    {
        global $wpdb;

        if ($criteria->get_roles()) {
            $meta_conditions = [
                'relation' => 'OR'
            ];

            foreach ($criteria->get_roles() as $role) {
                $meta_conditions[] = [
                    'key' => $wpdb->prefix.'capabilities',
                    'value' => $role,
                    'compare' => 'LIKE'
                ];
            }
            $args['meta_query'] = $meta_conditions;
        }
    }


    private function apply_course_criteria(array &$args, User_Query_Criteria $criteria): void
    {
        if ($criteria->get_course_ids()) {
            $meta_conditions = [
                'relation' => 'OR'
            ];
            foreach ($criteria->get_course_ids() as $course_id) {
                $course = $this->course_repository->find_by_id(new Course_ID($course_id));
                $product_id = $course->get_product_id()->to_int();
                $meta_conditions[] = [
                    'key' => self::ACCESS_TO_DOWNLOAD,
                    'value' => $product_id,
                    'compare' => '='
                ];
            }
            $args['meta_query'] = $meta_conditions;
        } elseif ($criteria->get_must_have_courses()) {
            global $wpdb;
            $key = self::ACCESS_TO_DOWNLOAD;
            $query = "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key='{$key}'";
            $ids_to_include = implode(
                ', ',
                array_map(function ($row) {
                    return (int)$row['user_id'];
                }, $wpdb->get_results($query, ARRAY_A))
            ); // if we include users filters do not apply to them

            if (empty($ids_to_include)) {
                $ids_to_include = 0;
            }

            $query = "SELECT ID FROM {$wpdb->users} WHERE ID NOT IN ($ids_to_include)";
            $ids_to_exclude = array_map(function ($row) {
                return (int)$row['ID'];
            }, $wpdb->get_results($query, ARRAY_A));

            $args['exclude'] = $ids_to_exclude;
        }
    }

    private function apply_name_criteria(array &$args, User_Query_Criteria $criteria): void
    {
        if ($criteria->get_name()) {
            $args['meta_query'] = [
                'relation' => 'OR',
            ];

            foreach (explode(' ', $criteria->get_name()) as $word) {
                $args['meta_query']['first_name_clause'] = [
                    'key' => 'first_name',
                    'value' => $word,
                    'operator' => 'LIKE',
                    'compare' => 'LIKE',
                    'compare_key' => 'LIKE'
                ];
                $args['meta_query']['last_name_clause'] = [
                    'key' => 'last_name',
                    'value' => $word,
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
            $direction = $sort->desc ? "DESC" : "ASC";

            switch ($sort->property) {
                case 'id':
                    $args['orderby']['ID'] = $direction;
                    break;
                case 'login':
                    $args['orderby']['login'] = $direction;
                    break;
                case 'full_name':
                    $args['orderby']['first_name_clause'] = $direction;
                    $args['orderby']['last_name_clause'] = $direction;
                    break;
                case 'email':
                    $args['orderby']['user_email'] = $direction;
                    break;
                default:
                    break;
            }
        }
    }

    private function get_wp_query_from_criteria(
        int $per_page,
        int $page,
        ?Sort_By_Clause $sort_by,
        User_Query_Criteria $criteria,
        array $args = []
    ): WP_User_Query {
        $default_args = [
            'number' => $per_page,
            'paged' => $page,
            'count_total' => true,
            'order_by' => []
        ];
        $args = array_merge($default_args, $args);

        $this->apply_email_criteria($args, $criteria);
        $this->apply_login_criteria($args, $criteria);
        $this->apply_name_criteria($args, $criteria);
        $this->apply_course_criteria($args, $criteria);
        $this->apply_roles_criteria($args, $criteria);

        $this->apply_sort_by($args, $sort_by);

        $args = wp_parse_args($args);

        return new WP_User_Query($args);
    }

}