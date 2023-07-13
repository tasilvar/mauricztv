<?php

namespace bpmj\wpidea\assets;

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

class Assets {

    const WP_IDEA_MIN_CSS_FILE_PATH = 'css/wp-idea.min.css';
    const WP_IDEA_MIN_DYNAMIC_CSS_FILE_PATH = 'css/dynamic-wp-idea.min.css';
    const WP_IDEA_MIN_JS_FILE_PATH = 'js/wp-idea.min.js';

    /**
     * @var Md5_Sum
     */
    private $md5sum;

    /**
     * @var Assets_Dir
     */
    private $dir;

    /**
     * @param string $template_dir Template root directory
     */
    public function __construct( $template_dir ) {
        $this->dir      = new Assets_Dir( $template_dir );
        $this->md5sum   = new Md5_Sum( $this );

        $this->create_missing_directories();
    }

    public function get_source_dir()
    {
        return $this->dir->template_dir . '/assets_src';
    }

    public function get_absolute_dir()
    {
        return $this->dir->absolute;
    }

    public function get_absolute_path($path)
    {
        return $this->dir->absolute . $path;
    }

    public function get_relative_dir()
    {
        return $this->dir->relative;
    }

    public function get_relative_path($path)
    {
        return $this->dir->relative . $path;
    }

    /**
     * Returns path to template catalog in the assets directory (returns path relative to assets dir location - wp-content by defalut)
     *
     * eg. lms-data/assets/scarlet
     *
     * @return string
     */
    public function get_assets_dir_path()
    {
        return $this->dir->get_assets_dir_path();
    }

    /**
     * Return old assets directory
     *
     * @param boolean $relative Relative to the template directory
     * @return string
     */
    public function get_old_dir( $relative = false )
    {
        if( $relative) return '/assets';

        return $this->dir->template_dir . '/assets';
    }

    /**
     * Check assets md5sum and regenerate if sum differs
     *
     * @return bool true if regenerated, false if nothing changed
     */
    public function regenerate()
    {
        $generated = false;

        if ($this->md5sum->differs() ) {
            // Something changed - reload assets
            bpmj_eddcm_reload_layout_template_settings();
            $generated = file_put_contents( $this->md5sum->file_path, $this->md5sum->get_new() );
        }

        return $generated ? true : false;
    }

    public function create_missing_directories()
    {
        $assets_path = $this->get_absolute_dir();

        if( empty( $assets_path ) ) return;

        $dirs = array(
            $assets_path,
            $assets_path . '/css',
            $assets_path . '/img',
            $assets_path . '/svg',
            $assets_path . '/gfx'
        );

        // create missing directories
        foreach ($dirs as $dir) {
            if (!file_exists( $dir )) {
                mkdir( $dir, 0777, true );
            }
        }
    }

    public function get_absolute_file_path( $file_path)
    {
        return $this->get_path($file_path, false);
    }

    public function get_relative_file_path( $file_path)
    {
        return $this->get_path($file_path);
    }

    private function get_path($file_path, $relative = true)
    {
        $absolute_lms_data_path       = $this->get_absolute_dir() . '/' . $file_path;
        $relative_lms_data_path = $this->get_relative_dir() . '/' . $file_path;
        $template_path       = $this->get_old_dir( $relative ) . '/' . $file_path;

        if( file_exists( $absolute_lms_data_path ) ){
            return ($relative) ? $relative_lms_data_path : $absolute_lms_data_path;
        }
        return $template_path;
    }
}
