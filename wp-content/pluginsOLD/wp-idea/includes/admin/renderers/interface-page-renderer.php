<?php

namespace bpmj\wpidea\admin\renderers;

interface Interface_Page_Renderer
{
    public function render_page(): void;

    public function get_rendered_page(): string;
}