<?php

namespace bpmj\wpidea\admin\helpers\wp;

class WP_Pages_Query_Helper {
    public static function get_all_pages(): array
    {
        $pages = [];

        foreach (get_pages() as $page) {
            $pages[$page->ID] = $page->post_title;
        }

        return $pages;
    }

    public static function get_id_of_first_page_containing_shortcode($shortcode): ?int
    {
        foreach (get_pages() as $page) {
            if(has_shortcode($page->post_content, $shortcode)) {
                return $page->ID;
            }
        }

        return null;
    }
}