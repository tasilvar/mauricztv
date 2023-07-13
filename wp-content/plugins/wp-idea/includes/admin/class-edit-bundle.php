<?php
namespace bpmj\wpidea\admin;

use bpmj\wpidea\Course_Bundle;

if (!defined('ABSPATH')) {
    exit;
}

class Edit_Bundle extends Edit_Course
{
    private const ADMIN_BODY_CLASSES = 'packages-editor';
    protected string $url_param;
    protected string $meta_key;

    public function init(): void
    {
        $this->url_param = Course_Bundle::PARAM_NAME;
        $this->meta_key = Course_Bundle::get_meta_key();
        add_action('admin_init', array($this, 'hook_init'));
        add_action( 'admin_head', array( $this, 'load_options' ) );
    }

    public function hook_init(): void
    {
        global $pagenow;

        if ('post.php' === $pagenow) {
            $post_id = isset($_GET['post']) ? $_GET['post'] : (isset($_POST['post_ID']) ? $_POST['post_ID'] : null);
            if (!$post_id) {
                if (false === strpos(wp_get_raw_referer(), $this->url_param . '=' . Course_Bundle::POST_SUBTYPE)) {
                    return;
                }
            } else if (Course_Bundle::POST_TYPE !== get_post_type($post_id) || Course_Bundle::POST_SUBTYPE !== get_post_meta($post_id, $this->meta_key, true)) {
                return;
            }
        } else if ('post-new.php' === $pagenow && isset($_GET['post_type']) && Course_Bundle::POST_TYPE === $_GET['post_type']) {
            if (!isset($_GET[$this->url_param]) || Course_Bundle::POST_SUBTYPE !== $_GET[$this->url_param]) {
                return;
            }
        } else {
            return;
        }

        remove_action('add_meta_boxes', 'edd_add_download_meta_box');
        $this->add_body_classes();
    }

    public function is_bundle_edit_screen(): bool
    {
        global $post, $pagenow;

        if (empty($post->ID)) {
            return false;
        }

        $product_type = get_post_meta( $post->ID, '_edd_product_type', true );

        return in_array( $pagenow, [
                'post-new.php',
                'post.php'
            ] ) && $post && 'download' === get_post_type( $post ) && 'bundle' === $product_type;
    }

    public function load_options(): void
    {
        global $post;

        if ( $this->is_bundle_edit_screen() ) {
            $this->options = (object) WPI()->courses->create_course_options_array( $post->ID, $post->ID );
        }
    }

    private function add_body_classes(): void
    {
        add_filter('admin_body_class', static function (string $classes) {
            $classes .= ' ' . self::ADMIN_BODY_CLASSES;


            return $classes;
        });
    }
}
