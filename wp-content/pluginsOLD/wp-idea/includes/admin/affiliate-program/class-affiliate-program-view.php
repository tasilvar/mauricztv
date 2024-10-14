<?php

namespace bpmj\wpidea\admin\affiliate_program;

use bpmj\wpidea\admin\pages\affiliate_program\Affiliate_Page_Renderer;

class Affiliate_Program_View
{
    private Affiliate_Page_Renderer $affiliate_page_renderer;

    public function __construct(
        Affiliate_Page_Renderer $affiliate_page_renderer
    ) {
        $this->affiliate_page_renderer = $affiliate_page_renderer;
    }

    public function render()
    {
        $this->affiliate_page_renderer->render_page();
    }
}
