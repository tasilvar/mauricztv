<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\digital_products\persistence;

interface Interface_Digital_Product_Persistence
{
    /**
     * @return array{id: int, name: string}|null
     */
    public function find_by_id(int $id): ?array;

    public function find_all(): array;

    public function find_files_by_product_id(int $product_id): array;

    public function update_files_by_product_id(int $product_id, array $files): void;

    public function save_or_update_product(?int $id, string $name): int;

    public function count_all(): int;
}