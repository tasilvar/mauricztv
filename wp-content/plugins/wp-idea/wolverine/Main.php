<?php
namespace bpmj\wpidea\wolverine;

use bpmj\wpidea\wolverine\course\progress\Progress;

class Main
{

    public function __construct()
    {
        new Progress();
    }
}
