<?php
/**
 *
 * The class responsible for edit course page
 *
 */
namespace bpmj\wpidea\admin;

use bpmj\wpidea\admin\helpers\html\Explanation_Popup;use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;use bpmj\wpidea\admin\pages\course_editor\Course_Editor_Page_Renderer;use bpmj\wpidea\admin\settings\Settings_Const;use bpmj\wpidea\admin\tables\simple\Simple_Table;use bpmj\wpidea\certificates\Certificate_Template;use bpmj\wpidea\certificates\Interface_Certificate_Repository;use bpmj\wpidea\Course_Progress;use bpmj\wpidea\events\Event_Name;use bpmj\wpidea\events\Interface_Events;use bpmj\wpidea\Helper;use bpmj\wpidea\helpers\Translator_Static_Helper;use bpmj\wpidea\learning\course\Course_ID;use bpmj\wpidea\learning\course\Interface_Readable_Course_Repository;use bpmj\wpidea\Packages;use bpmj\wpidea\routing\Interface_Url_Generator;use bpmj\wpidea\sales\product\core\event\Event_Name as Product_Event_Name;use bpmj\wpidea\sales\product\Custom_Purchase_Links_Helper;use bpmj\wpidea\sales\product\Flat_Rate_Tax_Symbol_Helper;use bpmj\wpidea\sales\product\Invoice_Tax_Payer_Helper;use bpmj\wpidea\sales\product\Meta_Helper as Product_Meta_Helper;use bpmj\wpidea\sales\product\model\Gtu;use bpmj\wpidea\settings\Interface_Settings;use bpmj\wpidea\Templates;use bpmj\wpidea\templates_system\admin\modules\Interface_Templates_System_Modules_Factory;use bpmj\wpidea\templates_system\admin\modules\settings_handlers\Interface_Templates_Settings_Handler;use bpmj\wpidea\templates_system\Templates_System;use bpmj\wpidea\translator\Interface_Translator;use bpmj\wpidea\user\Interface_User_Repository;use bpmj\wpidea\user\User_ID;use bpmj\wpidea\View;use bpmj\wpidea\wolverine\product\Repository as ProductRepository;use BPMJ_EDD_Sell_Discount_Product_Metabox;use WP_Post;use WP_User_Query;

if ( !defined( 'ABSPATH' ) )
	{exit;}

class Edit_Course {

    const SAVE_METABOX_PRIORITY = 1;
    private const NUMBERING_PATTERN = 'X / YYYY';

	/**
	 * @var StdClass
	 */
	public $options;

	var $commissions;

	private Interface_Templates_Settings_Handler$template_settings_handler;

	private Templates_System $templates_system;

    private Interface_Certificate_Repository $certificate_repository;

    private Interface_User_Repository $user_repository;

    private Interface_Readable_Course_Repository $course_repository;

	private Interface_Events $events;

    private Explanation_Popup $explanation_popup;

    private Interface_Translator $translator;
    
    protected Interface_Settings $settings;

    private Interface_Url_Generator $url_generator;

	function __construct(
	        Interface_Templates_System_Modules_Factory $templates_system_modules_factory,
	        Templates_System $templates_system,
	        Interface_Certificate_Repository $certificate_repository,
	        Interface_User_Repository $user_repository,
	        Interface_Readable_Course_Repository $course_repository,
	        Interface_Events $events,
	        Explanation_Popup $explanation_popup,
	        Interface_Translator $translator,
    	    Interface_Settings $settings,
    	    Interface_Url_Generator $url_generator
    ) {
	    $this->template_settings_handler = $templates_system_modules_factory->get_settings_handler();
	    $this->templates_system = $templates_system;
        $this->certificate_repository = $certificate_repository;
        $this->user_repository = $user_repository;
        $this->course_repository = $course_repository;
        $this->events = $events;
        $this->explanation_popup = $explanation_popup;
        $this->translator = $translator;
        $this->settings = $settings;
        $this->url_generator = $url_generator;

        $this->init();
    }

	public function init() {

		// Remove useless metaboxes
		add_action( 'admin_head', array( $this, 'remove_meta_box' ), 0 );

		// Get all options to variable
		add_action( 'admin_head', array( $this, 'get_options' ) );

		// Remove content editor if editing a module
		//add_action( 'admin_head', array( $this, 'remove_content_editor_for_module' ) );

		// Add new meta boxes
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ), 1 );

		// Save all things
		add_action( 'save_post', array( $this, 'save_lesson_options' ), 10, 2 );
		add_action( 'save_post', array( $this, 'save_test_questions' ), 10, 2 );

		add_action( 'admin_init', array( $this, 'redirect_on_download_page' ) );

		// commissions settings and class
		add_action( 'admin_init', array( $this, 'init_commissions' ) );

	}

	/**
	 * @param int $product_id
	 *
	 * @return string
	 */
	public static function get_add_to_cart_popup_html( $product_id ) {
		$variable_prices = edd_get_variable_prices( $product_id );
		$variable_prices_enabled = get_post_meta( $product_id, '_variable_pricing', true );
		if ( $variable_prices_enabled === '1' && ! empty( $variable_prices ) && is_array( $variable_prices ) ) {
			return static::get_variable_prices_add_to_cart_links_html( $product_id, $variable_prices );
		}
		ob_start();
		?>
        <label>
			<span class="bpmj-eddcm-add-to-cart-link-copied"><?php _e( 'Copied', BPMJ_EDDCM_DOMAIN ) ?></span><br>
            <input type="text" class="select-on-focus bpmj-eddcm-add-to-cart-link" style="width: 100%;"
                   data-product-id="<?php echo $product_id; ?>"
                   value="<?php echo esc_attr( edd_get_checkout_uri( array(
				       'add-to-cart' => $product_id,
				   ) ) ); ?>"/>
			<span class="bpmj-eddcm-add-to-cart-link-copy"><?php _e( 'Copy', BPMJ_EDDCM_DOMAIN ) ?></span>
        </label>
        <?php
		return ob_get_clean();
	}

    /**
     * @param int $product_id
     *
     * @return string
     */
    public static function get_show_stats_popup_html( $course_id, $force_stats = false ) {

        global $wpidea_settings;

        $product_course_id = get_post_meta( $course_id, 'course_id', true );
        $participants = WPI()->courses->get_course_participants( $course_id );

        if ( 0 == $participants['all'] ) {
            ob_start(); ?>
            <p><?php echo __( 'There are no statistics. No user is assigned to this course.', BPMJ_EDDCM_DOMAIN ); ?></p>
            <?php
            return ob_get_clean();
        }

        $users_query = new WP_User_Query( array(
            'meta_query' => array(
                array(
                    'key'     => '_course_progress_' . $product_course_id,
                    'compare' => 'EXISTS',
                ),
            ),
        ) );

        $module = get_post_meta( $course_id, 'module', true );

        ob_start();
        ?>
        <section class="edd-courses-manager-dashboard">
            <div class="row">
                <div class="full-column">
                    <div class="panel courses no-courses animated fadeInUp">
                        <div class="panel-body no-padding">
                            <table>
                                <thead>
                                <tr>
                                    <th class="title"><?= Translator_Static_Helper::translate('course_list.stats.lesson') ?></th>
                                    <th class="percents"><?= Translator_Static_Helper::translate('course_list.stats.passed') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ( $module as $mod ) {
                                        if ( $mod['mode'] !== 'full' ) {
                                            self::display_lesson_stat( $users_query, $product_course_id, $mod['id'], $participants['all'] );
                                        } else {
                                            if ( ! empty( $mod['module'] ) ) {
                                                foreach ( $mod['module'] as $m )
                                                    {self::display_lesson_stat( $users_query, $product_course_id, $m['id'], $participants['all'] );}
                                            }
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php

        return ob_get_clean();
	}

    public static function display_lesson_stat( $users_query, $product_course_id, $mod_id, $all_participants )
    {
        $users_done_content = [];

        echo '<tr>';

        foreach ( $users_query->get_results() as $user ) {
            $user_progress_for_course = get_user_meta( $user->ID, '_course_progress_' . $product_course_id, true );
            if ( 1 !== $user->ID && is_array($user_progress_for_course) && array_key_exists( $mod_id, $user_progress_for_course ) )
                {$users_done_content[] = $user->ID;}
        }

        $stats_inner_users = '<tr class="course-stats-users"><td colspan="2"><ul>';
        foreach ( $users_done_content as $user ) {
            $u = get_user_by( 'ID', $user );
			$login = apply_filters( 'lms_filter_sensitive__customer_login', $u->user_login, $u->ID );
            $stats_inner_users .= '<li>' . $login . '</li>';
        }
        $stats_inner_users .= '</ul></td></tr>';

        $link = '';
        if ( 0 < count( $users_done_content ) )
            {$link = ' (<a href="#" class="admin-courses-users-stats-toggle">' . __( 'Show who', BPMJ_EDDCM_DOMAIN ) . '</a>)';}

        $percent = round( ( count( $users_done_content ) / $all_participants ) * 100, 0 );
        echo '<td>' . get_the_title( $mod_id ) . '</td><td>' . $percent . '% - ' . count( $users_done_content ) . '/' . $all_participants . $link . '</td>';
        echo '</tr>' . $stats_inner_users;
	}

	public function init_commissions()
	{
		if ( is_plugin_active( 'edd-commissions/edd-commissions.php' ) ) {
			require_once('class-commissions.php');
			$this->commissions = new Commissions();
		}
	}

	/**
	 * Remove content editor if editing a module
	 */
	public function remove_content_editor_for_module() {
		global $post;

		if ( $post instanceof WP_Post && 'full' === get_post_meta( $post->ID, 'mode', true ) ) {
			remove_post_type_support( $post->post_type, 'editor' );
		}
	}

	/**
	 * Get all options
	 */
	public function get_options() {
		global $post;

		if ( $this->is_editting_a_course() ) {
			$this->options = (object) WPI()->courses->create_course_options_array( $post->ID );
		}
	}

	/**
     * Check if the screen currently displays post edit page for a course
     *
	 * @return bool
	 */
	public function is_editting_a_course() {
		global $post, $pagenow;

		return in_array( $pagenow, array(
				'post-new.php',
				'post.php'
			) ) && $post && 'courses' === get_post_type( $post );
	}

	/**
	 * Remove Meta Boxes
	 */
	public function remove_meta_box() {
	    global $post;

		if ( $this->is_editting_a_course() ) {
			echo '<style>.wrap h1:first-child a, #screen-meta-links{ display: none!important; }</style>';
		}

		if ( $post instanceof WP_Post && get_post_meta( $post->ID, '_bpmj_eddcm', true ) ) {
			if ( ! Helper::is_dev() ) {
				remove_meta_box( 'bpmj-eddpc-metabox', array( 'post', 'page' ), 'normal' );
			}
			remove_meta_box( 'pageparentdiv', array( 'post', 'page' ), 'side' );
		}
	}

	/**
     * Redirects the user if she somehow managed to open download edit page (post.php)
	 */
	public function redirect_on_download_page() {
		global $pagenow;
		if ( ! isset( $_GET[ 'post' ] ) ) {
			return;
		}

		$post = get_post( $_GET[ 'post' ] );
		if ( 'download' === get_post_type( $post ) &&
		     'post.php' === $pagenow &&
		     $post instanceof WP_Post &&
		     ! empty( $_GET[ 'action' ] ) &&
		     'edit' === $_GET[ 'action' ]
		) {
			$course = WPI()->courses->get_course_by_product( $post->ID );
			if ( false !== $course ) {
				if ( empty( $_GET[ 'edit_description' ] ) ) {
				    $url = get_edit_post_link( $post->ID, 'redirect' );
				    if (! empty( $url ) ) {
                        wp_redirect( $url );
                        exit;
					} else {
                        wp_die( __( 'Sorry, you are not allowed to access this page.') );
					}
				} else {
					add_action( 'admin_head', array( $this, 'prepare_edit_product_description_page' ), 999 );
				}
			}
		}
	}

	/**
	 * Register meta box(es).
	 */
	public function register_meta_boxes() {
		global $post;

		if( empty( $post ) ) {return;}

		add_thickbox();

        if ( 'courses' === get_post_type( $post ) ) {
			remove_action( 'add_meta_boxes', array( BPMJ_EDD_Sell_Discount_Product_Metabox::instance(), 'add' ) );
		}

		//Dodanie metboxów do lekcji
		if ( 'lesson' === get_post_meta( $post->ID, 'mode', true ) ) {
			add_meta_box( 'bpmj_eddcm_options', __( 'WP Idea Options', BPMJ_EDDCM_DOMAIN ), array( $this, 'metabox_lesson_settings' ), 'page', 'normal', 'high' );
            add_meta_box( 'bpmj_eddcm_edit_course_link', __( 'Useful links', BPMJ_EDDCM_DOMAIN ), array( $this, 'metabox_edit_course_link' ), 'page', 'side', 'default' );
		} else {if ( 'full' === get_post_meta( $post->ID, 'mode', true ) ) {
			add_meta_box( 'bpmj_eddcm_options', __( 'Module options', BPMJ_EDDCM_DOMAIN ), array( $this, 'metabox_module_settings' ), 'page', 'normal', 'high' );
            add_meta_box( 'bpmj_eddcm_edit_course_link', __( 'Useful links', BPMJ_EDDCM_DOMAIN ), array( $this, 'metabox_edit_course_link' ), 'page', 'side', 'default' );
        } else {if ( 'test' === get_post_meta( $post->ID, 'mode', true ) ) {
            add_meta_box( 'bpmj_eddcm_options', __( 'Quiz options', BPMJ_EDDCM_DOMAIN ), array( $this, 'metabox_quiz_settings'), 'page', 'normal', 'high' );
            add_meta_box( 'bpmj_eddcm_questions', __( 'Quiz questions', BPMJ_EDDCM_DOMAIN ), array( $this, 'metabox_quiz_questions'), 'page', 'normal', 'high' );
            add_meta_box( 'bpmj_eddcm_edit_course_link', __( 'Useful links', BPMJ_EDDCM_DOMAIN ), array( $this, 'metabox_edit_course_link' ), 'page', 'side', 'default' );
        }}}

		if ( 'tests' === get_post_type( $post ) ) {
		    add_meta_box( 'bpmj_eddcm_test_answers', __( 'Quiz answers', BPMJ_EDDCM_DOMAIN ), array( $this, 'quiz_answers_meta_box_body' ), 'tests' );
        }
	}

    public function quiz_answers_meta_box_body()
    {
        $questions = get_post_meta( get_the_ID(), 'questions', true );
        $user_answers = get_post_meta( get_the_ID(), 'user_answers', true );
        ?>
        <div class="bpmj-eddcm-cs-section-body">
            <div class="form-group">
                <ul id="bpmj_eddcm_answers_list" class="modules">
                    <?php $i = 0; ?>
                    <?php if ( ! empty( $questions ) ) : ?>
                        <?php foreach ( $questions as $question ) : ?>
                            <li class="module question editor">
                                <div class="question-header">
                                    <input class="eddcm-test-question-id" type="hidden" name="bpmj_eddcm_test_questions[<?php echo $i; ?>][id]" value="<?php echo $question['id']; ?>">
                                    <input class="eddcm-test-question-title" type="text" name="bpmj_eddcm_test_questions[<?php echo $i; ?>][title]" value="<?php echo $question['title']; ?>" disabled>
                                    <select disabled name="bpmj_eddcm_test_questions[<?php echo $i; ?>][type]" class="eddcm-test-question-type">
                                        <option value="single_radio" <?php echo 'single_radio' === $question['type'] ? 'selected' : ''; ?>><?php _e( 'Single choice question', BPMJ_EDDCM_DOMAIN ); ?>&nbsp;(radio)</option>
                                        <option value="single_select" <?php echo 'single_select' === $question['type'] ? 'selected' : ''; ?>><?php _e( 'Single choice question', BPMJ_EDDCM_DOMAIN ); ?>&nbsp;(select)</option>
                                        <option value="multiple" <?php echo 'multiple' === $question['type'] ? 'selected' : ''; ?>><?php _e( 'Multiple choice question', BPMJ_EDDCM_DOMAIN ); ?></option>
                                        <option value="text" <?php echo 'text' === $question['type'] ? 'selected' : ''; ?>><?php _e( 'Text question', BPMJ_EDDCM_DOMAIN ); ?></option>
                                        <option value="file" <?php echo 'file' === $question['type'] ? 'selected' : ''; ?>><?php _e( 'File question', BPMJ_EDDCM_DOMAIN ); ?></option>
                                    </select>
                                </div>
                                <div class="question-body">
                                    <div class="question-type-single-answer-tab question-type-tab">
                                        <ul class="answers">
                                            <?php if ( ( $question['type'] == 'single_radio' || $question['type'] === 'single_select' || $question['type'] === 'multiple' ) && ! empty( $question['answer'] ) ) : ?>
                                                <?php $j = 0; ?>
                                                <?php foreach ( $question['answer'] as $answer ) :
                                                    $answer_type = '';
                                                    if ( 'multiple' === $question['type'] ) {
                                                        if ( isset( $user_answers[ $question['id'] ] ) && in_array( $answer['id'], explode( ',', $user_answers[ $question['id'] ]['answer'] ) ) ) {
                                                            if ($answer['points'] > 0) {
                                                                $answer_type = 'correct';
                                                            } else {
                                                                $answer_type = 'incorrect';
                                                            }
                                                        }
                                                    } else {
                                                        if ( isset( $user_answers[ $question['id'] ]['answer'] ) && $user_answers[ $question['id'] ]['answer'] === $answer['id'] ) {
                                                            if ($answer['points'] > 0) {
                                                                $answer_type = 'correct';
                                                            } else {
                                                                $answer_type = 'incorrect';
                                                            }
                                                        }
                                                    }

                                                    ?>
                                                    <li class="answer">
                                                        <input class="eddcm-test-question-answer-id" type="hidden" name="bpmj_eddcm_test_questions[<?php echo $i; ?>][answer][<?php echo $j; ?>][id]" value="<?php echo $answer['id']; ?>">
                                                        <input disabled class="eddcm-test-question-answer-title" type="text" name="bpmj_eddcm_test_questions[<?php echo $i; ?>][answer][<?php echo $j; ?>][title]" value="<?php echo esc_html( $answer['title'] ); ?>">
                                                        <input disabled class="eddcm-test-question-answer-points points-value" type="number" name="bpmj_eddcm_test_questions[<?php echo $i; ?>][answer][<?php echo $j; ?>][points]" value="<?php echo $answer['points']; ?>">&nbsp;<?php _e( 'Points', BPMJ_EDDCM_DOMAIN ); ?>
                                                        <?php if ( 'correct' === $answer_type ) : ?>
                                                            <span class="bpmj-quiz-result-pass dashicons dashicons-yes"></span>
                                                        <?php elseif ( 'incorrect' === $answer_type ) : ?>
                                                            <span class="bpmj-quiz-result-nopass dashicons dashicons-no-alt"></span>
                                                        <?php endif; ?>
                                                    </li>
                                                    <?php $j++; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    <div class="question-type-text-answer-tab question-type-tab">
                                        <?php if ( ( $question['type'] == 'text' ) ) : ?>
                                            <textarea disabled name=""><?php echo do_shortcode($user_answers[$question['id']]['answer'] ?? ''); ?></textarea>
                                        <?php endif; ?>
                                    </div>
                                    <div class="question-type-file-answer-tab question-type-tab">
                                    <?php if ($question['type'] == 'file' && isset($user_answers[$question['id']]['answer'])) : ?>
                                        <?php
                                            $answer = $user_answers[ $question['id'] ]['answer'];
                                            ?>
                                            <?php if ( is_numeric( $answer ) ) : ?>
                                                <?php
                                                $url = wp_get_attachment_url( $user_answers[ $question['id'] ]['answer'] );
                                                ?>
                                                <a href="<?php echo bpmj_eddpc_encrypt_link( $url ); ?>" target="_blank"><?php echo get_the_title( $user_answers[ $question['id'] ]['answer'] ); ?></a>
                                            <?php else : ?>
                                                <?php echo $answer; ?>
                                            <?php endif; ?>
                                    <?php endif; ?>
                                    </div>
                                </div>
                            </li>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
                <?php
                $points_all = get_post_meta( get_the_ID(), 'test_questions_points_all', true );
                $points = get_post_meta( get_the_ID(), 'points', true );
                $is_passed = get_post_meta( get_the_ID(), 'is_passed', true );
                ?>
                <div class="pass-condition">
                    <p>
                        <label for="points-input"><?php _e( 'Points', BPMJ_EDDCM_DOMAIN ); ?></label>
                        <input id="points-input" type="number" min="0" max="<?php echo $points_all; ?>" value="<?php echo $points; ?>" name="points"> / <span id="pass-condition-points"><?php echo $points_all; ?></span>
                    </p>
                    <p>
                        <label for="mark-as-passed"><?php _e( 'Mark this quiz as:', BPMJ_EDDCM_DOMAIN ); ?></label>
                        <select name="quiz_is_passed">
                            <option value=""><?php _e( 'Select...', BPMJ_EDDCM_DOMAIN ); ?></option>
                            <option value="yes" <?php echo ( 'yes' === $is_passed ) ? 'selected' : ''; ?>><?php _e( 'Passed', BPMJ_EDDCM_DOMAIN ); ?></option>
                            <option value="no" <?php echo ( 'no' === $is_passed ) ? 'selected' : ''; ?>><?php _e( 'Failed', BPMJ_EDDCM_DOMAIN ); ?></option>
                        </select>
                    </p>
                </div>
            </div>
        </div>
        <?php
	}

	/**
	 * Main WP Idea metabox
	 *
	 * @param WP_Post $post
	 * @param string $box
	 */
	public function metabox_wpidea( WP_Post $post, $box ) {
		$renderer = new Tabbed_Content();
		$renderer
			->add_section( __( 'Modules / Lessons', BPMJ_EDDCM_DOMAIN ), array(
				$this,
				'metabox_modules'
			), 'dashicons-list-view' )
			->add_section( __( 'Section settings', BPMJ_EDDCM_DOMAIN ), array(
				$this,
				'metabox_sections'
			), 'dashicons-exerpt-view' )
			->add_section( __( 'Product settings', BPMJ_EDDCM_DOMAIN ), array(
				$this,
				'metabox_product_settings'
			), 'dashicons-cart' )
			->add_section( __( 'View options', BPMJ_EDDCM_DOMAIN ), array(
				$this,
				'metabox_view_settings'
			), 'dashicons-visibility' )
			->add_section( __( 'Link generator', BPMJ_EDDCM_DOMAIN ), array(
				$this,
				'metabox_add_to_cart_link'
			), 'dashicons-admin-links' );
		if ( $this->settings->get(Settings_Const::INVOICES_ENABLED) ) {
			$renderer->add_section( __( 'Invoices', BPMJ_EDDCM_DOMAIN ), array(
				$this,
				'metabox_invoice_settings'
			), 'dashicons-media-text' );
		}
		if ( WPI()->diagnostic->mailer_integration() ) {
			$renderer->add_section( __( 'Mailers', BPMJ_EDDCM_DOMAIN ), array(
				$this,
				'metabox_mailer_settings'
			), 'dashicons-email-alt' );
		}
		if ( apply_filters( 'bpmj_edd_sell_discount_enabled', true ) ) {
			$renderer->add_section( __( 'Discount code', BPMJ_EDDCM_DOMAIN ), array(
				BPMJ_EDD_Sell_Discount_Product_Metabox::instance(),
				'html'
			), 'dashicons-tag' );
		}
		$renderer->render( $post, $box );
	}

	/**
	 * @param WP_Post $post
	 * @param string $box
	 */
	public function metabox_sections( $post, $box ) {
		$this->metabox_main_section( $post, $box );
		$this->metabox_other_sections( $post, $box );
	}

	/**
	 * Metabox z ustawieniami pierwszej sekcji:
	 * czy wyświetlać,
	 * embed video
	 *
	 * @param WP_Post $object
	 * @param string $box
	 */
	public function metabox_main_section( $object, $box ) {
		?>
		<div id="bpmj_eddcm_options">

			<?php if ( WPI()->templates->is_feature_supported( Templates::FEATURE_COURSE_WELCOME_BANNER ) ): ?>
                <div class="form-group">
                    <label for="bpmj-wpidea-course-banner"><?php _e( 'Course banner', BPMJ_EDDCM_DOMAIN ); ?></label>
					<?php
					$banner_options = array(
						'name'           => 'banner',
						'label'          => __( 'Course banner', BPMJ_EDDCM_DOMAIN ),
						'type'           => 'file',
						'size'           => 'regular',
						'button_class'   => 'btn-eddcm btn-eddcm-primary',
						'save_to'        => 'bpmj_wpidea',
						'explicit_value' => get_post_meta( $object->ID, 'banner', true ),
					);
					WPI()->settings->settings_api->output_field( $banner_options );
					WPI()->settings->settings_api->script_file( false );
					?>
                </div>
			<?php endif; ?>

            <div class="form-group">
                <input type="hidden" name="bpmj_wpidea[first_section]" value="off">
                <input type="checkbox" style="float:left;"
                       name="bpmj_wpidea[first_section]" <?php if ( get_post_meta( $object->ID, 'first_section', true ) != 'off' ) {
					echo 'checked="checked"';
				} ?>>
                <label for="subtitle"><?php _e( 'Show the main section', BPMJ_EDDCM_DOMAIN ); ?></label>
                <div class="desc bpmj-eddcm-autotip"><?php _e( "Select the checkbox to show the first section.", BPMJ_EDDCM_DOMAIN ); ?></div>
            </div>

			<?php if ( WPI()->templates->is_feature_supported( Templates::FEATURE_COURSE_WELCOME_VIDEO ) ): ?>
            <div class="form-group">
                <label for="subtitle"><?php _e( 'Video', BPMJ_EDDCM_DOMAIN ); ?></label>
                <span class="input-group-addon">
					<input type="hidden" name="bpmj_wpidea[video_mode]" value="off">
					<input type="checkbox"
                           name="bpmj_wpidea[video_mode]" <?php if ( get_post_meta( $object->ID, 'video_mode', true ) == 'on' ) {
						echo 'checked="checked"';
					} ?>>
				</span>
                <input <?php if ( get_post_meta( $object->ID, 'video_mode', true ) != 'on' ) {
					echo 'disabled="disabled"';
				} ?> type="text" class="input-group" id="subtitle" name="bpmj_wpidea[video]"
                     value="<?php echo esc_attr( get_post_meta( $object->ID, 'video', true ) ); ?>">
                <div class="desc bpmj-eddcm-autotip"><?php _e( "You can show the video on your course panel page<br>YouTube, Vimeo, etc.", BPMJ_EDDCM_DOMAIN ); ?></div>
            </div>
			<?php endif; ?>

        </div>
		<?php
	}

	/**
	 * Metabox z ustawieniami kolejnych sekcji
	 *
	 * @param WP_Post $object
	 * @param string $box
	 */
	public function metabox_other_sections( $object, $box ) {
		?>
		<div id="bpmj_eddcm_options">

			<?php if ( WPI()->templates->is_feature_supported( Templates::FEATURE_COURSE_SECOND_SECTION ) ): ?>
			<div class="form-group">
				<input type="hidden" name="bpmj_wpidea[second_section]" value="off">
				<input type="checkbox" style="float:left;" name="bpmj_wpidea[second_section]" <?php if ( get_post_meta( $object->ID, 'second_section', true ) == 'on' ) {echo 'checked="checked"';} ?>>
				<label for="subtitle"><?php _e( 'Show the second section', BPMJ_EDDCM_DOMAIN ); ?></label>
				<div class="desc bpmj-eddcm-autotip"><?php _e( "Select the checkbox to show the second section.", BPMJ_EDDCM_DOMAIN ); ?></div>
			</div>

			<div class="form-group">
				<label for="subtitle"><?php _e( 'Title of the second section', BPMJ_EDDCM_DOMAIN ); ?></label>
				<span class="input-group-addon">
					<input type="hidden" name="bpmj_wpidea[second_section_title_mode]" value="off">
					<input type="checkbox" name="bpmj_wpidea[second_section_title_mode]" <?php if ( get_post_meta( $object->ID, 'second_section_title_mode', true ) == 'on' ) {echo 'checked="checked"';} ?>>
				</span>
                <input <?php if ( get_post_meta( $object->ID, 'second_section_title_mode', true ) != 'on' ) {
					echo 'disabled="disabled"';
				} ?> type="text" class="input-group" id="subtitle" name="bpmj_wpidea[second_section_title]"
                     value="<?php echo esc_attr( get_post_meta( $object->ID, 'second_section_title', true ) ); ?>">
				<div class="desc bpmj-eddcm-autotip"><?php _e( "Title of your second section", BPMJ_EDDCM_DOMAIN ); ?></div>
			</div>

			<div class="form-group">
				<label for="subtitle"><?php _e( 'Content of the second section', BPMJ_EDDCM_DOMAIN ); ?></label>
				<?php wp_editor( get_post_meta( $object->ID, 'second_section_content', true ), 'second_section_content', array( 'wpautop' => false, 'quicktags' => false, 'textarea_name' => 'bpmj_wpidea[second_section_content]', ) ); ?>
				<div class="desc bpmj-eddcm-autotip"><?php _e( 'This text will show up in the second section.', BPMJ_EDDCM_DOMAIN ); ?></div>
                <div class="desc"><?php _e( 'You can use <b>[wpi_btn href="http://your.domain" title="Sample title"]</b> to create a button.', BPMJ_EDDCM_DOMAIN ); ?></div>
			</div>
			<?php endif ?>

            <?php if(!$this->templates_system->is_new_templates_system_enabled()): ?>
			<div class="form-group">
				<input type="hidden" name="bpmj_wpidea[last_section]" value="off">
				<input type="checkbox" style="float:left;" name="bpmj_wpidea[last_section]" <?php if ( get_post_meta( $object->ID, 'last_section', true ) != 'off' ) {echo 'checked="checked"';} ?>>
				<label for="subtitle"><?php _e( 'Show the last section', BPMJ_EDDCM_DOMAIN ); ?></label>
				<div class="desc bpmj-eddcm-autotip"><?php _e( "Select the checkbox to show the last section - list of all lessons.", BPMJ_EDDCM_DOMAIN ); ?></div>
			</div>
            <?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Meta box display invoice settings
	 */
	public function metabox_invoice_settings( $post = false ) {

		$invoice_methods = array(
			'wp-fakturownia' => array(
				'label' => 'Fakturownia',
				'name'  => 'bpmj_wpfa',
				'gtu_supported' => true
			),
			'wp-ifirma'      => array(
				'label' => 'iFirma',
				'name'  => 'bpmj_wpifirma',
				'gtu_supported' => true
			),
			'wp-wfirma'      => array(
				'label' => 'wFirma',
				'name'  => 'bpmj_wpwf',
				'gtu_supported' => false
			),
			'wp-infakt'      => array(
				'label' => 'Infakt',
				'name'  => 'bpmj_wpinfakt',
				'gtu_supported' => true
			),
			'wp-taxe'        => array(
				'label' => 'Taxe',
				'name'  => 'bpmj_wptaxe',
				'gtu_supported' => true
			)
		);


		$variable_pricing = $this->options->variable_pricing ?? false;
		$flat_rate_is_enabled = Flat_Rate_Tax_Symbol_Helper::is_enabled();
        $single_flat_rate_style = ($variable_pricing || !$flat_rate_is_enabled) ? 'style="display: none;"' : '';
        $variable_flat_rate_style = ($variable_pricing && $flat_rate_is_enabled) ? '' : 'style="display: none;"';

		?>

		<div class="form-group" <?php echo $single_flat_rate_style; ?>>
		    <label for="bpmj_wpidea[flat_rate_tax_symbol]"><?=$this->translator->translate('invoices.flat_rate_tax_symbol'); ?></label>
			<?php
			$flat_rate_tax_value = '';

			if ( $post !== false ) {
			    $flat_rate_tax_value = Product_Meta_Helper::get_flat_rate_tax_symbol( is_numeric( $post ) ? $post : $post->ID );
			}

			$this->field_flat_rate_tax_symbol($flat_rate_tax_value);
			?>
			<p><?=$this->translator->translate('invoices.warning_flat_rate_tax_not_supported'); ?></p>
		</div>


        <div class="form-group" <?php echo $variable_flat_rate_style; ?>>
            <label><?=$this->translator->translate('invoices.flat_rate_tax_symbol'); ?></label>
			<?php $this->field_flat_rate_tax_symbol_for_variable_prices($this->options->variable_prices ?? []); ?>
			<p><?=$this->translator->translate('invoices.warning_flat_rate_tax_not_supported'); ?></p>
		</div>
		<?php
        if ( Invoice_Tax_Payer_Helper::is_enabled() ) : ?>

        	<?php

			$invoices_vat_rate_value = ! is_null( $this->options ) ? $this->options->invoices_vat_rate : '';

			?>

            <div class="form-group">
                <label for="invoices-vat-rate"><?php echo $this->translator->translate('invoices.vat_rate'); ?></label>
                <input type="text" name="invoices_vat_rate" id="invoices-vat-rate" placeholder="<?php echo $this->translator->translate('invoices.vat_rate'); ?>" class="half_width" value="<?php echo $invoices_vat_rate_value; ?>">
                <div class="desc"><?= $this->translator->translate('invoices.vat_rate.empty');  ?> <b><?php echo Invoice_Tax_Payer_Helper::get_default_vat_rate(); ?>%</b>.</div>
            </div>

        <?php
        endif;

		if(!$post) {
		    return;
		}

		$gtu_not_supported_for = [];

		foreach ( $invoice_methods as $slug => $method ) {
			if ( WPI()->diagnostic->is_integration_enabled( $slug ) ) {
			    if(!$method['gtu_supported']) {
			        $gtu_not_supported_for[] = $method['label'];
			    }
			}
		}

		if(!$post) {
		    return;
		}

		if(!empty($gtu_not_supported_for)) {
		    echo '<div class="form-group"><p>';
		    _e('Warning! GTU via API is not supported for:', BPMJ_EDDCM_DOMAIN);
		    echo ' ' . implode(', ', $gtu_not_supported_for) . '</p></div>';
		}



		$single_price_style    = $variable_pricing ? 'style="display: none;"' : '';
		$variable_prices_style = $variable_pricing ? '' : 'style="display: none;"';

		?>

		<div class="form-group" <?php echo $single_price_style; ?>>
		    <label for="bpmj_wpidea[gtu]"><?php _e( 'GTU code', BPMJ_EDDCM_DOMAIN ) ?></label>
			<?php $this->field_gtu($this->options->gtu ?? ''); ?>
		</div>

        <div class="form-group" <?php echo $variable_prices_style; ?>>
            <label><?php _e( 'GTU code', BPMJ_EDDCM_DOMAIN ) ?></label>
			<?php $this->field_gtu_for_variable_prices($this->options->variable_prices ?? []); ?>
		</div>
		<?php
	}

	protected function field_flat_rate_tax_symbol(string $flat_rate_tax_symbol, string $name = 'bpmj_wpidea[flat_rate_tax_symbol]')
	{
	    	?>
        <select name="<?= $name ?>" id="<?= $name ?>">
            <option value=""><?=$this->translator->translate('invoices.no_flat_rate_tax'); ?></option>
            <?php foreach(Flat_Rate_Tax_Symbol_Helper::AVAILABLE_TAX_SYMBOLS as $available_tax): ?>
                <?php
                $selected = '';

                if ( $available_tax === $flat_rate_tax_symbol ) {
                    $selected = 'selected="selected" ';
                }
                ?>
                <option <?php echo $selected; ?>value="<?php echo $available_tax; ?>"><?php echo strtoupper($available_tax); ?></option>
            <?php endforeach; ?>
        </select>
		<?php
	}

	protected function field_flat_rate_tax_symbol_for_variable_prices(array $variable_prices): void
    {
        $items = [];
        foreach ( $variable_prices as $price_id => $variable_price ) {
            ob_start();
                $this->field_flat_rate_tax_symbol($variable_price['flat_rate_tax_symbol'] ?? '', "variable_prices[" . $price_id . "][flat_rate_tax_symbol]");
            $field_html = ob_get_clean();

            $items[] = [
                $variable_price[ 'name' ],
                $field_html
            ];
        }

        Simple_Table::create('flat_rate_tax_symbols-list')
            ->add_header(__( 'Variant', BPMJ_EDDCM_DOMAIN ))
            ->add_header(__( 'Flat rate tax symbol', BPMJ_EDDCM_DOMAIN ))
            ->set_items($items)
            ->print();
	}

    protected function field_gtu(string $gtu, string $name = 'bpmj_wpidea[gtu]'): void
    {
		?>
        <select name="<?php echo $name; ?>" id="<?php echo $name; ?>">
            <option value=""><?php _e('No GTU code', BPMJ_EDDCM_DOMAIN); ?></option>
            <?php foreach(Gtu::AVAILABLE_CODES as $available_code): ?>
                <?php
                $selected = '';

                if ( $available_code === $gtu ) {
                    $selected = 'selected="selected" ';
                }
                ?>
                <option <?php echo $selected; ?>value="<?php echo $available_code; ?>"><?php echo strtoupper($available_code); ?></option>
            <?php endforeach; ?>
        </select>
		<?php
	}

	protected function field_gtu_for_variable_prices(array $variable_prices): void
    {
        $items = [];
        foreach ( $variable_prices as $price_id => $variable_price ) {
            ob_start();
                $this->field_gtu($variable_price['gtu'] ?? '', "variable_prices[" . $price_id . "][gtu]");
            $field_html = ob_get_clean();

            $items[] = [
                $variable_price[ 'name' ],
                $field_html
            ];
        }

        Simple_Table::create('gtu-codes-list')
            ->add_header(__( 'Variant', BPMJ_EDDCM_DOMAIN ))
            ->add_header(__( 'GTU code', BPMJ_EDDCM_DOMAIN ))
            ->set_items($items)
            ->print();
	}

/**
	 * Meta box display invoice settings
	 */
	public function metabox_mailer_settings( $post_id = false ) {

		if ( ! WPI()->packages->has_access_to_feature( Packages::FEAT_MAILERS ) ) {
			?>
            <section>
                <h2><?php _e( 'Upgrade needed', BPMJ_EDDCM_DOMAIN ); ?></h2>
                <div class="form-group">
					<?php echo WPI()->packages->feature_not_available_message( Packages::FEAT_MAILERS, __( 'In order to be allowed to use mailer systems integration, you need to upgrade your license to level: "%s"', BPMJ_EDDCM_DOMAIN ) ); ?>
                </div>
            </section>
			<?php
			return;
		}

		$mailer_integrations = array(
			'edd-mailchimp'      => array(
				'label'  => 'MailChimp',
				'inputs' => array(
					array(
						'name' => '_edd_mailchimp',
						'type' => 'multiselect',
						'desc' => __( 'Select the lists you wish buyers to be subscribed to when purchasing.', BPMJ_EDDCM_DOMAIN ),
					),
				),
			),
			'edd-mailerlite'     => array(
				'label'  => 'MailerLite',
				'inputs' => array(
					array(
						'name' => '_edd_mailerlite',
						'type' => 'checkbox',
						'desc' => __( 'Select the lists you wish buyers to be subscribed to when purchasing.', BPMJ_EDDCM_DOMAIN ),
					),
				),
			),
			'edd-freshmail'      => array(
				'label'  => 'FreshMail',
				'inputs' => array(
					array(
						'name' => '_edd_freshmail',
						'type' => 'checkbox',
						'desc' => __( 'Select the lists you wish buyers to be subscribed to when purchasing.', BPMJ_EDDCM_DOMAIN ),
					),
				),
			),
			'edd-ipresso'        => array(
				'label'  => 'iPresso',
				'inputs' => array(
					array(
						'name'  => '_edd_ipresso',
						'type'  => 'tags',
						'desc1' => __( 'Specify tags (separated by commas) that will be added to contacts in iPresso upon completed purchase.', BPMJ_EDDCM_DOMAIN ),
						'desc2' => __( 'Specify tags (separated by commas) that will be removed from contacts in iPresso on completed purchase.', BPMJ_EDDCM_DOMAIN ),
					),
				),
			),
			'edd-activecampaign' => array(
				'label'  => 'ActiveCampaign',
				'inputs' => array(
					array(
						'name'          => '_edd_activecampaign',
						'type'          => 'checkbox-double',
						'desc1'         => __( 'Select the lists you wish buyers to be <b>subscribed</b> to, when purchasing.', BPMJ_EDDCM_DOMAIN ),
						'desc2'         => __( 'Select the lists you wish buyers to be <b>unsubscribed</b> from, when purchasing.', BPMJ_EDDCM_DOMAIN ),
						'delete_suffix' => 'unsubscribe',
					),
					array(
						'name'  => '_edd_activecampaign_tags',
						'type'  => 'tags',
						'desc1' => __( 'Specify tags (separated by commas) that will be added to contacts in ActiveCampaign upon completed purchase.', BPMJ_EDDCM_DOMAIN ),
						'desc2' => __( 'Specify tags (separated by commas) that will be removed from contacts in ActiveCampaign upon completed purchase.', BPMJ_EDDCM_DOMAIN ),
					),
				),
			),
			'edd-getresponse'    => array(
				'label'  => 'GetResponse',
				'inputs' => array(
					array(
						'name'          => '_edd_getresponse',
						'type'          => 'checkbox-double',
						'desc1'         => __( 'Select the lists you wish buyers to be <b>subscribed</b> to when purchasing.', BPMJ_EDDCM_DOMAIN ),
						'desc2'         => __( 'Select the lists you wish buyers to be <b>unsubscribed</b> from when purchasing.', BPMJ_EDDCM_DOMAIN ),
						'delete_suffix' => 'unsubscribe',
					),
					array(
						'name'      => '_edd_getresponse_tags',
						'type'      => 'checkbox',
						'list_type' => 'tags',
						'desc'      => __( /** @lang text */
							'Select the tags you wish buyers to be added to when purchasing.', BPMJ_EDDCM_DOMAIN ),
					),
				),
			),
			'edd-salesmanago'    => array(
				'label'  => 'SALESmanago',
				'inputs' => array(
					array(
						'name' => '_bpmj_edd_sm_tags',
						'type' => 'tags',
						'desc' => 'Wpisz tagi (oddzielając je przecinkiem), które mają być dodane do kontaktu w panelu SALESmanago po zakupie tego produktu.',
					),
				),
			),
			'edd-interspire'     => array(
				'label'  => 'Interspire',
				'inputs' => array(
					array(
						'name' => '_edd_interspire',
						'type' => 'checkbox',
						'desc' => __( 'Select the lists you wish buyers to be subscribed to when purchasing.', BPMJ_EDDCM_DOMAIN ),
					),
				),
			),
			'edd-convertkit'     => array(
				'label'  => 'ConvertKit',
				'inputs' => array(
					array(
						'name'          => '_edd_convertkit',
						'type'          => 'checkbox',
						'desc'         => __( /** @lang text */
							'Select the forms you wish buyers to be subscribed to when purchasing.', BPMJ_EDDCM_DOMAIN ),
					),
					array(
						'name'          => '_edd_convertkit_tags',
						'type'          => 'checkbox-double',
						'list_type'     => 'tags',
						'desc1'         => __( /** @lang text */
							'Select the tags you wish buyers to be added to when purchasing.', BPMJ_EDDCM_DOMAIN ),
						'desc2'         => __( /** @lang text */
							'Select the tags you wish buyers to be removed from when purchasing.', BPMJ_EDDCM_DOMAIN ),
						'delete_suffix' => 'unsubscribe',
					),
				),
			),
		);

		$post_id = is_numeric( $post_id ) ? $post_id : ( isset( $this->options ) ? $this->options->product_id : null );

		foreach ( $mailer_integrations as $mailer_slug => $mailer ) {
			if ( WPI()->diagnostic->is_integration_enabled( $mailer_slug ) ) {
				?>
				<section class="mailer">
					<h2><?php echo $mailer[ 'label' ]; ?></h2>
					<?php
					foreach ( $mailer[ 'inputs' ] as $input ) {

						$value         = get_post_meta( $post_id, $input[ 'name' ], true );
						$list_type     = isset( $input[ 'list_type' ] ) ? $input[ 'list_type' ] : 'list';
						$delete_suffix = isset( $input[ 'delete_suffix' ] ) ? $input[ 'delete_suffix' ] : 'delete';
						?>
						<div class="form-group" style="clear: both;">

							<?php
							switch ( $input[ 'type' ] ) {

							    case 'multiselect':

							        $lists = bpmj_wpid_get_mailer_data( $mailer_slug, $list_type );

							        ?>
							        <div class="wpi-mailing-lists" data-mailer="<?= $mailer_slug; ?>">
							            <?php if ( ! empty( $value) ) : ?>
                                            <?php foreach ($value as $val) : ?>
                                                <div class="wpi-mailing-list" data-mailer="<?= $mailer_slug; ?>">
                                                    <select name="<?= $input[ 'name' ]; ?>[]" autocomplete="off">
                                                        <option hidden disabled selected value><?= __( 'Select list or group', BPMJ_EDDCM_DOMAIN ); ?></option>
                                                        <?php foreach ( $lists as $list_id => $list_value ) : ?>
                                                            <?php $checked = $list_id === $val ? ' selected' : ''; ?>
                                                            <option value="<?= $list_id; ?>"<?= $checked; ?>><?= $list_value; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <a href="#" class="wpi-remove-mailing-list"><span class="dashicons dashicons-no"></span></a>
                                                </div>
                                            <?php endforeach; ?>
							            <?php endif; ?>
                                    </div>

							        <button class="wpi-add-mailing-list btn-eddcm btn-eddcm-primary" data-mailer="<?= $mailer_slug; ?>"><?= __('Add next', BPMJ_EDDCM_DOMAIN ); ?></button>

							        <div class="wpi-mailing-list-template wpi-mailing-list" data-mailer="<?= $mailer_slug; ?>">
                                        <select data-name="<?= $input[ 'name' ]; ?>[]" autocomplete="off">
                                            <option hidden disabled selected value><?= __( 'Select list or group', BPMJ_EDDCM_DOMAIN ); ?></option>
                                            <?php foreach ( $lists as $list_id => $list_value ) : ?>
                                                <option value="<?= $list_id; ?>"><?= $list_value; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <a href="#" class="wpi-remove-mailing-list"><span class="dashicons dashicons-no"></span></a>
                                    </div>

							        <?php

							        break;

								case 'checkbox':
									$lists = bpmj_wpid_get_mailer_data( $mailer_slug, $list_type );
									if ( is_array( $lists ) && ! empty( $lists ) ) {
										foreach ( $lists as $id => $list ) {
											$checked = is_array( $value ) && in_array( $id, $value ) ? 'checked="checked"' : '';
											echo '<label for="' . $mailer_slug . '_' . $id . '" class="checkbox"><input type="checkbox" value="' . $id . '" name="' . $input[ 'name' ] . '[]" id="' . $mailer_slug . '_' . $id . '" ' . $checked . '> ' . $list . '</label>';
										}
									} else {
										printf( __( 'Invalid %s configuration or lists are not created.', BPMJ_EDDCM_DOMAIN ), isset( $input[ 'label' ] ) ? $input[ 'label' ] : $input[ 'name' ] );
									}
									echo '<div class="desc">' . $input[ 'desc' ] . '</div>';
									break;


								case 'checkbox-double':
									$lists     = bpmj_wpid_get_mailer_data( $mailer_slug, $list_type );
									$value_del = get_post_meta( $post_id, $input[ 'name' ] . '_' . $delete_suffix, true );

									if ( is_array( $lists ) && ! empty( $lists ) ) {

										// To add
										echo '<div class="half-width">';
										foreach ( $lists as $id => $list ) {
											if ( $id != 'none' ) {
												$checked = is_array( $value ) && in_array( $id, $value ) ? 'checked="checked"' : '';
												echo '<label class="checkbox"><input type="checkbox" value="' . $id . '" id="checkbox-double-' . $input[ 'name' ] . '-' . $id . '" data-pair="checkbox-double-' . $input[ 'name' ] . '_' . $delete_suffix . '-' . $id . '" class="checkbox-double" name="' . $input[ 'name' ] . '[]" ' . $checked . '> ' . $list . '</label>';
											}
										}
										echo '<div class="desc">' . $input[ 'desc1' ] . '</div>';
										echo '</div>';


										// To delete
										echo '<div class="half-width">';
										foreach ( $lists as $id => $list ) {
											if ( $id != 'none' ) {
												$checked = is_array( $value_del ) && in_array( $id, $value_del ) ? 'checked="checked"' : '';
												echo '<label class="checkbox"><input type="checkbox" value="' . $id . '" id="checkbox-double-' . $input[ 'name' ] . '_' . $delete_suffix . '-' . $id . '" data-pair="checkbox-double-' . $input[ 'name' ] . '-' . $id . '" class="checkbox-double" name="' . $input[ 'name' ] . '_' . $delete_suffix . '[]" ' . $checked . '> ' . $list . '</label>';
											}
										}
										echo '<div class="desc">' . $input[ 'desc2' ] . '</div>';
										echo '</div>';
									} else {
										printf( __( 'Invalid %s configuration or lists are not created.', BPMJ_EDDCM_DOMAIN ), isset( $input[ 'label' ] ) ? $input[ 'label' ] : $input[ 'name' ] );
									}
									break;


								case 'tags':
									if ( ! empty( $input[ 'desc' ] ) || ! empty( $input[ 'desc1' ] ) ) {
										echo '<input type="text" name="' . $input[ 'name' ] . '" class="bpmj_eddcm_tags" value="' . $value . '">';
										echo '<div class="desc">' . ( empty( $input[ 'desc' ] ) ? $input[ 'desc1' ] : $input[ 'desc' ] ) . '</div>';
									}
									if ( ! empty( $input[ 'desc2' ] ) ) {
										$value_unsubscribe = get_post_meta( $post_id, $input[ 'name' ] . '_unsubscribe', true );
										echo '<input type="text" name="' . $input[ 'name' ] . '_unsubscribe" class="bpmj_eddcm_tags" value="' . $value_unsubscribe . '">';
										echo '<div class="desc">' . $input[ 'desc2' ] . '</div>';
									}
									break;


								case 'text':
									echo '<input type="text" name="' . $input[ 'name' ] . '" class="half_width" value="' . $value . '">';
									echo '<div class="desc">' . $input[ 'desc' ] . '</div>';
									break;
							}
							?>
						</div>
						<?php
					}
					?>
				</section>
				<?php
			}
		}
	}

	/**
	 * Użyteczne linki
	 */
	public function metabox_useful_links( $post ) {
		?>
		<div class="useful-links">
			<a href="<?php echo get_permalink( $this->options->course_id ); ?>" class="btn-eddcm btn-eddcm-default"><?php _e( 'View Course', BPMJ_EDDCM_DOMAIN ); ?></a>
		</div>
		<?php
	}

	/**
	 * Commissions
	 */
	public function metabox_commissions_settings( $post ) {
		?>
		<div class="commissions-settings">
			<?php// echo $this->commissions->render_metabox($post); ?>
		</div>
		<?php
	}

	/**
	 * Adds a "select on focus" input with add to cart link for this product
	 *
	 * @param mixed $post
	 */
	public function metabox_add_to_cart_link( $post ) {
		$single_price_style    = $this->options->variable_pricing ? 'style="display: none;"' : '';
		$variable_prices_style = $this->options->variable_pricing ? '' : 'style="display: none;"';
		?>
		<div class="bpmj_eddcm_options bpmj-eddcm-add-to-cart-link-creator">
			<div class="form-group">
				<div class="bpmj-eddcm-add-to-cart-link-creator-help">
					<?php _e( 'Using the order link generator you can prepare a link that automatically adds this product to the cart after proceeding to it. This link can be used on any page (e.g., on your product landing page) and can also contain a promo code that will be applied.', BPMJ_EDDCM_DOMAIN ) ?>
				</div>
			</div>

			<?php
            $count = wp_count_posts('edd_discount');

            if ($count->active > 100) : ?>
                <datalist id="bpmj-eddcm-add-to-cart-discount-list"></datalist>
            <?php else : ?>
                <datalist id="bpmj-eddcm-add-to-cart-discount-list">
                    <?php
                    $discounts = edd_get_discounts( [ 'post_status' => [ 'active' ], 'posts_per_page' => - 1, 'no_found_rows' => true, ] );
                    $discounts = !empty($discounts) ? $discounts : [];

                    foreach ( $discounts as $discount ): ?>
                        <option value="<?php echo esc_attr( edd_get_discount_code( $discount->ID ) ); ?>"></option>
                        <?php
                    endforeach;
                    ?>
                </datalist>
            <?php endif; ?>
            
			<div class="form-group">
				<label for="bpmj-eddcm-add-to-cart-link-base"
				       style="display: block;font-size: 16px;margin-bottom: 5px;padding: 0;"><?php _e( 'Base URL', BPMJ_EDDCM_DOMAIN ) ?></label>
				<input type="text" id="bpmj-eddcm-add-to-cart-link-base"
				       name="atc_link_base"
				       value="<?php echo esc_attr( edd_get_checkout_uri() ); ?>"/>
				<label for="bpmj-eddcm-add-to-cart-link-discount"
				       style="display: block;font-size: 16px;margin-bottom: 5px;padding: 0;"><?php _e( 'Discount code', BPMJ_EDDCM_DOMAIN ) ?></label>
				<input type="text" name="atc_discount" list="bpmj-eddcm-add-to-cart-discount-list"
				       id="bpmj-eddcm-add-to-cart-link-discount" value=""/>
				<?php
				if ( edd_item_quantities_enabled() ):
					?>
					<label for="bpmj-eddcm-add-to-cart-link-quantity"
					       style="display: block;font-size: 16px;margin-bottom: 5px;padding: 0;"><?php _e( 'Quantity', BPMJ_EDDCM_DOMAIN ) ?></label>
					<input type="number" name="atc_quantity" id="bpmj-eddcm-add-to-cart-link-quantity" value="1"/>
				<?php
				endif;
				?>
				<?php
				if ( WPI()->packages->has_access_to_feature( Packages::FEAT_BUY_AS_GIFT ) ): ?>
					<label>
						<?php _e( 'Buy as a gift?', BPMJ_EDDCM_DOMAIN ); ?>
						<input type="checkbox" name="atc_gift" id="bpmj-eddcm-add-to-cart-link-gift"/>
					</label>
				<?php
				endif;
				?>
			</div>
			<div class="form-group bpmj-eddcm-single-price" <?php echo $single_price_style; ?>>
				<label for="bpmj-eddcm-add-to-cart-link"><?php _e( 'Add to cart link', BPMJ_EDDCM_DOMAIN ) ?><span class="bpmj-eddcm-add-to-cart-link-copied"><?php _e( 'Copied', BPMJ_EDDCM_DOMAIN ) ?></span><br></label>
				<input type="text" id="bpmj-eddcm-add-to-cart-link" class="select-on-focus bpmj-eddcm-add-to-cart-link"
				       style="width: 100%;"
				       data-product-id="<?php echo esc_attr( $this->options->product_id ); ?>"
					   value="<?php echo esc_attr( edd_get_checkout_uri() . '?add-to-cart=' . $this->options->product_id ); ?>"/>
				<span class="bpmj-eddcm-add-to-cart-link-copy"><?php _e( 'Copy', BPMJ_EDDCM_DOMAIN ) ?></span>
			</div>
			<div id="bpmj-eddcm-variable-prices-add-to-cart-links"
			     class="form-group bpmj-eddcm-variable-prices" <?php echo $variable_prices_style; ?>>
				<?php
				$variable_price = is_array($this->options->variable_prices) ? $this->options->variable_prices : [];
				echo static::get_variable_prices_add_to_cart_links_html( $this->options->product_id, $variable_price);
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * @param int $product_id
	 * @param array $variable_prices
	 *
	 * @return string
	 */
	public static function get_variable_prices_add_to_cart_links_html($product_id, array $variable_prices) {
		ob_start();
		foreach ( $variable_prices as $price_id => $variable_price ):
			?>
			<label>
				<?php echo __( 'Price', BPMJ_EDDCM_DOMAIN ) . ' ' . esc_html( $variable_price[ 'name' ] ); ?><span class="bpmj-eddcm-add-to-cart-link-copied"><?php _e( 'Copied', BPMJ_EDDCM_DOMAIN ) ?></span><br>
				<input type="text" class="select-on-focus bpmj-eddcm-add-to-cart-link" style="width: 100%;"
				       data-product-id="<?php echo $product_id; ?>"
				       data-price-id="<?php echo $price_id; ?>"
				       value="<?php echo esc_attr( edd_get_checkout_uri( array(
					       'add-to-cart' => $product_id,
					       'price-id'    => $price_id,
				       ) ) ); ?>"/>
				<span class="bpmj-eddcm-add-to-cart-link-copy"><?php _e( 'Copy', BPMJ_EDDCM_DOMAIN ) ?></span>
			</label><br><br>
			<?php
		endforeach;

		return ob_get_clean();
	}

	/**
	 * Meta box display product settings.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function metabox_product_settings( $post ) {
		global $wpidea_settings;
		echo '<input type="hidden" name="courses_manager_meta_noncename" id="courses_manager_meta_noncename" value="' . wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';

		$access_time_units = WPI()->courses->get_access_time_units();

		$no_access_to_access_start     = WPI()->packages->no_access_to_feature( Packages::FEAT_COURSE_ACCESS_START );
		$no_access_to_access_time      = WPI()->packages->no_access_to_feature( Packages::FEAT_COURSE_ACCESS_TIME );

		$single_price_style    = $this->options->variable_pricing ? 'style="display: none;"' : '';
		$variable_prices_style = $this->options->variable_pricing ? '' : 'style="display: none;"';

		//var_dump( get_post_meta($post->ID) );
		$post_type = get_post_type_object('download');
		?>
		<div class="form-group">
			<a href="<?php echo admin_url( sprintf( $post_type->_edit_link, $this->options->product_id ) ) . '&amp;action=edit&amp;edit_description=1'; ?>"
			   class="btn-eddcm btn-eddcm-primary"><span
					class="dashicons dashicons-welcome-write-blog"></span> <?php _e( 'Edit product description', BPMJ_EDDCM_DOMAIN ); ?>
			</a>
		</div>
		<div class="form-group">
			<?php $this->field_variable_pricing_checkbox( $this->options->variable_pricing ); ?>
		</div>

		<div class="form-group bpmj-eddcm-variable-prices" <?php echo $variable_prices_style; ?>>
			<?php $this->field_variable_pricing_table( $post->ID, $this->options->variable_prices, $this->options->default_price_id ); ?>
		</div>

        <div class="form-group bpmj-eddcm-variable-prices" <?php echo $variable_prices_style; ?>>
            <?php $this->field_sale_price_from( $this->options->variable_sale_price_from_date, $this->options->variable_sale_price_from_hour, 'variable_sale_price_from_date', 'variable_sale_price_from_hour' ); ?>
            <?php $this->field_sale_price_to( $this->options->variable_sale_price_to_date, $this->options->variable_sale_price_to_hour, 'variable_sale_price_to_date', 'variable_sale_price_to_hour' ); ?>
        </div>

        <div class="form-group bpmj-eddcm-single-price" <?php echo $single_price_style; ?>>
	        <?php $this->field_price( $this->options->price, __( 'How much does your course cost?', BPMJ_EDDCM_DOMAIN ) ); ?>
        </div>

        <div class="form-group bpmj-eddcm-single-price" <?php echo $single_price_style; ?>>
	        <?php $this->field_sale_price( $this->options->sale_price, __( 'What\'s the discounted price for your course? Leave blank if none.', BPMJ_EDDCM_DOMAIN ) ); ?>
            <?php $this->field_sale_price_from( $this->options->sale_price_from_date, $this->options->sale_price_from_hour ); ?>
            <?php $this->field_sale_price_to( $this->options->sale_price_to_date, $this->options->sale_price_to_hour ); ?>
        </div>

        <div class="form-group bpmj-eddcm-single-price" <?php echo $single_price_style; ?>>
            <label for="access_time"><?php _e( 'Access Time', BPMJ_EDDCM_DOMAIN ); ?></label>

            <input type="number" step="1" name="access_time" id="access_time" class="a_third_width"
                   value="<?php echo $this->options->access_time; ?>" <?php disabled( $no_access_to_access_time ); ?>>

            <select name="access_time_unit"
                    class="" <?php disabled( $no_access_to_access_time ); ?>>
				<?php
				foreach ( $access_time_units as $unit => $name ) {
					echo '<option value="' . $unit . '" ' . selected( $unit, $this->options->access_time_unit, false ) . '>' . $name . '</option>';
				}
				?>
            </select>

            <div class="desc bpmj-eddcm-autotip"><?php _e( 'How long user will be able to use course?<br>Leave blank to set unlimited access time.', BPMJ_EDDCM_DOMAIN ); ?></div>
	        <?php if ( $no_access_to_access_time ): ?>
                <div class="desc text-danger">
                    <strong><?php _e( 'Upgrade needed', BPMJ_EDDCM_DOMAIN ); ?>:</strong>
			        <?php echo WPI()->packages->feature_not_available_message( Packages::FEAT_COURSE_ACCESS_TIME, __( 'In order to set variable prices for a course, you need to upgrade your license to level: "%s"', BPMJ_EDDCM_DOMAIN ) ); ?>
                </div>
	        <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="drip_value"><?php _e( 'Redirect Page', BPMJ_EDDCM_DOMAIN ); ?></label>

            <div class="desc bpmj-eddcm-autotip"><?php _e( 'When no access, redirect to a specific page:', BPMJ_EDDCM_DOMAIN ); ?></div>
            <select name="redirect_page" class="a_third_width">
                <option value="none"><?php _e( 'Choose page...', BPMJ_EDDCM_DOMAIN ); ?></option>
				<?php
				$all_pages = get_posts( array( 'post_type' => 'page', 'posts_per_page' => -1 ) );
				foreach ( $all_pages as $page ) {
					echo '<option value="' . $page->ID . '" ' . selected( $page->ID, $this->options->redirect_page, true ) . '>' . $page->post_title . '</option>';
				}
				?>
            </select>

            <span class="desc"><?php _e( 'Or to URL:', BPMJ_EDDCM_DOMAIN ); ?></span>
            <input type="url" name="redirect_url" id="redirect_url" class="a_third_width" placeholder="URL"
                   value="<?php echo $this->options->redirect_url; ?>">

        </div>

        <div class="form-group">
	        <?php
	        if ( 1 === preg_match( '/(\d{4}-\d{2}-\d{2})\s+([0-2]\d)\:([0-5]\d)/', $this->options->access_start, $match ) ) {
		        $access_start_date = $match[ 1 ];
		        $access_start_hh   = $match[ 2 ];
		        $access_start_mm   = $match[ 3 ];
	        } else {
		        $access_start_date = $this->options->access_start;
		        $access_start_hh   = '00';
		        $access_start_mm   = '00';
	        }
	        ?>
            <label for="access_start"><?php _e( 'Access start date', BPMJ_EDDCM_DOMAIN ); ?></label>
            <p style="border: 1px solid red; padding: 3px;">
                <?php echo $this->translator->translate('edit_course.start_date_warning'); ?>
            </p>
            <div class="desc bpmj-eddcm-autotip"><?php _e( 'Use this to choose when the course will be accessible to participants. Leave blank to disable', BPMJ_EDDCM_DOMAIN ); ?></div>

            <input type="text" name="access_start" id="access_start" class="a_third_width wp-datepicker-field"
                   value="<?php echo $access_start_date; ?>" <?php disabled( $no_access_to_access_start ); ?> />

            <span class="desc"><?php _e( 'on time of the day', BPMJ_EDDCM_DOMAIN ); ?>:</span>
            <select name="access_start_hh" style="width: 50px;" <?php disabled( $no_access_to_access_start ); ?>>
		        <?php foreach ( range( 0, 23 ) as $hour ):
			        $hour_str = str_pad( $hour, 2, '0', STR_PAD_LEFT );
			        ?>
                    <option value="<?php echo $hour_str; ?>" <?php selected( $access_start_hh, $hour_str ); ?>><?php echo $hour_str; ?></option>
		        <?php endforeach; ?>
            </select>
            :
            <select name="access_start_mm" style="width: 50px;" <?php disabled( $no_access_to_access_start ); ?>>
		        <?php foreach ( range( 0, 59 ) as $minute ):
			        $minute_str = str_pad( $minute, 2, '0', STR_PAD_LEFT );
			        ?>
                    <option value="<?php echo $minute_str; ?>" <?php selected( $access_start_mm, $minute_str ); ?>><?php echo $minute_str; ?></option>
		        <?php endforeach; ?>
            </select>
	        <?php if ( $no_access_to_access_start ): ?>
                <div class="desc text-danger">
                    <strong><?php _e( 'Upgrade needed', BPMJ_EDDCM_DOMAIN ); ?>:</strong>
			        <?php echo WPI()->packages->feature_not_available_message( Packages::FEAT_COURSE_ACCESS_START, __( 'In order to set the course access time, you need to upgrade your license to level: "%s"', BPMJ_EDDCM_DOMAIN ) ); ?>
                </div>
	        <?php endif; ?>
        </div>

		<div class="form-group bpmj-eddcm-single-price" <?php echo $single_price_style; ?>>
			<label for="bpmj_eddcm_purchase_limit"><?php _e( 'Purchase limit', BPMJ_EDDCM_DOMAIN ); ?></label>

			<div class="desc"><?php _e( 'Leave blank or set to 0 to disable', BPMJ_EDDCM_DOMAIN ); ?></div>
			<input type="number" name="purchase_limit" id="bpmj_eddcm_purchase_limit" step="1" min="0"
			       class="half_width"
			       value="<?php echo esc_attr( $this->options->purchase_limit ); ?>"/>


			<div class="desc"><?php _e( 'Items left:', BPMJ_EDDCM_DOMAIN ); ?></div>
			<input type="number" name="purchase_limit_items_left" id="bpmj_eddcm_purchase_limit_items_left"
			       class="half_width"
			       value="<?php echo $this->options->purchase_limit_items_left; ?>">
		</div>

		<?php
		if ( 'courses' === get_post_type( $post ) ):
			$this->field_sales_disabled(
				get_post_meta( $post->ID, 'sales_disabled', true ),
				__( 'Block sales', BPMJ_EDDCM_DOMAIN ),
				__( 'Checking this option prevents users from purchasing this course and deactivates purchase links', BPMJ_EDDCM_DOMAIN ),
				'bpmj_wpidea[sales_disabled]',
				true
			);
			$this->field_hide_from_lists(
				get_post_meta( $post->ID, 'hide_from_lists', true ),
				__( 'Exclude this course from the course lists', BPMJ_EDDCM_DOMAIN ),
				'bpmj_wpidea[hide_from_lists]',
				true
			);
			$this->field_purchase_button_hidden(
				get_post_meta( $post->ID, 'purchase_button_hidden', true ),
				__( 'Hide default buy button', BPMJ_EDDCM_DOMAIN ),
                'bpmj_wpidea[purchase_button_hidden]',
				true,
                __( 'This option will hide buy button only on the course page', BPMJ_EDDCM_DOMAIN )
			);
			$wp_idea_settings = get_option( 'wp_idea' );
            if ( 'scarlet' === $wp_idea_settings['template'] ) {
                $this->field_purchase_button_hidden(
                    get_post_meta( $post->ID, 'promote_curse', true ),
                    __( 'Promote this product on the home page', BPMJ_EDDCM_DOMAIN ),
                    'bpmj_wpidea[promote_curse]',
                    true
                );
            }


            $enable_certificates = $wp_idea_settings['enable_certificates'];
            if ( 'on' === $enable_certificates ) {
                $this->field_purchase_button_hidden(
                    get_post_meta( $post->ID, 'disable_certificates', true ),
                    __( 'Disable certificates for this course', BPMJ_EDDCM_DOMAIN ),
                    'bpmj_wpidea[disable_certificates]',
                    true,
                    __( 'This disables certificates only for this course', BPMJ_EDDCM_DOMAIN )
                );

                $this->field_certificate_template($post->ID);
            }

            if(Custom_Purchase_Links_Helper::feature_is_active()){
                $this->field_custom_purchase_links();
            }

            $option = get_option( 'bmpj_eddpc_renewal' );
            if ( ! empty( $option ) ) {
                $this->field_purchase_button_hidden(
                    get_post_meta( $post->ID, 'disable_email_subscription', true ),
                    __( 'Disable sending email subscriptions', BPMJ_EDDCM_DOMAIN ),
                    'bpmj_wpidea[disable_email_subscription]',
                    true
                );
            }

			?>
			<?php
			if ( function_exists( 'edd_any_enabled_gateway_supports_recurring_payments' ) && edd_any_enabled_gateway_supports_recurring_payments() ):
				$recurring_payments_possible = edd_recurring_payments_possible_for_download( $this->options->product_id );
				?>
                <div class="form-group bpmj-eddcm-single-price" <?php echo $single_price_style; ?>>
					<?php $this->field_recurring_payments_checkbox( $recurring_payments_possible, $this->options->recurring_payments, __( 'Enable recurring payments for this course', BPMJ_EDDCM_DOMAIN ) ); ?>
                </div>
			<?php
			endif;
		endif;
		?>


		<?php
	}

	/**
	 * @param bool $variable_pricing
	 * @param string $input_name
	 */
	protected function field_variable_pricing_checkbox( $variable_pricing, $input_name = 'variable_pricing' ) {
		$no_access_to_variable_pricing = WPI()->packages->no_access_to_feature( Packages::FEAT_VARIABLE_PRICES );
		?>
        <input type="hidden" name="<?php echo $input_name; ?>"
               value=""/>
        <label>
            <input type="checkbox"
                   id="eddcm-variable-pricing"
                   name="<?php echo $input_name; ?>"
                   value="1"
				<?php checked( $variable_pricing ) ?>
				<?php disabled( $no_access_to_variable_pricing ); ?>/>
			<?php _e( 'Enable variable prices for this course', BPMJ_EDDCM_DOMAIN ); ?>
        </label>
		<?php if ( $no_access_to_variable_pricing ): ?>
            <div class="desc text-danger">
                <strong><?php _e( 'Upgrade needed', BPMJ_EDDCM_DOMAIN ); ?>:</strong>
				<?php echo WPI()->packages->feature_not_available_message( Packages::FEAT_VARIABLE_PRICES, __( 'In order to set variable prices for a course, you need to upgrade your license to level: "%s"', BPMJ_EDDCM_DOMAIN ) ); ?>
            </div>
		<?php endif; ?>
		<?php
	}

	/**
	 * @param int $post_id
	 * @param array $variable_prices
	 * @param int $default_price_id
	 */
	protected function field_variable_pricing_table( $post_id, $variable_prices, $default_price_id ) {
		?>
        <dl id="bpmj-eddcm-variable-prices">
			<?php echo $this->get_variable_prices_html( $variable_prices ? $variable_prices : array(), $default_price_id ); ?>
        </dl>
        <p>
            <a href=""
               class="btn-eddcm btn-eddcm-primary" data-action="edit-variable-prices"
               data-post-id="<?php echo $post_id; ?>"><span
                        class="dashicons dashicons-welcome-write-blog"></span> <?php _e( 'Edit', BPMJ_EDDCM_DOMAIN ); ?>
            </a>
        </p>
		<?php
	}

	/**
	 * @param array $variable_prices
	 * @param int $default_price_id
	 *
	 * @return string
	 */
	protected function get_variable_prices_html( array $variable_prices, $default_price_id = 0 ) {
		$html = '';
		foreach ( $variable_prices as $price_id => $variable_price ) {
			$class = $price_id == $default_price_id ? ' class="default"' : '';
			$html  .= '<dt' . $class . '>' . $variable_price[ 'name' ] . '</dt>';
			$html  .= '<dd' . $class . '>' . edd_currency_filter( $variable_price[ 'amount' ] ) . '</dd>';
		}

		if ( ! $html ) {
			$html = '(' . __( 'Setup variable prices by clicking the button below', BPMJ_EDDCM_DOMAIN ) . ')';
		}

		return $html;
	}

    /**
     * @param string $sale_price
     * @param string $label
     * @param string $name
     * @param bool $wrap
     */
    protected function field_sale_price_from( $sale_price_from_date, $sale_price_from_hour, $name_date_field = 'sale_price_from_date', $name_hour_field = 'sale_price_from_hour' )
    {
        $no_access_to_sale_price_dates = WPI()->packages->no_access_to_feature( Packages::FEAT_SALE_PRICE_DATES );

        if ( 'variable_sale_price_from_date' === $name_date_field ) : ?>
			<div><label for="<?php echo $name_date_field; ?>"><?php _e( 'Sale price time from', BPMJ_EDDCM_DOMAIN ); ?></label>
        <?php else : ?>
            <div><p></p>
            <label for="<?php echo $name_date_field; ?>"><?php _e( 'Sale price time from', BPMJ_EDDCM_DOMAIN ); ?></label>
        <?php endif; ?>
        <input type="text"
               class="wp-datepicker-field"
               name="<?php echo $name_date_field; ?>"
               id="<?php echo $name_date_field; ?>"
               value="<?php echo $sale_price_from_date; ?>"
               <?php disabled( $no_access_to_sale_price_dates ) ?>>
        <span class="desc"><?php _e( 'hour', BPMJ_EDDCM_DOMAIN ); ?></span>
        <select name="<?php echo $name_hour_field; ?>" id="<?php echo $name_hour_field; ?>" <?php disabled( $no_access_to_sale_price_dates ) ?>>
            <?php for ( $i = 0; $i <= 23; $i++ ) : ?>
                <?php
                $checked = '';
                $v = $i;
                if ( $i < 10 )
                    {$v = '0' . $v;}

                if ( $i == $sale_price_from_hour )
                    {$checked = 'selected="selected" ';}
                ?>
                <option <?php echo $checked; ?>value="<?php echo $i; ?>"><?php echo $v; ?></option>
            <?php endfor; ?>
        </select>
		<div class="desc bpmj-eddcm-autotip"><?php _e( 'The end time of the promotion may be delayed up to a maximum of 5 minutes', BPMJ_EDDCM_DOMAIN ); ?></div></div>
        <?php
    }

    /**
     * @param string $sale_price
     * @param string $label
     * @param string $name
     * @param bool $wrap
     */
    protected function field_sale_price_to( $sale_price_to_date, $sale_price_to_hour, $name_date_field = 'sale_price_to_date', $name_hour_field = 'sale_price_to_hour' )
    {
        $no_access_to_sale_price_dates = WPI()->packages->no_access_to_feature( Packages::FEAT_SALE_PRICE_DATES );


        if ( 'variable_sale_price_to_date' === $name_date_field ) : ?>
			<div><label for="<?php echo $name_date_field; ?>"><?php _e( 'Sale price time to', BPMJ_EDDCM_DOMAIN ); ?></label>
        <?php else : ?>
            <div><p></p>
            <label for="<?php echo $name_date_field; ?>"><?php _e( 'Sale price time to', BPMJ_EDDCM_DOMAIN ); ?></label>
        <?php endif; ?>
        <input type="text"
               class="wp-datepicker-field"
               name="<?php echo $name_date_field; ?>"
               id="<?php echo $name_date_field; ?>"
               value="<?php echo $sale_price_to_date; ?>"
               <?php disabled( $no_access_to_sale_price_dates ); ?>>
        <span class="desc"><?php _e( 'hour', BPMJ_EDDCM_DOMAIN ); ?></span>
        <select name="<?php echo $name_hour_field; ?>" id="<?php echo $name_hour_field; ?>" <?php disabled( $no_access_to_sale_price_dates ); ?>>
            <?php for ( $i = 0; $i <= 23; $i++ ) : ?>
                <?php
                $checked = '';
                $v = $i;
                if ( $i < 10 )
                    {$v = '0' . $v;}

                if ( $i == $sale_price_to_hour )
                    {$checked = 'selected="selected" ';}
                ?>
                <option <?php echo $checked; ?>value="<?php echo $i; ?>"><?php echo $v; ?></option>
            <?php endfor; ?>
        </select>
        <div class="desc bpmj-eddcm-autotip"><?php _e( 'The end time of the promotion may be delayed up to a maximum of 5 minutes', BPMJ_EDDCM_DOMAIN ); ?></div></div>
        <?php if ( $no_access_to_sale_price_dates ): ?>
            <div class="desc text-danger">
                <strong><?php _e( 'Upgrade needed', BPMJ_EDDCM_DOMAIN ); ?>:</strong>
                <?php echo WPI()->packages->feature_not_available_message( Packages::FEAT_SALE_PRICE_DATES, __( 'In order to enable sales price dates, you need to upgrade your license to level: "%s"', BPMJ_EDDCM_DOMAIN ) ); ?>
            </div>
        <?php endif; ?>
        <?php
    }

	/**
	 * @param string $price
	 * @param string $label
	 * @param string $name
	 * @param bool $wrap
	 */
	protected function field_price( $price, $label, $name = 'price', $wrap = false ) {
		$this->field_wrap( $wrap );
		?>
        <label for="<?php echo $name; ?>"><?php _e( 'Price', BPMJ_EDDCM_DOMAIN ); ?></label>
        <input type="number" step="0.01" name="<?php echo $name; ?>" id="<?php echo $name; ?>"
               placeholder="<?php echo edd_get_currency(); ?>"
               class="a_third_width" value="<?php echo $price; ?>">
        <div class="desc bpmj-eddcm-autotip"><?php echo $label; ?></div>
		<?php
		$this->field_unwrap( $wrap );
	}

	/**
	 * @param bool $wrap
	 */
	protected function field_wrap( $wrap ) {
		if ( ! $wrap ) {
			return;
		}
		?>
        <div class="form-group">
		<?php
	}

	/**
	 * @param bool $wrap
	 */
	protected function field_unwrap( $wrap ) {
		if ( ! $wrap ) {
			return;
		}
		?>
        </div>
		<?php
	}

	/**
	 * @param string $sale_price
	 * @param string $label
	 * @param string $name
	 * @param bool $wrap
	 */
	protected function field_sale_price( $sale_price, $label, $name = 'sale_price', $wrap = false ) {
		$this->field_wrap( $wrap );
		?>
        <label for="<?php echo $name; ?>"><?php _e( 'Sale price', BPMJ_EDDCM_DOMAIN ); ?></label>
        <input type="number" step="0.01" name="<?php echo $name; ?>" id="<?php echo $name; ?>"
               placeholder="<?php echo edd_get_currency(); ?>"
               class="a_third_width" value="<?php echo $sale_price; ?>">
        <div class="desc bpmj-eddcm-autotip"><?php echo $label ?></div>
		<?php
		$this->field_unwrap( $wrap );
	}

        /**
	 * @param string $sales_disabled
	 * @param string $label
	 * @param string $description
	 * @param string $name
	 * @param bool $wrap
	 */
	protected function field_sales_disabled( $sales_disabled, $label, $description, $name = 'bpmj_wpidea[sales_disabled]', $wrap = false ) {
		$this->field_wrap( $wrap );
		?>
        <input type="hidden" name="<?php echo $name; ?>" value="off">
        <label>
            <input type="checkbox" value="on"
                   name="<?php echo $name; ?>" <?php checked( 'on', $sales_disabled ); ?>>
			<?php echo $label; ?>
        </label>
        <div class="desc bpmj-eddcm-autotip"><?php echo $description; ?></div>
		<?php
		$this->field_unwrap( $wrap );
	}

	/**
	 * @param string $hide_from_lists
	 * @param string $label
	 * @param string $name
	 * @param bool $wrap
	 */
	protected function field_hide_from_lists( $hide_from_lists, $label, $name = 'bpmj_wpidea[hide_from_lists]', $wrap = false ) {
		$this->field_wrap( $wrap );
		?>
        <input type="hidden" name="<?php echo $name; ?>" value="off">
        <label>
            <input type="checkbox" value="on"
                   name="<?php echo $name; ?>" <?php checked( 'on', $hide_from_lists ); ?>>
			<?php echo $label; ?>
        </label>
		<?php
		$this->field_unwrap( $wrap );
	}

	/**
	 * @param string $purchase_button_hidden
	 * @param string $label
	 * @param string $name
	 * @param bool $wrap
	 */
	protected function field_purchase_button_hidden( $purchase_button_hidden, $label, $name = 'bpmj_wpidea[purchase_button_hidden]', $wrap = false, $label_title = '' ) {
		$this->field_wrap( $wrap );
		?>
        <input type="hidden" name="<?php echo $name; ?>" value="off">
        <label <?php echo ( ! empty( $label_title ) ) ? 'title="' . $label_title . '"' : '' ?>>
            <input type="checkbox" value="on"
                   name="<?php echo $name; ?>" <?php checked( 'on', $purchase_button_hidden ); ?>>
			<?php echo $label ?>
            <?php if ( ! empty( $label_title ) ) : ?>
                <span class="bpmj-eddcm-autotip-icon dashicons dashicons-editor-help"></span>
            <?php endif; ?>
        </label>
		<?php
		$this->field_unwrap( $wrap );
	}

	private function field_certificate_template(int $post_id): void
	{
        $certificate_template = new Certificate_Template();
        $certificate_template_id = $this->options->certificate_template_id ?? '';
        if(!$certificate_template::check_if_new_version_of_certificate_templates_is_enabled()) {
            return;
        }
	    ?>
	    <div class="form-group">
            <label for="certificate_template_id"><?php _e( 'Select a certificate template', BPMJ_EDDCM_DOMAIN ); ?></label>

            <div class="desc bpmj-eddcm-autotip"><?php _e( 'You can choose which certificate template is to be used to generate certificates after completing this course.', BPMJ_EDDCM_DOMAIN ); ?></div>

            <select name="certificate_template_id" class="a_third_width">
                <option value=""><?php _e( 'Default', BPMJ_EDDCM_DOMAIN ); ?></option>
				<?php
				foreach ( $certificate_template->find_all() as $certificate ) {
				    $selected_string = ($certificate_template_id == $certificate->get_id()) ? 'selected' : '';
					echo '<option value="' . $certificate->get_id() . '" ' . $selected_string . '>' . $certificate->get_name() . '</option>';
				}
				?>
            </select>

        </div>
        <?php
        $this->field_numbering_pattern($post_id);
	}

    private function field_numbering_pattern(int $post_id): void
	{
        $enable_certificate_numbering = get_post_meta( $post_id, 'enable_certificate_numbering', true );
        $certificate_numbering_pattern = get_post_meta( $post_id, 'certificate_numbering_pattern', true ) ?? self::NUMBERING_PATTERN;
        $name = 'bpmj_wpidea[enable_certificate_numbering]';
        ?>

        <div class="form-group">
             <input type="hidden" name="<?= $name ?>" value="off">
             <label>
                <input type="checkbox"
                       id="enable-certificate-numbering"
                       name="<?= $name ?>"
                       value="on"
                       autocomplete="off"
                <?php checked( 'on', $enable_certificate_numbering ) ?> />
                <?= $this->translator->translate('edit_courses.certificate_numbering.enable') ?>
            </label>

        </div>
        <div class="form-group" id="numbering-pattern"  <?php if($enable_certificate_numbering !='on'){ echo 'style="display: none;"'; } ?>>

            <label for="certificate_template_numbering_pattern">
               <?php
                 echo $this->translator->translate('edit_courses.certificate_numbering.pattern');

                 $this->explanation_popup->get_html('explanation_numbering_pattern','course.settings.certificate_number.explanation.title', 'course.settings.certificate_number.explanation.text');
                ?>
            </label>

	        <input type="text" name="bpmj_wpidea[certificate_numbering_pattern]" id="certificate_numbering_pattern" autocomplete="off" class="a_third_width"  placeholder="<?php echo self::NUMBERING_PATTERN; ?>" value="<?php echo $certificate_numbering_pattern; ?>">
	        <p class="text-error hidden-error" id="error-numbering-pattern"><?= $this->translator->translate('edit_courses.certificate_numbering.error') ?></p>
        </div>

        <?php
	}

	private function field_custom_purchase_links()
	{

        $custom_purchase_link = $this->options->custom_purchase_link ?? '';
	    ?>
	    <div class="form-group">
			<label for="bpmj_eddcm_purchase_limit">Link do zewnętrznej oferty</label>
            <div class="desc bpmj-eddcm-autotip">Adres url do którego ma nastąpić przekierowanie po kliknięciu w przycisk „Dodaj do koszyka”. Pozostaw pole puste jeżeli przycisk ma działać w standardowy sposób.</div>

			<input type="text" name="custom_purchase_link" id="custom_purchase_link"
			       class="half_width"
			       value="<?= $custom_purchase_link ?>"/>

		</div>
	    <?php
	}

	/**
	 * @param bool $recurring_payments_possible
	 * @param bool $recurring_payments
	 * @param string $label
	 * @param string $input_name
	 */
	protected function field_recurring_payments_checkbox( $recurring_payments_possible, $recurring_payments, $label, $input_name = 'recurring_payments_enabled' ) {
		$no_access_to_recurring_payments = WPI()->packages->no_access_to_feature( Packages::FEAT_RECURRING_PAYMENTS );

		?>
        <input type="hidden" name="<?php echo $input_name; ?>"
               value=""/>
        <label>
            <input type="checkbox"
                   name="<?php echo $input_name; ?>"
                   value="1"
				<?php checked( $recurring_payments, '1' ); ?>
				<?php disabled( $no_access_to_recurring_payments || ! $recurring_payments_possible ); ?>/> <?php echo $label; ?>
        </label>
		<?php if ( $no_access_to_recurring_payments ): ?>
            <div class="desc text-danger">
                <strong><?php _e( 'Upgrade needed', BPMJ_EDDCM_DOMAIN ); ?>:</strong>
				<?php echo WPI()->packages->feature_not_available_message( Packages::FEAT_RECURRING_PAYMENTS, __( 'In order to enable recurring payments, you need to upgrade your license to level: "%s"', BPMJ_EDDCM_DOMAIN ) ); ?>
            </div>
		<?php endif; ?>
		<?php
	}

	/**
	 * Meta box display modules.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function metabox_modules( $post ) {
		$no_access_to_dripping    = WPI()->packages->no_access_to_feature( Packages::FEAT_DELAYED_ACCESS );
		$access_time_units        = WPI()->courses->get_access_time_units();
		$variable_prices_template = Creator::get_variable_prices_module_template( $this->options->product_id );
        $no_access_to_tests       = WPI()->packages->no_access_to_feature( Packages::FEAT_TESTS );
		?>
        <div class="form-group">
            <label for="bpmj_eddcm_drip_value"><?php _e( 'Default drip value', BPMJ_EDDCM_DOMAIN ); ?></label>
            <input type="number" step="1" name="drip_value" id="bpmj_eddcm_drip_value" style="width: 60px;"
                   value="<?php echo $this->options->drip_value; ?>" <?php disabled( $no_access_to_dripping ); ?>>
            <span id="bpmj_eddcm_drip_unit_label"><?php echo WPI()->courses->get_access_time_unit( $this->options->drip_unit ); ?></span>
	        <?php if ( ! $no_access_to_dripping ): ?>
                <button type="button"
                        class="btn-eddcm btn-eddcm-primary"
                        data-action="set-drip-value"><?php _e( 'Set for modules/lessons', BPMJ_EDDCM_DOMAIN ); ?></button>
                <button type="button"
                        class="btn-eddcm btn-eddcm-default thickbox"
                        title="<?php esc_attr_e( 'Change drip unit', BPMJ_EDDCM_DOMAIN ); ?>"
                        data-action="change-drip-unit"><?php _e( 'Change drip unit', BPMJ_EDDCM_DOMAIN ); ?></button>

                <input type="hidden" id="bpmj_eddcm_drip_unit" name="drip_unit"
                       value="<?php echo esc_attr( $this->options->drip_unit ); ?>"/>
                <template id="bpmj_eddcm_drip_unit_modal" style="display: none;">
                    <div style="text-align: center;">
                        <select>
					        <?php
					        foreach ( $access_time_units as $unit => $name ) {
						        echo '<option value="' . $unit . '">' . $name . '</option>';
					        }
					        ?>
                        </select>
                        <button type="button"
                                class="btn-eddcm btn-eddcm-primary"
                                data-action="change-drip-unit-do"><?php _e( 'Set drip unit', BPMJ_EDDCM_DOMAIN ); ?></button>
                    </div>
                </template>


	        <?php endif; ?>

            <template id="bpmj_eddcm_new_module_full_template">
		        <?php echo Creator::create_module_get_html( 'full', false, false, '', true, false, $variable_prices_template ); ?>
            </template>
            <template id="bpmj_eddcm_new_module_lesson_template">
		        <?php echo Creator::create_module_get_html( 'lesson', false, false, '', true, false, $variable_prices_template ); ?>
            </template>
            <template id="bpmj_eddcm_new_module_test_template">
                <?php echo Creator::create_module_get_html( 'test', false, false, '', true, false, $variable_prices_template ); ?>
            </template>
            <template id="bpmj_eddcm_new_lesson_template">
		        <?php echo Creator::create_lesson_get_html( false, false, true, false, $variable_prices_template ); ?>
            </template>
            <div class="desc"><?php _e( 'Set, for example, <b>1 day</b>, to release one lesson every day.<br>Leave blank to release all lessons immediately after purchase.', BPMJ_EDDCM_DOMAIN ); ?></div>
	        <?php if ( $no_access_to_dripping ): ?>
                <div class="desc text-danger">
                    <strong><?php _e( 'Upgrade needed', BPMJ_EDDCM_DOMAIN ); ?>:</strong>
			        <?php echo WPI()->packages->feature_not_available_message( Packages::FEAT_DELAYED_ACCESS, __( 'In order to drip courses, you need to upgrade your license to level: "%s"', BPMJ_EDDCM_DOMAIN ) ); ?>
                </div>
	        <?php endif; ?>
        </div>

		<?php echo Creator::get_variable_prices_module_legend( $this->options->product_id ); ?>

        <div class="form-group">
            <ul class="modules" id="bpmj_eddcm_modules_list">
				<?php
				if ( isset( $this->options ) ) {
					if ( is_array( $this->options->modules ) ) {
						$content = array();

						foreach ( $this->options->modules as $module ) {

							$module_id = isset( $module[ 'id' ] ) ? $module[ 'id' ] : false;
							$lessons   = isset( $module[ 'lessons' ] ) ? $module[ 'lessons' ] : false;
							if(!$lessons) {
								$lessons   = isset( $module[ 'module' ] ) ? $module[ 'module' ] : false;
							}
							$title     = get_the_title( $module_id );

							$get_module = Creator::create_module( $module[ 'mode' ], $module_id, $lessons, $title, true, false, $variable_prices_template );

							$content[ $get_module[ 'editor_id' ] ] = $module;
							$content                               = $content + $get_module[ 'content' ];

							echo $get_module[ 'html' ];
						}
					}
				}
				?>
            </ul>

            <div class="desc">
				<?php _e( 'To change the content of new lessons, please save the course.', BPMJ_EDDCM_DOMAIN ); ?><br>
				<?php _e( 'You can move modules or lessons and change their order.', BPMJ_EDDCM_DOMAIN ); ?>
            </div>
        </div>

        <div class="creator-buttons text-center">
            <button type="button" class="btn-eddcm btn-eddcm-primary" data-action="add-module"
                    data-mode="full"><?php _e( 'Add module', BPMJ_EDDCM_DOMAIN ); ?></button>
            <button type="button" class="btn-eddcm btn-eddcm-primary" data-action="add-module"
                    data-mode="lesson"><?php _e( 'Add lesson', BPMJ_EDDCM_DOMAIN ); ?></button>
            <button<?php echo $no_access_to_tests ? ' disabled="disabled"' : ''; ?> type="button" class="btn-eddcm btn-eddcm-primary" data-action="add-module"
                    data-mode="test"><?php _e( 'Add quiz', BPMJ_EDDCM_DOMAIN ); ?></button>
            <?php if ( $no_access_to_tests ): ?>
                <div class="desc text-danger">
                    <p>
                        <strong><?php _e( 'Upgrade needed', BPMJ_EDDCM_DOMAIN ); ?>:</strong>
                        <?php echo WPI()->packages->feature_not_available_message( Packages::FEAT_TESTS, __( 'In order to add a quiz, you need to upgrade your license to level: "%s"', BPMJ_EDDCM_DOMAIN ) ); ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <input type="hidden" id="bpmj_eddcm_save_modules" name="bpmj_eddcm_save_modules" value=""/>

		<?php
	}

	/**
	 * Lesson settings
	 *
	 * @param WP_Post $object
	 */
	public function metabox_lesson_settings( $object ) {

		$files = get_post_meta( $object->ID, 'files', true );
		wp_nonce_field( basename( __FILE__ ), 'bpmj_wpidea_options_nonce' );

		$this->html_subtitle_field( $object );
		?>

		<div class="form-group" style="padding:10px;">
			<label for="level"><?php _e( 'Difficulty level', BPMJ_EDDCM_DOMAIN ); ?></label>
			<span class="input-group-addon">
				<input type="hidden" name="bpmj_wpidea[level_mode]" value="off">
				<input type="checkbox" name="bpmj_wpidea[level_mode]" <?php if ( get_post_meta( $object->ID, 'level_mode', true ) != 'off' ) {echo 'checked="checked"';} ?>>
			</span>
			<input <?php if ( get_post_meta( $object->ID, 'level_mode', true ) == 'off' ) {echo 'disabled="disabled"';} ?> type="text" class="input-group" id="level" name="bpmj_wpidea[level]" value="<?php echo get_post_meta( $object->ID, 'level', true ); ?>">
			<div class="desc"><?php _e( "Difficulty level of your lesson.", BPMJ_EDDCM_DOMAIN ); ?></div>
		</div>

		<div class="form-group" style="padding:10px;">
			<label for="duration"><?php _e( 'Duration time', BPMJ_EDDCM_DOMAIN ); ?></label>
			<span class="input-group-addon">
				<input type="hidden" name="bpmj_wpidea[duration_mode]" value="off">
				<input type="checkbox" name="bpmj_wpidea[duration_mode]" <?php if ( get_post_meta( $object->ID, 'duration_mode', true ) != 'off' ) {echo 'checked="checked"';} ?>>
			</span>
			<input <?php if ( get_post_meta( $object->ID, 'duration_mode', true ) == 'off' ) {echo 'disabled="disabled"';} ?> type="text" class="input-group" id="duration" name="bpmj_wpidea[duration]" value="<?php echo get_post_meta( $object->ID, 'duration', true ); ?>">
			<div class="desc"><?php _e( "Expected duration of this part of course.", BPMJ_EDDCM_DOMAIN ); ?></div>
		</div>
		<?php if ( WPI()->templates->is_feature_supported( Templates::FEATURE_LESSON_SHORT_DESCRIPTION ) ) : ?>
		<div class="form-group" style="padding:10px;">
			<label for="shortdesc"><?php _e( 'Short description', BPMJ_EDDCM_DOMAIN ); ?></label>
			<span class="input-group-addon">
				<input type="hidden" name="bpmj_wpidea[shortdesc_mode]" value="off">
				<input type="checkbox" name="bpmj_wpidea[shortdesc_mode]" <?php if ( get_post_meta( $object->ID, 'shortdesc_mode', true ) != 'off' ) {echo 'checked="checked"';} ?>>
			</span>
			<input <?php if ( get_post_meta( $object->ID, 'shortdesc_mode', true ) == 'off' ) {echo 'disabled="disabled"';} ?> type="text" class="input-group" id="shortdesc" name="bpmj_wpidea[shortdesc]" value="<?php echo get_post_meta( $object->ID, 'shortdesc', true ); ?>">
			<div class="desc"><?php _e( "Optional, short description of lesson.", BPMJ_EDDCM_DOMAIN ); ?></div>
		</div>
		<?php endif ?>

		<div class="form-group" style="padding:10px;">
			<label for="files"><?php _e( 'Files', BPMJ_EDDCM_DOMAIN ); ?></label>
			<div class="wp-idea-files">
				<?php
				if ( is_array( $files ) ) { ?>
                    <input type="hidden" name="bpmj_wpidea_remove_files" value="1"/>
					<?php
					foreach ( $files as $fileID => $file ) {
						$image = wp_get_attachment_image_src( $fileID, 'thumbnail', true );
						$style = 'background-image: url(' . $image[0] . ');';

						if ( !isset( $image[ 3 ] ) ) {
							$style .= 'background-position: center 5px;';
						} else {
							$style .= 'background-size: cover; background-position: center center';
						}

						echo '<div class="file" data-id="' . $fileID . '" style="' . $style . '">';
						echo '<span class="dashicons dashicons-no remove-file"></span>';

						if ( !isset( $image[ 3 ] ) ) {
							$attachment = basename( get_attached_file( $fileID ) );
							echo '<span class="title">' . $attachment . '</span>';
						}

						echo '<input type="text" id="files" name="bpmj_wpidea[files][' . $fileID . '][desc]" value="' . $file[ 'desc' ] . '" placeholder="' . __( 'Short file description', BPMJ_EDDCM_DOMAIN ) . '">';
						echo '</div>';
					}
				}
				?>
			</div>

			<a class="btn-eddcm btn-eddcm-primary" id="uploadFiles" href="<?php echo esc_url( get_upload_iframe_src() ); ?>" style="display: inline-block;"><?php _e( 'Upload files', BPMJ_EDDCM_DOMAIN ) ?></a>
			<div class="desc"><?php _e( "Files for this module/lesson.", BPMJ_EDDCM_DOMAIN ); ?></div>
		</div>
		<?php
            $show_download_section_position_field = $this->template_settings_handler->should_download_section_position_field_be_displayed();
        ?>
		<?php if ( $show_download_section_position_field ) : ?>
		<div class="form-group" style="padding:10px;">
			<label for="download_section_position"><?php _e( 'File section position', BPMJ_EDDCM_DOMAIN ); ?></label>
			<select name="bpmj_wpidea[download_section_position]" id="download_section_position">
				<?php
				$selected_option = get_post_meta( $object->ID, 'download_section_position', true );
				foreach ( WPI()->settings->get_download_position_options( true ) as $option => $option_description ):
					?>
					<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $option, $selected_option ); ?>><?php echo esc_html( $option_description ); ?></option>
					<?php
				endforeach;
				?>
			</select>
		</div>
		<?php endif ?>

		<?php
	}

	/**
	 * Lesson/module subtitle field
	 */
	public function html_subtitle_field( $object ) {
		if ( WPI()->templates->is_feature_supported( Templates::FEATURE_LESSON_SUBTITLE ) ) {
			?>
			<div class="form-group" style="padding:10px;">
				<label for="subtitle"><?= Translator_Static_Helper::translate('lesson_editor.short_description') ?></label>
				<span class="input-group-addon">
					<input type="hidden" name="bpmj_wpidea[subtitle_mode]" value="off">
					<input type="checkbox"
						   name="bpmj_wpidea[subtitle_mode]" <?php if ( get_post_meta( $object->ID, 'subtitle_mode', true ) != 'off' ) {
						echo 'checked="checked"';
					} ?>>
				</span>
				<input <?php if ( get_post_meta( $object->ID, 'subtitle_mode', true ) == 'off' ) {
					echo 'disabled="disabled"';
				} ?> type="text" class="input-group" id="subtitle" name="bpmj_wpidea[subtitle]"
					 value="<?php echo get_post_meta( $object->ID, 'subtitle', true ); ?>">
				<div class="desc"><?php _e( "It's optional field.", BPMJ_EDDCM_DOMAIN ); ?></div>
			</div>
			<?php
		}
	}

    /**
     * Displays button with link to course
     *
     * @param WP_Post $object
     */
    public function metabox_edit_course_link( $object ) {
	    $course_id = get_post_meta( $object->ID, '_bpmj_eddcm', true );
	    ?>

        <div class="useful-links">
            <a href="<?php echo $this->get_course_editor_page_url($course_id); ?>" class="btn-eddcm btn-eddcm-default"><?php _e( 'Edit Course', BPMJ_EDDCM_DOMAIN ); ?></a>
        </div>

        <?php
    }

    private function get_course_editor_page_url(int $course_id): string
    {
        return $this->url_generator->generate_admin_page_url('admin.php', [
            'page' => Admin_Menu_Item_Slug::EDITOR_COURSE,
            Course_Editor_Page_Renderer::COURSE_ID_QUERY_ARG_NAME => $course_id
        ]);
    }

	/**
	 * Module settings
	 *
	 * @param WP_Post $object
	 */
	public function metabox_module_settings( $object ) {

		$this->html_subtitle_field( $object );

	}

    /**
     * Test settings
     *
     * @param WP_Post $object
     */
    public function metabox_quiz_settings($object ) {

        $files = get_post_meta( $object->ID, 'files', true );
        wp_nonce_field( basename( __FILE__ ), 'bpmj_wpidea_test_settings_nonce' );

        $this->html_subtitle_field( $object );
        ?>

        <div class="form-group" style="padding:10px;">
            <label for="level"><?php _e( 'Difficulty level', BPMJ_EDDCM_DOMAIN ); ?></label>
            <span class="input-group-addon">
				<input type="hidden" name="bpmj_wpidea[level_mode]" value="off">
				<input type="checkbox" name="bpmj_wpidea[level_mode]" <?php if ( get_post_meta( $object->ID, 'level_mode', true ) != 'off' ) {echo 'checked="checked"';} ?>>
			</span>
            <input <?php if ( get_post_meta( $object->ID, 'level_mode', true ) == 'off' ) {echo 'disabled="disabled"';} ?> type="text" class="input-group" id="level" name="bpmj_wpidea[level]" value="<?php echo get_post_meta( $object->ID, 'level', true ); ?>">
            <div class="desc"><?php _e( "Difficulty level of your lesson.", BPMJ_EDDCM_DOMAIN ); ?></div>
        </div>

        <div class="form-group" style="padding:10px;">
            <label for="duration"><?php _e( 'Duration time', BPMJ_EDDCM_DOMAIN ); ?></label>
            <span class="input-group-addon">
				<input type="hidden" name="bpmj_wpidea[duration_mode]" value="off">
				<input type="checkbox" name="bpmj_wpidea[duration_mode]" <?php if ( get_post_meta( $object->ID, 'duration_mode', true ) != 'off' ) {echo 'checked="checked"';} ?>>
			</span>
            <input <?php if ( get_post_meta( $object->ID, 'duration_mode', true ) == 'off' ) {echo 'disabled="disabled"';} ?> type="text" class="input-group" id="duration" name="bpmj_wpidea[duration]" value="<?php echo get_post_meta( $object->ID, 'duration', true ); ?>">
            <div class="desc"><?php _e( "Expected duration of this part of course.", BPMJ_EDDCM_DOMAIN ); ?></div>
        </div>

        <div class="form-group" style="padding:10px;">
            <label for="time"><?= $this->translator->translate('quiz_editor.time_for_quiz') ?></label>
            <span class="input-group-addon">
				<input type="hidden" name="bpmj_wpidea[time_mode]" value="off">
				<input type="checkbox" name="bpmj_wpidea[time_mode]" <?php if ( get_post_meta( $object->ID, 'time_mode', true ) == 'on' ) echo 'checked="checked"'; ?>>
			</span>
            <input <?php if ( get_post_meta( $object->ID, 'time_mode', true ) !== 'on' ) echo 'disabled="disabled"'; ?> type="number" class="input-group" id="time" name="bpmj_wpidea[time]" value="<?php echo get_post_meta( $object->ID, 'time', true ); ?>">
            <div class="desc"><?= $this->translator->translate('quiz_editor.time_for_quiz.description') ?></div>
        </div>

        <div class="form-group" style="padding:10px;">
            <label for="number_test_attempts"><?= $this->translator->translate('course_editor.sections.structure.quiz.number_test_attempts') ?></label>
            <span class="input-group-addon">
				<input type="hidden" name="bpmj_wpidea[number_test_attempts_mode]" value="off">
				<input type="checkbox" name="bpmj_wpidea[number_test_attempts_mode]" <?php if ( get_post_meta( $object->ID, 'number_test_attempts_mode', true ) == 'on' ) {echo 'checked="checked"';} ?>>
			</span>
            <input <?php if ( get_post_meta( $object->ID, 'number_test_attempts_mode', true ) !== 'on' ) {echo 'disabled="disabled"';} ?> type="number" class="input-group" id="number_test_attempts" name="bpmj_wpidea[number_test_attempts]" value="<?php echo get_post_meta( $object->ID, 'number_test_attempts', true ); ?>">
            <div class="desc"><?= $this->translator->translate('course_editor.sections.structure.quiz.number_test_attempts.desc') ?></div>
        </div>


        <div class="form-group" style="padding:10px;">
            <label for="evaluated-by-admin"><?php _e( 'Moderate by the administrator', BPMJ_EDDCM_DOMAIN ); ?></label>
            <span class="input-group-addon evaluated-by-admin">
				<input type="hidden" name="bpmj_wpidea[evaluated_by_admin_mode]" value="off">
				<input type="checkbox" name="bpmj_wpidea[evaluated_by_admin_mode]" <?php if ( get_post_meta( $object->ID, 'evaluated_by_admin_mode', true ) == 'on' ) {echo 'checked="checked"';} ?>>
			</span>
            <input <?php if ( get_post_meta( $object->ID, 'evaluated_by_admin_mode', true ) == 'off' ) {echo 'disabled="disabled"';} ?> type="hidden" class="input-group" id="evaluated-by-admin" name="bpmj_wpidea[evaluated_by_admin]" value="<?php echo get_post_meta( $object->ID, 'evaluated_by_admin', true ); ?>">
            <span style="margin-left: 10px;"><?php _e( "This course can be passed after the administrator's assessment.", BPMJ_EDDCM_DOMAIN ); ?></span>
        </div>

        <div class="form-group" style="padding:10px;">
            <label for="can_see_answers_mode"><?= $this->translator->translate('quiz_editor.answers_preview') ?></label>
            <span class="input-group-addon can_see_answers_mode">
				<input type="hidden" name="bpmj_wpidea[can_see_answers_mode]" value="off">
				<input type="checkbox" name="bpmj_wpidea[can_see_answers_mode]" <?php if ( get_post_meta( $object->ID, 'can_see_answers_mode', true ) == 'on' ) echo 'checked="checked"'; ?>>
			</span>
           <span style="margin-left: 10px;"><?= $this->translator->translate('quiz_editor.answers_preview.desc') ?></span>
        </div>

        <div class="form-group" style="padding:10px;">
            <label for="also_show_correct_answers"><?= $this->translator->translate('quiz_editor.also_show_correct_answers') ?></label>
            <span class="input-group-addon also_show_correct_answers">
				<input type="hidden" name="bpmj_wpidea[also_show_correct_answers]" value="off">
				<input type="checkbox" name="bpmj_wpidea[also_show_correct_answers]" <?php if ( get_post_meta( $object->ID, 'also_show_correct_answers', true ) == 'on' ) echo 'checked="checked"'; ?>>
			</span>
            <span style="margin-left: 10px;"><?= $this->translator->translate('quiz_editor.also_show_correct_answers.desc') ?></span>
        </div>

        <div class="form-group" style="padding:10px;">
            <label for="randomize_question_order"><?= $this->translator->translate('quiz_editor.randomize_question_order') ?></label>
            <span class="input-group-addon randomize_question_order">
				<input type="hidden" name="bpmj_wpidea[randomize_question_order]" value="off">
				<input type="checkbox" name="bpmj_wpidea[randomize_question_order]" <?php if ( get_post_meta( $object->ID, 'randomize_question_order', true ) == 'on' ) {echo 'checked="checked"';} ?>>
			</span>
            <span style="margin-left: 10px;"><?= $this->translator->translate('quiz_editor.randomize_question_order.description') ?></span>
        </div>

        <div class="form-group" style="padding:10px;">
            <label for="randomize_answer_order"><?= $this->translator->translate('quiz_editor.randomize_answer_order') ?></label>
            <span class="input-group-addon randomize_answer_order">
				<input type="hidden" name="bpmj_wpidea[randomize_answer_order]" value="off">
				<input type="checkbox" name="bpmj_wpidea[randomize_answer_order]" <?php if ( get_post_meta( $object->ID, 'randomize_answer_order', true ) == 'on' ) {echo 'checked="checked"';} ?>>
			</span>
            <span style="margin-left: 10px;"><?= $this->translator->translate('quiz_editor.randomize_answer_order.description') ?></span>
        </div>

        <?php if ( WPI()->templates->is_feature_supported( Templates::FEATURE_LESSON_SHORT_DESCRIPTION ) ) : ?>
            <div class="form-group" style="padding:10px;">
                <label for="shortdesc"><?php _e( 'Short description', BPMJ_EDDCM_DOMAIN ); ?></label>
                <span class="input-group-addon">
                    <input type="hidden" name="bpmj_wpidea[shortdesc_mode]" value="off">
                    <input type="checkbox" name="bpmj_wpidea[shortdesc_mode]" <?php if ( get_post_meta( $object->ID, 'shortdesc_mode', true ) != 'off' ) {echo 'checked="checked"';} ?>>
                </span>
                <input <?php if ( get_post_meta( $object->ID, 'shortdesc_mode', true ) == 'off' ) {echo 'disabled="disabled"';} ?> type="text" class="input-group" id="shortdesc" name="bpmj_wpidea[shortdesc]" value="<?php echo get_post_meta( $object->ID, 'shortdesc', true ); ?>">
                <div class="desc"><?php _e( "Optional, short description of lesson.", BPMJ_EDDCM_DOMAIN ); ?></div>
            </div>
        <?php endif ?>

        <div class="form-group" style="padding:10px;">
            <label for="files"><?php _e( 'Files', BPMJ_EDDCM_DOMAIN ); ?></label>
            <div class="wp-idea-files">
                <?php
                if ( is_array( $files ) ) { ?>
                    <input type="hidden" name="bpmj_wpidea_remove_files" value="1"/>
                    <?php
                    foreach ( $files as $fileID => $file ) {
                        $image = wp_get_attachment_image_src( $fileID, 'thumbnail', true );
                        $style = 'background-image: url(' . $image[0] . ');';

                        if ( !isset( $image[ 3 ] ) ) {
                            $style .= 'background-position: center 5px;';
                        } else {
                            $style .= 'background-size: cover; background-position: center center';
                        }

                        echo '<div class="file" data-id="' . $fileID . '" style="' . $style . '">';
                        echo '<span class="dashicons dashicons-no remove-file"></span>';

                        if ( !isset( $image[ 3 ] ) ) {
                            $attachment = basename( get_attached_file( $fileID ) );
                            echo '<span class="title">' . $attachment . '</span>';
                        }

                        echo '<input type="text" id="files" name="bpmj_wpidea[files][' . $fileID . '][desc]" value="' . $file[ 'desc' ] . '" placeholder="' . __( 'Short file description', BPMJ_EDDCM_DOMAIN ) . '">';
                        echo '</div>';
                    }
                }
                ?>
            </div>

            <a class="btn-eddcm btn-eddcm-primary" id="uploadFiles" href="<?php echo esc_url( get_upload_iframe_src() ); ?>" style="display: inline-block;"><?php _e( 'Upload files', BPMJ_EDDCM_DOMAIN ) ?></a>
            <div class="desc"><?php _e( "Files for this module/lesson.", BPMJ_EDDCM_DOMAIN ); ?></div>
        </div>

        <?php
    }

    /**
     * Test questions
     *
     * @param WP_Post $object
     */
    public function metabox_quiz_questions($object ) {
        wp_nonce_field( basename( __FILE__ ), 'bpmj_wpidea_test_questions_nonce' );

        $questions = get_post_meta( $object->ID, 'test_questions', true );
        $pass_points = get_post_meta( $object->ID, 'test_questions_points_pass', true );
        $all_points = get_post_meta( $object->ID, 'test_questions_points_all', true );
        ?>
        <div class="bpmj-eddcm-cs-section-body">
            <div class="form-group" style="padding:10px;">
                <ul id="bpmj_eddcm_questions_list" class="modules">
                    <?php $i = 0; ?>
                    <?php if ( ! empty( $questions ) ) : ?>
                        <?php foreach ( $questions as $question ) : ?>
                            <li class="module question editor">
                                <div class="question-header">
                                    <span class="dashicons arrows"></span>
                                    <input class="eddcm-test-question-id" type="hidden" name="bpmj_eddcm_test_questions[<?php echo $i; ?>][id]" value="<?php echo $question['id']; ?>">
                                    <input class="eddcm-test-question-title" type="text" name="bpmj_eddcm_test_questions[<?php echo $i; ?>][title]" value="<?php echo esc_html($question['title']); ?>">
                                    <select name="bpmj_eddcm_test_questions[<?php echo $i; ?>][type]" class="eddcm-test-question-type">
                                        <option><?php _e( 'Select question type', BPMJ_EDDCM_DOMAIN ); ?></option>
                                        <option value="single_radio" <?php echo 'single_radio' === $question['type'] ? 'selected' : ''; ?>><?php _e( 'Single choice question', BPMJ_EDDCM_DOMAIN ); ?>&nbsp;(radio)</option>
                                        <option value="single_select" <?php echo 'single_select' === $question['type'] ? 'selected' : ''; ?>><?php _e( 'Single choice question', BPMJ_EDDCM_DOMAIN ); ?>&nbsp;(select)</option>
                                        <option value="multiple" <?php echo 'multiple' === $question['type'] ? 'selected' : ''; ?>><?php _e( 'Multiple choice question', BPMJ_EDDCM_DOMAIN ); ?></option>
                                        <option value="text" <?php echo 'text' === $question['type'] ? 'selected' : ''; ?>><?php _e( 'Text question', BPMJ_EDDCM_DOMAIN ); ?></option>
                                        <option value="file" <?php echo 'file' === $question['type'] ? 'selected' : ''; ?>><?php _e( 'File question', BPMJ_EDDCM_DOMAIN ); ?></option>
                                    </select>
                                    <span class="dashicons dashicons-no-alt remove-module" data-action="remove-question"></span>
                                </div>
                                <div class="question-body">
                                    <div class="question-type-single-tab question-type-tab">
                                        <ul class="answers">
                                            <?php if ( ( $question['type'] == 'single_radio' || $question['type'] === 'single_select' || $question['type'] === 'multiple' ) && ! empty( $question['answer'] ) ) : ?>
                                                <?php $j = 0; ?>
                                                <?php foreach ( $question['answer'] as $answer ) : ?>
                                                    <li class="answer">
                                                        <span class="dashicons arrows"></span>
                                                        <input class="eddcm-test-question-answer-id" type="hidden" name="bpmj_eddcm_test_questions[<?php echo $i; ?>][answer][<?php echo $j; ?>][id]" value="<?php echo $answer['id']; ?>">
                                                        <input class="eddcm-test-question-answer-title" type="text" name="bpmj_eddcm_test_questions[<?php echo $i; ?>][answer][<?php echo $j; ?>][title]" value="<?php echo esc_html( $answer['title'] ); ?>">
                                                        <span class="dashicons dashicons-no-alt remove-module" data-action="remove-answer"></span>
                                                        <input class="eddcm-test-question-answer-points points-value" type="number" name="bpmj_eddcm_test_questions[<?php echo $i; ?>][answer][<?php echo $j; ?>][points]" value="<?php echo $answer['points']; ?>">&nbsp;<?php _e( 'Points', BPMJ_EDDCM_DOMAIN ); ?>
                                                    </li>
                                                    <?php $j++; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                            <li class="add-answer" data-action="add-answer" data-type="single">
                                                <span class="dashicons dashicons-plus"></span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="question-type-text-tab question-type-tab"></div>
                                    <div class="question-type-file-tab question-type-tab"></div>
                                </div>
                            </li>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <template id="bpmj_eddcm_new_test_question_single_answer_template">
                <li class="answer">
                    <span class="dashicons arrows"></span>
                    <input class="eddcm-test-question-answer-id" type="hidden" name="bpmj_eddcm_test_questions[0][answer][0][id]" value="">
                    <input class="eddcm-test-question-answer-title" type="text" name="bpmj_eddcm_test_questions[0][answer][0][title]">
                    <span class="dashicons dashicons-no-alt remove-module" data-action="remove-answer"></span>
                    <input class="eddcm-test-question-answer-points points-value" type="number" name="bpmj_eddcm_test_questions[0][answer][0][points]">&nbsp;<?php _e( 'Points', BPMJ_EDDCM_DOMAIN ); ?>
                </li>
            </template>
            <template id="bpmj_eddcm_new_test_question_template">
                <li class="module question editor">
                    <div class="question-header">
                        <span class="dashicons arrows"></span>
                        <input class="eddcm-test-question-id" type="hidden" name="bpmj_eddcm_test_questions[0][id]" value="<?php echo $question['id']; ?>">
                        <input class="eddcm-test-question-title" type="text" name="bpmj_eddcm_test_questions[0][title]" class="focus-me">
                        <select name="bpmj_eddcm_test_questions[0][type]" class="eddcm-test-question-type template">
                            <option><?php _e( 'Select question type', BPMJ_EDDCM_DOMAIN ); ?></option>
                            <option value="single_radio"><?php _e( 'Single choice question', BPMJ_EDDCM_DOMAIN ); ?>&nbsp;(radio)</option>
                            <option value="single_select"><?php _e( 'Single choice question', BPMJ_EDDCM_DOMAIN ); ?>&nbsp;(select)</option>
                            <option value="multiple"><?php _e( 'Multiple choice question', BPMJ_EDDCM_DOMAIN ); ?></option>
                            <option value="text"><?php _e( 'Text question', BPMJ_EDDCM_DOMAIN ); ?></option>
                            <option value="file"><?php _e( 'File question', BPMJ_EDDCM_DOMAIN ); ?></option>
                        </select>
                        <span class="dashicons dashicons-no-alt remove-module" data-action="remove-question"></span>
                    </div>
                    <div class="question-body">
                        <div class="question-type-single-tab question-type-tab">
                            <ul class="answers">
                                <li class="add-answer" data-action="add-answer" data-type="single">
                                    <span class="dashicons dashicons-plus"></span>
                                </li>
                            </ul>
                        </div>
                        <div class="question-type-text-tab question-type-tab"></div>
                        <div class="question-type-file-tab question-type-tab"></div>
                    </div>
                </li>
            </template>
        </div>
        <div class="pass-condition">
            <p>
                <label for="pass-condition-points-input"><?php _e( 'Points for passing', BPMJ_EDDCM_DOMAIN ); ?></label>
                <input id="pass-condition-points-input" type="number" min="0" max="0" value="<?php echo empty( $pass_points ) ? '0' : $pass_points; ?>" name="test_questions_points_pass"> / <span id="pass-condition-points">0</span>
                <input id="pass-condition-points-input-all" type="hidden" name="test_questions_points_all">
            </p>
        </div>
        <div class="creator-buttons text-center">
            <p>
                <button type="button" class="btn-eddcm btn-eddcm-primary" data-action="add-question"><?php _e( 'Add question', BPMJ_EDDCM_DOMAIN ); ?></button>
            </p>
        </div>
        <?php
    }

	/**
	 * Various displaying options metabox
	 *
	 * @param WP_Post $post
	 * @param string $box
	 */
	public function metabox_view_settings( $post, $box ) {
	    global $wpidea_settings;
		$settings = WPI()->settings;
		?>
		<div id="bpmj_eddcm_course_view_settings"> <!-- begin wrapper -->

			<div class="form-group" style="padding:10px;">
				<h2><?php _e( 'Course logo', BPMJ_EDDCM_DOMAIN ); ?></h2>
				<?php
				$general_settings                 = $settings->get_general_settings();
				$logo_options                     = $general_settings[ 'logo' ];
				$logo_options[ 'explicit_value' ] = get_post_meta( $post->ID, 'logo', true );
				$logo_options[ 'save_to' ]        = 'bpmj_wpidea';
				WPI()->settings->settings_api->output_field( $logo_options );
				WPI()->settings->settings_api->script_file( false );
				?>
			</div>
			<div class="form-group" style="padding:10px;">
				<h2><?php _e( 'Navigation labels', BPMJ_EDDCM_DOMAIN ); ?></h2>
				<?php
				$view_options = $settings->get_view_settings();

				$display_setting_from_core = function ( $option_key, $disabled = false ) use ( $view_options, $post, $wpidea_settings ) {
					$label_options         = $view_options[ $option_key ];
					$default_label_options = $label_options[ 'options' ];
					$default_label_value   = empty( $wpidea_settings[ $option_key ] ) ? $label_options[ 'default' ] : $wpidea_settings[ $option_key ];
					unset( $label_options[ 'desc' ] );
					$label_options[ 'explicit_value' ] = get_post_meta( $post->ID, $option_key, true );
					$label_options[ 'options' ]        = array_merge( array( '' => sprintf( __( 'Default for site (%s)', BPMJ_EDDCM_DOMAIN ), isset( $default_label_options[ $default_label_value ] ) ? $default_label_options[ $default_label_value ] : $default_label_value ) ), $label_options[ 'options' ] );
					$label_options[ 'save_to' ]        = 'bpmj_wpidea';
					$label_options[ 'disabled' ]       = $disabled;
					echo "<span class='form-grup-label'>" . $label_options[ 'label' ] . "</span>";
					?><br>
					<div class="item-group">
						<?php
						WPI()->settings->settings_api->output_field( $label_options );
						?>
					</div>
					<?php
				};

				$display_setting_from_core( 'navigation_next_lesson_label' );
				$display_setting_from_core( 'navigation_previous_lesson_label' );

				?>
				<script type="text/javascript">
					jQuery( function ( $ ) {
						<?php $settings->settings_api->script_radio_with_other_option(); ?>
					} );
				</script>
			</div>
			<div class="form-group" style="padding:10px;">
				<?php $display_setting_from_core( 'inaccessible_lesson_display' ); ?>
			</div>
			<?php if ( WPI()->templates->is_feature_supported( Templates::FEATURE_LESSON_NAVIGATION_POSITION ) ): ?>
			<div class="form-group" style="padding:10px;">
				<label for="navigation_section_position"><?php _e( 'Lesson navigation section position', BPMJ_EDDCM_DOMAIN ); ?></label>
				<select name="bpmj_wpidea[lesson_navigation_section_position]" id="navigation_section_position">
					<?php
					$selected_option = get_post_meta( $post->ID, 'lesson_navigation_section_position', true );
					foreach ( WPI()->settings->get_lesson_navigation_position_options( true ) as $option => $option_description ):
						?>
						<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $option, $selected_option ); ?>><?php echo esc_html( $option_description ); ?></option>
						<?php
					endforeach;
					?>
				</select>
			</div>
			<?php endif ?>
			<div class="form-group" style="padding:10px;">
				<label for="bpmj_eddcm_progress_tracking"><?php _e( 'Progress tracking', BPMJ_EDDCM_DOMAIN ); ?></label>
				<select name="bpmj_wpidea[progress_tracking]"
						id="bpmj_eddcm_progress_tracking" <?php disabled( WPI()->packages->no_access_to_feature( Packages::FEAT_PROGRESS_TRACKING ) ); ?>>
					<?php
					$selected_option = get_post_meta( $post->ID, 'progress_tracking', true );
					if ( !$selected_option ) {
						$selected_option = '';
					}
					?>
					<option value="" <?php echo selected( $selected_option, '' ); ?>><?php echo sprintf( __( 'Default (%s)', BPMJ_EDDCM_DOMAIN ), 'on' === $wpidea_settings['progress_tracking'] ? __( 'enabled', BPMJ_EDDCM_DOMAIN ) : __( 'disabled', BPMJ_EDDCM_DOMAIN ) ) ?></option>
					<option value="on" <?php echo selected( $selected_option, 'on' ); ?>><?php _e( 'Enabled', BPMJ_EDDCM_DOMAIN ); ?></option>
					<option value="off" <?php echo selected( $selected_option, 'off' ); ?>><?php _e( 'Disabled', BPMJ_EDDCM_DOMAIN ); ?></option>
				</select>
				<?php if ( WPI()->packages->no_access_to_feature( Packages::FEAT_PROGRESS_TRACKING ) ): ?>
					<p class="text-danger">
						<?php echo WPI()->packages->feature_not_available_message( Packages::FEAT_PROGRESS_TRACKING, __( 'In order to use course progress tracking, you need to upgrade your license to level: "%s"', BPMJ_EDDCM_DOMAIN ) ); ?>
					</p>
				<?php endif; ?>
			</div>
			<?php if ( WPI()->templates->is_feature_supported( Templates::FEATURE_LESSON_PROGRESS_POSITION ) ): ?>
			<div class="form-group" style="padding:10px;">
				<label for="progress_section_position"><?php _e( 'Lesson progress section position', BPMJ_EDDCM_DOMAIN ); ?></label>
				<select name="bpmj_wpidea[lesson_progress_section_position]" id="progress_section_position">
					<?php
					$selected_option = get_post_meta( $post->ID, 'lesson_progress_section_position', true );
					foreach ( WPI()->settings->get_lesson_progress_position_options( true ) as $option => $option_description ):
						?>
						<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $option, $selected_option ); ?>><?php echo esc_html( $option_description ); ?></option>
						<?php
					endforeach;
					?>
				</select>
			</div>
			<?php endif ?>
			<div class="form-group" style="padding:10px;">
				<?php $display_setting_from_core( 'progress_forced', WPI()->packages->no_access_to_feature( Packages::FEAT_PROGRESS_TRACKING ) ); ?>
			</div>

		</div> <!-- end wrapper -->
		<?php
	}

	/**
	 * @param WP_Post $post
	 */
	public function metabox_product_description( $post ) {
		$product_id = get_post_meta( $post->ID, 'product_id', true );
		$product    = get_post( $product_id );

		$editor_settings = array(
			'teeny'         => true,
			'textarea_name' => 'product_description_content',
			'textarea_rows' => 10,
		);

		wp_editor( $product->post_content, 'bpmj_wpidea_product_description_content', $editor_settings );

		?>
        <div class="form-group">
            <div class="desc"><?php _e( 'This text will be visible on the product details page (before purchase).', BPMJ_EDDCM_DOMAIN ); ?></div>
        </div>
		<?php
	}

	/**
	 * Zapisywanie danych z ustawień lekcji
	 */
	public function save_lesson_options( $post_id, $post ) {

		if ( isset( $_POST[ 'bpmj_wpidea_options_nonce' ] ) && !wp_verify_nonce( $_POST[ 'bpmj_wpidea_options_nonce' ], basename( __FILE__ ) ) ) {
			return $post_id;
		}

		if ( !current_user_can( 'edit_post', $post_id ) )
			{return $post_id;}


		if ( isset( $_POST[ 'bpmj_wpidea' ] ) ) {
			$options = $_POST[ 'bpmj_wpidea' ];

			foreach ( $options as $name => $value ) {

                if(is_string($value)) {
                    $value = strip_tags($value);
                }

				$old_meta = get_post_meta( $post_id, $name, true );

				if ( $value && $value !== $old_meta )
					{update_post_meta( $post_id, $name, $value );}

				elseif ( !$value && $old_meta )
					{delete_post_meta( $post_id, $name, $old_meta );}
			}
			if ( ! empty( $_POST['bpmj_wpidea_remove_files'] ) && empty( $options['files'] ) ) {
				delete_post_meta( $post_id, 'files', get_post_meta( $post_id, 'files', true ) );
			}
		}

		return $post_id;

	}

    /**
     * Zapisywanie pytań do testów
     */
    public function save_test_questions( $post_id, $post ) {

        if ( isset( $_POST[ 'bpmj_wpidea_test_questions_nonce' ] ) && !wp_verify_nonce( $_POST[ 'bpmj_wpidea_test_questions_nonce' ], basename( __FILE__ ) ) ) {
            return $post_id;
        }

        if ( !current_user_can( 'edit_post', $post_id ) )
            {return $post_id;}

     if ( isset( $_POST['quiz_is_passed'] ) && 'tests' === get_post_type( $post ) ) {

            update_post_meta( $post_id, 'is_passed', $_POST['quiz_is_passed'] );
            update_post_meta( $post_id, 'points', $_POST['points'] );

            if ( 'yes' === $_POST['quiz_is_passed'] ) {

                $quiz_id = get_post_meta( $post_id, 'quiz_id', true );
                $eddcm_id = get_post_meta( $quiz_id, '_bpmj_eddcm', true );
                $course_id = get_post_meta( $eddcm_id, 'course_id', true );

                $user_email = get_post_meta( $post_id, 'user_email', true);
                $user = get_user_by( 'email', $user_email );

                $user_progress = new Course_Progress( $course_id, $quiz_id, false, $user->ID );
                $user_progress->toggle_finished( true );
                $user_progress->read_progress();

                $wpidea_settings = get_option( 'wp_idea' );

                $disable_certificates_option = get_post_meta( $course_id, 'disable_certificates', true);

                if (
                    $wpidea_settings['enable_certificates'] == 'on' &&
                    $disable_certificates_option !== 'on' &&
                    WPI()->packages->has_access_to_feature(Packages::FEAT_CERTIFICATES) &&
                    $user_progress->get_progress_percent() === 100
                ) {
                    $course = $this->course_repository->find_by_id(new Course_ID($course_id));
                    $user = $this->user_repository->find_by_id(new User_ID($user->ID));

                    if ($course && $user) {
                        $certificate = $this->certificate_repository->create_certificate($course, $user);
                        $this->events->emit(Event_Name::CERTIFICATE_ISSUED, $certificate, $course, $user);
                    }
                }
            }
        }

        $this->events->emit(Event_Name::RESOLVED_QUIZ_RESULT_UPDATED, ['quiz_id' => $post_id]);
    }

	/**
	 * Zapisywanie danych z metaboxa
     *
	 * @param int $post_id
	 * @param WP_Post $post
	 *
	 * @return mixed
	 */
	public function save_courses_metabox( $post_id, $post ) {


		if ( isset( $_POST[ 'courses_manager_meta_noncename' ] ) && !wp_verify_nonce( $_POST[ 'courses_manager_meta_noncename' ], plugin_basename( __FILE__ ) ) ) {
			return $post->ID;
		}

		if ( !current_user_can( 'edit_post', $post->ID ) ) {
			return $post->ID;
		}

		if ( 'courses' !== get_post_type( $post_id ) ) {
			return $post->ID;
		}

		if ( count($_POST, COUNT_RECURSIVE) >= ini_get("max_input_vars")) {
		    WPI()->notices->add_dismissible_notice($this->translator->translate('edit_courses.max_input_vars.error'), Notices::TYPE_ERROR);
		    return $post->ID;
        }

		$course_id  = get_post_meta( $post_id, 'course_id', true );
		$product_id = get_post_meta( $post_id, 'product_id', true );

		if ( ! $course_id || ! $product_id ) {
			// If we don't have these two values it's probably too early to save the course using this method
			return $post->ID;
		}

		if ( isset( $_POST[ 'bpmj_wpidea' ] ) ) {
			$options = $_POST[ 'bpmj_wpidea' ];

			foreach ( $options as $name => $value ) {

                $value = strip_tags($value);
				$old_meta = get_post_meta( $post_id, $name, true );

				if ( $value && '' == $old_meta ) {
					add_post_meta( $post_id, $name, $value, true );
				} elseif ( $value && $value != $old_meta ) {
					update_post_meta( $post_id, $name, $value );
				} elseif ( '' == $value && $old_meta ) {
					delete_post_meta( $post_id, $name, $old_meta );
				}
			}
		}


		if ( ! wp_is_post_revision( $post_id ) ) {

			if ( empty( $_POST[ 'bpmj_eddcm_save_modules' ] ) ) {
				$current_pages = array();
			} else {
				/** Delete removed pages (modules and lessons) * */
				$old_pages_ids = array();
				$old_pages     = get_post_meta( $post->ID, 'module', true );
				if ( $old_pages ) {
					foreach ( $old_pages as $module ) {
						$old_pages_ids[] = $module[ 'id' ];
						if ( isset( $module[ 'module' ] ) ) {
							foreach ( $module[ 'module' ] as $lesson ) {
								$old_pages_ids[] = $lesson[ 'id' ];
							}
						}
					}
				}

				$current_pages_ids = array();
				$current_pages     = isset( $_POST[ 'bpmj_eddcm_module' ] ) && ! empty( $_POST[ 'bpmj_eddcm_module' ] ) ? $_POST[ 'bpmj_eddcm_module' ] : array();
				foreach ( $current_pages as $module ) {
					if ( isset( $module[ 'created_id' ] ) ) {
						$current_pages_ids[] = $module[ 'created_id' ];
					}

					if ( isset( $module[ 'module' ] ) ) {
						foreach ( $module[ 'module' ] as $lesson ) {
							if ( isset( $lesson[ 'created_id' ] ) ) {
								$current_pages_ids[] = $lesson[ 'created_id' ];
							}
						}
					}
				}

				$pages_to_delete = array_diff( $old_pages_ids, $current_pages_ids );
				foreach ( $pages_to_delete as $page_id ) {
					wp_delete_post( $page_id, true );
				}
			}

			remove_action( 'save_post', array( $this, 'save_courses_metabox' ), static::SAVE_METABOX_PRIORITY );

			$has_access_to_dripping = WPI()->packages->has_access_to_feature( Packages::FEAT_DELAYED_ACCESS );
			if ( ! $has_access_to_dripping ) {
				// If this feature is disabled we make sure it doesn't work
				unset( $_POST[ 'drip_value' ], $_POST[ 'drip_unit' ] );
			}

			if ( WPI()->packages->no_access_to_feature( Packages::FEAT_COURSE_ACCESS_TIME ) ) {
				// If this feature is disabled we make sure it doesn't work
				unset( $_POST[ 'access_time' ], $_POST[ 'access_time_unit' ] );
			}

			if ( WPI()->packages->no_access_to_feature( Packages::FEAT_RECURRING_PAYMENTS ) ) {
				unset( $_POST[ 'recurring_payments_enabled' ] );
			}

			if ( WPI()->packages->no_access_to_feature( Packages::FEAT_VARIABLE_PRICES ) ) {
				unset( $_POST[ 'variable_pricing' ] );
			}

			$variable_prices_update = get_post_meta( $post_id, 'variable_prices', true );
			$variable_prices_clean = get_post_meta( $product_id, 'edd_variable_prices', true );
			$changed = false;
			foreach ( $variable_prices_clean as $key => $variable_price ) {
				if( !empty( $variable_price['changed'] ) ) {
					unset( $variable_prices_clean[ $key ]['changed'] );

					$changed = true;
				}
			}

			if( $changed ) {
				$variable_prices_update = $variable_prices_clean;
				update_post_meta( $product_id, 'edd_variable_prices', $variable_prices_clean );
			}

            if ( ! empty( $_POST['variable_pricing'] ) ) {

                $edd_variable_prices = $variable_prices_update;
				if( ! empty( $_POST['variable_sale_price_from_date'] ) ) {
					foreach ( $edd_variable_prices as $key => $variable_price ) {
						$edd_variable_prices[ $key ]['tmp_sale_price'] = $edd_variable_prices[ $key ]['sale_price'];
						$edd_variable_prices[ $key ]['sale_price']     = '';
					}
				}

                $this->maybe_emit_variable_prices_updated_event($product_id, $edd_variable_prices, $_POST);

                update_post_meta( $product_id, 'edd_variable_prices', $edd_variable_prices );

                if( ! empty( $_POST['variable_prices'] ) ) {
					foreach ( $_POST['variable_prices'] as $price_id => $variable ) {
					    Product_Meta_Helper::save_gtu_from_string_for_variant($product_id, $price_id, $variable[ProductRepository::GTU_META_NAME]);
					    Product_Meta_Helper::save_flat_rate_tax_symbol_for_variant($product_id, $price_id, $variable[Flat_Rate_Tax_Symbol_Helper::META_NAME]);
					}
				}
            }

            $invoices_vat_rate = $_POST['invoices_vat_rate'] ?? '';
            Product_Meta_Helper::save_invoices_vat_rate( $product_id, sanitize_text_field( $invoices_vat_rate ) );

            $thumbnail_id = $_POST[ '_thumbnail_id' ] ??  '';
            if(empty($thumbnail_id)){
                $thumbnail_id = get_post_thumbnail_id( $post->ID ) ?? '';
            }

			$form = array(
				'post_title'                  => isset( $_POST[ 'post_title' ] ) ? $_POST[ 'post_title' ]: $post->post_title,
				'content'                     => isset( $_POST[ 'content' ] ) ? $_POST[ 'content' ] : $post->post_content,
				'price'                       => isset( $_POST[ 'price' ] ) ? $_POST[ 'price' ] : '',
				'sale_price'                  => isset( $_POST[ 'sale_price' ] ) ? $this->parse_sale_price_to_accepted_format($_POST[ 'sale_price' ]) : '',
				'tmp_sale_price'              => isset( $_POST[ 'tmp_sale_price' ] ) ? $_POST[ 'tmp_sale_price' ] : '',
                'sale_price_from_date'          => isset( $_POST[ 'sale_price_from_date' ] ) ? $_POST[ 'sale_price_from_date' ] : '',
                'sale_price_from_hour'          => isset( $_POST[ 'sale_price_from_hour' ] ) ? $_POST[ 'sale_price_from_hour' ] : '',
                'sale_price_to_date'          => isset( $_POST[ 'sale_price_to_date' ] ) ? $_POST[ 'sale_price_to_date' ] : '',
                'sale_price_to_hour'          => isset( $_POST[ 'sale_price_to_hour' ] ) ? $_POST[ 'sale_price_to_hour' ] : '',
                'variable_sale_price_from_date' => isset( $_POST[ 'variable_sale_price_from_date' ] ) ? $_POST[ 'variable_sale_price_from_date' ] : '',
                'variable_sale_price_from_hour' => isset( $_POST[ 'variable_sale_price_from_hour' ] ) ? $_POST[ 'variable_sale_price_from_hour' ] : '',
                'variable_sale_price_to_date' => isset( $_POST[ 'variable_sale_price_to_date' ] ) ? $_POST[ 'variable_sale_price_to_date' ] : '',
                'variable_sale_price_to_hour' => isset( $_POST[ 'variable_sale_price_to_hour' ] ) ? $_POST[ 'variable_sale_price_to_hour' ] : '',
				'access_time'                 => isset( $_POST[ 'access_time' ] ) ? $_POST[ 'access_time' ] : '',
				'access_start'                => isset( $_POST[ 'access_start' ] ) ? $_POST[ 'access_start' ] : '',
				'access_time_unit'            => isset( $_POST[ 'access_time_unit' ] ) ? $_POST[ 'access_time_unit' ] : '',
				'drip_value'                  => isset( $_POST[ 'drip_value' ] ) ? $_POST[ 'drip_value' ] : '',
				'drip_unit'                   => isset( $_POST[ 'drip_unit' ] ) ? $_POST[ 'drip_unit' ] : '',
				'redirect_page'               => isset( $_POST[ 'redirect_page' ] ) ? $_POST[ 'redirect_page' ] : '',
				'certificate_template_id'    => isset( $_POST[ 'certificate_template_id' ] ) ? $_POST[ 'certificate_template_id' ] : '',
                'custom_purchase_link'        => isset( $_POST['custom_purchase_link'] ) ? $_POST['custom_purchase_link'] : '',
				'redirect_url'                => isset( $_POST[ 'redirect_url' ] ) ? $_POST[ 'redirect_url' ] : '',
				'_thumbnail_id'               => $thumbnail_id,
				'product_description_content' => isset( $_POST[ 'product_description_content' ] ) ? $_POST[ 'product_description_content' ] : '',
				'recurring_payments_enabled'  => isset( $_POST[ 'recurring_payments_enabled' ] ) ? $_POST[ 'recurring_payments_enabled' ] : '',
				'variable_pricing'            => isset( $_POST[ 'variable_pricing' ] ) ? $_POST[ 'variable_pricing' ] : '',
				'purchase_limit'              => isset( $_POST[ 'purchase_limit' ] ) && ! empty( $_POST[ 'purchase_limit' ] ) ? (int) $_POST[ 'purchase_limit' ] : '',
				'purchase_limit_items_left'   => isset( $_POST[ 'purchase_limit_items_left' ] ) && ! empty( $_POST[ 'purchase_limit_items_left' ] ) ? (int) $_POST[ 'purchase_limit_items_left' ] : '',
			);

            $reset_price = false;
            if ( ! empty( $form['sale_price'] ) && ! empty( $form['sale_price_from_date'] ) ) {
                $reset_price = true;
                $form['tmp_sale_price'] = $form['sale_price'];
            }

            $this->maybe_emit_regular_or_sale_price_changed_events($product_id, $form, $reset_price);

			$purchase_limit_unlimited = false;
			if ( ! empty( $form[ 'variable_pricing' ] ) ) {
                $variable_pricing_value_before_save  = get_post_meta( $post_id, 'variable_pricing', true );
                $was_variable_pricing_enabled        = ! empty( $variable_pricing_value_before_save );
				$variable_prices                     = edd_get_variable_prices( $product_id );
				$form[ 'purchase_limit' ]            = 0;
				$form[ 'purchase_limit_items_left' ] = 0;
				$any_purchase_limit_empty            = false;

				foreach ( $variable_prices as $price ) {
					if ( ! empty( $price[ 'bpmj_eddcm_purchase_limit' ] ) ) {
						$form[ 'purchase_limit' ] += (int) $price[ 'bpmj_eddcm_purchase_limit' ];
					} else {
						$any_purchase_limit_empty = true;
					}
					if ( ! empty( $price[ 'bpmj_eddcm_purchase_limit_items_left' ] ) ) {
						$form[ 'purchase_limit_items_left' ] += (int) $price[ 'bpmj_eddcm_purchase_limit_items_left' ];
					}
				}
				if ( $any_purchase_limit_empty && ! empty( $form[ 'purchase_limit' ] ) ) {
					$purchase_limit_unlimited = true;
				}
			} else {
				if ( $form[ 'purchase_limit_items_left' ] > $form[ 'purchase_limit' ] ) {
					$form[ 'purchase_limit_items_left' ] = $form[ 'purchase_limit' ];
				}
			}

			$form[ 'purchase_limit_unlimited' ] = $purchase_limit_unlimited;



			if ( ! empty( $form[ 'access_start' ] ) && ! empty( $_POST[ 'access_start_hh' ] ) && ! empty( $_POST[ 'access_start_mm' ] ) ) {
				$form[ 'access_start' ] .= ' ' . $_POST[ 'access_start_hh' ] . ':' . $_POST[ 'access_start_mm' ];
			}

			if ( ! empty( $form[ 'access_start' ] ) && ( false === strtotime( $form[ 'access_start' ] ) || WPI()->packages->no_access_to_feature( Packages::FEAT_COURSE_ACCESS_START ) ) ) {
				$form[ 'access_start' ] = '';
			}

			// Redirect Array
			$redirect = array(
				'page' => $form[ 'redirect_page' ],
				'url'  => $form[ 'redirect_url' ],
			);

			// Update course details
			$args = array(
				'ID'            => $course_id,
				'post_title'    => $form[ 'post_title' ],
				'post_content'  => $form[ 'content' ],
				'post_date'     => $post->post_date,
				'post_date_gmt' => $post->post_date_gmt,
				'meta_input'    => array(
					'_bpmj_eddpc_redirect_page'        => $form[ 'redirect_page' ],
					'_bpmj_eddpc_redirect_url'         => $form[ 'redirect_url' ],
					'_bpmj_eddpc_access_start_enabled' => ! empty( $form[ 'access_start' ] ),
					'_bpmj_eddpc_access_start'         => $form[ 'access_start' ],
					'_thumbnail_id'                    => $form[ '_thumbnail_id' ],
				),
			);
			wp_update_post( $args );

			/** Create and update pages (modules and lessons) * */
			foreach ( $current_pages as $module_order => $module ) {

				$module_restricted_to = array();
				if ( $form[ 'variable_pricing' ] ) {
                    if ( ! $was_variable_pricing_enabled ) {
                        $module_restricted_to[] = array( 'download' => $product_id, 'price_id' => 'all' );
                    } else {
                        if ( ! empty( $module[ 'variable_prices' ] ) && is_array( $module[ 'variable_prices' ] ) ) {
                            foreach ( $module[ 'variable_prices' ] as $price_id ) {
                                $module_restricted_to[] = array( 'download' => $product_id, 'price_id' => $price_id );
                            }
                        } else {
                            $module_restricted_to[] = array( 'download' => $product_id, 'price_id' => -1 );
                        }
                    }
				} else {
					$module_restricted_to[] = array( 'download' => $product_id, 'price_id' => 'all' );
				}
				if ( isset( $module[ 'created_id' ] ) ) {
					// Update module
					$args = array(
						'ID'          => $module[ 'created_id' ],
						'post_title'  => $module[ 'title' ],
						'post_parent' => $course_id,
						'menu_order'  => $module_order,
						'meta_input'  => array(
							/* 'mode' => isset( $module['mode'] ) ? $module['mode'] : 'lesson',
							  'subtitle' => isset( $module['subtitle'] ) ? $module['subtitle'] : '',
							  'level' => isset( $module['level'] ) ? $module['level'] : '',
							  'duration' => isset( $module['duration'] ) ? $module['duration'] : '',
							  'shortdesc' => isset( $module['shortdesc'] ) ? $module['shortdesc'] : '',
							  'files' => isset( $module['files'] ) ? $module['files'] : '', */
							'_bpmj_eddpc_redirect_page'        => $form[ 'redirect_page' ],
							'_bpmj_eddpc_redirect_url'         => $form[ 'redirect_url' ],
							'_bpmj_eddpc_access_start_enabled' => ! empty( $form[ 'access_start' ] ),
							'_bpmj_eddpc_access_start'         => $form[ 'access_start' ],
							'_bpmj_eddcm'                      => $post_id,
							'_bpmj_eddpc_drip_value'           => $has_access_to_dripping ? $module[ 'drip_value' ] : '',
							'_bpmj_eddpc_drip_unit'            => $has_access_to_dripping ? $module[ 'drip_unit' ] : '',
							'_bpmj_eddpc_restricted_to'        => $module_restricted_to,
						),
					);

					wp_update_post( $args );
					$module_id = $module[ 'created_id' ];

				} else {
					// Create new module
					$module_id = WPI()->courses->insert( $course_id, $module, $product_id, $module_order, $redirect, array(
						'_bpmj_eddpc_access_start_enabled' => ! empty( $form[ 'access_start' ] ),
						'_bpmj_eddpc_access_start'         => $form[ 'access_start' ],
						'_bpmj_eddcm'                      => $post_id,
						'_bpmj_eddpc_drip_value'           => $has_access_to_dripping ? $module[ 'drip_value' ] : '',
						'_bpmj_eddpc_drip_unit'            => $has_access_to_dripping ? $module[ 'drip_unit' ] : '',
						'_bpmj_eddpc_restricted_to'        => $module_restricted_to,
					) );
				}

				// Save data to array
				$current_pages[ $module_order ][ 'id' ] = $module_id;
				unset( $current_pages[ $module_order ][ 'title' ] );
				unset( $current_pages[ $module_order ][ 'content' ] );


				// Lessons
				if ( isset( $module[ 'module' ] ) ) {
					foreach ( $module[ 'module' ] as $lesson_order => $lesson ) {
						$lesson_restricted_to = array();
						if ( $form[ 'variable_pricing' ] ) {
                            if ( ! $was_variable_pricing_enabled ) {
                                $lesson_restricted_to[] = array( 'download' => $product_id, 'price_id' => 'all' );
                            } else {
                                if (!empty( $lesson[ 'variable_prices' ] ) && is_array( $lesson[ 'variable_prices' ] ) ) {
                                    foreach ( $lesson[ 'variable_prices' ] as $price_id ) {
                                        $lesson_restricted_to[] = array(
                                            'download' => $product_id,
                                            'price_id' => $price_id
                                        );
                                    }
                                } else {
                                    $lesson_restricted_to[] = array( 'download' => $product_id, 'price_id' => -1 );
                                }
                            }
						} else {
							$lesson_restricted_to[] = array( 'download' => $product_id, 'price_id' => 'all' );
						}

						if ( isset( $lesson[ 'created_id' ] ) ) {
						// Update lesson
							$args = array(
								'ID'          => $lesson[ 'created_id' ],
								'post_title'  => $lesson[ 'title' ],
								'post_parent' => $module_id,
								'menu_order'  => $lesson_order,
								'meta_input'  => array(
									/* 'mode' => isset( $lesson['mode'] ) ? $lesson['mode'] : 'lesson',
									  'subtitle' => isset( $lesson['subtitle'] ) ? $lesson['subtitle'] : '',
									  'level' => isset( $lesson['level'] ) ? $lesson['level'] : '',
									  'duration' => isset( $lesson['duration'] ) ? $lesson['duration'] : '',
									  'shortdesc' => isset( $lesson['shortdesc'] ) ? $lesson['shortdesc'] : '',
									  'files' => isset( $lesson['files'] ) ? $lesson['files'] : '', */
									'_bpmj_eddpc_redirect_page'        => $form[ 'redirect_page' ],
									'_bpmj_eddpc_redirect_url'         => $form[ 'redirect_url' ],
									'_bpmj_eddpc_access_start_enabled' => ! empty( $form[ 'access_start' ] ),
									'_bpmj_eddpc_access_start'         => $form[ 'access_start' ],
									'_bpmj_eddcm'                      => $post_id,
									'_bpmj_eddpc_drip_value'           => $has_access_to_dripping ? $lesson[ 'drip_value' ] : '',
									'_bpmj_eddpc_drip_unit'            => $has_access_to_dripping ? $lesson[ 'drip_unit' ] : '',
									'_bpmj_eddpc_restricted_to'        => $lesson_restricted_to,
								),
							);

							wp_update_post( $args );
							$lesson_id = $lesson[ 'created_id' ];

						} else {
							// Create new lesson
							$lesson_id = WPI()->courses->insert( $module_id, $lesson, $product_id, $lesson_order, $redirect, array(
								'_bpmj_eddpc_access_start_enabled' => ! empty( $form[ 'access_start' ] ),
								'_bpmj_eddpc_access_start'         => $form[ 'access_start' ],
								'_bpmj_eddcm'                      => $post_id,
								'_bpmj_eddpc_drip_value'           => $has_access_to_dripping ? $lesson[ 'drip_value' ] : '',
								'_bpmj_eddpc_drip_unit'            => $has_access_to_dripping ? $lesson[ 'drip_unit' ] : '',
								'_bpmj_eddpc_restricted_to'        => $lesson_restricted_to,
							) );
						}

						// Save data to array
						$current_pages[ $module_order ][ 'module' ][ $lesson_order ][ 'id' ] = $lesson_id;
						unset( $current_pages[ $module_order ][ 'module' ][ $lesson_order ][ 'title' ] );
						unset( $current_pages[ $module_order ][ 'module' ][ $lesson_order ][ 'content' ] );
					}
				}
			}

			/** Update product meta * */
			$args = array(
				'ID'            => $product_id,
				'post_title'    => $form[ 'post_title' ],
				'post_name'     => $form[ 'post_title' ],
				'post_content'  => $form[ 'product_description_content' ],
				'post_date'     => $post->post_date,
				'post_date_gmt' => $post->post_date_gmt,
				'meta_input'    => array(
					'edd_price'                             => $form[ 'price' ],
					'edd_sale_price'                        => $reset_price ? '' : $form[ 'sale_price' ],
					'tmp_sale_price'                        => $form[ 'tmp_sale_price' ],
                    'sale_price_from_date'                  => $form[ 'sale_price_from_date' ],
                    'sale_price_from_hour'                  => $form[ 'sale_price_from_hour' ],
                    'sale_price_to_date'                    => $form[ 'sale_price_to_date' ],
                    'sale_price_to_hour'                    => $form[ 'sale_price_to_hour' ],
                    'variable_sale_price_from_date'         => $form[ 'variable_sale_price_from_date' ],
                    'variable_sale_price_from_hour'         => $form[ 'variable_sale_price_from_hour' ],
                    'variable_sale_price_to_date'           => $form[ 'variable_sale_price_to_date' ],
                    'variable_sale_price_to_hour'           => $form[ 'variable_sale_price_to_hour' ],
					'_bpmj_eddpc_access_time'               => $form[ 'access_time' ],
					'_bpmj_eddpc_access_time_unit'          => $form[ 'access_time_unit' ],
					'_edd_mailchimp'                        => isset( $_POST[ '_edd_mailchimp' ] ) ? $_POST[ '_edd_mailchimp' ] : '',
					'_edd_mailerlite'                       => isset( $_POST[ '_edd_mailerlite' ] ) ? $_POST[ '_edd_mailerlite' ] : '',
					'_edd_freshmail'                        => isset( $_POST[ '_edd_freshmail' ] ) ? $_POST[ '_edd_freshmail' ] : '',
					'_edd_ipresso'                          => isset( $_POST[ '_edd_ipresso' ] ) ? $_POST[ '_edd_ipresso' ] : '',
					'_edd_ipresso_unsubscribe'              => isset( $_POST[ '_edd_ipresso_unsubscribe' ] ) ? $_POST[ '_edd_ipresso_unsubscribe' ] : '',
					'_edd_convertkit'                       => isset( $_POST[ '_edd_convertkit' ] ) ? $_POST[ '_edd_convertkit' ] : '',
					'_edd_convertkit_unsubscribe'           => isset( $_POST[ '_edd_convertkit_unsubscribe' ] ) ? $_POST[ '_edd_convertkit_unsubscribe' ] : '',
					'_edd_convertkit_tags'                  => isset( $_POST[ '_edd_convertkit_tags' ] ) ? $_POST[ '_edd_convertkit_tags' ] : '',
					'_edd_convertkit_tags_unsubscribe'      => isset( $_POST[ '_edd_convertkit_tags_unsubscribe' ] ) ? $_POST[ '_edd_convertkit_tags_unsubscribe' ] : '',
					'_edd_activecampaign'                   => isset( $_POST[ '_edd_activecampaign' ] ) ? $_POST[ '_edd_activecampaign' ] : '',
					'_edd_activecampaign_unsubscribe'       => isset( $_POST[ '_edd_activecampaign_unsubscribe' ] ) ? $_POST[ '_edd_activecampaign_unsubscribe' ] : '',
					'_edd_activecampaign_tags'              => isset( $_POST[ '_edd_activecampaign_tags' ] ) ? $_POST[ '_edd_activecampaign_tags' ] : '',
					'_edd_activecampaign_tags_unsubscribe'  => isset( $_POST[ '_edd_activecampaign_tags_unsubscribe' ] ) ? $_POST[ '_edd_activecampaign_tags_unsubscribe' ] : '',
					'_edd_getresponse'                      => isset( $_POST[ '_edd_getresponse' ] ) ? $_POST[ '_edd_getresponse' ] : '',
					'_edd_getresponse_tags'                 => isset( $_POST[ '_edd_getresponse_tags' ] ) ? $_POST[ '_edd_getresponse_tags' ] : '',
					'_edd_getresponse_unsubscribe'          => isset( $_POST[ '_edd_getresponse_unsubscribe' ] ) ? $_POST[ '_edd_getresponse_unsubscribe' ] : '',
					'_bpmj_edd_sm_tags'                     => isset( $_POST[ '_bpmj_edd_sm_tags' ] ) ? $_POST[ '_bpmj_edd_sm_tags' ] : '',
					'_edd_interspire'                       => isset( $_POST[ '_edd_interspire' ] ) ? $_POST[ '_edd_interspire' ] : '',
					'_thumbnail_id'                         => $form[ '_thumbnail_id' ],
					'_bpmj_eddpc_access_start_enabled'      => ! empty( $form[ 'access_start' ] ),
					'_bpmj_eddpc_access_start'              => $form[ 'access_start' ],
					'_edd_recurring_payments_enabled'       => $form[ 'recurring_payments_enabled' ],
					'_variable_pricing'                     => $form[ 'variable_pricing' ],
					'_bpmj_eddcm_purchase_limit'            => ! empty( $form[ 'purchase_limit' ] ) ? $form[ 'purchase_limit' ] : '',
					'_bpmj_eddcm_purchase_limit_items_left' => ! empty( $form[ 'purchase_limit_items_left' ] ) ? $form[ 'purchase_limit_items_left' ] : '',
					'_bpmj_eddcm_purchase_limit_unlimited'  => $form[ 'purchase_limit_unlimited' ] ? '1' : '',
					'custom_purchase_link'                  => $form['custom_purchase_link']
				),
			);
			wp_update_post( array_filter( $args ) );


			/** Update course cpt meta * */
			$args = array(
				'ID'         => $post_id,
				'meta_input' => array(
					'price'                      => $form[ 'price' ],
					'sale_price'                 => $form[ 'sale_price' ],
					'tmp_sale_price'             => $form[ 'tmp_sale_price' ],
                    'sale_price_from_date'       => $form[ 'sale_price_from_date' ],
                    'sale_price_from_hour'       => $form[ 'sale_price_from_hour' ],
                    'sale_price_to_date'         => $form[ 'sale_price_to_date' ],
                    'sale_price_to_hour'         => $form[ 'sale_price_to_hour' ],
                    'variable_sale_price_from_date' => $form[ 'variable_sale_price_from_date' ],
                    'variable_sale_price_from_hour' => $form[ 'variable_sale_price_from_hour' ],
                    'variable_sale_price_to_date' => $form[ 'variable_sale_price_to_date' ],
                    'variable_sale_price_to_hour' => $form[ 'variable_sale_price_to_hour' ],
					'access_time'                => $form[ 'access_time' ],
					'access_time_unit'           => $form[ 'access_time_unit' ],
					'access_start'               => $form[ 'access_start' ],
					'drip_value'                 => $form[ 'drip_value' ],
					'drip_unit'                  => $form[ 'drip_unit' ],
					'redirect_page'              => $form[ 'redirect_page' ],
					'certificate_template_id'   => $form[ 'certificate_template_id' ],
					'redirect_url'               => $form[ 'redirect_url' ],
					'_thumbnail_id'              => $form[ '_thumbnail_id' ],
					'recurring_payments_enabled' => $form[ 'recurring_payments_enabled' ],
					'variable_pricing'           => $form[ 'variable_pricing' ],
					'variable_prices'			 => $variable_prices_update
				),
			);
			if ( ! empty( $_POST[ 'bpmj_eddcm_save_modules' ] ) ) {
				$args[ 'meta_input' ][ 'module' ] = $current_pages;
			}
			wp_update_post( $args );

			add_action( 'save_post', array( $this, 'save_courses_metabox' ), static::SAVE_METABOX_PRIORITY, 2 );

			bpmj_eddcm_check_sales_dates();

			/** Drip posts * */
//			WPI()->courses->drip( $post_id );
		}

		return $post->ID;
	}

	private function parse_sale_price_to_accepted_format($sale_price){
            return ($sale_price == "0") ? "0.0" : $sale_price;
	}

	/**
	 *
	 */
	public function get_variable_prices($post_id) {

		if ( 'download' === get_post_type( $post_id ) ) {
			$product_id = $post_id;
			$course_id  = null;
			/*
			 * These functions were removed in EDD Paid Content - we need to reenable them, but only for normal products
			 * and bundles
			 */
			if ( function_exists( 'edd_meta_box_recurring_payments_interval_variable_head' ) ) {
				add_action( 'edd_download_price_table_head', 'edd_meta_box_recurring_payments_interval_variable_head' );
			}

			if ( function_exists( 'edd_meta_box_recurring_payments_interval_variable_row' ) ) {
				add_action( 'edd_download_price_table_row', 'edd_meta_box_recurring_payments_interval_variable_row', 10, 2 );
			}
		} else {
			$course_id  = $post_id;
			$product_id = get_post_meta( $course_id, 'product_id', true );
		}
		add_action( 'edd_download_price_table_head', array( $this, 'render_variable_prices_purchase_limit_head' ) );
		add_action( 'edd_download_price_table_row', array( $this, 'render_variable_prices_purchase_limit_row' ), 10, 3 );
		add_filter( 'edd_price_row_args', array( $this, 'variable_prices_purchase_limit_row_args' ), 10, 2 );

		remove_filter( 'edd_get_variable_prices', array( EDD_Sale_Price()->price, 'maybe_display_variable_sale_prices' ), 10 );

		remove_action( 'edd_download_price_table_head', array( EDD_Sale_Price()->admin_product, 'add_variable_sale_price_header' ), 5 );
		remove_action( 'edd_download_price_table_row', array( EDD_Sale_Price()->admin_product, 'variable_sale_price_field' ), 5 );

		add_action( 'edd_download_price_table_head', array( EDD_Sale_Price()->admin_product, 'add_variable_sale_price_header' ), 5 );
		add_action( 'edd_download_price_table_row', array( $this, 'variable_sale_price_field' ), 5, 3 );

		 return View::get_admin('/course/edit-variable-prices', [
            'product_id' => $product_id,
        ]);
	}

	/**
	 * Variable sale price.
	 *
	 * Display the variable sale price field.
	 *
	 * @param int $post_id ID of the download post.
	 * @param int $key Index key of the current price variation.
	 * @param array $args Array of value arguments.
	 *@see EDDSP_Admin_Product::variable_sale_price_field()
	 *
	 *
	 */
	public function variable_sale_price_field( $post_id, $key, $args ) {

		$defaults = array(
			'sale_price' => null,
		);
		$args     = wp_parse_args( $args, $defaults );

		$course = WPI()->courses->get_course_by_product( $post_id );

		$variable_prices = get_post_meta( $course->ID, 'variable_prices', true );
		if ( ! empty( $variable_prices[ $key ]['tmp_sale_price'] ) )
            {$args['sale_price'] = $variable_prices[ $key ]['tmp_sale_price'];}
		else {if ( ! empty( $variable_prices[ $key ]['sale_price'] ) )
            {$args['sale_price'] = $variable_prices[ $key ]['sale_price'];}}

		?>
		<td><?php

		$price_args = array(
			'name'  => 'edd_variable_prices[' . $key . '][sale_price]',
			'value' => ! empty( $args[ 'sale_price' ] ) ? $args[ 'sale_price' ] : '',
			'class' => 'edd-price-field edd-sale-price-field'
		);

		$currency_position = edd_get_option( 'currency_position' );
		if ( empty( $currency_position ) || $currency_position == 'before' ) :
			?>
			<span><?php echo '<span>' . edd_currency_filter( '' ) . ' ' . EDD()->html->text( $price_args ); ?></span><?php
		else :
			?><span>
                <?php echo EDD()->html->text( $price_args ); ?>
            </span>
        <?php

		endif;

		?></td><?php

	}

	/**
	 * @param array $args
	 * @param array $value
	 *
	 * @return array
	 */
	public function variable_prices_purchase_limit_row_args( $args, $value ) {
		$args[ 'bpmj_eddcm_purchase_limit' ]            = isset( $value[ 'bpmj_eddcm_purchase_limit' ] ) ? $value[ 'bpmj_eddcm_purchase_limit' ] : '';
		$args[ 'bpmj_eddcm_purchase_limit_items_left' ] = isset( $value[ 'bpmj_eddcm_purchase_limit_items_left' ] ) ? $value[ 'bpmj_eddcm_purchase_limit_items_left' ] : '';

		return $args;
	}

	/**
	 *
	 */
	public function render_variable_prices_purchase_limit_head() {
		?>
		<th><?php _e( 'Purchase limit', BPMJ_EDDCM_DOMAIN ); ?></th>
		<th><?php _e( 'Items left', BPMJ_EDDCM_DOMAIN ); ?></th>
		<?php
	}

	/**
	 * @param int $post_id
	 * @param string $key
	 * @param array $args
	 */
	public function render_variable_prices_purchase_limit_row( $post_id, $key, $args ) {
		?>
		<td><input type="number" name="edd_variable_prices[<?php echo $key; ?>][bpmj_eddcm_purchase_limit]"
		           value="<?php echo isset( $args[ 'bpmj_eddcm_purchase_limit' ] ) ? esc_attr( $args[ 'bpmj_eddcm_purchase_limit' ] ) : ''; ?>"
		           title="<?php esc_attr_e( 'Purchase limit', BPMJ_EDDCM_DOMAIN ); ?>"
		           style="width: 50px;"/>
			<input type="hidden"
			       name="edd_variable_prices[<?php echo $key; ?>][bpmj_eddcm_purchase_limit_prev]"
			       value="<?php echo isset( $args[ 'bpmj_eddcm_purchase_limit' ] ) ? esc_attr( $args[ 'bpmj_eddcm_purchase_limit' ] ) : ''; ?>"/>
		</td>
		<td><input type="number" name="edd_variable_prices[<?php echo $key; ?>][bpmj_eddcm_purchase_limit_items_left]"
		           value="<?php echo isset( $args[ 'bpmj_eddcm_purchase_limit_items_left' ] ) ? esc_attr( $args[ 'bpmj_eddcm_purchase_limit_items_left' ] ) : ''; ?>"
		           title="<?php esc_attr_e( 'Items left', BPMJ_EDDCM_DOMAIN ); ?>"
		           style="width: 50px;"/>
		</td>
		<?php
	}

	public function save_variable_prices(int $product_id, array $fields) {

		add_filter( 'sanitize_post_meta_edd_variable_prices', array( $this, 'sanitize_variable_prices' ) );
		if ( function_exists( 'edd_meta_box_recurring_payments_interval_save_variable' ) && ! has_filter( 'edd_metabox_save_edd_variable_prices', 'edd_meta_box_recurring_payments_interval_save_variable' ) ) {
			add_filter( 'edd_metabox_save_edd_variable_prices', 'edd_meta_box_recurring_payments_interval_save_variable' );
		}

		$new_price_ids = array();
		foreach ( $fields as $field => $variable_field) {

			if ( '_edd_default_price_id' === $field && edd_has_variable_prices( $product_id ) ) {

				if ( isset( $variable_field ) ) {
					$new_default_price_id = ( ! empty( $variable_field ) && is_numeric( $variable_field ) ) || ( 0 === (int) $variable_field ) ? (int) $variable_field : 1;
				} else {
					$new_default_price_id = 1;
				}

				update_post_meta( $product_id, $field, $new_default_price_id );

			} else {

				if ( ! empty( $variable_field ) ) {
					if ( 'edd_variable_prices' === $field ) {
						uasort( $variable_field, function ( $a, $b ) {
							if( empty( $a[ 'index' ] ) || empty( $b[ 'index' ] ) ) {
								return 0;
							}

							if ( '' === $a[ 'index' ] || '' === $b[ 'index' ] ) {
								if ( $a[ 'index' ] === $b[ 'index' ] ) {
									return 0;
								}

								return '' === $a[ 'index' ] ? 1 : - 1;
							}
							$a_int = (int) $a[ 'index' ];
							$b_int = (int) $b[ 'index' ];
							if ( $a_int === $b_int ) {
								return 0;
							}

							return $a_int < $b_int ? - 1 : 1;
						} );

						foreach ( $variable_field as $id => $f ) {
                            if(is_array($variable_field[$id])) {
                               $variable_field[$id]['changed'] = 1;
                            }
                        }

                        $new           = apply_filters( 'edd_metabox_save_' . $field, $variable_field );
                        $new_price_ids = $this->update_lessons_to_prices( $product_id, $new );

                        $this->events->emit(Product_Event_Name::VARIABLE_PRICES_UPDATED, $product_id, $new);
					}
					else {
					    $new           = apply_filters( 'edd_metabox_save_' . $field, $variable_field );
					}
					update_post_meta( $product_id, $field, $new );
				} else {
					delete_post_meta( $product_id, $field );
				}
			}
		}
		if ( edd_has_variable_prices( $product_id ) ) {
			$lowest = edd_get_lowest_price_option( $product_id );
			update_post_meta( $product_id, 'edd_price', $lowest );
		}

		$variable_prices_template = Creator::get_variable_prices_module_template( $product_id );
		$variable_prices          = edd_get_variable_prices( $product_id );
		bpmj_eddcm_set_overall_purchase_limits( $product_id, $variable_prices );

		$result = array(
			'variable_prices_summary_html'     => $this->get_variable_prices_html( $variable_prices, edd_get_default_variable_price( $product_id ) ),
			'variable_prices_module_template'  => $variable_prices_template,
			'variable_prices_module_legend'    => Creator::get_variable_prices_module_legend( $product_id ),
			'new_module_full_template'         => Creator::create_module_get_html( 'full', false, false, '', true, false, $variable_prices_template ),
			'new_module_module_template'       => Creator::create_module_get_html( 'lesson', false, false, '', true, false, $variable_prices_template ),
			'new_lesson_template'              => Creator::create_lesson_get_html( false, false, true, false, $variable_prices_template ),
			'variable_prices_add_to_cart_html' => static::get_variable_prices_add_to_cart_links_html( $product_id, $variable_prices ),
			'new_price_ids'                    => $new_price_ids,
		);

		do_action( 'wpi_after_save_variable_prices' );

		return $result;
	}

	/**
	 * @param int $product_id
	 * @param array $new_prices
	 *
	 * @return array
	 */
	protected function update_lessons_to_prices( $product_id, $new_prices ) {
		$old_prices    = get_post_meta( $product_id, 'edd_variable_prices', true );
		$new_price_ids = array_values( array_diff( array_keys( array_filter( $new_prices ) ), array_keys( $old_prices ) ) );
		$product_id    = (int) $product_id;
		if ( ! empty( $new_price_ids ) ) {
			$course = WPI()->courses->get_course_by_product( $product_id );
			if ( ! $course ) {
				return $new_price_ids;
			}
			$course_id     = $course->ID;
			$add_price_ids = function ( $post_id ) use ( $new_price_ids, $product_id ) {
				if ( ! $post_id ) {
					return;
				}

				$restricted_to = get_post_meta( $post_id, '_bpmj_eddpc_restricted_to', true );
				if ( ! is_array( $restricted_to ) ) {
					$restricted_to = array();
				}

				// clear 'all' price_id
				foreach ( $restricted_to as $restriction_key => $restriction_rule ) {
					if ( (int) $restriction_rule[ 'download' ] === $product_id && ( 'all' === $restriction_rule[ 'price_id' ] || in_array( (int) $restriction_rule[ 'price_id' ], $new_price_ids ) ) ) {
						unset( $restricted_to[ $restriction_key ] );
					}
				}

				foreach ( $new_price_ids as $price_id ) {
					$restricted_to[] = array(
						'download' => $product_id,
						'price_id' => $price_id,
					);
				}
				update_post_meta( $post_id, '_bpmj_eddpc_restricted_to', $restricted_to );
			};

			$modules = get_post_meta( $course_id, 'module', true );
			if ( is_array( $modules ) ) {
				foreach ( $modules as $module ) {
					$module_id = isset( $module[ 'id' ] ) ? $module[ 'id' ] : false;
					$lessons   = isset( $module[ 'module' ] ) ? $module[ 'module' ] : false;
					if ( is_array( $lessons ) ) {
						foreach ( $lessons as $lesson ) {
							$lesson_id = isset( $lesson[ 'id' ] ) ? $lesson[ 'id' ] : false;
							$add_price_ids( $lesson_id );
						}
					}
					$add_price_ids( $module_id );
				}
			}
		}

		return $new_price_ids;
	}

	/**
	 * @param array $prices
	 *
	 * @return array
	 */
	public function sanitize_variable_prices( $prices ) {
		foreach ( $prices as $id => $price ) {

			if(!empty($price['sale_price']) || $price['sale_price'] === "0")
			{
				$price['sale_price'] = number_format(str_replace(',','.',preg_replace('/\s+/', '',$price['sale_price'])), 2, '.', '');
			}

			$purchase_limit      = isset( $price[ 'bpmj_eddcm_purchase_limit' ] ) ? (int) $price[ 'bpmj_eddcm_purchase_limit' ] : '';
			$purchase_limit_prev = isset( $price[ 'bpmj_eddcm_purchase_limit_prev' ] ) ? (int) $price[ 'bpmj_eddcm_purchase_limit_prev' ] : '';
			$items_left          = isset( $price[ 'bpmj_eddcm_purchase_limit_items_left' ] ) ? (int) $price[ 'bpmj_eddcm_purchase_limit_items_left' ] : '';
			if ( empty( $purchase_limit ) ) {
				$price[ 'bpmj_eddcm_purchase_limit' ]            = '';
				$price[ 'bpmj_eddcm_purchase_limit_items_left' ] = '';
			} else {
				$price[ 'bpmj_eddcm_purchase_limit' ] = $purchase_limit;
				if ( 0 === $purchase_limit_prev && 0 === $items_left ) {
					$price[ 'bpmj_eddcm_purchase_limit_items_left' ] = $purchase_limit;
				} else {
					$price[ 'bpmj_eddcm_purchase_limit_items_left' ] = min( $items_left, $purchase_limit );
				}
			}
			$prices[ $id ] = $price;
		}

		return $prices;
	}

	/**
	 *
	 */
	public function prepare_edit_product_description_page() {
		global $wp_meta_boxes;

		$screen = get_current_screen();
		$page   = $screen->id;

		// Remove ALL metaboxes for EDD products except for "submitdiv"
		if ( ! empty( $wp_meta_boxes[ $page ] ) ) {
			$submitdiv = isset( $wp_meta_boxes[ $page ][ 'side' ] ) && isset( $wp_meta_boxes[ $page ][ 'side' ][ 'core' ] ) && isset( $wp_meta_boxes[ $page ][ 'side' ][ 'core' ][ 'submitdiv' ] )
				? $wp_meta_boxes[ $page ][ 'side' ][ 'core' ][ 'submitdiv' ]
				: null;
			if ( $submitdiv ) {
				$wp_meta_boxes[ $page ] = array(
					'side' => array(
						'core' => array(
							'submitdiv' => $submitdiv,
						),
					),
				);
			} else {
				$wp_meta_boxes[ $page ] = array();
			}
		}

		// Remove title input
		remove_post_type_support( 'download', 'title' );
	}

    /**
     * Metabox that displays disable sale checkbox on course bundle
     *
     * @param WP_Post $post Current post object.
     */
	public function disable_bundle_sales( $post ) {
        $product_type = get_post_meta($post->ID, '_edd_product_type', true);
        if( 'bundle' == $product_type ) {
            ?>

            <div class="form-group">
                <input type="hidden" name="bpmj_wpidea[sales_disabled]" value="off">
                <label>
                    <input type="checkbox" style="float:left;" value="on"
                           name="bpmj_wpidea[sales_disabled]" <?php checked('on', get_post_meta($post->ID, 'sales_disabled', true)); ?>>
                    <?php _e('Block sales', BPMJ_EDDCM_DOMAIN); ?>
                </label>
            </div>
            <div class="form-group">
                <input type="hidden" name="bpmj_wpidea[purchase_button_hidden]" value="off">
                <label>
                    <input type="checkbox" style="float:left;" value="on"
                           name="bpmj_wpidea[purchase_button_hidden]" <?php checked( 'on', get_post_meta( $post->ID, 'purchase_button_hidden', true ) ); ?>>
                    <?php _e( 'Hide default buy button', BPMJ_EDDCM_DOMAIN ); ?>
                </label>
            </div>

            <?php
        }
    }

	/**
	 * @param int $course_id
	 * @param WP_Post $course_after
	 * @param WP_Post $course_before
	 */
	public function course_updated( $course_id, WP_Post $course_after, WP_Post $course_before ) {

		if ( $course_after->post_type != 'courses' ) {
		    return;
		}
        // Propagate slug changes
        $course_page_id = get_post_meta( $course_id, 'course_id', true );
        if ( ! $course_page_id ) {
            return;
        }

        wp_update_post( array(
            'ID'        => $course_page_id,
            'post_name' => $course_after->post_name,
        ) );

        $product_id = get_post_meta( $course_id, 'product_id', true );
        if ( ! $product_id ) {
            return;
        }

        do_action('wpi_course_meta_has_been_cloned', $course_id, $course_page_id);

        $product = get_post( $product_id );
        if ( $product instanceof WP_Post ) {
            if ( $product->post_name === $course_before->post_name ) {
                // Update product slug only if it was the same as the course's
                wp_update_post( array(
                    'ID'        => $product_id,
                    'post_name' => $course_after->post_name,
                ) );
            }

            // Set EDD product meta
            $files = get_post_meta( $product_id, 'edd_download_files', true );
            if ( ! is_array( $files ) ) {
                $files = array();
            }
            if ( isset( $files[ $course_page_id ] ) ) {
                $files[ $course_page_id ] = array_merge( $files[ $course_page_id ], array(
                    'file' => get_permalink( $course_page_id ),
                ) );
                update_post_meta( $product_id, 'edd_download_files', $files );
            }
        }
    }

	/**
	 * @param bool $recurring_payments_possible
	 * @param array $recurring_payments_interval
	 */
	protected function field_recurring_payments_interval( $recurring_payments_possible, $recurring_payments_interval ) {
		$no_access_to_recurring_payments = WPI()->packages->no_access_to_feature( Packages::FEAT_RECURRING_PAYMENTS );
		?>
        <input type="hidden" name="_edd_recurring_payments_interval" value="this value will be changed on save"/>
        <label style="display: inline-block;">
            <select name="_edd_recurring_payments_interval_number" <?php disabled( $no_access_to_recurring_payments || ! $recurring_payments_possible ); ?>>
				<?php foreach ( range( 1, 30 ) as $number ): ?>
                    <option value="<?php echo $number; ?>" <?php selected( $number, false !== $recurring_payments_interval ? $recurring_payments_interval[ 'number' ] : false ); ?>>
                        +<?php echo $number; ?></option>
				<?php endforeach; ?>
            </select>
        </label>
        <label style="display: inline-block;">
            <select name="_edd_recurring_payments_interval_unit" <?php disabled( $no_access_to_recurring_payments || ! $recurring_payments_possible ); ?>>
				<?php foreach ( edd_recurring_get_interval_units() as $unit => $unit_name ): ?>
                    <option value="<?php echo $unit; ?>" <?php selected( $unit, false !== $recurring_payments_interval ? $recurring_payments_interval[ 'unit' ] : 'months' ); ?>>
						<?php echo $unit_name; ?></option>
				<?php endforeach; ?>
            </select>
        </label>
        <div class="desc"><?php _e( 'Set the interval between recurring payments for this item', BPMJ_EDDCM_DOMAIN ); ?></div>
		<?php
	}

    private function maybe_emit_variable_prices_updated_event(int $product_id, array $edd_variable_prices, array $post_data): void
    {
        if(empty($post_data[ 'variable_sale_price_from_date' ]) && empty($post_data[ 'variable_sale_price_to_date' ])) {
            $this->events->emit(Product_Event_Name::VARIABLE_PRICES_UPDATED, $product_id, $edd_variable_prices);
        }
    }

    private function maybe_emit_regular_or_sale_price_changed_events(int $product_id, array $form, bool $reset_price): void
    {
        if(empty($form['variable_pricing'])) {
            $this->events->emit(Product_Event_Name::REGULAR_PRICE_UPDATED, $product_id, $form['price']);

            if(empty($form[ 'sale_price_from_date' ]) && empty($form[ 'sale_price_to_date' ])) {
                $this->events->emit(Product_Event_Name::PROMO_PRICE_UPDATED, $product_id, $form['sale_price']);
            } elseif($reset_price) {
                $this->events->emit(Product_Event_Name::PROMO_PRICE_UPDATED, $product_id, null);
            }
        }
    }
}
