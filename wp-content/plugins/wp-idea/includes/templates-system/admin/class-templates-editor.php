<?php

namespace bpmj\wpidea\templates_system\admin;

use bpmj\wpidea\Post_Meta;
use bpmj\wpidea\templates_system\admin\blocks\Block;
use bpmj\wpidea\templates_system\templates\repository\Repository as TemplatesRepository;
use bpmj\wpidea\templates_system\templates\Template;

class Templates_Editor
{
    const BPMJ_WPI_TEMPLATE_BLOCKS_VAR = 'BPMJ_WPI_TEMPLATE_BLOCKS';

    const BMPJ_WPI_TEMPLATES_DELETE_WARNING_VAR = 'BMPJ_WPI_TEMPLATES_DELETE_WARNING';

    const SCRIPT_TPL_BLOCKS = 'wpi-templates-blocks';
    const SCRIPT_TPL_BLOCKS_EDITOR = 'wpi-templates-blocks-editor';

    const BLOCKS_CAT = 'wpi-template-blocks';

    private $blocks_to_register = [];

    private $template;

    public function init()
    {
        add_action('enqueue_block_editor_assets', [$this, 'load_templates_blocks']);
        add_action('enqueue_block_editor_assets', [$this, 'load_blocks_data']);
        
        if (version_compare(get_bloginfo('version'), '5.8.0', '<')) {
            add_filter( 'block_categories', [$this, 'load_templates_blocks_cat_for_wp_older_than_5_8'], 10, 2 );
        } else {
            add_filter( 'block_categories_all', [$this, 'load_templates_blocks_cat'], 10, 2 );
        }

        $this->init_template();
    }

    private function init_template()
    {
        if(empty($_GET['post'])) return;

        if(!$this->is_supported_post_type($_GET['post'])) return;
            
        $this->template = Template::find($_GET['post']);

        if(empty($this->template)) $this->template = new Template();

        $this->template->init();
    }

    public function is_supported_post_type($post_id)
    {
        if(get_post_type($post_id) == TemplatesRepository::TEMPLATES_POST_TYPE) return true;

        if(Post_Meta::get($post_id, 'mode') == 'lesson') return true;

        return false;
    }

    public function get_currently_edited_template()
    {
        return $this->template;
    }

    public function load_templates_blocks() 
    {
        wp_enqueue_script(
          self::SCRIPT_TPL_BLOCKS,
          BPMJ_EDDCM_URL. '/assets/js/admin/templates-editor/blocks.js',
          array('wp-blocks','wp-editor'),
          true
        );
    }


    public function load_templates_blocks_cat(array $categories, ?\WP_Block_Editor_Context $wp_block_editor_context): array
    {
        if (empty($wp_block_editor_context->post)) {
            return $categories;
        }
    
        return $this->load_templates_blocks_cat_for_wp_older_than_5_8($categories, $wp_block_editor_context->post);
    }

    public function load_templates_blocks_cat_for_wp_older_than_5_8(array $categories, \WP_Post $post): array
    {
        if (!$this->is_supported_post_type($post->ID)) {
            return $categories;
        }
        
        return array_merge(
            $categories,
            array(
                array(
                    'slug' => self::BLOCKS_CAT,
                    'title' => __('WP Idea template blocks', BPMJ_EDDCM_DOMAIN),
                    'icon'  => 'lightbulb',
                ),
            )
        );
    }



    public function register_block(Block $block)
    {
        if(!empty($this->blocks_to_register[$block->get_name()])) return;
        
        $this->blocks_to_register[$block->get_name()] = $block;
    }

    public function load_blocks_data()
    {
        $blocks_data = [];
        foreach ($this->blocks_to_register as $key => $block) {
            $blocks_data[] = [
                'name' => $block->get_name(),
                'title' => $block->get_title(),
                'attributes' => $block->get_attributes(),
                'cat' => self::BLOCKS_CAT
            ];
        }

        wp_localize_script(self::SCRIPT_TPL_BLOCKS, self::BPMJ_WPI_TEMPLATE_BLOCKS_VAR, $blocks_data);
    }
}
