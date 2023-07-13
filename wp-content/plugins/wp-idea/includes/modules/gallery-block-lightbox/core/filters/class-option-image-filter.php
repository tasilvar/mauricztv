<?php

namespace bpmj\wpidea\modules\gallery_block_lightbox\core\filters;

use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\instantiator\Interface_Initiable;

class Option_Image_Filter implements Interface_Initiable
{
    private Interface_Filters $filters;

    public function __construct(Interface_Filters $filters)
    {
        $this->filters = $filters;
    }

    public function init(): void
    {
        $this->filters->add('option_image_default_link_type', fn() => 'file');
    }
}