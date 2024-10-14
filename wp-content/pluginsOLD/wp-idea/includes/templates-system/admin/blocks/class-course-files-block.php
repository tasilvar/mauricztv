<?php

namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\templates_system\admin\blocks\attributes\Toggle_Attribute;
use bpmj\wpidea\View;

class Course_Files_Block extends Block
{
    const BLOCK_NAME = 'wpi/course-files';

    private const COMPACT_MODE_ATTR = 'compact_mode';
    private const COMPACT_MODE_ATTR_DEFAULT_VAL = false;

    public function __construct() {
        parent::__construct();

        $this->title = __('Course Files', BPMJ_EDDCM_DOMAIN);
    }

    public function get_content_to_render($atts)
    {
        $files = WPI()->templates->get_meta( 'files' );
        $lesson_page_id = get_the_ID();
        $restricted = bpmj_eddpc_is_restricted($lesson_page_id);
        $visible_files_block = true;
        $compact_mode_on = $atts[self::COMPACT_MODE_ATTR];

        if($restricted){
            $has_access = bpmj_eddpc_user_can_access(null, $restricted, $lesson_page_id);
            if ('valid' !== $has_access['status']) {
                $visible_files_block = false;
            }
        }

        return View::get($this->get_template_path_base() . '/course/files', [
            'files' => $files,
            'lesson_page_id' => $lesson_page_id,
            'visible_files_block' => $visible_files_block,
            'compact_mode_on' => $compact_mode_on
        ]);
    }

    protected function setup_attributes()
    {
        $this->add_compact_mode_attribute();
    }

    private function add_compact_mode_attribute(): void
    {
        $title = __('Compact Mode', BPMJ_EDDCM_DOMAIN);
        $hint = __('Display all files in one column on desktop.', BPMJ_EDDCM_DOMAIN);
        $default_value = self::COMPACT_MODE_ATTR_DEFAULT_VAL;
        $attr = new Toggle_Attribute(self::COMPACT_MODE_ATTR, $title, $hint, $default_value);

        $this->add_attribute($attr);
    }
}
