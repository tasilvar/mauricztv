<?php

namespace bpmj\wpidea\templates_system\templates\base;

use bpmj\wpidea\templates_system\admin\blocks\{Comments_Block,
    Course_Content_List,
    Course_Files_Block,
    Course_Navigation,
    Course_Navigation_Next,
    Course_Navigation_Prev,
    Course_Top_Bar_Block,
    Page_Content_Block};
use bpmj\wpidea\templates_system\templates\Template;

abstract class Abstract_Course_Lesson_Template extends Template
{
    protected $registers_blocks = [
        Course_Top_Bar_Block::class,
        Course_Navigation::class,
        Course_Navigation_Prev::class,
        Course_Navigation_Next::class,
        Course_Files_Block::class,
        Comments_Block::class,
        Course_Content_List::class
    ];

    public function get_default_name()
    {
        return 'template_name.course_lesson_page';
    }
}
