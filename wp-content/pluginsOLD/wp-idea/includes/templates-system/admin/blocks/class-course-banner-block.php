<?php

namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\templates_system\admin\blocks\attributes\Alignment_Matrix_Attribute;
use bpmj\wpidea\templates_system\admin\blocks\attributes\Color_Picker_Attribute;
use bpmj\wpidea\templates_system\admin\blocks\attributes\Toggle_Attribute;
use bpmj\wpidea\View;

class Course_Banner_Block extends Block
{
    public const BLOCK_NAME = 'wpi/curse-banner';

    private const SHOW_TITLE_ATTR = 'show_title';
    private const SHOW_TITLE_ATTR_DEFAULT_VAL = true;

    private const TITLE_COLOR_ATTR = 'title_color';
    private const TITLE_COLOR_ATTR_DEFAULT_VAL = '#ffffff';

    private const TITLE_ALIGNMENT_ATTR = 'title_alignment';
    private const TITLE_ALIGNMENT_ATTR_DEFAULT_VAL = 'center left';

    public function __construct()
    {
        parent::__construct();

        $this->title = __('Course Panel Slider', BPMJ_EDDCM_DOMAIN);
    }

    public function get_content_to_render($atts)
    {
        $show_title = $atts[self::SHOW_TITLE_ATTR];
        $title_color = $atts[self::TITLE_COLOR_ATTR];
        $title_alignment = $atts[self::TITLE_ALIGNMENT_ATTR];

        $banner_image = WPI()->templates->get_meta('banner');
        $title_alignment_v = $this->get_align_items_value_from_alignment_matrix_attr_value($title_alignment);
        $title_alignment_h = $this->get_justify_content_value_from_alignment_matrix_attr_value($title_alignment);

        return View::get('/course/banner', [
            'image' => $banner_image,
            'title' => $show_title ? get_the_title() : null,
            'title_color' => $title_color,
            'title_alignment_v' => $title_alignment_v,
            'title_alignment_h' => $title_alignment_h
        ]);
    }

    private function get_align_items_value_from_alignment_matrix_attr_value(string $matrix_attr_value): string
    {
        $alignment = explode(' ', $matrix_attr_value)[0];

        switch ($alignment) {
            case 'top':
                return 'start';

            case 'center':
            default:
                return 'center';

            case 'bottom':
                return 'end';
        }
    }

    private function get_justify_content_value_from_alignment_matrix_attr_value(string $matrix_attr_value): string
    {
        $alignment = explode(' ', $matrix_attr_value)[1];

        switch ($alignment) {
            case 'left':
                return 'start';

            case 'center':
            default:
                return 'center';

            case 'right':
                return 'end';
        }
    }

    protected function setup_attributes()
    {
        $this->add_show_title_attribute();
        $this->add_title_color_attribute();
        $this->add_title_alignment_attribute();
    }

    private function add_show_title_attribute(): void
    {
        $label = __('Show course title', BPMJ_EDDCM_DOMAIN);
        $default_value = self::SHOW_TITLE_ATTR_DEFAULT_VAL;
        $attr = new Toggle_Attribute(self::SHOW_TITLE_ATTR, $label, null, $default_value);

        $this->add_attribute($attr);
    }

    private function add_title_color_attribute(): void
    {
        $label = __('Select title color', BPMJ_EDDCM_DOMAIN) . ':';
        $default_value = self::TITLE_COLOR_ATTR_DEFAULT_VAL;
        $attr = new Color_Picker_Attribute(self::TITLE_COLOR_ATTR, $label, null, $default_value);

        $this->add_attribute($attr);
    }

    private function add_title_alignment_attribute(): void
    {
        $label = __('Select title alignment', BPMJ_EDDCM_DOMAIN) . ':';
        $default_value = self::TITLE_ALIGNMENT_ATTR_DEFAULT_VAL;
        $attr = new Alignment_Matrix_Attribute(self::TITLE_ALIGNMENT_ATTR, $label, null, $default_value);

        $this->add_attribute($attr);
    }
}
