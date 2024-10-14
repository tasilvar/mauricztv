<?php

namespace bpmj\wpidea\admin\affiliate_program;

use bpmj\wpidea\admin\pages\affiliate_program\Affiliate_Program_Partners_Page_Renderer;

class Affiliate_Program_Partners_View
{
    private Affiliate_Program_Partners_Page_Renderer $renderer;

    public function __construct(
        Affiliate_Program_Partners_Page_Renderer $renderer
    ) {
        $this->renderer = $renderer;
    }

    public function render()
    {
        $this->renderer->render_page();
    }
}
