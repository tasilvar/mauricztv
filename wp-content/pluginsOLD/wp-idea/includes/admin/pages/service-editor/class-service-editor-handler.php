<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\pages\service_editor;

use bpmj\wpidea\admin\pages\service_editor\metaboxes\Service_Metaboxes_Renderer;
use bpmj\wpidea\instantiator\Interface_Initiable;

class Service_Editor_Handler implements Interface_Initiable
{
    public const OFFER_POST_TYPE = 'download';

    private Service_Metaboxes_Renderer $metaboxes_renderer;

    public function __construct(
        Service_Metaboxes_Renderer $metaboxes_renderer
    ) {
        $this->metaboxes_renderer = $metaboxes_renderer;
    }

    public function init(): void
    {
        $this->metaboxes_renderer->init();
    }
}