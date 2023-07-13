<?php
// Exit if accessed directly

namespace bpmj\wpidea\admin;

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;
use bpmj\wpidea\admin\pages\quiz_editor\Quiz_Editor_Page_Renderer;
use bpmj\wpidea\Courses;
use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\Packages;
use bpmj\wpidea\sales\product\Meta_Helper as Product_Meta_Helper;
use bpmj\wpidea\wolverine\event\Events;
use WP_Query;

if (!defined('ABSPATH'))
    exit;

class Creator
{
    private const TEST_MODE = 'test';

    /**
     * @param int $product_id
     *
     * @return string
     */
    public static function get_variable_prices_module_legend($product_id)
    {
        ob_start();
        ?>
        <div id="bpmj-eddcm-variant-color-legend" class="form-group no-border">
            <?php
            if (edd_has_variable_prices($product_id)):
                ?>
                <label><?php _e('Choose which lessons/modules are accessible in each variant', BPMJ_EDDCM_DOMAIN) ?>
                    :</label>
                <?php
                foreach (edd_get_variable_prices($product_id) as $price_id => $variable_price):
                    $color_key = $price_id % 8;
                    ?>
                    <div>
                        <div class="bpmj-eddcm-variant-color bpmj-eddcm-variant-color-<?php echo $color_key; ?>"></div>
                        - <?php echo $variable_price['name']; ?></div>
                <?php
                endforeach;
            endif;
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public function create_cpt(): ?int
    {
        return WPI()->courses->createCPT();
    }

    public function create_course(array $form, Courses $courses): ?int
    {
        if (!empty($form['create_sample_structure'])) {
            $form = $this->prepare_sample_structure($form);
        }

        // Create product for this course
        $product_id = $courses->create_product($form);

        if (is_numeric($product_id)) {

            // Create all pages
            $form = $courses->create_pages($form, $product_id);

            // Create CPT Course post
            $form['product_id'] = $product_id;
            $cpt_id = $courses->add_cpt($form, 'publish');

            // Drip posts
            $courses->drip($cpt_id);

            if ($form['bpmj_wpidea'] && $form['bpmj_wpidea']['flat_rate_tax_symbol']) {
                update_post_meta(
                    $cpt_id,
                    'flat_rate_tax_symbol',
                    $form['bpmj_wpidea']['flat_rate_tax_symbol']
                );
            }

            $invoices_vat_rate = $form['invoices_vat_rate'] ?? '';
            Product_Meta_Helper::save_invoices_vat_rate( $product_id, sanitize_text_field( $invoices_vat_rate ) );

            if ($cpt_id) {
                Events::trigger(Courses::EVENT_COURSE_CREATED, ['course_id' => $cpt_id]);
                return $cpt_id;
            }
        }

        return null;
    }

    public function create_bundle(string $form_param): ?int
    {

        if (!WPI()->packages->has_access_to_feature(Packages::FEAT_COURSE_BUNDLING)) {
            return null;
        }

        $form = WPI()->courses->prepare_array($form_param);
        $bundled_products = $form['bundled_courses'] ?? [];

        $args = [
            'post_type' => 'download',
            'post_title' => $form['title'],
            'post_status' => 'publish',
            'post_content' => $form['content'],
            'comment_status' => 'closed',
            'meta_input' => [
                '_eddcm_subtype' => 'bundle',
                '_edd_product_type' => 'bundle',
                'edd_price' => $form['price'],
                'edd_sale_price' => $form['sale_price'],
                'edd_variable_prices' => [],
                'edd_download_files' => [],
                '_edd_bundled_products' => $bundled_products,
            ],
        ];
        return wp_insert_post(array_filter($args));
    }

    /**
     * Get post content
     *
     * @param bool $id
     *
     * @return string
     */
    public static function get_post_content($id = false)
    {

        $post_id = isset($_POST['id']) && $_POST['id'] ? $_POST['id'] : $id;
        $content = '';

        if ($post_id) {
            $p = get_post($post_id);
            $content = $p->post_content;
        }
        if (isset($_POST['id']) && $_POST['id']) {
            die(json_encode($content));
        } else {
            return $content;
        }
    }

    /**
     * Create module for content
     *
     * @param int $id
     * @param string $values
     */
    public static function get_content_editor($id = 0, $values = '')
    {

        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            $id = $_POST['id'];
            $values['content'] = '';
        } else {

            $values['content'] = self::get_post_content($values['id']);
        }

        $settings = array(
            'quicktags' => false,
            'media_buttons' => false,
            'editor_height' => 200
        );
        ?>

        <div class="modal" data-modal="<?php echo $id; ?>">
            <div class="row">
                <div class="container">
                    <div class="panel">

                        <div class="panel-heading">
                            <?php _e('Edit Module/Lesson', BPMJ_EDDCM_DOMAIN); ?>
                            <span class="dashicons dashicons-no-alt close-modal" data-action="close-modal"
                                  data-id="<?php echo $id; ?>"></span>
                        </div>

                        <div class="panel-body">
                            <div class="form-group">
                                <label for="subtitle"><?php _e('Subtitle', BPMJ_EDDCM_DOMAIN); ?></label>
                                <input type="text" id="subtitle"
                                       value="<?php if (isset($values['subtitle'])) echo $values['subtitle']; ?>">
                                <div class="desc"><?php _e("Optional field.", BPMJ_EDDCM_DOMAIN); ?></div>
                            </div>

                            <div class="form-group">

                                <div class="half_width inline">
                                    <label for="level"><?php _e('Difficulty level', BPMJ_EDDCM_DOMAIN); ?></label>
                                    <input type="text" id="level"
                                           value="<?php if (isset($values['level'])) echo $values['level']; ?>">
                                    <div class="desc"><?php _e("Difficulty level of your module/lesson.", BPMJ_EDDCM_DOMAIN); ?></div>
                                </div>

                                <div class="half_width inline float-right">
                                    <label for="duration"><?php _e('Duration time', BPMJ_EDDCM_DOMAIN); ?></label>
                                    <input type="text" id="duration"
                                           value="<?php if (isset($values['duration'])) echo $values['duration']; ?>">
                                    <div class="desc"><?php _e("Expected duration of this part of the course.", BPMJ_EDDCM_DOMAIN); ?></div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="shortdesc"><?php _e('Short description', BPMJ_EDDCM_DOMAIN); ?></label>
                                <input type="text" id="shortdesc"
                                       value="<?php if (isset($values['shortdesc'])) echo $values['shortdesc']; ?>">
                                <div class="desc"><?php _e("Optional, short description of the module/lesson.", BPMJ_EDDCM_DOMAIN); ?></div>
                            </div>

                            <div class="form-group">
                                <label for="files"><?php _e('Files', BPMJ_EDDCM_DOMAIN); ?></label>
                                <div class="files">
                                    <?php
                                    if (is_array($values['files'])) {
                                        foreach ($values['files'] as $fileID => $file) {
                                            $image = wp_get_attachment_image_src($fileID, 'thumbnail', true);
                                            $style = 'background-image: url(' . $image[0] . ');';

                                            if (!isset($image[3])) {
                                                $style .= 'background-position: center 5px;';
                                            } else {
                                                $style .= 'background-size: cover; background-position: center center';
                                            }

                                            echo '<div class="file" data-id="' . $fileID . '" style="' . $style . '">';
                                            echo '<span class="dashicons dashicons-no remove-file"></span>';

                                            if (!isset($image[3])) {
                                                $attachment = basename(get_attached_file($fileID));
                                                echo '<span class="title">' . $attachment . '</span>';
                                            }

                                            echo '<input type="text" id="files" data-id="' . $fileID . '" value="' . $file['desc'] . '" placeholder="' . __('Short file description', BPMJ_EDDCM_DOMAIN) . '">';
                                            echo '</div>';
                                        }
                                    }
                                    ?>
                                </div>
                                <a class="btn-eddcm btn-eddcm-primary" id="uploadFiles"
                                   href="<?php echo esc_url(get_upload_iframe_src()); ?>"
                                   style="display: inline-block;"><?php _e('Upload files', BPMJ_EDDCM_DOMAIN) ?></a>
                                <div class="desc"><?php _e("Files for this module/lesson.", BPMJ_EDDCM_DOMAIN); ?></div>
                            </div>

                            <div class="form-group">
                                <label for="content"><?php _e('Content', BPMJ_EDDCM_DOMAIN); ?></label>
                                <?php wp_editor($values['content'], 'content_' . $id, $settings); ?>
                                <div class="desc"><?php _e('Here you can write the content of your module/lesson.', BPMJ_EDDCM_DOMAIN); ?></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <?php
        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            die();
        }
    }

    /**
     * Create module
     *
     * @param string $mode
     * @param bool $eddcm_id
     * @param array|bool $lessons
     * @param string $title
     * @param bool $editor
     * @param bool $clone_mode
     * @param string $variable_prices_template
     * @param bool $force_return
     *
     * @return array
     */
    public static function create_module($mode = 'full', $eddcm_id = false, $lessons = false, $title = '', $editor = false, $clone_mode = false, $variable_prices_template = '', $force_return = false)
    {
        if ($mode == 'full') {
            $name = __('module', BPMJ_EDDCM_DOMAIN);
        } else if ($mode == 'test') {
            $name = __('test', BPMJ_EDDCM_DOMAIN);
        } else {
            $name = __('lesson', BPMJ_EDDCM_DOMAIN);
        }

        if ($clone_mode) {
            $id_name = 'cloned_from_id';
        } else if ($editor) {
            $id_name = 'created_id';
        } else {
            $id_name = 'id';
        }


        // Get id for module WYSIWYG editor
        $id = substr(md5(time() . rand()), 0, 10);

        // HTML Content
        $html = '<li class="module ' . $mode . ' ' . ($editor ? 'editor' : '') . '" data-module="0"><input type="hidden" name="bpmj_eddcm_module[0][mode]" id="eddcm_mode" value="' . $mode . '">';

        $html .= '<div class="module-title-block">'.str_replace( 'test', 'quiz', $name ).'</div>';

        $html .= '<img style="vertical-align: middle;" src="'. BPMJ_EDDCM_URL .'/assets/imgs/settings/move-icon.svg" alt="">';

        if (isset($eddcm_id) && $eddcm_id) {
            $html .= '<input type="hidden" name="bpmj_eddcm_module[0][' . $id_name . ']" id="eddcm_' . $id_name . '" value="' . $eddcm_id . '">';
        }

        $html .= '<input style="margin-left:10px; height:40px; color: #2c3338;" type="text" name="bpmj_eddcm_module[0][title]" id="eddcm_title" placeholder="' . sprintf(__('Title of your %s', BPMJ_EDDCM_DOMAIN), $name) . '" value="' . $title . '">';

        if ($editor && WPI()->packages->has_access_to_feature(Packages::FEAT_DELAYED_ACCESS)) {
            $drip_value = get_post_meta($eddcm_id, '_bpmj_eddpc_drip_value', true);
            $drip_unit = get_post_meta($eddcm_id, '_bpmj_eddpc_drip_unit', true);

            $html .= '<span class="dashicons dashicons-clock bpmj-eddcm-drip-icon" title="' . __('Drip Value', BPMJ_EDDCM_DOMAIN) . '"></span>';
            $html .= '<input style="height:40px; color: #2c3338;" title="' . __('Drip Value', BPMJ_EDDCM_DOMAIN) . '" class="drip_value" type="number" min="0" max="999" name="bpmj_eddcm_module[0][drip_value]" value="' . $drip_value . '" oninput="validity.valid||(value=\'\');" /> <span class="drip_unit_label">' . WPI()->courses->get_access_time_unit($drip_unit) . '</span>';
            $html .= '<input type="hidden" name="bpmj_eddcm_module[0][drip_unit]" value="' . $drip_unit . '" />';
        }

        $html .= '<span style="margin-top:5px;" class="remove-module" data-action="remove"><img src="'. BPMJ_EDDCM_URL .'/assets/imgs/settings/delete-icon.svg" alt=""></span>';


        if (!$clone_mode && isset($eddcm_id) && $eddcm_id) {
            $edit_link = self::get_edit_post_link($mode, $eddcm_id);

	        $html .= '<a href="' . $edit_link . '" target="_blank" class="edit-module"><img src="' . BPMJ_EDDCM_URL . '/assets/imgs/settings/edit-icon.svg" alt=""></a>';
        }else{
            $html .= '<img class="edit-module-disabled" src="'. BPMJ_EDDCM_URL .'/assets/imgs/settings/edit-icon.svg" alt="" title="'.Translator_Static_Helper::translate('course_editor.sections.structure.fieldset.module.disabled').'">';
        }

        $html .= static::interpolate_variable_prices_template($variable_prices_template, $eddcm_id, 'module[0]');

        $content = array();

        if ($mode == 'full') {
            $html .= '<ul class="modules module-submodules">';


            if ($lessons) {
                if (isset($lessons)) {
                    foreach ($lessons as $lesson) {

                        $connected_lesson_id = isset($lesson['id']) ? $lesson['id'] : false;
                        if ($connected_lesson_id) {
                            $title = get_the_title($connected_lesson_id);
                        } else {
                            $title = isset($lesson['title']) ? $lesson['title'] : '';
                        }

                        $get_lesson = Creator::create_lesson($connected_lesson_id, $title, $editor, $clone_mode, $variable_prices_template, $force_return);
                        $content[$get_lesson['editor_id']] = $lesson;
                        $html .= $get_lesson['html'];
                    }
                }
            }

            $no_access_to_tests = WPI()->packages->no_access_to_feature(Packages::FEAT_TESTS);

            $html .= '</ul>
                <div class="creator-buttons text-left">
                    <button style="margin-left:0;" type="button" class="creator-buttons-add" data-action="add-module"
                            data-mode="lesson">' . __('Add lesson', BPMJ_EDDCM_DOMAIN) . '</button>
                    <button ' . ($no_access_to_tests ? ' disabled="disabled"' : '') . ' type="button" class="creator-buttons-add" data-action="add-module"
                            data-mode="test">' . __('Add quiz', BPMJ_EDDCM_DOMAIN) . '</button>
                </div>';
        }
        $html .= '</li>';

        return array(
            'html' => $html,
            'editor_id' => $id,
            'content' => $content
        );
    }

    /**
     * @param string $mode
     * @param bool $eddcm_id
     * @param bool $lessons
     * @param string $title
     * @param bool $editor
     * @param bool $clone_mode
     * @param string $variable_prices_template
     *
     * @return string
     */
    public static function create_module_get_html($mode = 'full', $eddcm_id = false, $lessons = false, $title = '', $editor = false, $clone_mode = false, $variable_prices_template = '')
    {
        $result = static::create_module($mode, $eddcm_id, $lessons, $title, $editor, $clone_mode, $variable_prices_template, true);
        if (isset($result['html'])) {
            return $result['html'];
        }

        return $result['module_html'];
    }

    /**
     * Create lesson
     *
     * @param bool $id
     * @param bool $title
     * @param bool $editor
     * @param bool $clone_mode
     * @param string $variable_prices_template
     * @param bool $force_return
     *
     * @return array
     */
    public static function create_lesson($id = false, $title = false, $editor = false, $clone_mode = false, $variable_prices_template = '', $force_return = false)
    {
        $mode = get_post_meta($id, 'mode', true);
        if ($mode == 'test') {
            $mode_name = 'test';
            $title_input = __('test', BPMJ_EDDCM_DOMAIN);
        } else {
            $mode_name = 'lesson';
            $title_input = __('lesson', BPMJ_EDDCM_DOMAIN);
        }

        $id_value = $id;
        if (isset($id) && $id) {

            if ($clone_mode) {
                $id_name = 'cloned_from_id';
            } else if ($editor) {
                $id_name = 'created_id';
            } else {
                $id_name = 'id';
            }

            $id = '<input type="hidden" name="bpmj_eddcm_module[0][module][0][' . $id_name . ']" id="eddcm_' . $id_name . '" value="' . $id . '">';
        }

        // Get id for module WYSIWYG editor
        $editor_id = substr(md5(time() . rand()), 0, 10);

        // HTML Content
        $html = '<li class="module ' . $mode_name . ' ' . ($editor ? 'editor' : '') . '" data-module="0"><input type="hidden" name="bpmj_eddcm_module[0][mode]" id="eddcm_mode" value="' . $mode . '">';

        $html .= '<div class="module-title-block">'.str_replace( 'test', 'quiz', $title_input ).'</div>';

        $html .= '<img style="vertical-align: middle;" src="'. BPMJ_EDDCM_URL .'/assets/imgs/settings/move-icon.svg" alt="">';

        $html .= $id . '<input style="margin-left:10px;" type="text" name="bpmj_eddcm_module[0][module][0][title]" id="eddcm_title" placeholder="' . __('Title of your lesson', BPMJ_EDDCM_DOMAIN) . '" value="' . $title . '">';

        if ($editor && WPI()->packages->has_access_to_feature(Packages::FEAT_DELAYED_ACCESS)) {
            $drip_value = get_post_meta($id_value, '_bpmj_eddpc_drip_value', true);
            $drip_unit = get_post_meta($id_value, '_bpmj_eddpc_drip_unit', true);
            $html .= '<span class="dashicons dashicons-clock bpmj-eddcm-drip-icon" title="' . __('Drip Value', BPMJ_EDDCM_DOMAIN) . '"></span>';
            $html .= '<input title="' . __('Drip Value', BPMJ_EDDCM_DOMAIN) . '" class="drip_value" type="number" name="bpmj_eddcm_module[0][module][0][drip_value]" value="' . $drip_value . '" /> <span class="drip_unit_label">' . WPI()->courses->get_access_time_unit($drip_unit) . '</span>';
            $html .= '<input type="hidden" name="bpmj_eddcm_module[0][module][0][drip_unit]" value="' . $drip_unit . '" />';
        }

        $html .= '<span style="margin-top:5px;"  class="remove-module" data-action="remove"><img src="'. BPMJ_EDDCM_URL .'/assets/imgs/settings/delete-icon.svg" alt=""></span>';

        if (!$clone_mode && isset($id_value) && $id_value) {
            $edit_link = self::get_edit_post_link($mode, $id_value);
            $html .= '<a href="' . $edit_link . '" target="_blank" class="edit-module"><img src="'. BPMJ_EDDCM_URL .'/assets/imgs/settings/edit-icon.svg" alt=""></a>';
        }

        $html .= static::interpolate_variable_prices_template($variable_prices_template, $id_value, 'bpmj_eddcm_module[0][module][0]');

        $html .= '</li>';

        return array(
            'html' => $html,
            'editor_id' => $editor_id
        );
    }

    /**
     * @param bool $id
     * @param bool $title
     * @param bool $editor
     * @param bool $clone_mode
     * @param string $variable_prices_template
     *
     * @return string
     */
    public static function create_lesson_get_html($id = false, $title = false, $editor = false, $clone_mode = false, $variable_prices_template = '')
    {
        $result = static::create_lesson($id, $title, $editor, $clone_mode, $variable_prices_template, true);
        if (isset($result['html'])) {
            return $result['html'];
        }

        return $result['module_html'];
    }


    /**
     * Get pages
     */
    public function get_pages()
    {

        $search_term = $_POST['search'];
        $args = array(
            'post_type' => 'page',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            's' => $search_term,
            'meta_key' => '_bpmj_eddcm_connected_page',
            'meta_value' => '',
            'meta_compare' => 'NOT EXISTS'
        );
        $q = new WP_Query($args);
        $result = array();
        $status = true;

        if ($q->have_posts()):
            while ($q->have_posts()): $q->the_post();

                $id = get_the_id();
                $result[] = array(
                    'id' => get_the_id(),
                    'title' => get_the_title()
                );

            endwhile;

        else:
            $status = false;
            $result = __('No results', BPMJ_EDDCM_DOMAIN);
        endif;

        $result = json_encode(array(
            'status' => $status,
            'result' => $result
        ));

        wp_reset_postdata();
        die($result);
    }

    /**
     * Get children
     */
    public function get_children()
    {

        $id = $_POST['id'];

        $q = new WP_Query();
        $args = $q->query(array(
            'post_type' => 'page',
            'posts_per_page' => -1
        ));

        $get = get_page_children($id, $args);
        if ($get) {
            $children = array();
            //var_dump($get);
            foreach ($get as $child) {
                $children[] = array(
                    'id' => $child->ID,
                    'title' => get_the_title($child->ID)
                );
            }
            $children = array_reverse($children);

            $result = array(
                'status' => true,
                'children' => $children
            );
        } else {
            $result = array(
                'status' => false
            );
        }

        wp_reset_postdata();
        die(json_encode($result));
    }

    /**
     * Generates sample data for course's structure
     *
     * @param array $form
     * @return array
     */
    public function prepare_sample_structure($form)
    {
        unset($form['create_sample_structure']);
        $sample_files = $this->prepare_sample_files();
        $convert_sample_files = function ($descriptions) use ($sample_files) {
            $result = array();
            foreach ($sample_files as $type => $id) {
                $result[$id] = array(
                    'desc' => empty($descriptions[$type]) ? '' : $descriptions[$type]
                );
            }
            return $result;
        };
        $create_lipsum_picker = function ($choices) {
            $choices_count = count($choices);
            return function () use ($choices, $choices_count) {
                static $pick = -1;
                $picked = $choices[++$pick];
                if ($pick === $choices_count - 1) {
                    $pick = -1;
                }
                return $picked;
            };
        };
        $pick_multiple_paragraphs = $create_lipsum_picker(array(
            '<p>Morbi interdum sit amet nisi eu rhoncus. Vestibulum vitae laoreet ex. Pellentesque ultrices vel lacus ut auctor. Nam commodo nisi vel commodo lobortis. Mauris vitae iaculis ante, gravida gravida diam. Aenean cursus rutrum quam, eu ullamcorper enim tempus eget. Nunc ipsum sem, feugiat a vehicula nec, sodales in sem. Morbi ut est lorem. Morbi quis sollicitudin justo. Aenean laoreet viverra venenatis. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Praesent tincidunt risus quis libero egestas vulputate. Donec nec eros bibendum nunc molestie efficitur sit amet fermentum ex. Integer elit arcu, finibus vel justo in, pellentesque sodales urna.</p><p>Maecenas a arcu sed nibh pulvinar mattis non nec odio. Integer risus ante, fermentum non consectetur vel, consectetur luctus libero. Quisque facilisis justo ut ex aliquet, tincidunt porttitor lacus efficitur. Ut metus velit, blandit at consequat ut, accumsan nec lectus. Sed scelerisque sit amet massa at pellentesque. Phasellus pellentesque dapibus justo, fermentum tristique mauris placerat id. Sed vitae mauris commodo, tincidunt turpis sollicitudin, porttitor nisl. Aenean congue elit nec sapien scelerisque auctor a eget lacus. Sed lacinia dolor at ipsum rhoncus consequat.</p><p>Aliquam in turpis sit amet velit volutpat mollis. Vivamus sem diam, pharetra sed rutrum eu, posuere ac dui. Nulla viverra, mi aliquet scelerisque faucibus, est nunc luctus nibh, id sagittis nulla quam at urna. In ultrices, diam sed tempus porta, metus nisi ultrices risus, sit amet sodales risus augue quis arcu. Suspendisse eget nulla sed ex tempus luctus. Curabitur pharetra mi lectus, eu molestie felis maximus a. Vestibulum congue, purus ac gravida mollis, nulla quam facilisis purus, quis commodo augue augue a lorem. Sed eu purus dui. Fusce luctus pellentesque massa, ac aliquet ex condimentum quis.</p>',
            '<p>Pellentesque condimentum dui eget lectus auctor, in cursus sem dignissim. Praesent est ligula, malesuada quis aliquet at, ultricies non eros. Quisque fermentum nulla ut accumsan facilisis. Nunc non ipsum aliquam, sagittis eros eget, ullamcorper nibh. Pellentesque molestie, erat sit amet efficitur imperdiet, sem mauris ultricies urna, vitae vulputate dolor purus eget nisi. Suspendisse in aliquam ligula, non feugiat diam. Nunc pretium, nisl non lobortis vulputate, urna diam eleifend orci, non sagittis lacus ex non erat. Praesent ut suscipit sapien. Integer in elementum lorem, id sodales lacus. Proin imperdiet massa at ante gravida, semper viverra purus gravida. Fusce tempus eros mauris, varius malesuada neque tempor et.</p><p>In egestas ultricies leo. Sed urna dui, sagittis at tortor consequat, tristique ullamcorper lectus. Vestibulum commodo lacinia mi. Maecenas fermentum venenatis orci. Aenean sed quam ac ante porta convallis. Sed tempor neque arcu, eu consequat lacus elementum quis. Pellentesque ac tellus a augue blandit rutrum in vitae sapien. Maecenas eu dui sapien. Duis dictum velit ultricies, elementum magna at, consectetur purus. Ut sit amet mauris massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aenean sapien massa, porttitor at dapibus id, porta finibus velit. Phasellus vulputate hendrerit massa luctus molestie. Integer eu orci tincidunt enim viverra blandit.</p><p>Praesent dictum non ipsum vitae egestas. Nunc a tortor sit amet dui cursus pellentesque. Maecenas at laoreet orci. Vestibulum blandit ac erat ac ultrices. Aenean tincidunt erat imperdiet sollicitudin varius. Nullam quis risus et elit viverra convallis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus finibus nisl eu nisi elementum aliquam. Quisque fermentum dui a neque varius, et interdum nulla molestie. Integer congue maximus gravida. Vivamus sodales at ipsum lacinia porttitor.</p>',
            '<p>In pretium ipsum lacus, sit amet hendrerit sem placerat id. Sed pulvinar maximus nisi. Morbi luctus nibh sit amet est vulputate varius. Aenean venenatis felis non malesuada auctor. Ut malesuada ultrices leo, sed hendrerit felis. Nullam ligula diam, dignissim vitae iaculis id, blandit vel velit. Nulla nulla nulla, pretium at semper et, dapibus id lectus.</p><p>Aliquam eu posuere enim, eu pretium ipsum. Duis viverra, erat vitae hendrerit vulputate, ex mauris auctor urna, sit amet tempor lectus turpis vel arcu. Etiam facilisis, nunc a posuere convallis, libero dolor efficitur eros, et volutpat enim tortor ut sapien. Curabitur gravida tincidunt sapien porta posuere. Nunc consectetur sapien quis ex volutpat, et laoreet est lobortis. Aenean sit amet dolor eget ex placerat vulputate at at ante. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Phasellus mauris quam, consectetur at condimentum vitae, elementum quis magna. Nullam eget vulputate diam. Morbi et lectus vitae odio vehicula porta sed a mi. Praesent eros nibh, viverra non tristique et, lobortis quis lorem. Sed laoreet sapien aliquet tempor faucibus. Morbi sagittis lacinia arcu eget venenatis.</p><p>Nunc enim libero, viverra et lorem eget, pellentesque hendrerit nunc. Vestibulum tempor sagittis sem vel eleifend. Morbi nisl sem, malesuada nec posuere eu, lacinia sit amet enim. Pellentesque ultricies eros et eros tincidunt feugiat. Sed tincidunt enim at pretium lobortis. Nunc quam purus, pellentesque ut semper nec, convallis ac sapien. Aliquam rutrum turpis et fermentum dignissim. Etiam ac nisi non ex laoreet molestie ac et urna. Nulla lobortis erat lorem, et sagittis massa bibendum id. Cras viverra dapibus lacus, eget rhoncus dui tristique vel. Nullam congue faucibus accumsan. Aliquam erat volutpat. Ut finibus malesuada fringilla. Morbi et massa non mi pellentesque convallis. Aliquam fermentum odio ac sagittis luctus. Donec porta, felis nec blandit venenatis, augue ante mollis mi, ut molestie magna sapien ornare lorem.</p>',
            '<p>Suspendisse bibendum lacus vel urna laoreet suscipit. Mauris laoreet eleifend massa, non condimentum felis elementum sit amet. Nullam lobortis purus nec est tristique molestie. Sed sed sem cursus, ornare sem ut, feugiat justo. Maecenas eros justo, sollicitudin id augue vitae, porta porttitor nisl. Curabitur eu mi in lorem dignissim varius dignissim sit amet elit. Phasellus facilisis quam eu malesuada convallis. Phasellus vitae nisl ac sapien fringilla euismod suscipit at lectus.</p><p>Nullam ullamcorper, erat ullamcorper convallis pellentesque, lacus nulla suscipit massa, a lobortis elit ante non risus. Nullam sed porta tortor. Etiam auctor ante elit, aliquam sagittis nibh ornare id. Fusce dapibus et neque id congue. Donec congue dolor odio, sit amet consequat dui tempus sed. Donec finibus mauris sed nulla ornare finibus. Vestibulum elit est, finibus quis justo tempor, placerat lobortis ipsum. Integer interdum leo quis nunc imperdiet, nec malesuada ligula rhoncus. Quisque maximus faucibus velit eget ullamcorper. Suspendisse diam orci, tempor nec tempus eu, fermentum sit amet risus. Morbi et magna suscipit, sagittis neque in, consectetur nisi. Cras diam est, ultrices dapibus ex id, placerat suscipit nunc. Vestibulum dapibus nulla mi, ac tempus augue rhoncus congue. Donec pellentesque ornare massa, non euismod felis congue quis. Curabitur eu sapien quis lacus accumsan congue viverra vel justo.</p><p>Ut auctor suscipit erat, eu suscipit libero efficitur vitae. Suspendisse lacinia dolor quis arcu aliquet ultrices. Donec vel sodales mi, non scelerisque ante. Pellentesque volutpat velit lorem, vitae tempor nunc vehicula eget. Nulla fermentum non urna vitae aliquet. Curabitur aliquam at lacus a elementum. Nullam risus arcu, iaculis eget arcu et, ultricies euismod lorem. Nunc non maximus dui. Aenean aliquam ultricies arcu, et cursus dolor bibendum suscipit. Praesent congue, nisi eu dictum sodales, eros mauris bibendum mauris, ut lobortis neque nisi vitae odio. Donec id nulla varius, congue urna id, gravida mauris. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vivamus tempor, velit in malesuada dignissim, felis massa luctus ligula, sed pharetra quam justo ut augue. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;</p>',
            '<p>Nulla sagittis massa nec ligula dapibus, vitae ultrices nisl suscipit. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Morbi elementum lacinia magna, vel ornare libero tincidunt ut. In ligula enim, ullamcorper eu maximus at, dapibus nec augue. Nullam lobortis orci id luctus tempus. Praesent mattis suscipit libero, vitae dapibus mauris sodales id. Cras fringilla nunc vitae faucibus tempus. Praesent dictum dolor dui, in tempor nulla dapibus quis. Quisque augue quam, tempus ac placerat quis, ultricies maximus felis. Sed vitae rhoncus nisi. Fusce maximus lectus sit amet mi sodales fringilla.</p><p>Phasellus ac commodo ipsum. Nullam ullamcorper, nulla et facilisis scelerisque, odio nibh porttitor nulla, eu vulputate ante risus hendrerit purus. Quisque ornare mauris at tristique posuere. Integer blandit facilisis pharetra. In hac habitasse platea dictumst. Vestibulum sed sapien a tellus malesuada venenatis non vel sem. Vivamus ultricies augue libero, a dictum mauris hendrerit non. Nullam sapien sapien, pulvinar quis tristique vitae, mollis id nunc. Nulla feugiat dapibus metus, at scelerisque nisl ullamcorper nec. Nam in nisi eget sem commodo congue eget vel nulla. Integer vel semper arcu, sed semper enim. Mauris ac consequat erat. Ut ut tortor leo.</p><p>Duis nec ligula eu urna finibus rhoncus. Morbi semper neque eu orci congue, vitae eleifend libero aliquam. Curabitur eu velit vel eros sollicitudin placerat quis vitae augue. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Etiam vitae tempus ex, at dictum nulla. Vivamus at pellentesque arcu. Sed diam odio, ullamcorper interdum finibus ut, elementum non felis. Integer finibus vulputate elit et elementum.</p>',
            '<p>Quisque sit amet nisl vitae turpis pretium vehicula eu ac magna. Proin rhoncus augue in dapibus semper. Nam ligula mauris, pharetra at sagittis rhoncus, aliquam a enim. Fusce vulputate iaculis orci quis fringilla. Mauris neque elit, egestas id tristique eget, laoreet vitae ante. Morbi sit amet mauris eu tellus accumsan tempor eu ut mauris. Curabitur eget placerat diam. Vivamus enim lorem, lacinia sit amet aliquam in, finibus vel dui. Ut posuere condimentum nisi dapibus fringilla. Pellentesque sollicitudin pellentesque odio a laoreet.</p><p>Curabitur pellentesque euismod sagittis. Aliquam ac pulvinar orci, non auctor leo. In arcu arcu, mollis non interdum eu, auctor eget mauris. Sed sagittis ex ut erat euismod tincidunt. Nam ut dui non lectus consectetur pretium aliquet nec velit. Nullam imperdiet gravida nulla, ac vehicula mi dapibus pellentesque. Sed condimentum sit amet urna non lacinia. Sed fermentum urna ut nulla facilisis, at sagittis diam ultricies. Curabitur ut odio consequat, congue mauris sed, rutrum ante. Nam vitae venenatis sem. In tristique diam et magna blandit bibendum accumsan non ipsum.</p><p>Donec in placerat quam, eu elementum nisi. Integer ac nulla consectetur, blandit sem non, interdum ligula. Aliquam pretium venenatis elit non hendrerit. Proin blandit, est ut ultrices tempus, enim augue congue purus, sed cursus est dui a massa. In viverra purus et leo convallis ultrices. Curabitur diam dolor, tincidunt eu tincidunt quis, cursus eu velit. Maecenas aliquet neque nec feugiat consectetur. Curabitur ullamcorper nulla a semper condimentum. Cras id felis et ligula hendrerit sodales a eget neque. Suspendisse vehicula nibh a neque pulvinar tincidunt non non risus.</p>',
            '<p>In sollicitudin nec lectus sed consectetur. Nullam eget mauris vel mi ultrices mollis ut at dui. Etiam faucibus, justo ut iaculis ultrices, arcu massa imperdiet dui, ac fermentum arcu magna a lectus. Pellentesque venenatis eu massa ac vulputate. Quisque fermentum vestibulum orci. Aenean aliquam eu neque et molestie. Cras blandit turpis neque, non scelerisque sapien molestie in. Suspendisse tincidunt nisl at sapien semper tincidunt. Sed maximus, elit eget condimentum ullamcorper, erat nibh ornare leo, vitae euismod enim sapien auctor enim. Quisque non pharetra nibh. Integer dapibus eu nisi sit amet lacinia. Quisque ac mollis nisl, eleifend venenatis mauris. Vestibulum ac egestas velit.</p><p>Duis blandit laoreet dapibus. Fusce gravida arcu augue, vel scelerisque turpis elementum ac. Proin consequat enim elit, ac consequat ligula rutrum vel. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed dictum erat id nisi laoreet ullamcorper. Quisque libero eros, consequat non orci nec, consequat feugiat tellus. Ut maximus, felis et aliquam rutrum, nibh dui euismod tellus, vel aliquam augue ipsum sit amet justo. Cras velit lectus, fringilla vel risus mollis, malesuada hendrerit odio. Donec nec gravida dolor, hendrerit tristique nulla. Vestibulum lorem elit, porta in eros a, rutrum dictum neque. Maecenas rutrum volutpat pretium. Nullam ultrices gravida nisl.</p><p>In turpis eros, condimentum eu congue quis, iaculis nec est. Etiam quis vestibulum justo, in laoreet purus. Integer efficitur pulvinar nibh viverra dignissim. Mauris accumsan nunc eget eros lobortis, eget dignissim ex efficitur. Nullam varius imperdiet libero. Aenean consequat risus sit amet imperdiet scelerisque. Phasellus maximus, risus nec ornare iaculis, nisl metus mattis felis, non pharetra urna ligula eget elit. Fusce a tellus et tortor rhoncus accumsan. Nunc vel elit et arcu molestie bibendum. Donec pharetra consequat massa, vitae gravida tellus fermentum vel. Nulla egestas est a nisi semper commodo id vestibulum tortor. Mauris tincidunt ipsum ipsum, vitae pretium urna feugiat id. Ut posuere vehicula dapibus. Nam pulvinar, quam a varius placerat, lorem lorem tempor diam, et euismod est ex vitae sem. In id iaculis libero. Aliquam erat volutpat.</p>',
            '<p>Donec cursus ante et egestas mollis. Sed vitae gravida tellus. Donec ornare libero nec iaculis interdum. Vestibulum et risus felis. Fusce id odio enim. Vivamus mattis blandit lorem, nec molestie quam consequat vel. Praesent dapibus porta sem sed facilisis. Vestibulum faucibus sollicitudin lectus vel volutpat. Integer luctus massa quis quam lobortis malesuada. Duis interdum ipsum libero. Integer pharetra faucibus odio ac semper. Sed dui orci, iaculis in sapien luctus, mattis vestibulum urna. Proin imperdiet tincidunt fermentum. Donec sed tellus a augue maximus scelerisque. Aliquam erat volutpat. Phasellus semper rhoncus magna vel condimentum.</p><p>Nunc ut rhoncus nunc. Aliquam rutrum auctor tincidunt. Praesent vitae lorem aliquam, vehicula ante ut, euismod felis. Nam eu varius turpis. Nullam consequat et turpis id congue. Curabitur at suscipit tortor, nec sagittis massa. Vestibulum nisl orci, efficitur mollis tellus sit amet, scelerisque hendrerit tellus. Fusce bibendum, nulla eget volutpat rhoncus, augue arcu dignissim tortor, quis viverra ipsum nibh mattis dui. Aliquam et sapien mattis, pellentesque erat nec, malesuada sem. Duis faucibus maximus placerat. Nunc sapien metus, facilisis eu aliquet dignissim, faucibus id ipsum. Morbi porta, purus id dapibus commodo, nibh leo viverra enim, eu finibus diam sapien sed sapien.</p><p>Nam tellus sem, elementum vel magna a, faucibus cursus tellus. Suspendisse potenti. Vestibulum sit amet placerat augue, sed lobortis elit. Morbi a odio odio. Nulla sagittis vehicula massa id scelerisque. Praesent vitae ex nec lorem vestibulum viverra sed eu lacus. Nulla imperdiet arcu consectetur ligula aliquam fermentum. Pellentesque porttitor justo vel consectetur condimentum. Curabitur aliquet, ipsum sed vestibulum mollis, ligula odio mollis felis, in tincidunt libero urna ac ante. Nullam leo orci, suscipit quis luctus eu, tincidunt ac quam. Maecenas ac libero magna. Donec at mattis massa, a tempus quam. Integer aliquam mi leo, in vehicula tortor posuere a. Ut euismod facilisis massa, mattis gravida diam volutpat id. Curabitur ut ullamcorper lectus, non tristique risus.</p>',
        ));
        $pick_long_sentence = $create_lipsum_picker(array(
            'Vivamus pellentesque turpis nec dolor dapibus, ac accumsan ipsum malesuada. Integer eu quam a leo congue egestas mollis ut massa.',
            'Phasellus vulputate purus in libero pretium mattis. Vivamus eu magna non diam iaculis semper.',
            'Mauris dapibus elit a mollis lacinia. In at massa ut nisl maximus scelerisque.',
            'Nulla pharetra orci quis efficitur vehicula. Vivamus ac diam interdum, congue ligula nec, consectetur dui.',
            'Vivamus quis dolor fermentum, hendrerit tellus quis, tristique orci. Aliquam mattis turpis in efficitur lobortis.',
            'Morbi placerat nibh vel urna bibendum aliquet ac ut tellus. Mauris laoreet nibh ut turpis gravida gravida.',
            'Phasellus et diam ac ligula ornare tincidunt. Morbi in ipsum ac nisl consequat dignissim quis in risus.',
            'Integer et nulla sed orci vestibulum rhoncus. Proin tincidunt nunc quis massa varius mattis.',
            'Donec malesuada lectus eu maximus hendrerit. Suspendisse nec dolor ut nibh consequat blandit.',
            'Quisque ac lorem sed neque commodo imperdiet vel vitae magna. Integer tempor magna et quam fringilla accumsan.',
            'Pellentesque eget magna luctus, dictum mauris quis, vulputate lorem. Etiam vehicula lectus sit amet arcu luctus, ut pulvinar erat elementum.',
            'Ut sed nulla vel justo interdum scelerisque vel eu nisi. Maecenas quis felis semper, ullamcorper enim a, feugiat mi.',
            'Aenean vitae orci et ligula facilisis tristique ac nec mi. Mauris condimentum nibh nec sapien auctor rhoncus.',
            'Integer ut enim in velit dictum bibendum. Nunc vestibulum dolor a imperdiet commodo.',
            'Curabitur vel odio a nunc lacinia ornare at a tortor. Vivamus a arcu mattis, commodo libero quis, gravida diam.',
            'Vivamus quis orci nec augue euismod placerat. Sed euismod neque sit amet gravida ultrices.',
            'Vivamus ut justo vitae mauris commodo ultrices a id urna. Sed ornare turpis a augue iaculis consequat.',
            'Pellentesque ut est ultrices, aliquet ipsum quis, egestas nisl. Fusce sit amet felis quis metus fermentum laoreet et id massa.',
            'Vivamus sagittis magna interdum odio fringilla, ut accumsan mi ornare. Sed ornare tortor et pulvinar vehicula.',
            'Sed ullamcorper mi ut vestibulum tempus. Fusce feugiat libero ac augue tincidunt, in porttitor libero viverra.',
        ));
        $pick_short_sentence = $create_lipsum_picker(array(
            'Sed et tellus non ante rutrum lobortis.',
            'Quisque ornare lorem sodales arcu mattis, eu pellentesque nibh elementum.',
            'Ut at tellus facilisis, rutrum massa non, venenatis turpis.',
            'In congue elit pellentesque auctor consequat.',
            'Quisque in risus vel velit pharetra congue.',
            'Aliquam semper ex vitae sapien ultrices, at tincidunt felis condimentum.',
            'Duis eu quam quis odio porttitor aliquet.',
            'Nullam vitae lectus ut dolor bibendum gravida non eu felis.',
            'Sed vel neque at ligula laoreet consequat.',
            'Suspendisse pellentesque velit non enim sodales tempor.',
            'Suspendisse lacinia dui non imperdiet feugiat.',
            'Mauris eu quam in tortor viverra condimentum.',
            'Maecenas molestie sapien sit amet porttitor sagittis.',
            'Suspendisse pretium lectus a nisi eleifend porta.',
            'Aenean ut ipsum vel ante lobortis facilisis ac non lorem.',
            'Morbi vulputate tellus sagittis condimentum tristique.',
            'Donec non nulla posuere, iaculis est eu, ultricies sem.',
            'Quisque viverra sem in mauris tristique congue.',
            'Etiam et purus interdum, egestas erat quis, tristique mauris.',
            'Integer scelerisque tellus non blandit fermentum.',
        ));
        $pick_long_name = $create_lipsum_picker(array(
            'Maecenas ornare elit at lorem mollis',
            'Proin ac magna ut nulla gravida',
            'In ut enim vel nunc imperdiet',
            'Vestibulum tristique massa nec elit rutrum',
            'Suspendisse at dui a sapien consequat',
            'Sed id lorem ac odio vulputate',
            'Nunc vitae mi nec augue mollis',
            'Fusce vitae mauris id ante laoreet',
            'Etiam nec dui ac libero euismod',
            'Sed porttitor ligula a libero aliquam',
            'Nulla ultricies elit lacinia ante vehicula',
            'Donec laoreet massa nec orci vestibulum',
            'Suspendisse sit amet leo mattis',
            'Donec et ex gravida, commodo',
            'Pellentesque ut lectus suscipit, vestibulum',
            'Fusce pharetra nunc vel orci efficitur',
            'Nam dapibus ligula id facilisis commodo',
            'Integer accumsan purus dictum, cursus',
            'Donec congue sem at libero venenatis',
            'Aliquam bibendum lectus quis quam aliquam',
        ));
        $pick_short_name = $create_lipsum_picker(array(
            'Curabitur mattis augue',
            'Vitae iaculis sagittis',
            'Cras iaculis leo ',
            'Et molestie varius',
            'Morbi placerat libero',
            'Ut gravida maximus',
            'Fusce tincidunt erat',
            'A mi faucibus',
            'Sed ut urna',
            'Ac nisl bibendum',
            'Phasellus sit amet',
            'Leo lobortis',
            'Duis vehicula lectus',
            'Et urna ullamcorper',
            'Nam bibendum lorem',
            'Ut mauris laoreet dictum',
            'Ut et lacus',
            'A neque consequat porttitor',
            'Curabitur eget tortor',
            'Eu orci ullamcorper',
        ));
        $pick_duration = function () {
            $choices = array(
                '120 min', '90 min', '60 min', '30 min',
            );
            return $choices[array_rand($choices)];
        };
        $pick_level = function () {
            $choices = array(
                __('Easy', BPMJ_EDDCM_DOMAIN),
                __('Medium', BPMJ_EDDCM_DOMAIN),
                __('Hard', BPMJ_EDDCM_DOMAIN),
            );
            return $choices[array_rand($choices)];
        };
        $form['video_mode'] = 'on';
        $form['video'] = 'https://www.youtube.com/watch?v=c9613bTfDjI';
        $create_lesson = function () use ($pick_long_name, $pick_multiple_paragraphs, $pick_short_sentence, $pick_short_name, $pick_duration, $pick_level, $convert_sample_files) {
            return array(
                'title' => $pick_long_name(),
                'content' => $pick_multiple_paragraphs(),
                'mode' => 'lesson',
                'subtitle' => $pick_short_name(),
                'shortdesc' => $pick_short_sentence(),
                'duration' => $pick_duration(),
                'level' => $pick_level(),
                'files' => $convert_sample_files(array(
                    'audio' => $pick_short_name(),
                    'pdf' => $pick_short_name(),
                    'doc' => $pick_short_name(),
                )),
            );
        };
        $create_module = function ($lessons) use ($create_lesson, $pick_long_name, $pick_multiple_paragraphs, $pick_short_sentence) {
            $lessons_array = array();
            for ($i = 0; $i < $lessons; ++$i) {
                $lessons_array[] = $create_lesson();
            }
            return array(
                'title' => $pick_long_name(),
                'content' => $pick_multiple_paragraphs(),
                'mode' => 'full',
                'subtitle' => $pick_long_name(),
                'shortdesc' => $pick_short_sentence(),
                'module' => $lessons_array,
            );
        };
        $form['bpmj_eddcm_module'] = array(
            $create_module(rand(3, 5)),
            $create_module(rand(3, 5)),
            $create_module(rand(3, 5)),
            $create_lesson(),
            $create_lesson(),
            $create_lesson(),
        );
        return $form;
    }

    public function prepare_sample_files()
    {
        $sample_files = array(
            'audio' => '',
            'pdf' => '',
            'doc' => '',
        );
        $file_ext = function ($type) {
            switch ($type) {
                case 'audio':
                    return 'mp3';
                case 'pdf':
                    return 'pdf';
                default:
                    return 'doc';
            }
        };
        $file_mime_type = function ($type) {
            switch ($type) {
                case 'audio':
                    return 'audio/mpeg';
                case 'pdf':
                    return 'application/pdf';
                default:
                    return 'text/plain';
            }
        };
        $sample_file_posts = get_posts(
            array(
                'post_type' => 'attachment',
                'meta_key' => '_bpmj_sample_media',
                'numberposts' => -1,
            )
        );
        foreach ($sample_file_posts as $attachment) {
            $type = get_post_meta($attachment->ID, '_bpmj_sample_media', true);
            $sample_files[$type] = $attachment->ID;
        }
        foreach ($sample_files as $type => $id) {
            if (!$id) {
                /*
                 * We need to create the file
                 */
                $file_path = tempnam(sys_get_temp_dir(), 'wp_sample');
                file_put_contents($file_path, '');
                $file = array(
                    'name' => 'sample.' . $file_ext($type),
                    'type' => $file_mime_type($type),
                    'tmp_name' => $file_path,
                    'error' => 0,
                    'size' => 0,
                );

                $overrides = array(
                    'test_form' => false,
                    'test_size' => false,
                );

                // Move the temporary file into the uploads directory
                $results = wp_handle_sideload($file, $overrides);

                if (empty($results['error'])) {
                    $wp_upload_dir = wp_upload_dir();
                    $attachment = array(
                        'guid' => $wp_upload_dir['url'] . '/' . basename($results['file']),
                        'post_mime_type' => $results['type'],
                        'post_title' => preg_replace('/\.[^.]+$/', '', basename($results['file'])),
                        'post_content' => '',
                        'post_status' => 'publish',
                    );

                    $sample_files[$type] = $id = wp_insert_attachment($attachment, $results['file']);

                    // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
                    require_once(ABSPATH . 'wp-admin/includes/image.php');

                    // Generate the metadata for the attachment, and update the database record.
                    $attach_data = wp_generate_attachment_metadata($id, $results['file']);
                    wp_update_attachment_metadata($id, $attach_data);
                    add_post_meta($id, '_bpmj_sample_media', $type);
                }
            }
        }
        return $sample_files;
    }

    /**
     * @param int $product_id
     *
     * @return string
     */
    public static function get_variable_prices_module_template($product_id)
    {
        $html = '<span class="variable-prices">';
        if (edd_has_variable_prices($product_id)) {
            foreach (edd_get_variable_prices($product_id) as $price_id => $variable_price) {
                $color_key = $price_id % 8;
                $html .= '<label title="' . esc_attr($variable_price['name']) . '" class="bpmj-eddcm-variant-color-' . $color_key . '">';
                $html .= '<input class="_price_id_' . $price_id . '" type="checkbox" name="{key}[variable_prices][]" value="' . $price_id . '" {checked-' . $price_id . '} />';
                $html .= '</label>';
            }
        }
        $html .= '</span>';

        return $html;
    }

    /**
     * @param string $variable_prices_template
     * @param int $module_or_lesson_id
     * @param string $key
     *
     * @return mixed
     */
    public static function interpolate_variable_prices_template($variable_prices_template, $module_or_lesson_id, $key)
    {
        if (!$module_or_lesson_id) {
            $price_ids = 'all';
        } else {
            $restricted = bpmj_eddpc_is_restricted($module_or_lesson_id);
            $price_ids = array();
            if (is_array($restricted)) {
                foreach ($restricted as $restriction_rule) {
                    if (!empty($restriction_rule['price_id'])) {
                        if ('all' === $restriction_rule['price_id']) {
                            $price_ids = 'all';
                            break;
                        }
                        $price_ids[] = $restriction_rule['price_id'];
                    }
                }
            }
            if (is_array($price_ids)) {
                $price_ids = array_unique($price_ids);
            }
        }
        $interpolated_string = preg_replace_callback('/\{([a-z0-9\-]+)\}/', function ($match) use ($price_ids, $key) {
            if ('key' === $match[1]) {
                return $key;
            } else if (preg_match('/checked-(\d+)/', $match[1], $checked_matches) === 1) {
                if ('all' === $price_ids || in_array((int)$checked_matches[1], $price_ids)) {
                    return ' checked="checked" ';
                }
            }

            return '';
        }, $variable_prices_template);

        return $interpolated_string;
    }

    private static function get_edit_post_link(string $mode, $id): ?string
    {
        return $mode === self::TEST_MODE
            ? add_query_arg([
                'page' => Admin_Menu_Item_Slug::EDITOR_QUIZ,
                Quiz_Editor_Page_Renderer::QUIZ_ID_QUERY_ARG_NAME => $id,
            ], admin_url('admin.php'))
            : get_edit_post_link($id);
    }
}
