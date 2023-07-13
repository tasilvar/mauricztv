<?php

namespace bpmj\wpidea\templates_system\templates\base;

use bpmj\wpidea\templates_system\admin\blocks\{
    Course_Panel_Lessons_List_Block,
    Course_Panel_Lessons_Modules_Block,
    Deprecated_Course_Panel_Slider_Block,
    Course_Top_Bar_Block
};
use bpmj\wpidea\templates_system\templates\Template;

abstract class Abstract_Course_Panel_Template extends Template
{
    protected $registers_blocks = [
        Course_Top_Bar_Block::class,
        Deprecated_Course_Panel_Slider_Block::class,
        Course_Panel_Lessons_Modules_Block::class,
        Course_Panel_Lessons_List_Block::class,
    ];

    public function get_default_name()
    {
        return 'template_name.course_panel_page';
    }
}
