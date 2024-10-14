<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\api\dto;

class Partner_External_Landing_Link_DTO
{
    private int $id;
    private int $product_id;
    private string $affiliate_url;

    private function __construct(int $id, int $product_id, string $affiliate_url)
    {
        $this->id = $id;
        $this->product_id = $product_id;
        $this->affiliate_url = $affiliate_url;
    }

    public static function create(
        int $id,
        int $product_id,
        string $affiliate_url
    ): self
    {
        return new self($id, $product_id, $affiliate_url);
    }

    public function get_id(): int
    {
        return $this->id;
    }

    public function get_product_id(): int
    {
        return $this->product_id;
    }

    public function get_affiliate_url(): string
    {
        return $this->affiliate_url;
    }
}