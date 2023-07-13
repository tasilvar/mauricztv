<?php

namespace bpmj\wpidea\admin\tables;

class Label
{
    public const TYPE_DEFAULT = 'default';

    public const TYPE_HIDDEN = 'hidden';

    private $text;

    private $type;

    private function __construct(string $text, string $type = self::TYPE_DEFAULT)
    {
        $this->text = $text;
        $this->type = $type;
    }

    public static function create(string $text, string $type = self::TYPE_DEFAULT): self
    {
        return new self($text, $type);
    }

    public static function create_hidden(string $text): self
    {
        return new self($text, self::TYPE_HIDDEN);
    }

    public function get_text(): string
    {
        return $this->text;
    }

    public function get_type(): string
    {
        return $this->type;
    }

    public function is_of_type(string $type): bool
    {
        return $this->get_type() === $type;
    }
}
