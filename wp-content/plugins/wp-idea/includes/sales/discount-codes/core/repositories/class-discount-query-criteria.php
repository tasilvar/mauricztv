<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\discount_codes\core\repositories;

class Discount_Query_Criteria
{
    public const TYPE_FILTER_EXCLUDE_AUTO_GENERATED = 'type_filter_exclude_auto_generated';
    
    private ?string $code_contains = null;
    private ?string $name_contains = null;
    private ?string $status = null;
    private ?string $type_filter = null;

    public static function create(): self
    {
        return new self();
    }

    public function set_code_contains(?string $code_fragment): void
    {
        $this->code_contains = $code_fragment;
    }

    public function get_code_contains(): ?string
    {
        return $this->code_contains;
    }

    public function get_name_contains(): ?string
    {
        return $this->name_contains;
    }

    public function set_name_contains(?string $name_contains): void
    {
        $this->name_contains = $name_contains;
    }

    public function set_status_equals(?string $status): void
    {
        $this->status = $status;
    }

    public function get_status(): ?string
    {
        return $this->status;
    }

    public function set_type_filter(?string $type_filter): void
    {
        $this->type_filter = $type_filter;
    }
    
    public function get_type_filter(): ?string
    {
        return $this->type_filter;
    }
    
}