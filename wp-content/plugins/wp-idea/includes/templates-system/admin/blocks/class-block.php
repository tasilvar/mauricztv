<?php
namespace bpmj\wpidea\templates_system\admin\blocks;

use bpmj\wpidea\templates_system\admin\blocks\attributes\Attribute;
use bpmj\wpidea\templates_system\groups\Template_Group;

abstract class Block
{
    const BLOCK_NAME = 'wpi/block-name';

    protected $name;

    protected $title;

    protected $shortcode_to_render;

    protected $attributes = [];

    public function __construct() {
        $this->name = static::BLOCK_NAME;

        $this->setup_attributes();
    }

    public function get_name()
    {
        return $this->name;
    }

    public static function get_slug()
    {
        return str_replace('/', '-', static::BLOCK_NAME);
    }

    public function get_title()
    {
        return $this->title;
    }

    protected function setup_attributes()
    {
        //override this method to register block attributes
        // eg. $this->add_attribute(...)
    }

    public function add_attribute(Attribute $attr)
    {
        $this->attributes = array_merge(
            $this->attributes,
            $attr->get()
        );

        return $this;
    }

    public function get_attributes()
    {
        return $this->attributes;
    }

    public function get_content_to_render($atts)
    {
        return '';
    }

    public static function get_gutenberg_block_content()
    {
        $block = '<!-- wp:' . static::BLOCK_NAME . ' -->
        <!-- /wp:' . static::BLOCK_NAME . ' -->';

        return $block;
    }

    protected function get_template_path_base(): string
    {
        $active_group = Template_Group::get_active_group();

        if($active_group === null) {
            return '';
        }

        $base_template = $active_group->get_base_template();

        return "/templates/{$base_template}";
    }
}