<?php

namespace bpmj\wpidea\headers;

use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\events\actions\Interface_Actions;

class HSTS_Header implements Interface_Header, Interface_Initiable
{
    private const MAX_AGE = 10886400;

    private $actions;

    public function __construct(Interface_Actions $actions)
    {
        $this->actions = $actions;
    }

    public function init(): void
    {
        $this->send();
    }

    private function get_name(): string
    {
        return 'Strict-Transport-Security: max-age=' . self::MAX_AGE;
    }

    public function send(): void
    {
        $name = $this->get_name();
        $this->actions->add('send_headers', function () use ($name){
            header($name);
        });
    }
}
