<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\infrastructure\persistence;

use bpmj\wpidea\modules\affiliate_program\core\repositories\Interface_External_Landing_Link_Query_Criteria;

class External_Landing_Link_Query_Criteria implements Interface_External_Landing_Link_Query_Criteria
{
    public ?int $id;
    public ?array $product_ids;
    public ?string $url;

    public function __construct(
        ?int $id = null,
        ?array $product_ids = null,
        ?string $url = null
    ) {
        $this->id = $id;
        $this->product_ids = $product_ids;
        $this->url = $url;
    }

    public function get_id(): ?int
    {
        return $this->id;
    }

    public function get_product_ids(): ?array
    {
        return $this->product_ids;
    }

    public function get_url(): ?string
    {
        return $this->url;
    }
}