<?php

namespace bpmj\wpidea\admin\helpers\html;

interface Interface_Renderable
{
    public function get_html(): string;

    public function print_html(): void;
}
