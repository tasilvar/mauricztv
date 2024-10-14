<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\model;

class Product_Tags
{
    private array $tags;

    private function __construct(
        array $tags
    ) {
        $this->tags = $tags;
    }

    public static function from_string(string $tags): self
    {
        $tags_array = explode(',', $tags);

        return new self($tags_array);
    }

    public static function create_from_array(array $tags): self
    {
        return new self($tags);
    }

    public function to_array(): array
    {
        return $this->tags;
    }

    public function to_string(): string
    {
        return implode(',', $this->tags);
    }
}