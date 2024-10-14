<?php

namespace bpmj\wpidea\modules\gallery_block_lightbox\web;

use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\modules\gallery_block_lightbox\core\filters\Filter_Names;

class Gallery_Block_Lightbox_Renderer
{
    private const LIGHTBOX_CSS_URL = 'https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.1/baguetteBox.min.css';
    private const LIGHTBOX_JS_URL = 'https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.1/baguetteBox.min.js';

    private Interface_Actions $actions;
    private Interface_Filters $filters;

    public function __construct(
        Interface_Actions $actions,
        Interface_Filters $filters
    )
    {
        $this->actions = $actions;
        $this->filters = $filters;
    }

    public function init(): void
    {
        $this->actions->add(Action_Name::HEAD, [$this, 'load_gallery_block_lightbox']);
    }

    public function load_gallery_block_lightbox(): void
    {
        if (!$this->should_load_lightbox()) {
            return;
        }

        echo $this->render_lightbox_css();
        echo $this->render_lightbox_js();
        echo $this->render_script_to_run_lightbox();
    }

    private function render_lightbox_css(): string
    {
        return '<link rel="stylesheet" href="' . self::LIGHTBOX_CSS_URL . '">';
    }

    private function render_lightbox_js(): string
    {
        return '<script src="' . self::LIGHTBOX_JS_URL . '" async></script>';
    }

    private function render_script_to_run_lightbox(): string
    {
        $baguettebox_selector = $this->filters->apply(Filter_Names::LIGHTBOX_SELECTOR, '.wp-block-gallery,:not(.wp-block-gallery)>.wp-block-image,.wp-block-media-text__media,.gallery,.wp-block-coblocks-gallery-masonry,.wp-block-coblocks-gallery-stacked,.wp-block-coblocks-gallery-collage,.wp-block-coblocks-gallery-offset,.wp-block-coblocks-gallery-stacked,.mgl-gallery,.gb-block-image');
        $baguettebox_filter = $this->filters->apply(Filter_Names::LIGHTBOX_FILTER, '/.+\.(gif|jpe?g|png|webp|svg|avif|heif|heic|tif?f|)($|\?)/i');

        return '<script> 
                 window.addEventListener("load", function() {baguetteBox.run("' . $baguettebox_selector . '",{captions:function(t){var e=t.parentElement.classList.contains("wp-block-image")||t.parentElement.classList.contains("wp-block-media-text__media")?t.parentElement.querySelector("figcaption"):t.parentElement.parentElement.querySelector("figcaption,dd");return!!e&&e.innerHTML},filter:' . $baguettebox_filter . '});});
               </script>';
    }

    private function should_load_lightbox(): bool
    {
        return $this->filters->apply(Filter_Names::LIGHTBOX_ENQUEUE_ASSETS,
            has_block('core/gallery') ||
            has_block('core/image') ||
            has_block('core/media-text') ||
            get_post_gallery() ||
            has_block('coblocks/gallery-masonry') ||
            has_block('coblocks/gallery-stacked') ||
            has_block('coblocks/gallery-collage') ||
            has_block('coblocks/gallery-offset') ||
            has_block('coblocks/gallery-stacked') ||
            has_block('meow-gallery/gallery') ||
            has_block('generateblocks/image')
        );
    }
}