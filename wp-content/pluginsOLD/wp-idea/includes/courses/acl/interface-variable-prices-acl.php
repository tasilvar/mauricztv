<?php
declare(strict_types=1);

namespace bpmj\wpidea\courses\acl;

interface Interface_Variable_Prices_ACL
{
    public function save(int $product_id, array $fields): array;

    public function get_variable_prices(int $post_id): string;
}