<?php

namespace bpmj\wpidea\view;

interface Interface_View_Provider_Aware
{
    public function set_view_provider(Interface_View_Provider $view_provider): void;
}