<?php

namespace bpmj\wpidea\templates_system\templates\classic;

use bpmj\wpidea\templates_system\admin\blocks\Course_Lesson_Info_Block;
use bpmj\wpidea\templates_system\admin\blocks\Course_Lesson_Title_Section_Block;
use bpmj\wpidea\templates_system\admin\blocks\Course_Progress_Block;
use bpmj\wpidea\templates_system\templates\base\Abstract_Course_Lesson_Template;
use bpmj\wpidea\View;

class Course_Lesson_Template extends Abstract_Course_Lesson_Template
{
    public function __construct()
    {
        $this->registers_blocks = array_merge($this->registers_blocks, [
            Course_Lesson_Info_Block::class,
            Course_Progress_Block::class,
            Course_Lesson_Title_Section_Block::class
        ]);

        parent::__construct();
    }

    public function get_default_content()
    {
        return View::get_admin('/gutenberg/templates/classic/lesson-template');
    }

    public function before_render(): void
    {
        add_filter('bpmj_wpi_footer_class', function(string $footer_class) {
            return $footer_class . ' ' . 'alt';
        });

        parent::before_render();
    }
}
