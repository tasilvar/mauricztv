<?php

namespace bpmj\wpidea\modules\meta_conversion_api;

use bpmj\wpidea\modules\meta_conversion_api\infrastructure\events\Event_Handlers_Initiator;
use bpmj\wpidea\modules\meta_conversion_api\web\Meta_Pixel_Scripts_Renderer;
use bpmj\wpidea\shared\abstractions\modules\Interface_Module;

class Meta_Conversion_API_Module implements Interface_Module
{
    private Meta_Pixel_Scripts_Renderer $meta_pixel_scripts_renderer;
    private Event_Handlers_Initiator $event_handlers_initiator;

    public const COOKIE_FBP_NAME = '_fbp';
    public const COOKIE_FBC_NAME = '_fbc';

    public function __construct(
        Meta_Pixel_Scripts_Renderer $meta_pixel_scripts_renderer,
        Event_Handlers_Initiator $event_handlers_initiator
    ) {
        $this->meta_pixel_scripts_renderer = $meta_pixel_scripts_renderer;
        $this->event_handlers_initiator = $event_handlers_initiator;
    }

    public function init(): void
    {
        $this->meta_pixel_scripts_renderer->init();
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