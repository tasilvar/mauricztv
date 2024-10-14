<?php
namespace bpmj\wpidea\mods;

use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\events\actions\Interface_Actions;

class Custom_Editor_Font_Sizes implements Interface_Initiable
{
    private const ENQUEUE_BLOCK_ASSETS = 'enqueue_block_assets';
    private const EDITOR_FONT_SIZES = 'editor-font-sizes';
    private const WP_BLOCK_LIBRARY = 'wp-block-library';

    private Interface_Actions $actions;

    public function __construct(Interface_Actions $actions)
    {
        $this->actions = $actions;
    }

    public function init(): void
    {
        $this->actions->add(self::ENQUEUE_BLOCK_ASSETS, [$this, 'add_custom_editor_font_sizes']);
    }

     public function add_custom_editor_font_sizes(): void
     {
         list( $font_sizes ) = (array) get_theme_support( self::EDITOR_FONT_SIZES );
         if ( ! $font_sizes ) {
             return;
         }

         $css = '';
         foreach ( $font_sizes as $size ) {
             $css .= '.has-'.$size['slug'].'-font-size {font-size: '.$size['size'].'px;}';
         }

        wp_add_inline_style( self::WP_BLOCK_LIBRARY, $css );
    }

}

