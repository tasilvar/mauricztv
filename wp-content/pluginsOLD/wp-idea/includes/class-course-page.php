<?php

namespace bpmj\wpidea;

use bpmj\wpidea\Post_Decorator;
use WP_Post;

/**
 * Created by PhpStorm.
 * User: psypek
 * Date: 24.12.16
 * Time: 09:49
 */
class Course_Page extends Post_Decorator
{

    /**
     * @var Course_Page[]
     */
    protected $children;

    /**
     * @var string
     */
    protected $page_type;

    /**
     * @var int
     */
    protected $course_id;

    /**
     * @var int
     */
    protected $course_page_id;

    /**
     * @var string
     */
    protected $thumbnail;

    /**
     * @var int
     */
    protected $item_index;

    /**
     * @var array
     */
    protected $access;

    /**
     * @var string
     */
    protected $access_status;

    /**
     * @var string
     */
    protected $inaccessible_lesson_visibility;

    /**
     * @var bool
     */
    protected $can_access_lesson;

    /**
     * @var bool
     */
    protected $is_hidden = false;

    /**
     * @param bool $include_hidden
     *
     * @return Course_Page[]
     */
    public function get_children($include_hidden = false)
    {
        if (!isset($this->children)) {
            $this->children = array();
            $args = array(
                'post_parent' => $this->ID,
                'meta_key' => '_bpmj_eddcm',
                'meta_value' => $this->get_course_id(),
                'orderby' => 'menu_order',
                'order' => 'ASC',
            );
            $children = get_children($args);
            foreach ($children as $lesson_post) {
                /** @var WP_Post $lesson_post */
                $lesson = new static($lesson_post);
                $lesson->course_id = $this->get_course_id();
                $lesson->course_page_id = $this->get_course_page_id();
                $this->children[$lesson_post->ID] = $lesson;
            }
        }

        if (!$include_hidden) {
            $children = array();
            foreach ($this->children as $lesson_post) {
                if (!$lesson_post->get_is_hidden()) {
                    $children[$lesson_post->ID] = $lesson_post;
                }
            }

            return $children;
        }

        return $this->children;
    }

    /**
     * Get course page type (either lesson or module)
     *
     * @return string
     */
    public function get_page_type()
    {
        if (!isset($this->page_type)) {
            switch ($this->get_meta('mode', true)) {
                case 'lesson':
                    $this->page_type = 'lesson';
                    break;
                case 'test':
                    $this->page_type = 'test';
                    break;
                default:
                    $this->page_type = 'module';
            }
        }

        return $this->page_type;
    }

    /**
     * Check if the page is a module
     *
     * @return bool
     */
    public function is_module()
    {
        return 'module' === $this->get_page_type();
    }

    /**
     * Check if the page is a lesson
     *
     * @return bool
     */
    public function is_lesson()
    {
        return 'lesson' === $this->get_page_type();
    }

    /**
     * Check if the page is a test
     *
     * @return bool
     */
    public function is_test()
    {
        return 'test' === $this->get_page_type();
    }

    /**
     * Get course id
     *
     * @return int
     */
    public function get_course_id()
    {
        if (!isset($this->course_id)) {
            $this->course_id = (int)$this->get_meta('_bpmj_eddcm', true);
        }

        return $this->course_id;
    }

    /**
     * @return false|string
     */
    public function get_permalink()
    {
        return get_permalink($this->ID);
    }

    /**
     * @return mixed
     */
    public function get_subtitle()
    {
        return $this->get_meta('subtitle', true);
    }

    /**
     * @return string
     */
    public function get_thumbnail()
    {
        if (!isset($this->thumbnail)) {
            $thumb_id = get_post_thumbnail_id($this->ID);
            $thumb_url = wp_get_attachment_image_src($thumb_id, 'thumbnail-size');
            $this->thumbnail = isset($thumb_url[0]) ? $thumb_url[0] : '';
        }

        return $this->thumbnail;
    }

    /**
     * @return string
     */
    public function get_indexed_caption()
    {
        return sprintf($this->is_module() ? __('Module %s', BPMJ_EDDCM_DOMAIN) : __('Lesson %s', BPMJ_EDDCM_DOMAIN), $this->get_item_index());
    }

    /**
     * @return int
     */
    public function get_item_index()
    {
        if (!isset($this->item_index)) {
            $this->item_index = -1;
            $all_items = $this->is_module() ? WPI()->courses->get_all_modules($this->get_course_page_id()) : WPI()->courses->get_all_lessons($this->get_course_page_id());
            $index = array_search($this->ID, array_keys($all_items));
            if (false !== $index) {
                $this->item_index = $index + 1;
            }
        }

        return $this->item_index;
    }

    /**
     * @return int
     */
    private function get_course_page_id()
    {
        if (!isset($this->course_page_id)) {
            $this->course_page_id = get_post_meta($this->get_course_id(), 'course_id', true);
        }

        return $this->course_page_id;
    }

    /**
     * Check whether $this is the parent of $lesson_page_id
     *
     * @param $lesson_page_id
     *
     * @return bool
     */
    public function has_child($lesson_page_id)
    {
        foreach ($this->get_children() as $child) {
            if ($child->ID == $lesson_page_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function get_access()
    {
        if (!isset($this->access)) {
            $this->access = bpmj_eddpc_user_can_access(false, bpmj_eddpc_is_restricted($this->ID), $this->ID);
        }

        return $this->access;
    }

    /**
     * @return string
     */
    public function get_access_status()
    {
        if (!isset($this->access_status)) {
            $access = $this->get_access();
            $this->access_status = $access['status'];
        }

        return $this->access_status;
    }

    /**
     * @param string $access_status
     *
     * @return $this
     */
    public function set_access_status($access_status)
    {
        $this->access_status = $access_status;

        return $this;
    }

    /**
     * @return bool
     */
    public function is_access_valid()
    {
        return 'valid' === $this->get_access_status();
    }

    /**
     * @return bool
     */
    public function is_access_waiting()
    {
        return 'waiting' === $this->get_access_status();
    }

    /**
     * @return string
     */
    public function get_inaccessible_lesson_visibility()
    {
        if (!isset($this->inaccessible_lesson_visibility)) {
            $this->inaccessible_lesson_visibility = WPI()->courses->get_inaccessible_lesson_display_mode($this->get_course_id());
        }

        return $this->inaccessible_lesson_visibility;
    }

    /**
     * @param string $inaccessible_lesson_visibility
     *
     * @return $this
     */
    public function set_inaccessible_lesson_visibility($inaccessible_lesson_visibility)
    {
        $this->inaccessible_lesson_visibility = $inaccessible_lesson_visibility;

        return $this;
    }

    /**
     * @return bool
     */
    public function should_be_grayed_out()
    {
        return !$this->get_can_access_lesson() || $this->is_access_waiting() && 'grayed' === $this->get_inaccessible_lesson_visibility();
    }

    /**
     * @return bool
     */
    public function get_can_access_lesson()
    {
        return $this->can_access_lesson;
    }

    /**
     * @param bool $can_access_lesson
     *
     * @return $this
     */
    public function set_can_access_lesson($can_access_lesson)
    {
        $this->can_access_lesson = $can_access_lesson;

        return $this;
    }

    /**
     * @param int $lesson_id
     *
     * @return $this
     */
    public function remove_child($lesson_id)
    {
        unset($this->children[$lesson_id]);

        return $this;
    }

    /**
     * @return bool
     */
    public function get_is_hidden()
    {
        return $this->is_hidden;
    }

    /**
     * @param bool $is_hidden
     *
     * @return $this
     */
    public function set_is_hidden($is_hidden)
    {
        $this->is_hidden = $is_hidden;

        return $this;
    }

    /**
     * @return int
     */
    public function get_drip()
    {
        if (!isset($this->access)) {
            $this->access = bpmj_eddpc_user_can_access(false, bpmj_eddpc_is_restricted($this->ID), $this->ID);
        }

        return !empty($this->access['drip']) ? $this->access['drip'] : 0;
    }

    /**
     * @return int
     */
    public function get_calculated_drip()
    {
        if (!isset($this->access)) {
            $this->access = bpmj_eddpc_user_can_access(false, bpmj_eddpc_is_restricted($this->ID), $this->ID);
        }

        if (!empty($this->access['drip'])) {
            if (~PHP_INT_MAX == $this->access['total_time'] || PHP_INT_MAX == $this->access['drip']) {
                return 0;
            }

            $drip = $this->access['drip'] - $this->access['total_time'];
            return $drip > 0 ? $drip : 0;
        }

        return 0;
    }

}
