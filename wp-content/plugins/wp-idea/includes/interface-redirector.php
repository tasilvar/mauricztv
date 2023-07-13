<?php

namespace bpmj\wpidea;

interface Interface_Redirector
{
    public function redirect( string $location, int $status_code = 302 );

    public function redirect_back(): void;
}
