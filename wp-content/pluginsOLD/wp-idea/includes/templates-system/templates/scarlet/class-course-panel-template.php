<?php

namespace bpmj\wpidea\templates_system\templates\scarlet;

use bpmj\wpidea\templates_system\admin\blocks\{Course_Banner_Block, Opinions_Block};
use bpmj\wpidea\templates_system\templates\base\Abstract_Course_Panel_Template;
use bpmj\wpidea\View;

class Course_Panel_Template extends Abstract_Course_Panel_Template
{
    public function __construct()
    {
        $this->registers_blocks = array_merge($this->registers_blocks, [
            Course_Banner_Block::class,
            Opinions_Block::class
        ]);

        parent::__construct();
    }
    public function get_default_content()
    {
        return View::get_admin('/gutenberg/templates/scarlet/course-panel-template');
    }
}
