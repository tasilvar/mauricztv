<?php

namespace bpmj\wpidea\modules\gallery_block_lightbox;

use bpmj\wpidea\modules\gallery_block_lightbox\core\filters\Option_Image_Filter;
use bpmj\wpidea\modules\gallery_block_lightbox\web\Gallery_Block_Lightbox_Renderer;
use bpmj\wpidea\shared\abstractions\modules\Interface_Module;

class Gallery_Block_Lightbox_Module implements Interface_Module
{
    private Gallery_Block_Lightbox_Renderer $lightbox_renderer;
    private Option_Image_Filter $option_image_filter;

    public function __construct(
        Gallery_Block_Lightbox_Renderer $lightbox_renderer,
        Option_Image_Filter $option_image_filter
    )
    {
        $this->lightbox_renderer = $lightbox_renderer;
        $this->option_image_filter = $option_image_filter;
    }

    public function init(): void
    {
        $this->lightbox_renderer->init();
        $this->option_image_filter->init();
    }

    public function get_routes(): array
    {
        return [];
    }

    public function get_translations(): array
    {
        return [];
    }
}