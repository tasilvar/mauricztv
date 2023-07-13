<?php

declare(strict_types=1);

namespace bpmj\wpidea\admin\renderers;

abstract class Abstract_Page_Renderer implements Interface_Page_Renderer
{
    public function render_page(): void
    {
        echo $this->get_rendered_page();
    }
}