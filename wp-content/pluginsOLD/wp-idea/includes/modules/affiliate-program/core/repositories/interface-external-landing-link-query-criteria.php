<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\affiliate_program\core\repositories;

interface Interface_External_Landing_Link_Query_Criteria
{
    public function get_id(): ?int;

    public function get_product_ids(): ?array;

    public function get_url(): ?string;
}