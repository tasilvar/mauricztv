<?php
namespace bpmj\wpidea\admin\integrations;

interface Interface_Tracker
{
    public const TYPE_GENERAL = 'general';
    public const TYPE_STRING = 'string';
    public const TYPE_TOGGLEABLE = 'toggleable';
    public const TYPE_INT = 'int';
    public const TYPE_FLOAT = 'float';

    public function add_data(string $name, string $value, string $type = self::TYPE_GENERAL): void;
}
