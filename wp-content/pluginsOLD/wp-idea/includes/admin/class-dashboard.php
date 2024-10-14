<?php

/**
 *
 * The class responsible for plugin dashboard
 *
 */

// Exit if accessed directly
namespace bpmj\wpidea\admin;

use bpmj\wpidea\Course_Bundle;
use BPMJ_EDDPC_User_Access;

if (!defined('ABSPATH'))
    exit;

class Dashboard
{

    /**
     * @var array[]
     */
    protected $courses;

    function __construct()
    {
        $this->init();
    }

    // Initalize class
    public function init()
    {

    }

    /**
     * Get all created courses
     *
     * @param bool $load_user_statistics
     *
     * @return array|bool
     */
    public function get_courses($load_user_statistics = false)
    {
        if (!isset($this->courses)) {
            $this->courses = WPI()->courses->get_courses(array('publish'));
        }

        if ($load_user_statistics && !empty($this->courses)) {
            $product_id_array = array();
            foreach ($this->courses as $course) {
                $product_id_array[] = get_post_meta($course['id'], 'product_id', true);
            }
        }

        return empty($this->courses) ? false : $this->courses;
    }

    /**
     *
     * @param array $args
     * @return \bpmj\wpidea\Course_Bundle[]
     */
    public function get_bundles($args = array())
    {
        return Course_Bundle::get_list($args);
    }

    /**
     * Return true if course have access time set even if variable prices has set access time
     *
     * @param array $course
     * @return bool
     */
    public function course_have_access_time(array $course)
    {
        if ('draft' === $course['status']) return false;

        $download_id = get_post_meta($course['id'], 'product_id', true);

        if (edd_has_variable_prices($download_id)) {
            $prices = edd_get_variable_prices($download_id);

            $has_variable_price = false;
            foreach ($prices as $price) {
                if (!empty($price['access_time'])) {
                    $has_variable_price = true;
                    break;
                }
            }

            return $has_variable_price;
        } else {
            $access_time = get_post_meta($course['id'], 'access_time', true);

            if (!empty($access_time))
                return true;

            return false;
        }
    }
}
