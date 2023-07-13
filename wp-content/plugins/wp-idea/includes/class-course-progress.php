<?php

namespace bpmj\wpidea;

/**
 * Created by PhpStorm.
 * User: psypek
 * Date: 26.12.16
 * Time: 22:13
 */
class Course_Progress
{

    /**
     *
     */
    const META_KEY = '_course_progress_%d';
    const TRACKING_ENABLED = 'on';
    const TRACKING_DISABLED = 'off';

    /**
     * @var int
     */
    protected $course_page_id;

    /**
     * @var int
     */
    protected $lesson_page_id;

    /**
     * @var int
     */
    protected $user_id;

    /**
     * @var int
     */
    protected $course_lesson_count;

    /**
     * @var array
     */
    protected $progress;

    /**
     * @var int
     */
    protected $finished_lesson_count;

    /**
     * @var bool
     */
    protected $tracking_enabled;

    /**
     * @var bool
     */
    protected $no_content_access_mode;

    /**
     * CourseProgress constructor.
     *
     * @param $course_page_id
     * @param $lesson_page_id
     * @param bool $read_progress
     * @param int|null $user_id
     */
    public function __construct($course_page_id, $lesson_page_id = null, $read_progress = true, $user_id = null)
    {
        $this->course_page_id = $course_page_id;
        $this->lesson_page_id = $lesson_page_id;
        $this->user_id = (int)$user_id ? (int)$user_id : get_current_user_id();
        if ($read_progress) {
            $this->read_progress();
        }
        $this->check_tracking_enabled();
        $this->check_no_content_access_mode();
    }

    /**
     * @return int
     */
    public function get_course_page_id()
    {
        return $this->course_page_id;
    }

    /**
     * @return int
     */
    public function get_lesson_page_id()
    {
        return $this->lesson_page_id;
    }

    /**
     * @return int
     */
    public function get_course_lesson_count()
    {
        if (!isset($this->course_lesson_count)) {
            $lessons = WPI()->courses->get_all_lessons($this->course_page_id, true);
            $this->course_lesson_count = count($lessons);
        }

        return $this->course_lesson_count;
    }

    /**
     *
     */
    public function read_progress()
    {
        $this->progress = get_user_meta($this->user_id, $this->get_meta_key(), true);
        if (!is_array($this->progress)) {
            $this->progress = array();
        } else {
            $finished_lesson_ids = array_keys($this->progress);
            foreach ($finished_lesson_ids as $lesson_id) {
                if (!WPI()->courses->check_if_lesson_is_part_of_course($lesson_id, $this->course_page_id)) {
                    unset($this->progress[$lesson_id]);
                }
            }
        }
        $this->finished_lesson_count = count($this->progress);
    }

    /**
     * @param int $lesson_page_id
     *
     * @return bool
     */
    public function is_lesson_finished($lesson_page_id = null)
    {
        if ($this->is_tracking_enabled() && key_exists($lesson_page_id ? $lesson_page_id : $this->lesson_page_id, $this->progress)) {
            return true;
        }

        return false;
    }

    /**
     * @return int
     */
    public function get_progress_percent()
    {
        if (!isset($this->progress)) {
            $this->read_progress();
        }
        if ($this->get_course_lesson_count() > 0) {
            return (int)ceil($this->finished_lesson_count * 100 / $this->get_course_lesson_count());
        }

        return 100;
    }

    /**
     * @return int
     */
    public function get_finished_lesson_count()
    {
        return $this->finished_lesson_count;
    }

    protected function get_meta_key()
    {
        return sprintf(self::META_KEY, $this->get_course_page_id());
    }

    public function toggle_finished($finished)
    {
        if (!isset($this->progress)) {
            $this->read_progress();
        }
        if (!$this->progress) {
            $this->progress = array();
        }
        if ($finished) {
            $this->progress[$this->get_lesson_page_id()] = array(
                'date_finished' => time(),
                'finished' => true,
            );
        } else {
            unset($this->progress[$this->get_lesson_page_id()]);
        }
        $this->save_progress();
    }

    protected function save_progress()
    {
        update_user_meta($this->user_id, $this->get_meta_key(), $this->progress);
    }

    public function get_course_progress_widget()
    {
        ob_start();
        WPI()->templates->html_progress_section($this);
        return ob_get_clean();
    }

    public function output_course_progress_widget()
    {
        echo $this->get_course_progress_widget();
    }

    /**
     *
     */
    private function check_tracking_enabled()
    {
        global $wpidea_settings;

        if (WPI()->packages->no_access_to_feature(Packages::FEAT_PROGRESS_TRACKING)) {
            $this->tracking_enabled = false;

            return;
        }

        $globally_enabled = ($wpidea_settings['progress_tracking'] ?? self::TRACKING_DISABLED) === self::TRACKING_ENABLED;
        $course_enabled = get_post_meta($this->course_page_id, 'progress_tracking', true);

        $this->tracking_enabled = $globally_enabled;

        if (self::TRACKING_ENABLED === $course_enabled) {
            $this->tracking_enabled = true;
        } else if (self::TRACKING_DISABLED === $course_enabled) {
            $this->tracking_enabled = false;
        }
    }

    private function check_no_content_access_mode()
    {
        $product = WPI()->courses->get_product_by_page_id($this->course_page_id);
        $this->no_content_access_mode = !$product->currentUserHasNoContentAccess();
    }

    /**
     * @return bool
     */
    public function is_tracking_enabled()
    {
        return $this->tracking_enabled;
    }

    
    /**
     * @return bool
     */
    public function is_no_content_access_mode()
    {
        return $this->no_content_access_mode;
    }
    
    public function get_progress()
    {
        return $this->progress;
    }

}
