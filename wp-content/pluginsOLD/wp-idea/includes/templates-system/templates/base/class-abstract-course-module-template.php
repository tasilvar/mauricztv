<?php

namespace bpmj\wpidea\templates_system\templates\base;

use bpmj\wpidea\templates_system\templates\Template;
use bpmj\wpidea\templates_system\admin\blocks\{
    Comments_Block,
    Course_Module_Lessons_List_Block,
    Course_Top_Bar_Block,
    Page_Content_Block
};

abstract class Abstract_Course_Module_Template extends Template
{
    protected $registers_blocks = [
        Course_Top_Bar_Block::class,
        Page_Content_Block::class,
        Course_Module_Lessons_List_Block::class,
        Comments_Block::class,
    ];

    public function get_default_name()
    {
        return 'template_name.course_module_page';
    }
}
