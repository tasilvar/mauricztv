<?php

namespace bpmj\wpidea\templates_system\templates\base;

use bpmj\wpidea\templates_system\admin\blocks\{Course_Offer_Page_Content_Block,
	Course_Offer_Page_Details_Block,
	Deprecated_Course_Offer_Page_Banner_Block,
	Opinions_Block};
use bpmj\wpidea\templates_system\templates\Template;

abstract class Abstract_Course_Offer_Page_Template extends Template
{
    protected $registers_blocks = [
        Course_Offer_Page_Content_Block::class,
        Course_Offer_Page_Details_Block::class,
        Deprecated_Course_Offer_Page_Banner_Block::class,
        Opinions_Block::class
    ];

    public function get_default_name()
    {
        return 'template_name.course_offer_page';
    }
}