<?php

namespace bpmj\wpidea\templates_system\templates\base;

use bpmj\wpidea\templates_system\admin\blocks\{
    Comments_Block,
    Course_Content_List,
    Course_Files_Block,
    Course_Navigation,
    Course_Quiz_Content_Block,
    Course_Top_Bar_Block
};
use bpmj\wpidea\templates_system\templates\Template;

abstract class Abstract_Course_Quiz_Template extends Template
{
    protected $registers_blocks = [
        Course_Top_Bar_Block::class,
        Course_Navigation::class,
        Course_Content_List::class,
        Course_Quiz_Content_Block::class,
        Course_Files_Block::class,
        Comments_Block::class,
    ];

    public function get_default_name()
    {
        return 'template_name.course_quiz_page';
    }
}
