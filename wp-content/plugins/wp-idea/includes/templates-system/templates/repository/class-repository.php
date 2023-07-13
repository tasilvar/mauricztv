<?php

namespace bpmj\wpidea\templates_system\templates\repository;

use bpmj\wpidea\Post_Meta;
use bpmj\wpidea\templates_system\groups\Template_Group_Id;
use bpmj\wpidea\templates_system\templates\Template_Data;
use WP_Post;

class Repository
{
    public const TEMPLATES_POST_TYPE = 'wpi_page_templates';
    public const META_IS_BASIC = '_is_basic';
    public const META_IS_ACTIVE = '_is_active';
    public const META_CLASS_NAME = '_class_name';
    public const META_GROUP_ID = '_template_group_id';

    public function setup(): void
    {
        add_action('init', [self::class, 'create_post_type']);
    }
    
    public static function create_post_type(): void
    {
        register_post_type( self::TEMPLATES_POST_TYPE,
            [
                'labels' => [
                    'name' => __( 'Templates', BPMJ_EDDCM_DOMAIN ),
                    'singular_name' => __( 'Template', BPMJ_EDDCM_DOMAIN )
                ],
                'public' => true,
                'has_archive' => false,
                'show_in_rest' => true,
                'show_in_menu' => false,
                'supports' => ['editor']
            ]
        );
    }

    public function store(Template_Data $data): Template_Data
    {
        $data->id = empty($data->id) ? $this->create_post($data) : $this->update_post($data);

        $this->update_template_data($data);

        return $data;
    }

    public function find_all(): array
    {
        $templates_data = [];

        $posts = get_posts([
            'post_type' => self::TEMPLATES_POST_TYPE,
            'post_status' => 'private',
            'numberposts' => -1
        ]);

        foreach ($posts as $key => $post) {
            $data = $this->get_data_from_post($post);

            $templates_data[] = $data;
        }

        return $templates_data;
    }

    public function find($id): ?Template_Data
    {        
        $post = get_post($id);

        if(empty($post)) return null;

        if(get_post_type($id) !== self::TEMPLATES_POST_TYPE) return null;

        if($post->post_status !== 'private') return null;

        return $this->get_data_from_post($post);
    }

    public function find_active($class_name, Template_Group_Id $group_id): ?Template_Data
    {
        return $this->find_by_meta_and_group($class_name, self::META_IS_ACTIVE, $group_id);
    }

    public function find_default($class_name, Template_Group_Id $group_id): ?Template_Data
    {
        return $this->find_by_meta_and_group($class_name, self::META_IS_BASIC, $group_id);
    }

    private function find_by_meta_and_group(string $template_class_name, string $meta_name, Template_Group_Id $group_id): ?Template_Data
    {
        $post = $this->find_first_post_with_meta_value_true_by_group_id($template_class_name, $meta_name, $group_id->stringify());

        return $post !== null ? $this->get_data_from_post($post) : null;
    }

    public function get_rendered_template_content($id): string
    {
        return do_shortcode(do_blocks(get_post_field('post_content', $id)));
    }

    protected function set_class_name($id, $class_name)
    {
        return Post_Meta::set($id, self::META_CLASS_NAME, wp_slash($class_name));
    }

    protected function get_class_name($id)
    {
        return Post_Meta::get($id, self::META_CLASS_NAME);
    }

    protected function set_is_basic($id, $is_basic)
    {
        return Post_Meta::set($id, self::META_IS_BASIC, $is_basic);
    }

    protected function get_is_basic($id)
    {
        return !empty(Post_Meta::get($id, self::META_IS_BASIC));
    }

    protected function set_is_active($id, $is_active)
    {
        return Post_Meta::set($id, self::META_IS_ACTIVE, $is_active);
    }

    protected function get_is_active($id): bool
    {
        return !empty(Post_Meta::get($id, self::META_IS_ACTIVE));
    }

    private function set_group_id(int $id, ?string $template_group_id)
    {
        if(is_null($template_group_id)) {
            return false;
        }

        return Post_Meta::set($id, self::META_GROUP_ID, $template_group_id);
    }

    private function get_group_id(int $id): ?string
    {
        $meta = Post_Meta::get($id, self::META_GROUP_ID);

        return !empty($meta) ? $meta : null;
    }

    protected function get_data_from_post(WP_Post $post): Template_Data
    {
        $data               = new Template_Data();
        $data->id           = $post->ID;
        $data->name         = get_the_title($post->ID);
        $data->content      = $post->post_content;
        $data->is_basic     = $this->get_is_basic($post->ID);
        $data->is_active    = $this->get_is_active($post->ID);
        $data->class_name   = $this->get_class_name($post->ID);
        $data->template_group_id = $this->get_group_id($post->ID);

        return $data;
    }

    private function create_post(Template_Data $data): int
    {
        return wp_insert_post([
            'post_type' => self::TEMPLATES_POST_TYPE,
            'post_title' => $data->name,
            'post_content' => $data->content,
            'post_status' => 'private',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
        ]);
    }

    private function update_template_data(Template_Data $data): void
    {
        $this->set_class_name($data->id, $data->class_name);

        if(!is_null($data->is_basic)) $this->set_is_basic($data->id, $data->is_basic);
        if(!is_null($data->is_active)) $this->set_is_active($data->id, $data->is_active);
        if(!is_null($data->template_group_id)) $this->set_group_id($data->id, $data->template_group_id);
    }

    private function update_post(Template_Data $data): int
    {
        return wp_update_post([
            'ID' => $data->id,
            'post_title' => $data->name,
            'post_content' => $data->content
        ]);
    }

    private function find_first_post_with_meta_value_true_by_group_id(string $template_class_name, string $meta_name, string $group_id): ?WP_Post
    {
        $posts = get_posts([
            'post_type' => self::TEMPLATES_POST_TYPE,
            'post_status' => 'private',
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key'     => $meta_name,
                    'value'   => true,
                    'compare' => '=',
                ],
                [
                    'key'     => self::META_CLASS_NAME,
                    'value'   => $template_class_name,
                    'compare' => '=',
                ],
                [
                    'key'     => self::META_GROUP_ID,
                    'value'   => $group_id,
                    'compare' => '=',
                ],
            ],
            'numberposts' => 1
        ]);

        return !empty($posts) ? reset($posts) : null;
    }
}