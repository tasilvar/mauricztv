<?php

namespace bpmj\wpidea;

use bpmj\wpidea\admin\pages\course_editor\core\configuration\General_Course_Group;
use bpmj\wpidea\certificates\Interface_Certificate_Repository;
use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;
use bpmj\wpidea\learning\quiz\api\Interface_Quiz_Api;
use bpmj\wpidea\learning\quiz\value_object\Quiz_Time_Limit_Settings;
use bpmj\wpidea\sales\product\core\services\Product_Events;
use bpmj\wpidea\sales\product\Custom_Purchase_Links_Helper;
use bpmj\wpidea\sales\product\Meta_Helper as Product_Meta_Helper;
use bpmj\wpidea\sales\product\model\Gtu;
use bpmj\wpidea\user\Interface_Current_User_Getter;
use bpmj\wpidea\wolverine\product\course\Repository as ProductCourseRepository;
use bpmj\wpidea\wolverine\product\Product;
use bpmj\wpidea\wolverine\quiz\ResolvedQuiz;
use bpmj\wpidea\wolverine\quiz\ResolvedQuizData;
use bpmj\wpidea\wolverine\user\User;
use BPMJ_EDDPC_User_Access;
use WP_Error;
use WP_Post;
use WP_Query;

/**
 *
 * The class responsible for courses
 *
 */

class Courses
{
    const EVENT_COURSE_CREATED = 'course_created';
    private const MAX_PROGRESS_PERCENT = 100;

    /**
     * This array is indexed by course IDs
     *
     * @var array
     */
    protected $course_structure_cache;
    private $product_repository;
    private Interface_Events $events;

    public static $allowed_quiz_file_types = array(
        'txt' => 'text/plain',
        'csv' => 'text/csv',
        'html' => 'text/html',
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'ppt' => 'application/vnd.ms-powerpoint',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',

        'jpg|jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'bmp' => 'image/bmp',
    );

    private Interface_Current_User_Getter $current_user_getter;

    private Interface_Certificate_Repository $certificate_repository;

    private Interface_Readable_Course_Repository $course_repository;
    private Product_Events $product_events;

    private Interface_Quiz_Api $quiz_api;

    //Construct
    function __construct(
        ProductCourseRepository  $course_repository,
        Interface_Events $events,
        Interface_Current_User_Getter $current_user_getter,
        Interface_Certificate_Repository $certificate_repository,
        Interface_Readable_Course_Repository $readable_course_repository,
        Product_Events $product_events,
        Interface_Quiz_Api $quiz_api
    )
    {
        $this->product_repository = $course_repository;
        $this->events = $events;
        $this->current_user_getter = $current_user_getter;
        $this->certificate_repository = $certificate_repository;
        $this->course_repository = $readable_course_repository;
        $this->product_events = $product_events;
        $this->course_structure_cache = array();
        $this->quiz_api = $quiz_api;

        $this->init();
    }

    //Init
    public function init()
    {
        add_action('init', array($this, 'register_courses'));
        add_action('init', array($this, 'add_to_cart'));
        add_action('init', array($this, 'register_tests'));
        add_action('init', array($this, 'register_certificates'));

        add_filter('post_type_link', array($this, 'modify_course_link'), 10, 3);
        
        add_filter('edd_get_option_thousands_separator', array($this, 'get_thousands_separator'), 10, 3);
    }

    /**
     * Custom post type
     * Courses (hidden)
     */
    public function register_courses()
    {

        $args = array(
            'description' => __('Courses from the WP Idea.', BPMJ_EDDCM_DOMAIN),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'query_var' => true,
            'rewrite' => array('slug' => 'courses'),
            'capability_type' => 'post',
            'has_archive' => false,
            'show_in_rest' => true,
            'hierarchical' => false,
            'menu_position' => null,
            'taxonomies' => array('download_category', 'download_tag'),
            'supports' => array('title', 'editor', 'thumbnail'),
            'labels' => array(
                'name' => __('Courses', BPMJ_EDDCM_DOMAIN),
                'singular' => __('Course', BPMJ_EDDCM_DOMAIN),
                'edit_item' => __('Edit Course', BPMJ_EDDCM_DOMAIN),
            ),
        );

        register_post_type('courses', $args);
    }

    public function register_tests()
    {

        $args = array(
            'description' => __('Tests from WP Idea.', BPMJ_EDDCM_DOMAIN),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'query_var' => true,
            'rewrite' => array('slug' => 'tests'),
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title'),
            'labels' => array(
                'name' => __('Tests', BPMJ_EDDCM_DOMAIN),
                'singular' => __('Test', BPMJ_EDDCM_DOMAIN),
                'edit_item' => __('Edit Test', BPMJ_EDDCM_DOMAIN),
            ),
        );

        register_post_type('tests', $args);
    }

    public function register_certificates()
    {

        $args = array(
            'description' => __('Certificates from the WP Idea.', BPMJ_EDDCM_DOMAIN),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => false,
            'query_var' => true,
            'rewrite' => array('slug' => 'certificates'),
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title'),
            'labels' => array(
                'name' => __('Certificates', BPMJ_EDDCM_DOMAIN),
                'singular' => __('Certificate', BPMJ_EDDCM_DOMAIN),
                'edit_item' => __('Edit Certificate', BPMJ_EDDCM_DOMAIN),
            ),
        );

        register_post_type('certificates', $args);
    }

    public function finish_quiz($inserted_quiz_id, $quiz_post_id,$files, array $question = [])
    {


        $query = new WP_Query(array(
            'post_status' => 'draft',
            'post_type' => 'tests',
            'meta_query' => array(
                array(
                    'key' => 'user_email',
                    'value' => wp_get_current_user()->user_email,
                ),
            ),
        ));

        if ($query->post_count > 0) {
            foreach ($query->get_posts() as $post) {
                $quiz_id = get_post_meta($post->ID, 'quiz_id', true);
                $time_mode = get_post_meta($quiz_id, 'time_mode', true);
                $time_end = get_post_meta($post->ID, 'time_end', true);

                if ('on' === $time_mode && time() >= $time_end) {
                    $user_answers = get_post_meta($post->ID, 'user_answers', true);
                    $user_answers = is_array($user_answers) ? $user_answers : array();
                    $this->quiz_api->save_time_is_up($post->ID);
                    $this->finish_quiz_and_add_points($quiz_id, $post->ID, $user_answers);
                }
            }
        }

        $user_answers = $question;

        $inserted_quiz_id_status = get_post_status($inserted_quiz_id);
        if ('draft' !== $inserted_quiz_id_status)
            return;

        if (!empty($files)) {
            foreach ($files['name'] as $key => $value) {
                if (empty($value)) {
                    $user_answers[$key]['answer'] = __('The user did not send any file.', BPMJ_EDDCM_DOMAIN);
                    continue;
                }

                $file = array(
                    'name' => $files['name'][$key],
                    'type' => $files['type'][$key],
                    'tmp_name' => $files['tmp_name'][$key],
                    'error' => $files['error'][$key],
                    'size' => $files['size'][$key],
                );

                require_once(ABSPATH . 'wp-admin/includes/file.php');

                $result = wp_handle_upload($file, array(
                    'test_form' => false,
                    'mimes' => self::$allowed_quiz_file_types,
                ));

                if (!isset($result['error'])) {
                    $attachment_id = wp_insert_attachment(array(
                        'guid' => $result['file'],
                        'post_mime_type' => mime_content_type($result['file']),
                        'post_title' => $file['name'],
                        'post_content' => '',
                    ), $result['file'], $inserted_quiz_id);

                    $user_answers[$key]['answer'] = $attachment_id;
                } else {
                    $user_answers[$key]['answer'] = __('The user uploaded the incorrect file format.', BPMJ_EDDCM_DOMAIN);
                }
            }
        }

        array_walk_recursive($user_answers, 'sanitize_text_field');

        return $this->finish_quiz_and_add_points($quiz_post_id, $inserted_quiz_id, $user_answers);

    }

    protected function finish_quiz_and_add_points($quiz_post_id, $inserted_quiz_id, $user_answers)
    {
        $questions = get_post_meta($quiz_post_id, 'test_questions', true);
        $pass_points = get_post_meta($quiz_post_id, 'test_questions_points_pass', true);
        $all_points = get_post_meta($quiz_post_id, 'test_questions_points_all', true);
        $evaluated = get_post_meta($quiz_post_id, 'evaluated_by_admin_mode', true);
        $points = 0;

        foreach ($questions as $question) {

            if ('single_radio' === $question['type'] || 'single_select' === $question['type']) {
                if (!empty($user_answers) && isset($user_answers[$question['id']]['answer']) && ($user_answers[$question['id']]['answer'] !== '')) {
                    $user_selection = $user_answers[$question['id']]['answer'][0];
                    $points += (int)$question['answer'][$user_selection]['points'];
                }
            } else if ('multiple' === $question['type']) {

                if (!empty($user_answers) && isset($user_answers[$question['id']]['answer'])) {
                    if (is_array($user_answers[$question['id']]['answer'])) {
                        $user_selection = $user_answers[$question['id']]['answer'];
                    } else {
                        $user_selection = explode(',', $user_answers[$question['id']]['answer']);
                    }

                    foreach ($user_selection as $selection) {
                        if ($selection !== '') {
                            $points += (int)$question['answer'][$selection]['points'];
                        }
                    }
                }
            }
        }

        if ('on' !== $evaluated && $points >= $pass_points) {
            $eddcm_id = get_post_meta($quiz_post_id, '_bpmj_eddcm', true);
            $course_id = get_post_meta($eddcm_id, 'course_id', true);

            $user_progress = new Course_Progress($course_id, $quiz_post_id, false);
            $user_progress->toggle_finished(true);
            $user_progress->read_progress();

            $wpidea_settings = get_option('wp_idea');

            $disable_certificates_option = get_post_meta($course_id, 'disable_certificates', true);

            if (
                $wpidea_settings['enable_certificates'] == 'on' &&
                $disable_certificates_option !== 'on' &&
                WPI()->packages->has_access_to_feature(Packages::FEAT_CERTIFICATES) &&
                $user_progress->get_progress_percent() === 100
            ) {
                $user = $this->current_user_getter->get();
                $course = $this->course_repository->find_by_id(new Course_ID($course_id));
                if ($course || $user) {
                    $certificate = $this->certificate_repository->create_certificate($course, $user);
                    $this->events->emit(Event_Name::CERTIFICATE_ISSUED, $certificate, $course, $user);
                }
            }
        }

        update_post_meta($inserted_quiz_id, 'points', $points);
        update_post_meta($inserted_quiz_id, 'test_questions_points_pass', $pass_points);
        update_post_meta($inserted_quiz_id, 'test_questions_points_all', $all_points);
        update_post_meta($inserted_quiz_id, 'user_answers', $user_answers);

        if ('on' !== $evaluated) {
            if ($points >= $pass_points) {
                update_post_meta($inserted_quiz_id, 'is_passed', 'yes');
            } else {
                update_post_meta($inserted_quiz_id, 'is_passed', 'no');
            }
        } else {
            update_post_meta($inserted_quiz_id, 'is_passed', '');
        }

        wp_update_post(array(
            'ID' => $inserted_quiz_id,
            'post_status' => 'publish',
        ));

        $this->events->emit(Event_Name::QUIZ_FINISHED, $inserted_quiz_id);

        return $points;
    }

    /**
     * Prepare array
     */
    public function prepare_array($fields)
    {
        $fields = array_merge(array(
            // Default values
            'last_section' => 'on'
        ), json_decode($fields, true)
        );

        if (empty($fields['variable_pricing'])) {
            $fields['variable_prices'] = array();
        }

        if (!empty($fields['variable_prices'])) {
            $fields['variable_prices'] = is_array($fields['variable_prices']) ? array_filter($fields['variable_prices']) : array();
        }

        if (!empty($fields['access_start']) && !empty($fields['access_start_hh']) && !empty($fields['access_start_mm'])) {
            $fields['access_start'] .= ' ' . $fields['access_start_hh'] . ':' . $fields['access_start_mm'];
        }

        return $fields;
    }

    /**
     * Create CPT for auto saving
     *
     * Rusza przy odpaleniu kreatora - nadaje ID postu
     */
    public function createCPT()
    {
        $args = array(
            'post_type' => 'courses',
            'post_title' => '#' . time() . ' Auto-Save',
            'post_status' => 'draft',
        );
        $id = wp_insert_post($args);

        return $id;
    }

    /**
     * Add course to Custom post type
     *
     * @param array $form Serialized form
     * @param string $status publish or draft
     *
     * @return int the ID of Course CPT
     */
    public function add_cpt($form, $status = 'draft')
    {

        $id = isset($form['cpt_id']) && $form['cpt_id'] ? $form['cpt_id'] : false;

        if ($id) {

            $course['ID'] = $id;
            $cloned_object_id = false;
            
            if (!empty($form['cloned_from_id'])) {
                $cloned_object_id = $form['cloned_from_id'];;
                $cloned_post = get_post($cloned_object_id);
            }
            
            // Update course title and content
            $course['post_title'] = !empty($form['title']) ? $form['title'] : '#' . time() . ' Auto-Save';
            $course['post_content'] = !empty($form['content']) ? $form['content'] : ($cloned_object_id ? $cloned_post->post_content : '');
            $course['post_status'] = $status;

            wp_update_post($course);
            unset($form['title']);
            unset($form['content']);

            $form['module'] = $form['bpmj_eddcm_module'];
            unset($form['bpmj_eddcm_module']);

            foreach ($form as $key => $field) {
                add_post_meta($id, $key, $field);
            }

            if ($cloned_object_id) {

                // copy all meta entries from source object - but skip the "hidden" (prefixed with an underscore) ones
                foreach (get_post_meta($cloned_object_id) as $meta_key => $meta_value) {
                    if (!isset($form[$meta_key]) && $this->meta_key_should_be_cloned($meta_key)) {
                        add_post_meta($id, $meta_key, $this->prepare_cloned_meta_value($meta_value));
                    }
                }
            }

            // Save CPT id to modules/lessons
            if (isset($form['module']) && $status == 'publish') {
                foreach ($form['module'] as $module) {
                    update_post_meta($module['id'], '_bpmj_eddcm', $id);
                    if (isset($module['module'])) {
                        foreach ($module['module'] as $lesson) {
                            update_post_meta($lesson['id'], '_bpmj_eddcm', $id);
                        }
                    }
                }
            }
        }

        return $id;
    }

    /**
     * Create drip things
     *
     * @param int $id Course Manager Post id
     */
    public function drip($id = null)
    {

        if (!$id) {
            return;
        }

        $params = ( object )array(
            'value' => get_post_meta($id, 'drip_value', true),
            'unit' => get_post_meta($id, 'drip_unit', true),
            'modules' => get_post_meta($id, 'module', true)
        );

        if (WPI()->packages->no_access_to_feature(Packages::FEAT_DELAYED_ACCESS)) {
            // If this feature is disabled we make sure it doesn't work
            $params->value = 0;
        }

        if (empty($params->modules)) {
            return;
        }

        $time = 0;
        $count = 1;
        // Modules
        foreach ($params->modules as $module) {
            $i = 0;

            update_post_meta($module['id'], '_bpmj_eddpc_drip_unit', $params->unit);
            if ($count > 1) {
                if (!$params->value) {
                    delete_post_meta($module['id'], '_bpmj_eddpc_drip_value');
                } else {
                    $time = $params->value + $time;
                    $temp_time = $time;
                    update_post_meta($module['id'], '_bpmj_eddpc_drip_value', $time);
                }
            }


            // Lessons
            if (isset($module['module'])) {
                foreach ($module['module'] as $lesson) {
                    update_post_meta($lesson['id'], '_bpmj_eddpc_drip_unit', $params->unit);
                    if ($count > 1) {
                        if (!$params->value) {
                            delete_post_meta($lesson['id'], '_bpmj_eddpc_drip_value');
                        } else {
                            if ($i == 0) {
                                $time = $temp_time;
                            } else {
                                $time = $params->value + $time;
                            }
                            update_post_meta($lesson['id'], '_bpmj_eddpc_drip_value', $time);
                        }
                    }
                    $i++;
                    $count++;
                }
            }

            $count++;
        }
    }

    /**
     * Create product - price, access_time, access_time_unit
     *
     * @param array $form Serialized form
     *
     * @return int the ID of product
     */
    public function create_product($form)
    {

        $form['title'] = !empty($form['title']) ? $form['title'] : '';

        $variable_prices = isset($form['variable_prices']) ? $form['variable_prices'] : array();
        $overall_purchase_limit = 0;
        $any_purchase_limit_empty = false;
        foreach ($variable_prices as &$price) {
            $purchase_limit = isset($price['bpmj_eddcm_purchase_limit']) ? (int)$price['bpmj_eddcm_purchase_limit'] : '';
            if ($purchase_limit) {
                $price['bpmj_eddcm_purchase_limit'] = $purchase_limit;
                $price['bpmj_eddcm_purchase_limit_items_left'] = $purchase_limit;
                $overall_purchase_limit += $purchase_limit;
            } else {
                $price['bpmj_eddcm_purchase_limit'] = '';
                $price['bpmj_eddcm_purchase_limit_items_left'] = '';
                $any_purchase_limit_empty = true;
            }
        }

        $purchase_limit_unlimited = false;
        if ($overall_purchase_limit > 0 && $any_purchase_limit_empty) {
            $purchase_limit_unlimited = true;
        }
        if (empty($overall_purchase_limit) && !empty($form['purchase_limit'])) {
            $overall_purchase_limit = $form['purchase_limit'];
        }

        $args = array(
            'post_type' => 'download',
            'post_title' => $form['title'],
            'post_status' => 'publish',
            'post_content' => $form['content'],
            'comment_status' => 'closed',
            'meta_input' => array(
                'edd_price' => $form['price'],
                'edd_sale_price' => $form['sale_price'],
                '_variable_pricing' => $form['variable_pricing'],
                'edd_variable_prices' => $variable_prices,
                '_bpmj_eddpc_access_time' => $form['access_time'],
                '_bpmj_eddpc_access_time_unit' => $form['access_time_unit'],
                '_bpmj_eddpc_access_start_enabled' => !empty($form['access_start']),
                '_bpmj_eddpc_access_start' => isset($form['access_start']) ? $form['access_start'] : '',
                '_edd_mailchimp' => isset($form['_edd_mailchimp']) ? $form['_edd_mailchimp'] : '',
                '_edd_mailerlite' => isset($form['_edd_mailerlite']) ? $form['_edd_mailerlite'] : '',
                '_edd_freshmail' => isset($form['_edd_freshmail']) ? $form['_edd_freshmail'] : '',
                '_edd_ipresso' => isset($form['_edd_ipresso']) ? $form['_edd_ipresso'] : '',
                '_edd_ipresso_unsubscribe' => isset($form['_edd_ipresso_unsubscribe']) ? $form['_edd_ipresso_unsubscribe'] : '',
                '_edd_convertkit' => isset($form['_edd_convertkit']) ? $form['_edd_convertkit'] : '',
                '_edd_convertkit_tags' => isset($form['_edd_convertkit_tags']) ? $form['_edd_convertkit_tags'] : '',
                '_edd_convertkit_tags_unsubscribe' => isset($form['_edd_convertkit_tags_unsubscribe']) ? $form['_edd_convertkit_tags_unsubscribe'] : '',
                '_edd_activecampaign' => isset($form['_edd_activecampaign']) ? $form['_edd_activecampaign'] : '',
                '_edd_activecampaign_unsubscribe' => isset($form['_edd_activecampaign_unsubscribe']) ? $form['_edd_activecampaign_unsubscribe'] : '',
                '_edd_activecampaign_tags' => isset($form['_edd_activecampaign_tags']) ? $form['_edd_activecampaign_tags'] : '',
                '_edd_activecampaign_tags_unsubscribe' => isset($form['_edd_activecampaign_tags_unsubscribe']) ? $form['_edd_activecampaign_tags_unsubscribe'] : '',
                '_edd_getresponse' => isset($form['_edd_getresponse']) ? $form['_edd_getresponse'] : '',
                '_edd_getresponse_unsubscribe' => isset($form['_edd_getresponse_unsubscribe']) ? $form['_edd_getresponse_unsubscribe'] : '',
                '_edd_getresponse_tags' => isset($form['_edd_getresponse_tags']) ? $form['_edd_getresponse_tags'] : '',
                '_bpmj_edd_sm_tags' => isset($form['_bpmj_edd_sm_tags']) ? $form['_bpmj_edd_sm_tags'] : '',
                '_edd_recurring_payments_enabled' => isset($form['recurring_payments_enabled']) ? $form['recurring_payments_enabled'] : '',
                '_bpmj_eddcm_purchase_limit' => !empty($overall_purchase_limit) ? (int)$overall_purchase_limit : '',
                '_bpmj_eddcm_purchase_limit_items_left' => !empty($overall_purchase_limit) ? (int)$overall_purchase_limit : '',
                '_bpmj_eddcm_purchase_limit_unlimited' => $purchase_limit_unlimited ? '1' : '',
            ),
        );

        if ($form['banner']) {
            $args['meta_input']['banner'] = $form['banner'];
        }

        $cloned_product_id = null;
        if (!empty($form['cloned_from_id'])) {
            $cloned_product_id = get_post_meta($form['cloned_from_id'], 'product_id', true);

            // copy all meta entries from source object - but skip the "hidden" (prefixed with an underscore) ones
            foreach (get_post_meta($cloned_product_id) as $meta_key => $meta_value) {
                if (!isset($args['meta_input'][$meta_key]) && $this->meta_key_should_be_cloned($meta_key)) {
                    $args['meta_input'][$meta_key] = $this->prepare_cloned_meta_value($meta_value, true);
                }
            }

            $cloned_product_post = get_post($cloned_product_id);
            if ($cloned_product_post instanceof WP_Post) {
                $args['post_content'] = $cloned_product_post->post_content;
            }
        }

        $id = wp_insert_post(array_filter($args));

        if ($cloned_product_id) {
            do_action('bpmj_eddcm_clone_post', $id, $cloned_product_id);
        }

        return $id;
    }

    /**
     * Create all pages (Course, Modules and Lessons)
     *
     * @param array $form Serialized form
     * @param integer $product_id ID of connected product
     *
     * @return array    Form with new id's and without content and title
     */
    public function create_pages($form, $product_id)
    {
        // Redirect Array
        $redirect = array(
            'page' => $form['redirect_page'],
            'url' => $form['redirect_url']
        );

        // Create Course
        $form['mode'] = 'home';
        $meta_input = array(
            '_bpmj_eddpc_access_start_enabled' => !empty($form['access_start']),
            '_bpmj_eddpc_access_start' => isset($form['access_start']) ? $form['access_start'] : '',
        );
        if ($form['banner']) {
            $meta_input['banner'] = $form['banner'];
        }

        $course_id = $this->insert(false, $form, $product_id, false, $redirect, $meta_input);
        if ($course_id):

            // Set course ID
            $form['course_id'] = $course_id;

            // Create Modules
            $modules = isset($form['bpmj_eddcm_module']) ? $form['bpmj_eddcm_module'] : array();
            foreach ($modules as $key_module => $module):

                /**
                 * If ID is already set - just update post
                 */
                if (isset($module['id'])) {
                    $module_id = $this->update($course_id, $module, $product_id, $key_module, $redirect, $meta_input);

                    if (isset($module['module'])) {
                        $this->check_children($module_id, $module['module']);
                    }
                } else {
                    $module_id = $this->insert($course_id, $module, $product_id, $key_module, $redirect, $meta_input);
                }

                if ($module_id):

                    // Set module ID and unset title and content
                    $form['bpmj_eddcm_module'][$key_module]['id'] = $module_id;
                    unset($form['bpmj_eddcm_module'][$key_module]['title']);
                    unset($form['bpmj_eddcm_module'][$key_module]['content']);

                    // Create lessons
                    $lessons = isset($module['module']) ? $module['module'] : array();
                    foreach ($lessons as $key_lesson => $lesson):

                        /**
                         * If ID is already set - just update post
                         */
                        if (isset($lesson['id'])) {
                            $lesson_id = $this->update($module_id, $lesson, $product_id, $key_lesson, $redirect, $meta_input);
                        } else {
                            $lesson_id = $this->insert($module_id, $lesson, $product_id, $key_lesson, $redirect, $meta_input);
                        }

                        if ($lesson_id):

                            // Set lesson ID and unset title and content
                            $form['bpmj_eddcm_module'][$key_module]['module'][$key_lesson]['id'] = $lesson_id;
                            unset($form['bpmj_eddcm_module'][$key_module]['module'][$key_lesson]['title']);
                            unset($form['bpmj_eddcm_module'][$key_module]['module'][$key_lesson]['content']);

                        endif; // Lesson_ID
                    endforeach; // Lessons
                endif; // Module_ID
            endforeach; // Modules
        endif; // Course_ID

        return $form;
    }

    /**
     * Insert
     *
     * @param $parent_id
     * @param $object
     * @param $product_id
     * @param bool $menu_order
     * @param bool $redirect
     * @param array $meta_input
     *
     * @return bool|int|WP_Error
     */
    public function insert($parent_id, $object, $product_id, $menu_order = false, $redirect = false, $meta_input = array())
    {

        $args = array(
            'post_type' => 'page',
            'post_title' => $object['title'],
            'post_content' => isset($object['content']) ? $object['content'] : '',
            'post_status' => 'publish',
            'meta_input' => array_filter(array(
                'mode' => isset($object['mode']) ? $object['mode'] : 'lesson',
                'subtitle' => isset($object['subtitle']) ? $object['subtitle'] : '',
                'level' => isset($object['level']) ? $object['level'] : '',
                'duration' => isset($object['duration']) ? $object['duration'] : '',
                'shortdesc' => isset($object['shortdesc']) ? $object['shortdesc'] : '',
                'files' => isset($object['files']) ? $object['files'] : '',
                '_bpmj_eddpc_restricted_to' => array(array('download' => $product_id, 'price_id' => 'all',))
            )),
        );
        if (!empty($meta_input) && is_array($meta_input)) {
            $args['meta_input'] = array_merge($args['meta_input'], $meta_input);
        }
        if ($parent_id)
            $args['post_parent'] = $parent_id;

        if ($menu_order)
            $args['menu_order'] = $menu_order;

        if ($redirect) {
            $args['meta_input'] = array_merge($args['meta_input'], array(
                '_bpmj_eddpc_redirect_page' => $redirect['page'],
                '_bpmj_eddpc_redirect_url' => $redirect['url'],
            ));
        }

        $cloned_object_id = null;
        if (!empty($object['cloned_from_id']) && 'home' !== $args['meta_input']['mode']) {
            $cloned_object_id = $object['cloned_from_id'];

            $cloned_post = get_post($cloned_object_id);
            if (!isset($args['comment_status'])) {
                $args['comment_status'] = $cloned_post->comment_status;
            }
            if (empty($args['post_content'])) {
                $args['post_content'] = $cloned_post->post_content;
            }

            // copy all meta entries from source object - but skip the "hidden" (prefixed with an underscore) ones
            foreach (get_post_meta($cloned_object_id) as $meta_key => $meta_value) {
                if (!isset($args['meta_input'][$meta_key]) && $this->meta_key_should_be_cloned($meta_key)) {
                    $args['meta_input'][$meta_key] = $this->prepare_cloned_meta_value($meta_value, true);
                }
            }
        }

        if (!empty($object['cloned_from_id']) && 'home' === $args['meta_input']['mode']) {
            $cloned_home_object_id = $object['cloned_from_id'];
            
            $cloned_home_post = get_post($cloned_home_object_id);
            if (empty($args['post_content'])) {
                $args['post_content'] = $cloned_home_post->post_content;
            }
        }

        $id = wp_insert_post($args);

        if (!$id) {
            return false;
        }

        if ($cloned_object_id) {
            do_action('bpmj_eddcm_clone_post', $id, $cloned_object_id);
        }

        if (!$parent_id) {
            // Set EDD product meta
            $products = get_post_meta($product_id, 'edd_download_files', true);
            if (!is_array($products)) {
                $products = array();
            }
            $products[$id] = array(
                'index' => count($products) + 1,
                'name' => __('Course Panel', BPMJ_EDDCM_DOMAIN),
                'file' => get_permalink($id),
                'attachment_id' => 0,
                'condition' => 'all'
            );
            update_post_meta($product_id, 'edd_download_files', $products);

            // Set course meta
            foreach (array('last_section', 'video', 'video_mode') as $whitelisted_meta) {
                if (!empty($object[$whitelisted_meta])) {
                    add_post_meta($id, $whitelisted_meta, $object[$whitelisted_meta], true);
                }
            }
        }
        add_post_meta($product_id, '_bpmj_eddpc_protected_post', $id);

        return $id;
    }

    /**
     * Update course
     * Change product_id when module or lesson is created
     * but taken to another course
     *
     * @param $parent_id
     * @param $object
     * @param $product_id
     * @param bool $menu_order
     * @param bool $redirect
     * @param array $meta_input
     *
     * @return mixed
     */
    public function update($parent_id, $object, $product_id, $menu_order = false, $redirect = false, $meta_input = array())
    {

        $post = array(
            'ID' => $object['id'],
            'post_title' => $object['title'],
            //'post_content' => isset( $object['content'] ) ? $object['content'] : '',
            'meta_input' => array_filter(array(
                'mode' => isset($object['mode']) ? $object['mode'] : 'lesson',
                'subtitle' => isset($object['subtitle']) ? $object['subtitle'] : '',
                'level' => isset($object['level']) ? $object['level'] : '',
                'duration' => isset($object['duration']) ? $object['duration'] : '',
                'shortdesc' => isset($object['shortdesc']) ? $object['shortdesc'] : '',
                'files' => isset($object['files']) ? $object['files'] : '',
                '_bpmj_eddpc_restricted_to' => array(array('download' => $product_id, 'price_id' => 'all',)),
            )),
        );
        if (!empty($meta_input) && is_array($meta_input)) {
            $args['meta_input'] = array_merge($post['meta_input'], $meta_input);
        }

        if ($parent_id)
            $post['post_parent'] = $parent_id;

        if ($menu_order)
            $post['menu_order'] = $menu_order;

        if ($redirect)
            $post['meta_input'] += array(
                '_bpmj_eddpc_redirect_page' => $redirect['page'],
                '_bpmj_eddpc_redirect_url' => $redirect['url'],
            );

        wp_update_post($post);


        /**
         * Delete this ID from WP Idea post
         */
        $course_id = get_post_meta($object['id'], '_bpmj_eddcm', true);
        $modules = get_post_meta($course_id, 'module', true);
        if (is_array($modules) && !empty($modules)) {
            foreach ($modules as $key_module => $module) {

                if (isset($module['id']) && $module['id'] == $object['id']) {
                    unset($modules[$key_module]);
                    continue;
                }

                if (isset($module['module'])) {
                    foreach ($module['module'] as $key_lesson => $lesson) {
                        if (isset($lesson['id']) && $lesson['id'] == $object['id']) {
                            unset($modules[$key_module]['module'][$key_lesson]);
                        }
                    }
                }
            }
        }


        return $object['id'];
    }

    /**
     * Check children
     * Move to trash deleted children from modules
     */
    public function check_children($module_id, $lessons)
    {

        // Array with new created lessons ids
        $new_created = array();
        foreach ($lessons as $lesson) {
            if (isset($lesson['id']))
                $new_created[] = $lesson['id'];
        }


        // Current module children ids
        $current = array();
        $args = array(
            'post_parent' => $module_id,
            'post_type' => 'page',
            'numberposts' => -1,
            'post_status' => 'publish'
        );
        $children = get_children($args);
        if (!empty($children)) {
            foreach ($children as $child_id => $child) {
                $current[] = $child_id;
            }
        }


        // Compare arrays
        $delete_ids = array_diff($current, $new_created);
        if (!empty($delete_ids)) {
            foreach ($delete_ids as $id) {
                wp_delete_post($id);
            }
        }
    }


    public function delete_course($id): bool
    {
        $course_product = get_post_meta($id, 'product_id', true);

        if ( ! empty( $course_product) && $this->product_repository->checkIfProductBelongsToAnyBundle($course_product)) {
            return false;
        }

        $course_name = $this->get_course_name(new Course_ID($id));

        $course_modules = get_post_meta($id, 'module', true);
        $course_id = get_post_meta($id, 'course_id', true);

        // Delete all created modules
        if ($course_modules) {
            foreach ($course_modules as $module) {
                if (isset($module['id']))
                    wp_delete_post($module['id'], true);

                if (isset($module['module'])) {
                    foreach ($module['module'] as $lesson) {
                        if (isset($lesson['id']))
                            wp_delete_post($lesson['id'], true);
                    }
                }
            }
        }

        // Delete main course page
        if ($course_id)
            wp_delete_post($course_id, true);


        // Delete product page
        if ($course_product)
            wp_delete_post($course_product, true);


        // Delete Course Manager page
        if ($id)
            wp_delete_post($id, true);

        $this->product_events->emit_course_deleted_event(
            $course_name,
            $id
        );

        return true;
    }

    private function get_course_name(Course_ID $id): string
    {
        $course = $this->course_repository->find_by_id($id);

        return  $course ? $course->get_title() : '';
    }

    public function delete_bundle($id): bool
    {
        wp_delete_post($id, true);
        return true;
    }

    /**
     * Disables course sales via AJAX
     */
    public function disable_course_sales(Course_ID $id, $value)
    {
        update_post_meta($id->to_int(), General_Course_Group::SALES_DISABLED, $value);
        update_post_meta($this->get_product_by_course($id->to_int()), General_Course_Group::SALES_DISABLED, $value);

        $course_name = $this->get_course_name($id);

        $this->product_events->emit_course_field_toggle_sales_updated_event(
            $course_name,
            $value,
            $id->to_int()
        );

        return true;
    }

    public function update_course_progress(bool $finished, int $course_page_id, int $lesson_page_id): array
    {
        $user_progress = new Course_Progress($course_page_id, $lesson_page_id, false);

        $user_progress->toggle_finished($finished);
        $user_progress->read_progress();

        ob_start();
        WPI()->templates->html_navigation_section(null, $course_page_id, $lesson_page_id);
        $html_navigation_section = ob_get_clean();

        $data = [
            'course_progress_widget' => $user_progress->get_course_progress_widget(),
            'course_navigation_section' => $html_navigation_section,
            'user_can_go_to_next_lesson' => $this->user_can_go_to_next_lesson($course_page_id, $lesson_page_id)
        ];

        $wpidea_settings = get_option('wp_idea');

        $disable_certificates_option = get_post_meta($course_page_id, 'disable_certificates', true);

        $course = $this->course_repository->find_by_id(new Course_ID($course_page_id));
        $user = $this->current_user_getter->get();
        $progress_percent = $user_progress->get_progress_percent();

        if (
            $wpidea_settings['enable_certificates'] == 'on' &&
            $disable_certificates_option !== 'on' &&
            WPI()->packages->has_access_to_feature(Packages::FEAT_CERTIFICATES) &&
            $progress_percent === self::MAX_PROGRESS_PERCENT
        ) {
            if ($course || $user) {
                $certificate = $this->certificate_repository->create_certificate($course, $user);
                $this->events->emit(Event_Name::CERTIFICATE_ISSUED, $certificate, $course, $user);
            }
        }

        if($progress_percent === self::MAX_PROGRESS_PERCENT){
            $this->events->emit(Event_Name::COURSE_COMPLETED, $course, $user);
        }

        return $data;
    }

    public function user_can_go_to_next_lesson($course_page_id, $lesson_page_id)
    {
        $next_lesson = WPI()->courses->get_next_sibling_of($course_page_id, $lesson_page_id);

        if (empty($next_lesson)) return false;

        if ($next_lesson->should_be_grayed_out()) return false;

        return true;
    }

    public function replace_variables_for_certificate_pdf($template_content, $course, $user, $date_generated = null)
    {
        $query = new WP_Query(array(
            'post_type' => 'courses',
            'meta_query' => array(
                [
                    'key' => 'course_id',
                    'value' => $course->ID,
                ],
            ),
        ));

        $course_post = $query->post;

        $variable_pricing = get_post_meta($course_post->ID, 'variable_pricing', true);
        $variable_prices = get_post_meta($course_post->ID, 'variable_prices', true);
        if ('1' === $variable_pricing) {
            $product_id = get_post_meta($course_post->ID, 'product_id', true);
            $user_price_id = get_user_meta($user->ID, '_bpmj_eddpc_' . $product_id . '_price_id', true);
            $course_price = $variable_prices[$user_price_id[0]]['amount'];
        } else {
            $price = get_post_meta($course_post->ID, 'price', true);
            if (empty($price))
                $price = 0;

            $course_price = number_format_i18n($price, 2);
        }

        $template_content = str_replace('{course_name}', $course->post_title, $template_content);
        $template_content = str_replace('{course_price}', $course_price, $template_content);
        $template_content = str_replace('{student_name}', $this->get_student_name($user), $template_content);
        $template_content = str_replace('{student_first_name}', $user->first_name, $template_content);
        $template_content = str_replace('{student_last_name}', $user->last_name, $template_content);

        $certificate_date = is_null($date_generated) ? date('Y-m-d') : $date_generated;
        $template_content = str_replace('{certificate_date}', $certificate_date, $template_content);

        return $template_content;
    }

    protected function get_student_name($user)
    {
        $student_name = $user->first_name;
        if (empty($student_name)) {
            $student_name = $user->user_login;
        } else {
            $student_name .= ' ' . $user->last_name;
        }

        return $student_name;
    }

    public function get_quiz(int $quiz_post_id, int $course_post_id): string
    {
        $questions = $this->quiz_api->get_questions_for_single_test($quiz_post_id);

        if (empty($questions)) {
            echo '<h3 style="text-align: center;">' . __('Unfortunately, the quiz was incorrectly configured. You cannot start the quiz.', BPMJ_EDDCM_DOMAIN) . '</h3>';
            wp_die();
        }

        $quiz_post = get_post($quiz_post_id);

        $inserted_quiz_id = wp_insert_post(array(
            'post_title' => $quiz_post->post_title,
            'post_status' => 'draft',
            'post_type' => 'tests',
        ));

        $this->quiz_api->save_configuration_of_questions($inserted_quiz_id, $questions);

        $is_time_quiz = false;

        $time_mode = get_post_meta($quiz_post_id, 'time_mode', true);
        if ($time_mode == 'on') {
            $is_time_quiz = true;
            $time = get_post_meta($quiz_post_id, 'time', true);
			$time = !empty($time) ? $time : Quiz_Time_Limit_Settings::DEFAULT_TIME;
            $time_end = (time() + ($time * 60));

            update_post_meta($inserted_quiz_id, 'time_end', $time_end);
        }

        update_post_meta($inserted_quiz_id, 'quiz_id', $quiz_post_id);
        update_post_meta($inserted_quiz_id, 'course_id', $course_post_id);
        update_post_meta($inserted_quiz_id, 'user_email', wp_get_current_user()->user_email);
        update_post_meta($inserted_quiz_id, 'user_first_name', wp_get_current_user()->first_name);
        update_post_meta($inserted_quiz_id, 'user_last_name', wp_get_current_user()->last_name);


        return View::get('/course/quiz/container', [
            'is_time_quiz' => $is_time_quiz,
            'time' => $time ?? 0,
            'quiz_post' => $quiz_post,
            'quiz_post_id' => $quiz_post->ID,
            'inserted_quiz_id' => $inserted_quiz_id,
            'questions' => $questions,
        ]);
    }

    public function update_quiz(int $inserted_quiz_id, $user_answers): void
    {
        array_walk_recursive($user_answers, 'sanitize_text_field');
        update_post_meta($inserted_quiz_id, 'user_answers', $user_answers);
    }

    /**
     * Get course object tied to the specified page
     *
     * @param int $page_id
     * @return WP_Post|bool
     */
    public function get_course_by_page($page_id)
    {
        if ('home' !== get_post_meta($page_id, 'mode', true)) {
            return false;
        }
        $query_args = array(
            'meta_key' => 'course_id',
            'meta_value' => $page_id,
            'post_type' => 'courses',
            'post_status' => 'any',
            'posts_per_page' => 1
        );
        $posts = get_posts($query_args);
        if (empty($posts[0])) {
            return false;
        }

        return $posts[0];
    }

    public function get_product_by_page_id(int $page_id): Product
    {
        $course = $this->get_course_by_page($page_id);
        $product_id = $this->get_product_by_course($course->ID);
        return $this->product_repository->find($product_id);
    }

    /**
     * Get course object tied to the specified product
     *
     * @param int $product_id
     *
     * @return WP_Post|bool
     */
    public function get_course_by_product($product_id)
    {
        if ('download' !== get_post_type($product_id)) {
            return false;
        }
        $query_args = array(
            'meta_key' => 'product_id',
            'meta_value' => $product_id,
            'post_type' => 'courses',
            'post_status' => 'any',
            'posts_per_page' => -1
        );
        $posts = get_posts($query_args);
        if (empty($posts[0])) {
            return false;
        }

        return $posts[0];
    }

    /**
     * Get pruduct id tied to the specified course
     *
     * @param int $course_id
     *
     * @return int|bool
     */
    public function get_product_by_course($course_id)
    {
        if ('download' == get_post_type($course_id)) {
            return $course_id;
        }

        return get_post_meta($course_id, 'product_id', true);
    }

    /**
     * @param int $course_page_id
     * @param bool $include_lessons
     * @param bool $include_hidden
     *
     * @return Course_Page[]
     */
    public function get_course_level1_modules_or_lessons($course_page_id, $include_lessons = true, $include_hidden = false)
    {
        $tree = $this->get_course_structure_tree($course_page_id);
        $result = array();
        foreach ($tree as $level1_module_or_lesson) {
            if (!$include_hidden && $level1_module_or_lesson->get_is_hidden()) {
                continue;
            }
            if ($include_lessons || $level1_module_or_lesson->is_module()) {
                $result[] = $level1_module_or_lesson;
            }
        }

        return $result;
    }

    /**
     * @param int $course_page_id
     * @param bool $include_modules
     * @param bool $include_lessons
     * @param bool $include_hidden
     * @param bool $include_tests
     *
     * @return Course_Page[]
     */
    public function get_course_structure_flat($course_page_id = null, $include_modules = true, $include_lessons = true, $include_hidden = false, $include_tests = true)
    {
        global $post;
        if (!$course_page_id) {
            $course_page_id = $post->ID;
        }
        if (!isset($this->course_structure_cache[$course_page_id])) {
            $this->reload_course_structure($course_page_id);
        }
        $result = array();
        /** @var Course_Page $module_or_lesson */
        foreach ($this->course_structure_cache[$course_page_id]['flat'] as $module_or_lesson) {
            if (!$include_hidden && $module_or_lesson->get_is_hidden()) {
                continue;
            }
            if ($module_or_lesson->is_module() && $include_modules
                || $module_or_lesson->is_lesson() && $include_lessons
                || $module_or_lesson->is_test() && $include_tests) {
                $result[$module_or_lesson->ID] = $module_or_lesson;
            }
        }

        return $result;
    }

    /**
     * @param $course_page_id
     *
     * @return Course_Page[]
     */
    public function get_course_structure_tree($course_page_id = null)
    {
        global $post;
        if (!$course_page_id) {
            $course_page_id = $post->ID;
        }
        if (!isset($this->course_structure_cache[$course_page_id])) {
            $this->reload_course_structure($course_page_id);
        }

        return $this->course_structure_cache[$course_page_id]['tree'];
    }

    /**
     * @param $course_page_id
     */
    protected function reload_course_structure($course_page_id)
    {
        $this->course_structure_cache[$course_page_id] = array('tree' => array(), 'flat' => array());
        $course = $this->get_course_by_page($course_page_id);
        if (false === $course) {
            return;
        }
        $args = array(
            'post_parent' => $course_page_id,
            'meta_key' => '_bpmj_eddcm',
            'meta_value' => $course->ID,
            'orderby' => 'menu_order',
            'order' => 'ASC',
        );
        $inaccessible_lesson_visibility = WPI()->courses->get_inaccessible_lesson_display_mode($course->ID);
        $tree = array();
        $level1_modules_or_lessons = get_children($args);
        foreach ($level1_modules_or_lessons as $level1_module_or_lesson) {
            /* @var $level1_module_or_lesson WP_Post */
            $course_page = new Course_Page($level1_module_or_lesson);
            $tree[] = $course_page;
        }

        $flat_list = array();
        $is_progress_forced = $this->get_is_progress_forced($course->ID, $course_page_id);
        $include_following_lessons = true;
        if ($is_progress_forced) {
            $course_progress = new Course_Progress($course_page_id);
        }

        $user_can_view_all = false;
        $user_id = get_current_user_id();
        if ( bpmj_eddpc_cached_user_can( $user_id, Caps::CAP_MANAGE_OPTIONS ) || bpmj_eddpc_cached_user_can( $user_id, Caps::CAP_MANAGE_PRODUCTS) ) {
        	$user_can_view_all = true;
        }
        
        /** @var Course_Page $level1_module_or_lesson */
        foreach ($tree as $key => $level1_module_or_lesson) {
            $access = bpmj_eddpc_user_can_access(false, bpmj_eddpc_is_restricted($level1_module_or_lesson->ID), $level1_module_or_lesson->ID);
            $access_valid = 'valid' === $access['status'];
            $access_waiting = 'waiting' === $access['status'];

            if (!$access_valid && !$access_waiting) {
                unset($tree[$key]);
            } else {

                $level1_module_or_lesson->set_access_status($access['status']);
                $level1_module_or_lesson->set_inaccessible_lesson_visibility($inaccessible_lesson_visibility);
                $level1_module_or_lesson->set_can_access_lesson($include_following_lessons);
                if ('hidden' === $inaccessible_lesson_visibility && (!$include_following_lessons || !$level1_module_or_lesson->is_access_valid())) {
                    $level1_module_or_lesson->set_is_hidden(true);
                }
                $flat_list[$level1_module_or_lesson->ID] = $level1_module_or_lesson;
                if ($level1_module_or_lesson->is_lesson() || $level1_module_or_lesson->is_test()) {
                    if ($is_progress_forced && isset($course_progress) && !$course_progress->is_lesson_finished($level1_module_or_lesson->ID) && !$user_can_view_all) {
                        $include_following_lessons = false;
                    }
                } else {
                    foreach ($level1_module_or_lesson->get_children() as $level2_lesson) {
                        /** @var Course_Page $level2_lesson */
                        $access = bpmj_eddpc_user_can_access(false, bpmj_eddpc_is_restricted($level2_lesson->ID), $level2_lesson->ID);
                        $access_valid = 'valid' === $access['status'];
                        $access_waiting = 'waiting' === $access['status'];
                        if (!$access_valid && !$access_waiting) {
                            $level1_module_or_lesson->remove_child($level2_lesson->ID);
                        } else {
                            $level2_lesson->set_access_status($access['status']);
                            $level2_lesson->set_inaccessible_lesson_visibility($inaccessible_lesson_visibility);
                            $level2_lesson->set_can_access_lesson($include_following_lessons);
                            if ('hidden' === $inaccessible_lesson_visibility && (!$include_following_lessons || !$level2_lesson->is_access_valid())) {
                                $level2_lesson->set_is_hidden(true);
                            } else {
                            	if ($is_progress_forced && isset($course_progress) && !$course_progress->is_lesson_finished($level2_lesson->ID) && !$user_can_view_all) {
                                    $include_following_lessons = false;
                                }
                            }
                            $flat_list[$level2_lesson->ID] = $level2_lesson;
                        }
                    }
                }
            }
        }
        $this->course_structure_cache[$course_page_id]['tree'] = $tree;
        $this->course_structure_cache[$course_page_id]['flat'] = $flat_list;
    }

    /**
     * @param $module_course_page_id
     *
     * @return Course_Page|null
     */
    public function get_course_structure_module($module_course_page_id)
    {
        $module_course_page = get_post($module_course_page_id);
        if (!$module_course_page instanceof WP_Post) {
            return null;
        }
        $module_course_page_id = $module_course_page->ID;

        $course_page_id = $module_course_page->post_parent;
        $modules = $this->get_course_structure_flat($course_page_id, true, false);
        if (!empty($modules[$module_course_page_id])) {
            return $modules[$module_course_page_id];
        }

        return null;
    }

    /**
     * @param int $course_page_id
     *
     * @return bool|Course_Page
     */
    public function get_first_lesson($course_page_id)
    {
        $lessons = $this->get_course_structure_flat($course_page_id, false);
        if (count($lessons) > 0) {
            return reset($lessons);
        }

        return false;
    }

    /**
     * Returns the sibling of the specified lesson that is offset by $offset positions ($offset might be negative)
     * $offset = +1 means "closest next sibling" and $offset = -1 means "closest previous sibling"
     *
     * @param int $course_page_id
     * @param int $lesson_page_id
     * @param int $offset
     * @param bool $include_hidden
     *
     * @return Course_Page|null
     */
    public function get_sibling_of($course_page_id, $lesson_page_id, $offset, $include_hidden = false)
    {
        $lessons = $this->get_course_structure_flat($course_page_id, false, true, $include_hidden);
        if (empty($lessons[$lesson_page_id])) {
            return null;
        }
        $lesson_ids = array_keys($lessons);
        $key = array_search((int)$lesson_page_id, $lesson_ids);
        if (false === $key) {
            return null;
        }
        $other_lesson_id = isset($lesson_ids[$key + $offset]) ? $lesson_ids[$key + $offset] : false;
        if (false === $other_lesson_id) {
            return null;
        }

        return $lessons[$other_lesson_id];
    }

    /**
     * Get closest next sibling of the given lesson
     *
     * @param int $course_page_id
     * @param int $lesson_page_id
     *
     * @return null|Course_Page
     */
    public function get_next_sibling_of($course_page_id, $lesson_page_id)
    {
        return $this->get_sibling_of($course_page_id, $lesson_page_id, +1);
    }

    /**
     * Get closest previous sibling of the given lesson
     *
     * @param int $course_page_id
     * @param int $lesson_page_id
     *
     * @return bool|Course_Page
     */
    public function get_previous_sibling_of($course_page_id, $lesson_page_id)
    {
        return $this->get_sibling_of($course_page_id, $lesson_page_id, -1);
    }

    /**
     * @param int $post_id
     *
     * @return int
     */
    public function get_course_top_page($post_id)
    {
        $parents = get_post_ancestors($post_id);

        return is_array($parents) && !empty($parents) ? end($parents) : $post_id;
    }

    public function get_all_products_ids($per_page = null, $skip = null, $order_by = null, $order = 'ASC')
    {
        global $wpdb;

        if ('title' === $order_by) {
            $order_by = 'post_title';
        }

        $offset          = !empty($skip) ? "OFFSET {$skip}" : '';
        $limit           = !empty($per_page) ? "LIMIT {$per_page}" : '';
        $order_by_string = !empty($order_by) ? "ORDER BY p.{$order_by} {$order}" : '';

        if ('price' === $order_by) {
            $order_by_string = '';
            $limit = '';
            $offset = '';
        }

        if ('random' === $order_by) {
            $order_by_string = "ORDER BY RAND()";
        }

        $query = "
			SELECT p.ID
			FROM {$wpdb->posts} p 
			WHERE p.post_type = 'download'
				AND p.post_status = 'publish'
				-- select only if the product should be visible on a list
				AND NOT EXISTS (SELECT 1 
									FROM {$wpdb->postmeta} pm 
									WHERE pm.post_id = p.ID 
										AND pm.meta_key = 'hide_from_lists'
										AND pm.meta_value = 'on')
			{$order_by_string}
			{$limit}
			{$offset}
		";

        $all_product_ids = wp_list_pluck($wpdb->get_results($query), 'ID');

        return $all_product_ids;
    }

    /**
     *
     * @param int $course_page_id
     * @param bool $include_hidden
     *
     * @return Course_Page[]
     */
    public function get_all_modules($course_page_id, $include_hidden = false)
    {
        return $this->get_course_structure_flat($course_page_id, true, false, $include_hidden);
    }

    /**
     *
     * @param int $course_page_id
     * @param bool $include_hidden
     *
     * @return Course_Page[]
     */
    public function get_all_lessons($course_page_id, $include_hidden = false)
    {
        return $this->get_course_structure_flat($course_page_id, false, true, $include_hidden);
    }

    /**
     * @param string $post_link
     * @param WP_Post $post
     * @param bool $leavename
     *
     * @return mixed|null|string
     * @internal param string $preview_link
     */
    public function modify_course_link($post_link, $post, $leavename = false)
    {
        $course_page_id = get_post_meta($post->ID, 'course_id', true);
        if (!$course_page_id) {
            return $post_link;
        }

        return get_permalink($course_page_id, $leavename);
    }

    /**
     * @param $post_ID
     *
     * @return array
     */
    public function create_course_options_array($post_ID, $product_id = null)
    {
        if ( is_null( $product_id ) ) {
            $product_id = get_post_meta($post_ID, 'product_id', true);
        }

        $variable_prices = edd_get_variable_prices($product_id);

        return array(
            'price' => get_post_meta($post_ID, 'price', true),
            'sale_price' => get_post_meta($post_ID, 'sale_price', true),
            'sale_price_from_date' => get_post_meta($post_ID, 'sale_price_from_date', true),
            'sale_price_from_hour' => get_post_meta($post_ID, 'sale_price_from_hour', true),
            'sale_price_to_date' => get_post_meta($post_ID, 'sale_price_to_date', true),
            'sale_price_to_hour' => get_post_meta($post_ID, 'sale_price_to_hour', true),
            'variable_sale_price_from_date' => get_post_meta($post_ID, 'variable_sale_price_from_date', true),
            'variable_sale_price_from_hour' => get_post_meta($post_ID, 'variable_sale_price_from_hour', true),
            'variable_sale_price_to_date' => get_post_meta($post_ID, 'variable_sale_price_to_date', true),
            'variable_sale_price_to_hour' => get_post_meta($post_ID, 'variable_sale_price_to_hour', true),
            'access_time' => get_post_meta($post_ID, 'access_time', true),
            'access_time_unit' => get_post_meta($post_ID, 'access_time_unit', true),
            'drip_value' => get_post_meta($post_ID, 'drip_value', true),
            'drip_unit' => get_post_meta($post_ID, 'drip_unit', true),
            'redirect_page' => get_post_meta($post_ID, 'redirect_page', true),
            'redirect_url' => get_post_meta($post_ID, 'redirect_url', true),
            'modules' => get_post_meta($post_ID, 'module', true),
            'product_id' => $product_id,
            'course_id' => get_post_meta($post_ID, 'course_id', true),
            'access_start' => get_post_meta($post_ID, 'access_start', true),
            'recurring_payments' => get_post_meta($post_ID, 'recurring_payments_enabled', true),
            'variable_pricing' => get_post_meta($post_ID, 'variable_pricing', true),
            'variable_prices' => !empty($variable_prices) ? $variable_prices : [],
            'default_price_id' => !empty($variable_prices) ? edd_get_default_variable_price($product_id) : false,
            'purchase_limit' => get_post_meta($product_id, '_bpmj_eddcm_purchase_limit', true),
            'purchase_limit_items_left' => get_post_meta($product_id, '_bpmj_eddcm_purchase_limit_items_left', true),
            'gtu' => $product_id ? Product_Meta_Helper::get_gtu_as_string($product_id) : Gtu::NO_GTU,
            'certificate_template_id' => get_post_meta($post_ID, 'certificate_template_id', true),
            'custom_purchase_link' => $product_id ? Custom_Purchase_Links_Helper::get_custom_purchase_link_as_string($product_id) : Custom_Purchase_Links_Helper::NO_CUSTOM_PURCHASE_LINK,
            'invoices_vat_rate' => Product_Meta_Helper::get_invoices_vat_rate($product_id),
        );
    }

    /**
     * @param $meta_key
     *
     * @return bool
     */
    private function meta_key_should_be_cloned($meta_key)
    {
        // we copy all not hidden meta fields...
        if ('_' !== substr($meta_key, 0, 1)) {
            // ...minus some blacklisted ones...
            if (in_array($meta_key, array(
                'edd_download_files',
            ))) {
                return false;
            }

            return true;
        }
        // ...plus some whitelisted hidden ones
        if (in_array($meta_key, array(
            '_thumbnail_id',
        ))) {
            return true;
        }
        foreach (apply_filters('bpmj_eddcm_clone_course_meta_prefixes', array()) as $meta_key_prefix) {
            if (0 === strpos($meta_key, $meta_key_prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array|mixed $meta_value
     * @param bool $slash
     *
     * @return array|mixed
     */
    private function prepare_cloned_meta_value($meta_value, $slash = false)
    {
        // if it's a one-element array get the value of that single element
        $value = is_array($meta_value) && 1 === count($meta_value) ? reset($meta_value) : $meta_value;
        if (is_array($value)) {
            $result = array_map('maybe_unserialize', $value);
        } else {
            $result = maybe_unserialize($value);
        }
        if ($slash) {
            return wp_slash($result);
        }

        return $result;
    }


    /**
     * @param int $user_id
     * @param bool $include_waiting
     *
     * @return array
     */
    public function get_users_accessible_courses($user_id = null, $include_waiting = false)
    {
        if (!$user_id) {
            if (!is_user_logged_in()) {
                return array();
            }
            $user_id = get_current_user_id();
        }

        if (user_can($user_id, 'manage_options') || user_can($user_id, Caps::CAP_MANAGE_PRODUCTS)) {
            // Admins can access all courses by default
            $all_course_products = get_posts(array(
                'post_type' => 'download',
                'posts_per_page' => -1,
                'post_status' => 'publish',
            ));

            $users_products = array();
            if ($all_course_products) {
                /** @var WP_Post $product */
                foreach ($all_course_products as $product) {
                    $users_products[] = $product->ID;
                }
            }
        } else {
            $users_products = BPMJ_EDDPC_User_Access::instance()->get_user_product_list($user_id);
        }

        $user_courses = array();
        foreach ($users_products as $product_id) {
            $restricted_to = array(array('download' => $product_id));
            $course = WPI()->courses->get_course_by_product($product_id);
            if (false === $course) {
                continue;
            }
            $course_id = $course->ID;
            $course_page_id = bpmj_eddpc_cached_get_post_meta($course_id, 'course_id');
            // Filter courses which the user has access to
            $access = bpmj_eddpc_user_can_access($user_id, $restricted_to, $course_page_id);
            if ('valid' === $access['status'] || ($include_waiting && 'waiting' === $access['status'])) {
                $course = array(
                    'id' => $course_id,
                    'title' => bpmj_eddpc_cached_get_the_title($course_id),
                    'status' => bpmj_eddpc_cached_get_post_status($course_id),
                    'url' => bpmj_eddpc_cached_get_permalink($course_id),
                    'access' => $access['status'],
                    'product_id' => $product_id,
                    'page_id' => $course_page_id,
                );
                $user_courses[] = $course;
            }
        }

        return $user_courses;
    }

    public function get_certificates($certificates_per_page = null, $paged = null)
    {
        $certificates_data = [
            'post_type' => 'certificates',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ];

        if (!is_null($certificates_per_page)) {
            $certificates_data['posts_per_page'] = $certificates_per_page;
            $certificates_data['paged'] = $paged;
        }

        return get_posts($certificates_data);
    }

    /**
     * Get all created courses
     *
     * @param array $statuses
     *
     * @return array
     */
    public function get_courses($statuses = array('publish'))
    {
        global $post;

        $old_global_post = $post;
        $courses = array();
        $args = array(
            'post_type' => 'courses',
            'posts_per_page' => -1,
            'post_status' => $statuses
        );

        $q = new WP_Query($args);

        if ($q->have_posts()):
            while ($q->have_posts()): $q->the_post();
                $courses[] = array(
                    'id' => get_the_id(),
                    'title' => get_the_title(),
                    'status' => get_post_status(),
                    'url' => get_permalink(),
                );

            endwhile;
        endif;

        wp_reset_postdata();
        $GLOBALS['post'] = $old_global_post;

        return $courses;
    }

    /**
     * @param int $payment_id
     *
     * @return WP_Post[]
     */
    public function get_courses_by_payment($payment_id)
    {
        $cart_details = edd_get_payment_meta_cart_details($payment_id);
        $courses = array();

        if (is_array($cart_details)) {
            foreach ($cart_details as $download) {
                if ('bundle' === edd_get_download_type($download['id'])) {
                    $bundled_products = edd_get_bundled_products($download['id']);
                    if (!empty($bundled_products) && is_array($bundled_products)) {
                        foreach ($bundled_products as $bundled_product_id) {
                            $course = $this->get_course_by_product($bundled_product_id);
                            if ($course instanceof WP_Post) {
                                $courses[$course->ID] = $course;
                            }
                        }
                    }
                } else {
                    $course = $this->get_course_by_product($download['id']);
                    if ($course instanceof WP_Post) {
                        $courses[$course->ID] = $course;
                    }
                }
            }
        }

        return $courses;
    }

    /**
     * @param int $course_id
     *
     * @return array
     */
    public function get_users_that_had_access_to_the_course($course_id)
    {
        $product_id = get_post_meta($course_id, 'product_id', true);
        $user_ids = BPMJ_EDDPC_User_Access::instance()->get_product_user_list($product_id);

        return $user_ids;
    }

    /**
     * @param int $course_id
     *
     * @return array
     */
    public function get_course_participants($course_id)
    {
        $product_id = get_post_meta($course_id, 'product_id', true);
        $data = BPMJ_EDDPC_User_Access::instance()->get_product_user_stats($product_id);

        return array(
            'all' => empty($data['all_users_count']) ? 0 : $data['all_users_count'],
            'active' => empty($data['access_valid_count']) ? 0 : $data['access_valid_count'],
            'inactive' => isset($data['all_users_count']) && isset($data['access_valid_count']) ? $data['all_users_count'] - $data['access_valid_count'] : 0,
        );
    }

    /**
     * @return array
     */
    public function get_access_time_units()
    {
        return array(
            'minutes' => __('Minutes', BPMJ_EDDCM_DOMAIN),
            'hours' => __('Hours', BPMJ_EDDCM_DOMAIN),
            'days' => __('Days', BPMJ_EDDCM_DOMAIN),
            'months' => __('Months', BPMJ_EDDCM_DOMAIN),
            'years' => __('Years', BPMJ_EDDCM_DOMAIN)
        );
    }

    /**
     * @param $unit_key
     *
     * @return string
     */
    public function get_access_time_unit($unit_key)
    {
        $units = $this->get_access_time_units();
        if (isset($units[$unit_key])) {
            return $units[$unit_key];
        }

        if (isset($units['days'])) return $units['days'];

        return null;
    }

    /**
     * @param int $course_id
     * @param int $user_id
     * @param int $payment_id
     */
    public function add_course_participant($course_id, $user_id, $payment_id)
    {
        $participants = get_post_meta($course_id, '_bpmj_eddcm_participants', true);
        if (!is_array($participants)) {
            $participants = array();
        }
        if (empty($participants[$user_id])) {
            $participants[$user_id] = array();
        }
        if (!in_array($payment_id, $participants[$user_id])) {
            $participants[$user_id][] = $payment_id;
        }
        update_post_meta($course_id, '_bpmj_eddcm_participants', $participants);

        $this->events->emit(Event_Name::STUDENT_ENROLLED_IN_COURSE, $course_id, $user_id);

    }

    /**
     * @param int $course_id
     * @param int $user_id
     * @param int $payment_id
     */
    public function remove_course_participant($course_id, $user_id, $payment_id)
    {
        $participants = get_post_meta($course_id, '_bpmj_eddcm_participants', true);
        if (!empty($participants[$user_id]) && is_array($participants[$user_id]) && false !== ($payment_key = array_search($payment_id, $participants[$user_id]))) {
            unset($participants[$user_id][$payment_key]);
            if (empty($participants[$user_id])) {
                // clear completely participation of this user in this course
                unset($participants[$user_id]);
            } else {
                // reset numeric keys
                $participants[$user_id] = array_values($participants[$user_id]);
            }
            update_post_meta($course_id, '_bpmj_eddcm_participants', $participants);
        }
    }

    /**
     * @param int $course_id
     * @param string $option_label
     * @param string $default
     *
     * @return string
     * @internal param $default
     */
    protected function get_core_course_setting($course_id, $option_label, $default)
    {
        global $wpidea_settings;

        $option = '';
        if ($course_id) {
            $option = get_post_meta($course_id, $option_label, true);
        }
        if (!$option) {
            $option = isset($wpidea_settings[$option_label]) ? $wpidea_settings[$option_label] : $default;
        }

        return $option;
    }

    /**
     * @param int $course_id
     *
     * @return string
     */
    public function get_inaccessible_lesson_display_mode($course_id)
    {
        return $this->get_core_course_setting($course_id, 'inaccessible_lesson_display', 'visible');
    }

    /**
     * @param int $course_id
     * @param int $course_page_id
     *
     * @return bool
     */
    public function get_is_progress_forced($course_id, $course_page_id = null)
    {
        $default = 'disabled';
        if (WPI()->packages->no_access_to_feature(Packages::FEAT_PROGRESS_TRACKING)) {
            return false;
        }

        if (!$course_page_id) {
            $course_page_id = get_post_meta($course_id, 'course_id', true);
        }
        $progress = new Course_Progress($course_page_id, null, false);
        if (!$progress->is_tracking_enabled()) {
            return false;
        }

        return in_array($this->get_core_course_setting($course_id, 'progress_forced', $default), ['enabled', 'on']);
    }

    /**
     * @param int $course_id
     * @param int|null $product_id
     *
     * @return array
     */
    public function get_sales_status($course_id, $product_id = null)
    {
        $sales_disabled = get_post_meta($course_id, 'sales_disabled', true);
        if ('on' === $sales_disabled) {
            return array(
                'status' => 'disabled',
                'reason' => __('Sales disabled', BPMJ_EDDCM_DOMAIN),
                'reason_long' => __('Sales of this course are currently disabled.', BPMJ_EDDCM_DOMAIN),
            );
        }

        if (!$product_id) {
            $product_id = get_post_meta($course_id, 'product_id', true);
        }

        $purchase_limit = (int)get_post_meta($product_id, '_bpmj_eddcm_purchase_limit', true);
        $purchase_limit_items_left = (int)get_post_meta($product_id, '_bpmj_eddcm_purchase_limit_items_left', true);
        $purchase_limit_unlimited = '1' === get_post_meta($product_id, '_bpmj_eddcm_purchase_limit_unlimited', true);

        if ($purchase_limit > 0 && $purchase_limit_items_left <= 0 && !$purchase_limit_unlimited) {
            return array(
                'status' => 'disabled',
                'reason' => __('Sold out', BPMJ_EDDCM_DOMAIN),
                'reason_long' => __('No more items can be purchased at this time.', BPMJ_EDDCM_DOMAIN),
            );
        }

        return array('status' => 'enabled');
    }

    /**
     *
     * @param int $course_id
     * @param array|null $roles
     *
     * @return array
     * @see count_users()
     */
    public function get_course_user_role_stats($course_id, array $roles = null)
    {
        global $wpdb;

        $product_id = (int)get_post_meta($course_id, 'product_id', true);
        if (!$product_id) {
            return array();
        }

        $id = get_current_blog_id();
        $blog_prefix = $wpdb->get_blog_prefix($id);
        $result = array();

        if (is_null($roles)) {
            $roles = array_keys(wp_roles()->get_names());
        }

        $select_count = array();
        foreach ($roles as $this_role) {
            $select_count[] = $wpdb->prepare("COUNT(NULLIF(`meta_value` LIKE %s, false))", '%' . $wpdb->esc_like('"' . $this_role . '"') . '%');
        }
        $select_count[] = "COUNT(NULLIF(`meta_value` = 'a:0:{}', false))";
        $select_count[] = "COUNT(1)";
        $select_count = implode(', ', $select_count);

        $wpdb->query('SET SQL_BIG_SELECTS=1');
        $query = "
			SELECT 
			    $select_count
			FROM
			    (" . BPMJ_EDDPC_User_Access::get_base_query() . ") t JOIN {$wpdb->usermeta} um ON um.user_id = t.user_id AND um.meta_key = '{$blog_prefix}capabilities'
		    WHERE
                t.product_id = " . $product_id;
        $row = $wpdb->get_row($query, ARRAY_N);

        // Run the previous loop again to associate results with role names.
        $col = 0;
        $role_counts = array();
        foreach ($roles as $this_role) {
            $count = (int)$row[$col++];
            if ($count > 0) {
                $role_counts[$this_role] = $count;
            }
        }

        $role_counts['none'] = (int)$row[$col++];

        // Get the meta_value index from the end of the result set.
        $total_users = (int)$row[$col];

        $result['total_users'] = $total_users;
        $result['avail_roles'] = $role_counts;

        if (is_multisite()) {
            $result['avail_roles']['none'] = 0;
        }

        return $result;
    }

    /**
     * @param int $post_id
     *
     * @return bool
     */
    public function user_shouldnt_have_access_to_course_page($post_id)
    {
        $course_id = get_post_meta($post_id, '_bpmj_eddcm', true);
        if ($course_id) {
            $ancestors = get_post_ancestors($post_id);
            $top = ($ancestors) ? $ancestors[count($ancestors) - 1] : $post_id;
            $course_accessible_pages = WPI()->courses->get_course_structure_flat($top);
            $has_access = false;

            if (isset($course_accessible_pages[$post_id])) {
                $course_page = $course_accessible_pages[$post_id];
                if (!$course_page->should_be_grayed_out()) {
                    $has_access = true;
                }
            }

            return !$has_access;
        }

        return false;
    }

    /**
     *
     */
    public function add_to_cart()
    {
        if (empty($_GET['add-to-cart'])) {
            return;
        }

        EDD()->session->set('bpmj_eddcm_gift', false);

        $gift = !empty($_GET['gift']);
        if ($gift) {
            EDD()->session->set('bpmj_eddcm_gift', true);
        }

        $price_id = isset($_GET['price-id']) ? (int)$_GET['price-id'] : 0;
        $product = edd_get_download((int)$_GET['add-to-cart']);
        if (!$product || false !== bpmj_eddcm_is_item_in_cart($product->ID, $price_id)) {
            return;
        }
        $product_id = $product->ID;

        $has_variable_prices = edd_has_variable_prices($product_id);

        if ($price_id && !$has_variable_prices || !$price_id && $has_variable_prices) {
            return;
        }

        if ($has_variable_prices) {
            $variable_prices = edd_get_variable_prices($product_id);
            if (!isset($variable_prices[$price_id]))
                return;
        }

        if ($price_id && false !== ($cart_key = bpmj_eddcm_is_item_in_cart($product_id))) {
            edd_remove_from_cart($cart_key);
        }

        $discount = isset($_GET['discount']) ? $_GET['discount'] : '';
        $quantity = !empty($_GET['quantity']) ? (int)$_GET['quantity'] : 1;

        $options = array(
            'quantity' => $quantity,
        );
        if ($price_id) {
            $options['price_id'] = $price_id;
        }
        $did_action = did_action('edd_post_add_to_cart');
        edd_add_to_cart($product_id, $options);
        if ($did_action === did_action('edd_post_add_to_cart')) {
            // Nothing has been added for some reason
            return;
        }

        // At this point we are sure the product has been added to cart successfully
        if ($discount && edd_is_discount_valid( $discount, User::getCurrentUserId() ) ) {
            edd_set_cart_discount($discount);
        }

        $_SESSION['buy-by-purchase-link'] = true;

        $this->events->emit(Event_Name::PRODUCT_ADDED_TO_CART_FROM_LINK, (int)$product_id, $options);

        wp_redirect(remove_query_arg(array('add-to-cart', 'discount', 'quantity', 'price-id', 'gift')));
        edd_die();
    }


    /**
     * @param string $value
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get_thousands_separator($value, $key, $default)
    {
        global $edd_options;
        $value = isset($edd_options[$key]) ? $edd_options[$key] : $default;

        return $value;
    }

    /**
     * @return int|null
     */
    public function get_courses_menu()
    {
        $menu_terms = get_terms(array(
            'taxonomy' => 'nav_menu',
            'meta_key' => 'bpmj_eddcm_menu',
            'meta_value' => '1',
        ));
        if (!empty($menu_terms)) {
            return $menu_terms[0]->term_id;
        }

        $menu = get_term_by('name', 'WP Idea menu', 'nav_menu');
        if ($menu && !is_wp_error($menu)) {
            update_term_meta($menu->term_id, 'bpmj_eddcm_menu', '1');

            return $menu->term_id;
        }

        return null;
    }

    public function check_if_lesson_is_part_of_course($lesson_id, $course_id)
    {
        $l = get_post($lesson_id);
        if (empty($l)) {
            return false;
        }

        $course_id = (int)$course_id;
        return $this->get_course_top_page($l->ID) === $course_id;
    }

    /**
     * Checks if course has active sheduled sale
     *
     * @param int $id post id
     * @return boolean true if sale is active, false otherwise
     */
    public function is_sheduled_sale_active($id)
    {
        $sale_price_from_date = get_post_meta($id, 'sale_price_from_date', true);
        $sale_price_from_hour = get_post_meta($id, 'sale_price_from_hour', true);
        $sale_price_to_date = get_post_meta($id, 'sale_price_to_date', true);
        $sale_price_to_hour = get_post_meta($id, 'sale_price_to_hour', true);

        $variable_sale_price_from_date = get_post_meta($id, 'variable_sale_price_from_date', true);
        $variable_sale_price_from_hour = get_post_meta($id, 'variable_sale_price_from_hour', true);
        $variable_sale_price_to_date = get_post_meta($id, 'variable_sale_price_to_date', true);
        $variable_sale_price_to_hour = get_post_meta($id, 'variable_sale_price_to_hour', true);

        $product_id = get_post_meta($id, 'product_id', true);

        $started = false;
        $ended = false;

        if (edd_has_variable_prices($product_id)) {
            if (!empty($variable_sale_price_from_date)) {
                $date_from = date_create_from_format('Y-m-d H', $variable_sale_price_from_date . ' ' . $variable_sale_price_from_hour);
                if ($date_from != false && bpmj_eddpc_adjust_timestamp(time()) >= $date_from->getTimestamp()) {
                    $started = true;
                }
            } else {
                $started = true;
            }

            if (!empty($variable_sale_price_to_date)) {
                $date_to = date_create_from_format('Y-m-d H', $variable_sale_price_to_date . ' ' . $variable_sale_price_to_hour);
                if ($date_to != false && bpmj_eddpc_adjust_timestamp(time()) >= $date_to->getTimestamp()) {
                    $ended = true;
                }
            }
        } else {

            if (!empty($sale_price_from_date)) {
                $date_from = date_create_from_format('Y-m-d H', $sale_price_from_date . ' ' . $sale_price_from_hour);
                if ($date_from != false && bpmj_eddpc_adjust_timestamp(time()) >= $date_from->getTimestamp()) {
                    $started = true;
                }
            } else {
                $started = true;
            }

            if (!empty($sale_price_to_date)) {
                $date_to = date_create_from_format('Y-m-d H', $sale_price_to_date . ' ' . $sale_price_to_hour);
                if ($date_to != false && bpmj_eddpc_adjust_timestamp(time()) >= $date_to->getTimestamp()) {
                    $ended = true;
                }
            }

        }

        return $started && !$ended;
    }

    /**
     * Checks if course has any content attached
     *
     * @param int $course_id
     *
     * @return boolean true if course has content, false otherwise
     */

    public function course_has_content($course_id)
    {
        $modules = get_post_meta($course_id, 'module', true);
        if (empty($modules))
            return false;

        return true;
    }

    public function get_page_id_by_course_id($course_id)
    {
        return get_post_meta($course_id, 'course_id', true);
    }
}
