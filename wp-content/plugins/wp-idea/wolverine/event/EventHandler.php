<?php
namespace bpmj\wpidea\wolverine\event;

abstract class EventHandler
{

    public abstract function run(array $data);
}
