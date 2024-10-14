<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\physical_product_editor;

use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\admin\pages\physical_product_editor\metaboxes\Physical_Product_Metaboxes_Renderer;

class Physical_Product_Editor_Handler implements Interface_Initiable
{
    public const OFFER_POST_TYPE = 'download';

    private Physical_Product_Metaboxes_Renderer $metaboxes_renderer;

    public function __construct(
        Physical_Product_Metaboxes_Renderer $metaboxes_renderer
    ) {
        $this->metaboxes_renderer = $metaboxes_renderer;
    }

    public function init(): void
    {
        $this->metaboxes_renderer->init();
    }
}