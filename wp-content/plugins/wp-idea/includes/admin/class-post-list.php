<?php

/**
 *
 * The class responsible for list pages page
 *
 */

// Exit if accessed directly
namespace bpmj\wpidea\admin;

use bpmj\wpidea\Helper;
use bpmj\wpidea\instantiator\Interface_Initiable;
use WP_Post;
use WP_Query;

class Post_List implements Interface_Initiable
{
    /**
     * Add hooks and filters
     */
    public function init(): void
    {
        add_filter('admin_init', array($this, 'hook_admin_init'));
    }

    /**
     * Add additional hooks and filters if we are on a supported page
     *
     * @global string $pagenow
     */
    public function hook_admin_init()
    {
        global $pagenow, $typenow;
        if ('edit.php' !== $pagenow || 'page' !== $typenow) {
            return;
        }
        add_action('pre_get_posts', array($this, 'hook_pre_get_posts'));
        add_filter('wp_count_posts', array($this, 'filter_count_posts'), 10, 3);
    }

    /**
     * @param WP_Query $query
     */
    public function hook_pre_get_posts(WP_Query &$query)
    {
        if ('page' !== $query->query_vars['post_type'] || Helper::is_dev()) {
            return;
        }
        $meta_query = array(
            array(
                'key' => 'mode',
                'compare' => 'NOT EXISTS',
            ),
        );
        $query->query_vars['meta_query'] = $meta_query;
    }
    
    public function filter_count_posts($counts, $type, $perm) {
        $publigo_counts = $this->publigo_count_posts($type, $perm);

        foreach (get_object_vars($counts) as $key => $value) {
            $counts->$key -= $publigo_counts->$key;
        }

        return $counts;
    }
    
    protected function publigo_count_posts( $type = 'post', $perm = '' ) {
        global $wpdb;

        $cache_key = _count_posts_cache_key( $type, $perm );
        
        $counts = wp_cache_get( $cache_key, 'publigo_counts' );
        if ( false !== $counts ) {
            foreach ( get_post_stati() as $status ) {
                if ( ! isset( $counts->{$status} ) ) {
                    $counts->{$status} = 0;
                }
            }

            return $counts;
        }

        $query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s AND EXISTS (select m.meta_id from {$wpdb->postmeta} m WHERE m.post_id=ID AND m.meta_key='mode')";

        if ( 'readable' === $perm && is_user_logged_in() ) {
            $post_type_object = get_post_type_object( $type );
            if ( ! current_user_can( $post_type_object->cap->read_private_posts ) ) {
                $query .= $wpdb->prepare(
                    " AND (post_status != 'private' OR ( post_author = %d AND post_status = 'private' ))",
                    get_current_user_id()
                );
            }
        }

        $query .= ' GROUP BY post_status';
        $results = (array) $wpdb->get_results( $wpdb->prepare( $query, $type ), ARRAY_A );
        $counts  = array_fill_keys( get_post_stati(), 0 );
                    
        foreach ( $results as $row ) {
            $counts[ $row['post_status'] ] = $row['num_posts'];
        }

        $counts = (object) $counts;
        wp_cache_set( $cache_key, $counts, 'publigo_counts' );

        return $counts;
    }

}