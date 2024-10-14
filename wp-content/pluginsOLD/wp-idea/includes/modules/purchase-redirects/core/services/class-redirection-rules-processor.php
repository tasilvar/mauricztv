<?php

declare(strict_types=1);

namespace bpmj\wpidea\modules\purchase_redirects\core\services;

class Redirection_Rules_Processor
{
    public function get_redirection_url_for_order_content(array $redirection_rules, array $order_product_ids): ?string
    {
        foreach ($redirection_rules as $redirection_rule) {
            $rule_relation = $redirection_rule['relation'] ?? 'or';
            $rule_product_ids = $redirection_rule['product_ids'] ?? [];
            $redirection_url = $redirection_rule['redirection_url'] ?? null;

            if (!$redirection_url) {
                return null;
            }

            if (
                ($rule_relation === 'or')
                && $this->cart_contains_any_of_the_products($rule_product_ids, $order_product_ids)
            ) {
                return $redirection_url;
            }

            if (
                ($rule_relation === 'and')
                && $this->cart_contains_all_of_the_products($rule_product_ids, $order_product_ids)
            ) {
                return $redirection_url;
            }
        }

        return null;
    }

    private function cart_contains_any_of_the_products(array $product_ids, array $cart_product_ids): bool
    {
        foreach ($product_ids as $product_id) {
            $product_id = (int)$product_id;

            if (in_array($product_id, $cart_product_ids, true)) {
                return true;
            }
        }

        return false;
    }

    private function cart_contains_all_of_the_products(array $product_ids, array $cart_product_ids): bool
    {
        foreach ($product_ids as $product_id) {
            $product_id = (int)$product_id;

            if (!in_array($product_id, $cart_product_ids, true)) {
                return false;
            }
        }

        return true;
    }
}