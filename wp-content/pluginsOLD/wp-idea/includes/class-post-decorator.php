<?php

/**
 * This class is meant to extend WP_Post functionality
 */

// Exit if accessed directly
namespace bpmj\wpidea;
use stdClass;
use WP_Post;

if (!defined('ABSPATH'))
    exit;

/**
 * WP_Post decorator base class
 *
 * @property int $ID
 * @property string $post_author
 * @property string $post_date
 * @property string $post_date_gmt
 * @property string $post_content
 * @property string $post_title
 * @property string $post_excerpt
 * @property string $post_status
 * @property string $comment_status
 * @property string $ping_status
 * @property string $post_password
 * @property string $post_name
 * @property string $to_ping
 * @property string $pinged
 * @property string $post_modified
 * @property string $post_modified_gmt
 * @property string $post_content_filtered
 * @property int $post_parent
 * @property string $guid
 * @property int $menu_order
 * @property string $post_type
 * @property string $post_mime_type
 * @property string $comment_count
 * @property string $filter
 * @property string $page_template
 * @property-read array $ancestors
 * @property-read int $post_category
 * @property-read string $tag_input
 *
 * @method self|array|bool|object|WP_Post filter(string $filter)
 * @method array to_array()
 */
abstract class Post_Decorator
{

    /**
     *
     * @var WP_Post
     */
    private $post;

    public function __construct($post)
    {
        if (!$post instanceof WP_Post) {
            $post = WP_Post::get_instance($post);
        }
        $this->post = $post ? $post : new WP_Post(new stdClass());
    }

    public function __get($property)
    {
        if (property_exists($this->post, $property) || in_array($property, array('page_template', 'ancestors', 'post_category', 'tag_input'))) {
            return $this->post->{$property};
        }
        _doing_it_wrong(__FUNCTION__, sprintf(__('Trying to access an inexistent property %s', BPMJ_EDDCM_DOMAIN), 'WP_Post::$' . $property), '4.6.1');
        return null;
    }

    public function __set($property, $value)
    {
        if (property_exists($this->post, $property) || in_array($property, array('page_template'))) {
            $this->post->{$property} = $value;
        }
        _doing_it_wrong(__FUNCTION__, sprintf(__('Trying to write to an inexistent property %s', BPMJ_EDDCM_DOMAIN), 'WP_Post::$' . $property), '4.6.1');
    }

    public function __call($method, $arguments)
    {
        if (method_exists($this->post, $method)) {
            call_user_func_array(array($this->post, $method), $arguments);
        }
        _doing_it_wrong(__FUNCTION__, sprintf(__('Trying to call an inexistent method %s', BPMJ_EDDCM_DOMAIN), 'WP_Post::' . $method . '()'), '4.6.1');
    }

    public static function get_posts($args, $decorator_class = null)
    {
        if (!$decorator_class) {
            $decorator_class = __CLASS__;
        }
        $posts = get_posts($args);
        if (__CLASS__ !== $decorator_class && !is_subclass_of($decorator_class, __CLASS__)) {
            _doing_it_wrong(__FUNCTION__, sprintf(__('Class %s must extend %s to act as a decorator', BPMJ_EDDCM_DOMAIN, $decorator_class, __CLASS__)), '4.6.1');
            return $posts;
        }
        $posts_decorated = array();
        foreach ($posts as $key => $post) {
            $posts_decorated[$key] = new $decorator_class($post);
        }
        return $posts_decorated;
    }

    /**
     * Shortcut for get_post_meta()
     *
     * @param string $key
     * @param bool $single
     *
     * @return mixed
     */
    public function get_meta($key = '', $single = false)
    {
        return get_post_meta($this->ID, $key, $single);
    }

    /**
     * @return WP_Post
     */
    public function unwrap()
    {
        return $this->post;
    }

}
