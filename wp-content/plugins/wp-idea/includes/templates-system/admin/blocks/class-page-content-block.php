<?php

namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\View;

class Page_Content_Block extends Block
{
    const BLOCK_NAME = 'wpi/page-content';

    public function __construct() {
        parent::__construct();

        $this->title = __('Page Content', BPMJ_EDDCM_DOMAIN);
    }

    public function get_content_to_render($atts)
    {
        $is_rest_request = strpos($_SERVER[ 'REQUEST_URI' ], '/wp-json/') !== false;

        if($is_rest_request) return '';

        wp_reset_postdata();

        $page_id = get_the_ID();
        $course_page = WPI()->courses->get_course_by_page( $page_id );

        return View::get($this->get_template_path_base() . '/page/content', [
            'custom_class' => apply_filters('wpi_add_custom_class', '', ($course_page && $course_page->ID) ? $course_page->ID : $page_id)
        ]);

    }
}
