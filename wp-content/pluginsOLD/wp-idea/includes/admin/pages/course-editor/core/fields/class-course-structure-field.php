<?php

namespace bpmj\wpidea\admin\pages\course_editor\core\fields;

use bpmj\wpidea\admin\Creator;
use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\Courses;
use bpmj\wpidea\learning\course\Course_ID;
use bpmj\wpidea\Packages;
use bpmj\wpidea\packages\Interface_Packages_API;
use bpmj\wpidea\translator\Interface_Translator;

class Course_Structure_Field extends Abstract_Setting_Field
{
    private Course_ID $course_id;
    private Courses $courses;
    private Interface_Packages_API $packages_api;
    private Interface_Translator $translator;

    public function __construct(
        string $name,
        Course_ID $course_id,
        Courses $courses,
        Interface_Packages_API $packages_api,
        Interface_Translator $translator
    ) {
        $this->course_id = $course_id;
        $this->courses = $courses;
        $this->packages_api = $packages_api;
        $this->translator = $translator;

        parent::__construct($name);
    }

    public function render_to_string(): string
    {
        $course_options = (object)$this->courses->create_course_options_array($this->course_id->to_int());
        $variable_prices_template = $this->get_variable_prices_template((int)$course_options->product_id);
        $no_access_to_tests = !$this->packages_api->has_access_to_feature(Packages::FEAT_TESTS);

        if($no_access_to_tests) {
            echo "<div class='quizzes-package-info'>" . $this->packages_api->render_no_access_to_feature_info(Packages::FEAT_TESTS, $this->translator->translate(
                'course_editor.sections.structure.quiz.upgrade_needed'
            )) . '</div>';
        }

        ob_start();
        ?>

        <div class="bpmj-eddcm-cs-section-body">

            <form id="form-course-structure">

                <div class="form-group">
                    <input type="hidden" name="drip_value" id="bpmj_eddcm_drip_value" value="<?= $course_options->drip_value ?>" />
                    <input type="hidden" name="drip_unit" id="bpmj_eddcm_drip_unit" value="<?= esc_attr(
                        $course_options->drip_unit) ?>"/>

                    <template id="bpmj_eddcm_new_module_full_template">
                        <?= Creator::create_module_get_html('full', false, false, '', true, false, $variable_prices_template) ?>
                    </template>
                    <template id="bpmj_eddcm_new_module_lesson_template">
                        <?= Creator::create_module_get_html('lesson', false, false, '', true, false, $variable_prices_template) ?>
                    </template>
                    <template id="bpmj_eddcm_new_module_test_template">
                        <?= Creator::create_module_get_html('test', false, false, '', true, false, $variable_prices_template) ?>
                    </template>
                    <template id="bpmj_eddcm_new_lesson_template">
                        <?= Creator::create_lesson_get_html(false, false, true, false, $variable_prices_template) ?>
                    </template>
                </div>

                <?= $this->get_variable_prices_module_legend((int)$course_options->product_id) ?>

                <div class="form-group">
                    <ul class="modules" id="bpmj_eddcm_modules_list">
                        <?php
                        if (isset($course_options)) {
                            if (is_array($course_options->modules)) {
                                $content = [];

                                foreach ($course_options->modules as $module) {
                                    $module_id = isset($module['id']) ? $module['id'] : false;
                                    $lessons = isset($module['lessons']) ? $module['lessons'] : false;

                                    if (!$lessons) {
                                        $lessons = isset($module['module']) ? $module['module'] : false;
                                    }
                                    $title = get_the_title($module_id);

                                    $get_module = Creator::create_module($module['mode'], $module_id, $lessons, $title, true, false, $variable_prices_template);

                                    $content[$get_module['editor_id']] = $module;
                                    $content = $content + $get_module['content'];

                                    echo $get_module['html'];
                                }
                            }
                        }
                        ?>
                    </ul>

                    <div class="desc">
                        <?= $this->translator->translate('course_editor.sections.structure.before_save.info1') ?><br>
                        <?= $this->translator->translate('course_editor.sections.structure.before_save.info2') ?>
                    </div>
                </div>

                <div class="creator-buttons text-center">

                </div>

                <input type="hidden" id="bpmj_eddcm_save_modules" name="bpmj_eddcm_save_modules" value=""/>
                <input type="hidden" id="course_id" name="course_id" value="<?= $this->course_id->to_int() ?>"/>

                <script>
                    window.bpmj_eddcm_course_structure_creator_init('new_course_editor');
                </script>
            </form>

            <div class="save-fixed-bottom-box">
                <button type="button" class="creator-buttons-add"
                        data-action="add-module"
                        data-mode="full">
                    <?= $this->translator->translate('course_editor.sections.structure.button.add_module') ?>
                </button>

                <button type="button" class="creator-buttons-add"
                        data-action="add-module"
                        data-mode="lesson">
                    <?= $this->translator->translate('course_editor.sections.structure.button.add_lesson') ?>
                </button>

                <button<?php
                echo $no_access_to_tests ? ' disabled="disabled"' : ''; ?>
                        type="button" class="creator-buttons-add"
                        data-action="add-module"
                        data-mode="test">
                    <?= $this->translator->translate('course_editor.sections.structure.button.add_quiz') ?>
                </button>

                <button type="button" id="save-course-structure" class="creator-buttons-add">
                    <?= $this->translator->translate('settings.field.button.save') ?>
                </button>
                <br style="clear:both;">
            </div>

        </div>


        <?php

        return ob_get_clean();
    }

    private function get_variable_prices_template(int $product_id): string
    {
        $html = '<div class="variable-prices">';
        if (edd_has_variable_prices($product_id)) {

            $variable_prices = edd_get_variable_prices($product_id);
            if(!empty($variable_prices)){

               foreach ($variable_prices as $price_id => $variable_price) {
                  $color_key = $price_id % 8;
                  $html .= '<label title="' . esc_attr($variable_price['name']) . '" class="bpmj-eddcm-variant-color-' . $color_key . '">';
                  $html .= '<input class="_price_id_' . $price_id . '" type="checkbox" name="{key}[variable_prices][]" value="' . $price_id . '" {checked-' . $price_id . '} />';
                  $html .= '</label>';
               }

            }
        }
        $html .= '</div>';

        return $html;
    }

    private function get_variable_prices_module_legend(int $product_id): string
    {
        ob_start();
        if (edd_has_variable_prices($product_id)):

            $variable_prices = edd_get_variable_prices($product_id);

              if(!empty($variable_prices)):

            ?>
        <div class="variant-color-legend">
            <p>
                <?= $this->translator->translate('course_editor.sections.structure.variant_color_legend') ?>
                <?php
                $n = 0;
                $i = 1;
                $count_variable_prices = count($variable_prices);
                foreach ($variable_prices as $price_id => $variable_price):

                    $div_start_variable_prices = '';
                    $div_end_variable_prices = '';

                    if($n === 0){
                        $div_start_variable_prices = '<div class="variant-color-block">';
                        $div_end_variable_prices = '';
                    }

                    $n++;
                    $n = ($n % 2);

                    if($n === 0){
                       $div_start_variable_prices = '';
                       $div_end_variable_prices = '</div>';
                    }

                    if($n!==0 && $i === $count_variable_prices){
                        $div_start_variable_prices = '<div class="variant-color-block">';
                        $div_end_variable_prices = '</div>';
                    }

                    $color_key = $price_id % 8;

                    echo $div_start_variable_prices;
                    ?>
                        <div style="margin-bottom:10px;"><div class="bpmj-eddcm-variant-color bpmj-eddcm-variant-color-<?php echo $color_key; ?>"></div>
                   <?php echo $variable_price['name'].'</div>';
                     echo $div_end_variable_prices;
                     $i++;
                endforeach;
                ?>
            <br style="clear:both;">
            </p>
        </div>
        <?php
              endif;
            endif;
        return ob_get_clean();
    }
}