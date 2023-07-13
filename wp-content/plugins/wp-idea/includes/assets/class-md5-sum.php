<?php

namespace bpmj\wpidea\assets;

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) )
	exit;

class Md5_Sum {

    /**
     * @var Assets
     */
    private $assets;

    /**
     * MD5 file path
     *  
     * @var string
     */
    public $file_path;

    /**
     * Newly generated md5sum file content
     *
     * @var string
     */
    public $new_file;

    /**
     * @param Assets $assets
     */
    public function __construct( $assets ) {
        $this->assets = $assets;

        $this->file_path = $this->assets->get_source_dir() . '/' . 'assets.md5sum';
    }

    public function get_current()
    {
        return file_exists( $this->file_path ) ? file_get_contents( $this->file_path ) : '';
    }

    public function generate_new()
    {
        $assets_md5_file = $this->get_current();

        $md5_dir = function ( $dir, array $exclude = array() ) use ( &$md5_dir ) {
            $dir_handle = dir( $dir );
            $file_md5s  = array();
            while ( false !== ( $entry = $dir_handle->read() ) ) {
                if ( '.' !== $entry && '..' !== $entry && ! in_array( $entry, $exclude ) ) {
                    if ( is_dir( $dir . '/' . $entry ) ) {
                        $file_md5s[] = $md5_dir( $dir . '/' . $entry, $exclude );
                    } else {
                        $file_md5s[] = md5_file( $dir . '/' . $entry );
                    }
                }
            }
            $dir_handle->close();

            return md5( implode( '', $file_md5s ) );
        };

        $new_file = $md5_dir( $this->assets->get_source_dir(), array( $assets_md5_file ) );
        
        $this->new_file = $new_file;
        
        return $new_file;
    }

    /**
     * This method does not generate a new file if it has already been generated.
     * Call generate_new() to force file regeneration.
     */
    public function get_new()
    {
        if( $this->new_file ) return $this->new_file;

        return $this->generate_new();
    }

    public function differs()
    {
        $current    = $this->get_current();
        $new        = $this->get_new();

        return $current !== $new;
    }
}