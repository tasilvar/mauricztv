<?php

namespace bpmj\wpidea\mods;

use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\events\filters\Interface_Filters;

class Change_Language_Button_Remover implements Interface_Initiable
{
    private Interface_Filters $filters;

    public function __construct(Interface_Filters $filters)
    {
        $this->filters = $filters;
    }

    public function init(): void
    {
        $this->filters->add( 'login_display_language_dropdown', '__return_false' );
    }
}