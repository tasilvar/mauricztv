<?php namespace bpmj\wpidea\certificates\regenerator;

use bpmj\wpidea\certificates\Interface_Certificate;

interface Interface_Certificate_Regenerator
{
    public function regenerate(Interface_Certificate $certificate): ?Interface_Certificate;
}