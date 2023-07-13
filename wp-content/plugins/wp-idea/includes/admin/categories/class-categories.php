<?php

namespace bpmj\wpidea\admin\categories;

class Categories
{
    public function __construct()
    {
        add_action( 'parent_file', [ $this, 'categories_menu_highlight' ] );
    }

    public function categories_menu_highlight( $parent_file )
    {
        global $current_screen;

        $taxonomy = $current_screen->taxonomy;
        if ( 'download_category' === $taxonomy || 'download_tag' === $taxonomy ) {
            $parent_file = 'wp-idea';
        }

        return $parent_file;
    }
}
