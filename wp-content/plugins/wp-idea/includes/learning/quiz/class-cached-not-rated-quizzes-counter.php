<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\quiz;

use Psr\SimpleCache\CacheInterface;

class Cached_Not_Rated_Quizzes_Counter
{
    private const CACHE_ITEM_KEY = 'not_rated_quizzes_count';

    private CacheInterface $cache;
    private Interface_Resolved_Quiz_Repository $quiz_repository;

    public function __construct(
        Interface_Resolved_Quiz_Repository $quiz_repository,
        CacheInterface            $cache
    )
    {
        $this->quiz_repository = $quiz_repository;
        $this->cache = $cache;
    }

    public function count(): int
    {
        $cached_count = $this->cache->get(self::CACHE_ITEM_KEY);

        if(!is_null($cached_count)) {
            return (int)$cached_count;
        }

        $count = $this->quiz_repository->count_not_rated();

        $this->cache->set(self::CACHE_ITEM_KEY, $count);

        return $count;
    }

    public function clear_cache(): void
    {
        $this->cache->delete(self::CACHE_ITEM_KEY);
    }
}