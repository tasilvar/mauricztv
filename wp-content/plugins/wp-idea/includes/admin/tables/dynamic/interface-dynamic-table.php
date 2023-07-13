<?php

namespace bpmj\wpidea\admin\tables\dynamic;

interface Interface_Dynamic_Table
{
    public function get_html(string $html_classes = ''): string;
}