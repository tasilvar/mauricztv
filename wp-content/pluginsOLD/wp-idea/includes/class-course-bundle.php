<?php

/**
 * Instances of this class represent course bundle data object
 */
// Exit if accessed directly
namespace bpmj\wpidea;

use bpmj\wpidea\Post_Decorator;
use WP_Post;

if (!defined('ABSPATH'))
    exit;

class Course_Bundle extends Post_Decorator
{

    const PARAM_NAME = 'eddcm_subtype';
    const POST_TYPE = 'download';
    const POST_SUBTYPE = 'bundle';

    /**
     *
     * @var WP_Post[]
     */
    protected $bundled_courses;

    public static function get_meta_key()
    {
        return '_' . static::PARAM_NAME;
    }

    /**
     *
     * @param array $args
     * @return Course_Bundle[]
     */
    public static function get_list($args = array())
    {
        $posts_query_args = array_merge(array(
            'meta_key' => static::get_meta_key(),
            'meta_value' => static::POST_SUBTYPE,
            'post_type' => static::POST_TYPE,
            'post_status' => 'any',
            'posts_per_page' => -1
        ), $args);
        return parent::get_posts($posts_query_args, __CLASS__);
    }

    public function get_price()
    {
        return $this->get_meta('edd_price', true);
    }

    public function get_sale_price()
    {
        return $this->get_meta('edd_sale_price', true);
    }

    public function get_bundled_courses()
    {
        if (!isset($this->bundled_courses)) {
            $this->bundled_courses = array();
            $courses = $this->get_meta('_edd_bundled_products', true) ?: array();
            foreach ($courses as $product_id) {
                $this->bundled_courses[] = get_post($product_id);
            }
        }
        return $this->bundled_courses;
    }

    public function has_course(int $course_id): bool
    {
        foreach($this->get_bundled_courses() as $course) {
            if ($course->ID === $course_id) {
                return true;
            }
        }
        return false;
    }

    public function get_bundled_courses_total()
    {
        $total = '0.00';
        foreach ($this->get_bundled_courses() as $product) {
            $total = bcadd($total, get_post_meta($product->ID, 'edd_price', true));
        }
        return number_format($total, 2, '.', '');
    }

}
