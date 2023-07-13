<?php

namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\Course_Progress;
use bpmj\wpidea\modules\app_view\api\App_View_API_Static_Helper;
use bpmj\wpidea\templates_system\admin\blocks\attributes\Toggle_Attribute;
use bpmj\wpidea\View;

class Course_Panel_Lessons_List_Block extends Block
{
    const BLOCK_NAME = 'wpi/panel-list';

    private const COMPACT_MODE_ATTR = 'compact_mode';
    private const COMPACT_MODE_ATTR_DEFAULT_VAL = false;

    private const SHOW_TITLE_ATTR = 'show_title';
    private const SHOW_TITLE_ATTR_DEFAULT_VAL = true;

    public function __construct() {
        parent::__construct();

        $this->title = __('Lessons List Block', BPMJ_EDDCM_DOMAIN);
    }

    public function get_content_to_render($atts)
    {
        if (App_View_API_Static_Helper::is_active()) {
            return;
        }

        $lesson_page_id = get_the_ID();
        $course_page_id = WPI()->courses->get_course_top_page( $lesson_page_id );
        $lessons  = WPI()->courses->get_course_structure_flat( $course_page_id, false );
        $progress = new Course_Progress( $course_page_id );
        $compact_mode_on = $atts[self::COMPACT_MODE_ATTR];
        $show_title = $atts[self::SHOW_TITLE_ATTR];

        return View::get($this->get_template_path_base() . '/course-panel/list/list', [
            'lesson_page_id' => $lesson_page_id,
            'lessons' => $lessons,
            'progress' => $progress,
            'compact_mode_on' => $compact_mode_on,
            'show_title' => $show_title
        ]);
    }

    protected function setup_attributes()
    {
        $this->add_compact_mode_attribute();
        $this->add_show_title_attribute();
    }

    private function add_compact_mode_attribute(): void
    {
        $title = __('Compact Mode', BPMJ_EDDCM_DOMAIN);
        $hint = __('Display all lessons and modules in one column.', BPMJ_EDDCM_DOMAIN);
        $default_value = self::COMPACT_MODE_ATTR_DEFAULT_VAL;
        $attr = new Toggle_Attribute(self::COMPACT_MODE_ATTR, $title, $hint, $default_value);

        $this->add_attribute($attr);
    }

    private function add_show_title_attribute(): void
    {
        $title = __('Show section title', BPMJ_EDDCM_DOMAIN);
        $default_value = self::SHOW_TITLE_ATTR_DEFAULT_VAL;
        $attr = new Toggle_Attribute(self::SHOW_TITLE_ATTR, $title, null, $default_value);

        $this->add_attribute($attr);
    }
}
