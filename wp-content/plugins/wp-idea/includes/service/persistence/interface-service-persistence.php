<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\service\persistence;

interface Interface_Service_Persistence
{
    /**
     * @return array{id: int, name: string}|null
     */
    public function find_by_id(int $id): ?array;

    public function find_all(): array;

    public function save_or_update_service(?int $id, string $name): int;

    public function count_all(): int;
}
