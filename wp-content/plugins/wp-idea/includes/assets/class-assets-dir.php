<?php

namespace bpmj\wpidea\assets;

class Assets_Dir {

    const EXTERNAL_DIR_NAME = 'lms-data';

    /**
     * Directory path relative to WPI template directory
     *
     * @var string
     */
    public $relative;

    /**
     * Absolute directory path
     *
     * @var string
     */
    public $absolute;

    /**
     * Template directory
     *
     * @var string
     */
    public $template_dir;

    public function __construct( $template_dir ) {
        $this->template_dir = $template_dir;

        $this->relative = '../../../../' . $this->get_assets_dir_path();
        $this->absolute = BPMJ_EDDCM_DIR . '../../' . $this->get_assets_dir_path();
    }

    public function get_assets_dir_path()
    {
        return self::EXTERNAL_DIR_NAME . '/assets/' . $this->get_template_dir_name();
    }

    public function get_template_dir_name()
    {
        $template_dir_elements  = preg_split( '~[\\\\/]~', $this->template_dir );

        return end( $template_dir_elements );
    }
}
