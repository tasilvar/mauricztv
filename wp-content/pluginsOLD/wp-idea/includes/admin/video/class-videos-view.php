<?php

namespace bpmj\wpidea\admin\video;

use bpmj\wpidea\admin\pages\videos\Videos_Page_Renderer;

class Videos_View
{
    private Videos_Page_Renderer $videos_page_renderer;

    public function __construct(
        Videos_Page_Renderer $videos_page_renderer
    ) {
        $this->videos_page_renderer = $videos_page_renderer;
    }

    public function render()
    {
        $this->videos_page_renderer->render_page();
    }
}
