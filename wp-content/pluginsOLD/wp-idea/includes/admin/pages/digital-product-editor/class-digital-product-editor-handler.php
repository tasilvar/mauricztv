<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\digital_product_editor;

use bpmj\wpidea\admin\pages\digital_product_editor\metaboxes\Digital_Product_Metaboxes_Renderer;
use bpmj\wpidea\instantiator\Interface_Initiable;

class Digital_Product_Editor_Handler implements Interface_Initiable
{
    public const OFFER_POST_TYPE = 'download';

    private Digital_Product_Metaboxes_Renderer $metaboxes_renderer;

    public function __construct(
        Digital_Product_Metaboxes_Renderer $metaboxes_renderer
    )
    {
        $this->metaboxes_renderer = $metaboxes_renderer;
    }

    public function init(): void
    {
        $this->metaboxes_renderer->init();
    }
}