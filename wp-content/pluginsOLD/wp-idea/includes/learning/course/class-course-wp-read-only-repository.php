<?php

declare(strict_types=1);

namespace bpmj\wpidea\learning\course;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\user\Interface_User;
use bpmj\wpidea\user\User;
use WP_Query;

class Course_Wp_Read_Only_Repository implements Interface_Readable_Course_Repository
{
    private const KEY_MODE = 'mode';
    private const KEY_COURSE_ID = 'course_id';
    private const KEY_PRODUCT_ID = 'product_id';
    private const POST_TYPE_COURSES = 'courses';
    private const KEY_CERTIFICATE_TEMPLATE_ID = 'certificate_template_id';
    private const ACCESS_TO_DOWNLOAD = '_bpmj_eddpc_access_to_download';

    public function find_by_id(Course_ID $id): ?Course
    {
        $post = get_post($id->to_int());

        if (!$post) {
            return null;
        }

        $certificate_template_id = get_post_meta($id->to_int(), self::KEY_CERTIFICATE_TEMPLATE_ID, true);

        return new Course(
            $id,
            $post->post_title,
            new Author_ID((int)$post->post_author),
            $this->get_product_id($id),
            $certificate_template_id ? new ID((int)$certificate_template_id) : null
        );
    }

    public function find_by_product_id(ID $product_id): ?Course
    {
        $wp_course = WPI()->courses->get_course_by_product($product_id->to_int());
        if (empty($wp_course)) {
            return null;
        }
        $certificate_template_id = get_post_meta($wp_course->ID, self::KEY_CERTIFICATE_TEMPLATE_ID, true);

        return new Course(
            new Course_ID($wp_course->ID),
            $wp_course->post_title,
            new Author_ID((int)$wp_course->post_author),
            $product_id,
            $certificate_template_id ? new ID((int)$certificate_template_id) : null
        );
    }

    public function find_by_certificate_id(ID $certificate_id): ?Course
    {
        $course_id = get_post_meta($certificate_id->to_int(), self::KEY_COURSE_ID, true);
        $query = new WP_Query([
            'post_type' => self::POST_TYPE_COURSES,
            'meta_query' => [
                [
                    'key' => self::KEY_COURSE_ID,
                    'value' => $course_id,
                ]
            ]
        ]);
        $course_post = $query->post;
        if (is_null($course_post->ID)) {
            return null;
        }

        return $this->find_by_id(new Course_ID($course_post->ID));
    }

    public function find_by_page_id(ID $page): ?Course
    {
        $course = WPI()->courses->get_course_by_page($page->to_int());

        if (!$course) {
            return null;
        }

        return $this->find_by_id(new Course_ID((int)$course->ID));
    }

    public function find_by_user(Interface_User $user): Course_Collection
    {
        return $this->find_by_user_id($user->get_id()->to_int());
    }

    public function find_by_user_id(int $user_id): Course_Collection
    {
        global $wpdb;
        $collection = new Course_Collection();
        $ids = get_user_meta($user_id, self::ACCESS_TO_DOWNLOAD);

        foreach ($ids as $id) {
            try {
                $product_id = $wpdb->get_results(
                        "select post_id, meta_key from $wpdb->postmeta where meta_value = 
                                          '{$id}' AND meta_key = 'product_id'",
                        ARRAY_A
                    )[0]['post_id'] ?? null;

                if (!$product_id) {
                    continue;
                }

                $post = get_post($product_id);
                if ($post->post_type === self::POST_TYPE_COURSES) {
                    $collection->add($this->find_by_id(new Course_ID((int)$product_id)));
                }
            } catch (\Exception $e) {
            }
        }

        return $collection;
    }

    public function find_all(): Course_Collection
    {
        $posts = get_posts([
            'post_type' => self::POST_TYPE_COURSES,
            'posts_per_page' => -1
        ]);

        $collection = new Course_Collection();

        foreach ($posts as $post) {
            $collection->add(
                $this->find_by_id(new Course_ID($post->ID))
            );
        }

        return $collection;
    }

    public function get_course_panel_id(Course_ID $id): int
    {
        return (int)get_post_meta($id->to_int(), 'course_id', true);
    }

    public function get_course_price_for_user(Course_ID $course_id, User $user): ?string
    {
        $query = new WP_Query(array(
            'post_type' => 'courses',
            'meta_query' => array(
                [
                    'key' => 'course_id',
                    'value' => $course_id->to_int(),
                ],
            ),
        ));

        $course_post = $query->post;

        $variable_pricing = get_post_meta($course_post->ID, 'variable_pricing', true);
        $variable_prices = get_post_meta($course_post->ID, 'variable_prices', true);
        if ('1' === $variable_pricing) {
            $product_id = get_post_meta($course_post->ID, 'product_id', true);
            $user_price_id = get_user_meta($user->get_id()->to_int(), '_bpmj_eddpc_' . $product_id . '_price_id', true);
            $course_price = $variable_prices[$user_price_id[0]]['amount'];
        } else {
            $price = get_post_meta($course_post->ID, 'price', true);
            if (empty($price)) {
                $price = 0;
            }

            $course_price = number_format_i18n($price, 2);
        }

        return $course_price;
    }


    protected function get_product_id(Course_ID $id): ?ID
    {
        $product_id = get_post_meta($id->to_int(), self::KEY_PRODUCT_ID, true);

        return $product_id ? new ID((int)$product_id) : null;
    }

    public function is_course_panel_page(int $page_id): bool
    {
        return get_post_meta($page_id, self::KEY_MODE, true) === 'home';
    }

    public function is_course_lesson_page(int $page_id): bool
    {
        return get_post_meta($page_id, self::KEY_MODE, true) === 'lesson';
    }

    public function is_course_test_page(int $page_id): bool
    {
        return get_post_meta($page_id, self::KEY_MODE, true) === 'test';
    }

    public function is_course_module_page(int $page_id): bool
    {
        return get_post_meta($page_id, self::KEY_MODE, true) === 'full';
    }

    public function count(): int
    {
        $posts = get_posts([
            'post_type' => self::POST_TYPE_COURSES,
            'posts_per_page' => -1
        ]);

        return count($posts);
    }
}
