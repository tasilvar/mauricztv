<?php
/**
 *
 * The class responsible for users edit page
 *
 */

// Exit if accessed directly
namespace bpmj\wpidea\admin;

use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\Caps;
use bpmj\wpidea\Course_Progress;
use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\View;
use bpmj\wpidea\wolverine\user\User;

if (!defined('ABSPATH')) {
    exit;
}

class Edit_User
{
    const REMOVE_USER_BAN = 'remove-user-ban';
    const ADD_USER_BAN = 'add-user-ban';
    const CHANGE_USER_BAN_NONCE = 'change-user-ban';

    private ?int $user_id;

    private Interface_Events $events;

    function __construct(Interface_Events $events)
    {
        $this->events = $events;
        $this->init();
    }

    /**
     * @return int
     */
    public function get_user_id()
    {
        return $this->user_id;
    }

    /**
     *
     */
    public function init()
    {
        add_action('admin_head', [$this, 'hook_admin_init']);
        add_action('show_user_profile', [$this, 'html_display_user_banned_field']);
        add_action('edit_user_profile', [$this, 'html_display_user_banned_field']);
        add_action('show_user_profile', [$this, 'html_display_user_courses']);
        add_action('edit_user_profile', [$this, 'html_display_user_courses']);
        add_action('admin_init', [$this, 'ajax_remove_user_ban']);
        add_action('admin_init', [$this, 'ajax_add_user_ban']);
        add_action('wp_ajax_bpmj_eddcm_set_access_time', [$this, 'ajax_set_access_time']);
        add_action('wp_ajax_bpmj_eddcm_set_total_time', [$this, 'ajax_set_total_time']);
        add_action('wp_ajax_bpmj_eddcm_remove_from_course', [$this, 'ajax_remove_from_course']);
        add_action('wp_ajax_bpmj_eddcm_add_to_course', [$this, 'ajax_add_to_course']);
        add_action('wp_ajax_bpmj_eddcm_show_course_progress', [$this, 'ajax_show_course_progress']);
    }

    /**
     *
     */
    public function hook_admin_init()
    {
        global $user_id;

        if ($user_id instanceof \WP_Error) {
            return;
        }

        $this->user_id = (int)$user_id;

        if (function_exists('EDD')) {
            remove_action('show_user_profile', array(EDD()->api, 'user_key_field'));
            remove_action('edit_user_profile', array(EDD()->api, 'user_key_field'));
        }
    }

    /**
     *
     */
    public function html_display_user_courses()
    {
        $courses_functionality_enabled = LMS_Settings::get_option(Settings_Const::COURSES_ENABLED) ?? true;
         if(!$courses_functionality_enabled){
             return;
         }

        require_once BPMJ_EDDCM_DIR . 'includes/admin/views/users-courses.php';
    }


    public function html_display_user_banned_field()
    {
        if (!$this->get_user_id()) {
            return;
        }

        $user = User::find($this->get_user_id());
        echo View::get_admin('/edit-user/user-banned-field', [
            'user' => $user,
            'nonce' => wp_create_nonce(self::CHANGE_USER_BAN_NONCE)
        ]);
    }

    public function ajax_remove_user_ban()
    {
        if (!isset($_POST['action'])) {
            return;
        }

        if ($_POST['action'] != self::REMOVE_USER_BAN) {
            return;
        }

        $this->validate_change_user_ban();
        $user_id = (int)$_POST['user_id'];
        $user = User::find($user_id);
        if (!$user) {
            wp_send_json_success(['success' => false, 'message' => __('User not found', BPMJ_EDDCM_DOMAIN)]);
        }
        $user->removeBan();
        wp_send_json_success(['success' => true]);
    }

    public function ajax_add_user_ban()
    {
        if (!isset($_POST['action'])) {
            return;
        }

        if ($_POST['action'] != self::ADD_USER_BAN) {
            return;
        }

        $this->validate_change_user_ban();
        $user_id = (int)$_POST['user_id'];
        $user = User::find($user_id);
        if (!$user) {
            wp_send_json_success(['success' => false, 'message' => __('User not found', BPMJ_EDDCM_DOMAIN)]);
        }
        $user->banForever();
        wp_send_json_success(['success' => true, 'message' => __('User banned forever', BPMJ_EDDCM_DOMAIN)]);
    }

    protected function validate_change_user_ban()
    {
        if (!current_user_can(Caps::CAP_MANAGE_USERS)) {
            wp_send_json_success(['success' => false, 'message' => __('Permission denied!', BPMJ_EDDCM_DOMAIN)]);
        }

        if (!isset($_POST['nonce'])) {
            wp_send_json_success(['success' => false, 'message' => __('Nonce not found!', BPMJ_EDDCM_DOMAIN)]);
        }

        if (!wp_verify_nonce($_POST['nonce'], self::CHANGE_USER_BAN_NONCE)) {
            wp_send_json_success(['success' => false, 'message' => __('Invalid nonce!', BPMJ_EDDCM_DOMAIN)]);
        }

        if (!isset($_POST['user_id'])) {
            wp_send_json_success(['success' => false, 'message' => __('Variables are missing!', BPMJ_EDDCM_DOMAIN)]);
        }
    }


    /**
     *
     */
    public function ajax_set_access_time()
    {
        $data = array();
        if (current_user_can('edit_users') && check_ajax_referer('bpmj_eddcm_users_manager')) {
            $user_id = (int)$_POST['user_id'];
            $product_id = (int)$_POST['product_id'];
            $course_id = (int)$_POST['course_id'];
            if (!empty($_POST['no_limit'])) {
                $access_time = 0;
            } else {
                $access_time_str = $_POST['access_due_date'] . ' ' . $_POST['access_due_hh'] . ':' . $_POST['access_due_mm'];
                $access_time = bpmj_eddpc_adjust_timestamp(strtotime($access_time_str), false);
            }
            if ($user_id && $product_id) {
                $data['cell_html'] = $this->html_access_time_cell($access_time, $user_id, $course_id, $product_id);
                $access_time_data = get_user_meta($user_id, "_bpmj_eddpc_access", true);
                $access_time_data[$product_id]['access_time'] = $access_time;
                update_user_meta($user_id, '_bpmj_eddpc_access', $access_time_data);
            }
        }
        die(json_encode($data));
    }

    /**
     *
     */
    public function ajax_set_total_time()
    {
        $data = array();
        if (current_user_can('edit_users') && check_ajax_referer('bpmj_eddcm_users_manager')) {
            $user_id = (int)$_POST['user_id'];
            $product_id = (int)$_POST['product_id'];
            $days = (int)$_POST['total_time_dd'];
            $hours = (int)$_POST['total_time_hh'];
            $minutes = (int)$_POST['total_time_mm'];
            $seconds = (int)$_POST['total_time_ss'];
            $sign = $_POST['total_time_sign'];

            $total_time = $days * 86400 + $hours * 3600 + $minutes * 60 + $seconds;

            if ($user_id && $product_id) {
                $data['total_time'] = $sign === '-' ? -1 * $total_time : $total_time;
                $access_time_data = get_user_meta($user_id, "_bpmj_eddpc_access", true);
                $access_time_data[$product_id]['total_time'] = $total_time;
                update_user_meta($user_id, '_bpmj_eddpc_access', $access_time_data);
            }
        }
        die(json_encode($data));
    }

    /**
     *
     */
    public function ajax_remove_from_course()
    {
        $data = array();
        if (current_user_can('edit_users') && check_ajax_referer('bpmj_eddcm_users_manager')) {
            $user_id = (int)$_POST['user_id'];
            $product_id = (int)$_POST['product_id'];
            if ($user_id && $product_id) {
                $access_time_data = get_user_meta($user_id, "_bpmj_eddpc_access", true);
                unset($access_time_data[$product_id]);
                update_user_meta($user_id, '_bpmj_eddpc_access', $access_time_data);
            }
        }
        die(json_encode($data));
    }

    /**
     *
     */
    public function ajax_add_to_course()
    {


        $data = array();
        if (current_user_can('edit_users') && check_ajax_referer('bpmj_eddcm_users_manager')) {
            $user_id = (int)$_POST['user_id'];
            $product_id = (int)$_POST['product_id'];
            $price_id = (int)$_POST['price_id'];

            if ($user_id && $product_id) {
                bpmj_eddpc_add_time($user_id, $product_id, $price_id);

                $course_id = WPI()->courses->get_course_by_product($product_id);
                if($course_id){
                    $this->events->emit(Event_Name::STUDENT_ENROLLED_IN_COURSE, $course_id->ID, $user_id);
                }
            }
        }
        die(json_encode($data));
    }

    /**
     *
     */
    public function ajax_show_course_progress()
    {
        if (current_user_can('edit_users') && check_ajax_referer('bpmj_eddcm_users_manager')) {
            $user_id = (int)$_POST['user_id'];
            $course_id = (int)$_POST['course_id'];
            if ($user_id && $course_id) {
                $course_page_id = get_post_meta($course_id, 'course_id', true);
                $progress = new Course_Progress($course_page_id, null, true, $user_id);
                ?>
                <section class="edd-courses-manager-dashboard">
                    <div class="row">
                        <div class="full-column">
                            <div class="panel courses no-courses animated fadeInUp">
                                <div class="panel-body no-padding">
                                    <table>
                                        <thead>
                                        <tr>
                                            <th><span class="dashicons dashicons-yes"></span></th>
                                            <th class="title"><?php
                                                _e('Lesson', BPMJ_EDDCM_DOMAIN); ?></th>
                                            <th class="text-right"><?php
                                                _e('Actions', BPMJ_EDDCM_DOMAIN); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach (WPI()->courses->get_all_lessons($course_page_id) as $lesson):
                                            $lesson_finished = $progress->is_lesson_finished($lesson->ID);
                                            ?>
                                            <tr<?php
                                            if ($lesson_finished): ?> class="course-lesson-finished"<?php
                                            endif; ?>>
                                                <td><?php
                                                    if ($lesson_finished): ?><span
                                                            class="dashicons dashicons-yes"></span><?php
                                                    endif; ?></td>
                                                <td class="title"><?php
                                                    echo esc_html($lesson->post_title); ?></td>
                                                <td class="text-right">
                                                    <a href="<?php
                                                    echo get_permalink($lesson->ID); ?>"
                                                       class="btn-eddcm btn-eddcm-default"><?php
                                                        _e('View lesson', BPMJ_EDDCM_DOMAIN); ?></a>
                                                    <a href="<?php
                                                    echo esc_attr(admin_url('post.php?post=' . $lesson->ID . '&action=edit')); ?>"
                                                       class="btn-eddcm btn-eddcm-primary"><?php
                                                        _e('Edit', BPMJ_EDDCM_DOMAIN); ?></a>
                                                </td>
                                            </tr>
                                        <?php
                                        endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <?php
            }
        }
        die();
    }

    /**
     * @param int $access_time
     * @param int $user_id
     * @param int $course_id
     * @param int $product_id
     *
     * @return string
     */
    public function html_access_time_cell($access_time, $user_id, $course_id, $product_id)
    {
        ob_start();
        if ($access_time && $this->can_be_converted_to_datetime($access_time)) {
            echo bpmj_eddpc_date_i18n('d.m.Y - H:i:s', $access_time);
        } else {
            _e('No limit', BPMJ_EDDCM_DOMAIN);
        }
        ?>
    <a href="" style="text-decoration: none; float: right;"
       data-action="set-access-time-popup"
       data-user-id="<?php
       echo $user_id; ?>"
       data-product-id="<?php
       echo $product_id; ?>"
       data-course-id="<?php
       echo $course_id; ?>"
       data-access-time="<?php
       echo bpmj_eddpc_adjust_timestamp($access_time); ?>"><span
                class="dashicons dashicons-welcome-write-blog icons bpmj-icons"></span>
        </a><?php
        return ob_get_clean();
    }

    private function can_be_converted_to_datetime($timestamp): bool
    {
        $local_time = gmdate('Y-m-d H:i:s', $timestamp);
        $timezone = wp_timezone();
        $datetime = date_create($local_time, $timezone);
        return is_object($datetime);
    }
}
