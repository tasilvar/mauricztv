<?php
namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\View;

class Course_Offer_Page_Content_Block extends Block
{
    const BLOCK_NAME = 'wpi/course-offer-page-content';

    public function __construct() {
        parent::__construct();

        $this->title = __('Offer Content', BPMJ_EDDCM_DOMAIN);
    }

    public function get_content_to_render($atts)
    {
        return View::get($this->get_template_path_base() . '/course-offer-page/content');
    }
}
