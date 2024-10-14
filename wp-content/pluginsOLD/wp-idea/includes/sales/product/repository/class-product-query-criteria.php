<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\repository;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\sales\product\model\Product_ID;

class Product_Query_Criteria
{
    private ?ID $linked_resource_id = null;
    private ?Product_ID $in_bundle = null;
    private ?bool $is_bundle = null;
    private ?bool $is_visible_in_catalog = null;
    private ?string $phrase = null;
    private ?array $product_ids = null;

    public function set_linked_resource_id(?ID $linked_resource_id): void
    {
        $this->linked_resource_id = $linked_resource_id;
    }

    public function get_linked_resource_id(): ?ID
    {
        return $this->linked_resource_id;
    }

    public function get_in_bundle(): ?Product_ID
    {
        return $this->in_bundle;
    }

    public function set_in_bundle(?Product_ID $in_bundle): void
    {
        $this->in_bundle = $in_bundle;
    }

    public function get_is_bundle(): ?bool
    {
        return $this->is_bundle;
    }

    public function set_is_bundle(?bool $is_bundle): void
    {
        $this->is_bundle = $is_bundle;
    }

    public function set_phrase(?string $phrase): void
    {
        $this->phrase = $phrase;
    }

    public function get_phrase(): ?string
    {
        return $this->phrase;
    }

    public function set_is_visible_in_catalog(?bool $is_visible_in_catalog): void
    {
        $this->is_visible_in_catalog = $is_visible_in_catalog;
    }

    public function get_is_visible_in_catalog(): ?bool
    {
        return $this->is_visible_in_catalog;
    }

    public function get_product_ids(): ?array
    {
        return $this->product_ids;
    }

    public function set_product_ids(?array $product_ids): void
    {
        $this->product_ids = $product_ids;
    }
}