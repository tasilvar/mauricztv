<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\api;

class Product_API_Search_Criteria
{
    private ?bool $visible_in_catalog = null;
    private ?string $phrase = null;
    private ?bool $is_bundle = null;

    private function __construct()
    {
    }

    public static function create(): self
    {
        return new self();
    }

    public function set_visible_in_catalog(?bool $visible): self
    {
        $this->visible_in_catalog = $visible;

        return $this;
    }

    public function get_visible_in_catalog(): ?bool
    {
        return $this->visible_in_catalog;
    }

    public function set_phrase(?string $phrase): self
    {
        $this->phrase = $phrase;

        return $this;
    }

    public function get_phrase(): ?string
    {
        return $this->phrase;
    }

    public function get_is_bundle(): ?bool
    {
        return $this->is_bundle;
    }

    public function set_is_bundle(?bool $is_bundle): self
    {
        $this->is_bundle = $is_bundle;

        return $this;
    }
}