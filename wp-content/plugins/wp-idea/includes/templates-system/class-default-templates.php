<?php

namespace bpmj\wpidea\templates_system;

use bpmj\wpidea\templates_system\groups\Template_Group;
use bpmj\wpidea\templates_system\templates\classic\Cart_Template as Cart_Template_Classic;
use bpmj\wpidea\templates_system\templates\classic\Course_Lesson_Template as Course_Lesson_Template_Classic;
use bpmj\wpidea\templates_system\templates\classic\Course_Module_Template as Course_Module_Template_Classic;
use bpmj\wpidea\templates_system\templates\classic\Course_Offer_Page_Template as Course_Offer_Page_Template_Classic;
use bpmj\wpidea\templates_system\templates\classic\Course_Panel_Template as Course_Panel_Template_Classic;
use bpmj\wpidea\templates_system\templates\classic\Courses_Page_Template as Courses_Page_Template_Classic;
use bpmj\wpidea\templates_system\templates\classic\User_Account_Template as User_Account_Template_Classic;
use bpmj\wpidea\templates_system\templates\classic\Category_Page_Template as Category_Page_Template_Classic;
use bpmj\wpidea\templates_system\templates\classic\Tag_Page_Template as Tag_Page_Template_Classic;
use bpmj\wpidea\templates_system\templates\scarlet\Cart_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Course_Lesson_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Course_Module_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Course_Offer_Page_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Course_Panel_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Course_Quiz_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Courses_Page_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Experimental_Cart_Template;
use bpmj\wpidea\templates_system\templates\scarlet\User_Account_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Category_Page_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Tag_Page_Template;
use bpmj\wpidea\templates_system\templates\scarlet\Search_Page_Template;

class Default_Templates
{
    private $default_templates = [
        Template_Group::BASE_TEMPLATE_CLASSIC => [
            Courses_Page_Template_Classic::class,
            Course_Offer_Page_Template_Classic::class,
            Cart_Template_Classic::class,
            Course_Panel_Template_Classic::class,
            Course_Module_Template_Classic::class,
            Course_Lesson_Template_Classic::class,
            User_Account_Template_Classic::class,
            Category_Page_Template_Classic::class,
            Tag_Page_Template_Classic::class
        ],
        Template_Group::BASE_TEMPLATE_SCARLET => [
            Courses_Page_Template::class,
            Course_Offer_Page_Template::class,
            Cart_Template::class,
            Experimental_Cart_Template::class,
            Course_Module_Template::class,
            Course_Panel_Template::class,
            User_Account_Template::class,
            Course_Lesson_Template::class,
            Course_Quiz_Template::class,
            Category_Page_Template::class,
            Tag_Page_Template::class,
            Search_Page_Template::class
        ]
    ];

    public function get_all(): array
    {
        return $this->default_templates;
    }
}