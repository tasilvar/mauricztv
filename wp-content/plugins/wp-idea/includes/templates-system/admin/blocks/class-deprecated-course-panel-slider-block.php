<?php

namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\settings\LMS_Settings;
use bpmj\wpidea\View;

class Deprecated_Course_Panel_Slider_Block extends Block
{
    const BLOCK_NAME = 'wpi/curse-panel-slider';

    public function __construct() {
        parent::__construct();

        $this->title = __('Old course banner (deprecated)', BPMJ_EDDCM_DOMAIN);
    }

    public function get_content_to_render($atts)
    {
        $disable_banners = LMS_Settings::is_template_option_enabled(
            LMS_Settings::TEMPLATE_SCARLET,
            LMS_Settings::TEMPLATE_OPTION_DISABLE_BANNERS
        );

        if ($disable_banners) {
            return View::get('/course/banner-placeholder');
        }

        $bg_image = WPI()->templates->get_meta( 'banner' ) ?? bpmj_eddcm_template_get_file( 'assets/img/panelkursu.jpg' );

        return View::get('/course/deprecated-banner', [
            'bg_image' => $bg_image
        ]);
    }
}
