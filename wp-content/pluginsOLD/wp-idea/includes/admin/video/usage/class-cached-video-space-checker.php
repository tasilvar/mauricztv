<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\admin\video\usage;

use bpmj\wpidea\admin\video\events\Video_Event_Name;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\infrastructure\io\video\Interface_Video_Space_Checker;
use bpmj\wpidea\infrastructure\io\video\Video_Space_Usage_Info;
use bpmj\wpidea\instantiator\Interface_Initiable;
use Psr\SimpleCache\CacheInterface;
use Error;

class Cached_Video_Space_Checker implements Interface_Video_Space_Checker, Interface_Initiable
{
    private const CACHE_ITEM_KEY = 'video_space_usage_info_cache';

    private Interface_Video_Space_Checker $decorated;
    private Interface_Events $events;
    private CacheInterface $cache;

    public function __construct(
        Interface_Events $events,
        CacheInterface $cache
    )
    {
        $this->events = $events;
        $this->cache = $cache;
    }

    public function set_decorated(
        Interface_Video_Space_Checker $decorated
    ): void {
        $this->decorated = $decorated;
    }

    public function init(): void
    {
        $this->events->on(Video_Event_Name::VIDEO_STATUSES_CHECK_FINISHED, [$this, 'clear_cache']);
        $this->events->on(Video_Event_Name::VIDEO_STATUS_UPDATED, [$this, 'clear_cache']);
        $this->events->on(Video_Event_Name::REMOTE_VIDEO_DELETED, [$this, 'clear_cache']);
    }

    public function get_usage_info(): Video_Space_Usage_Info
    {
        $cached = $this->get_from_cache();

        if($cached && $this->is_cached_version_valid($cached)) {
            return $cached;
        }

        $usage_info = $this->decorated->get_usage_info();

        $this->set_in_cache($usage_info);

        return $usage_info;
    }

    private function get_from_cache(): ?Video_Space_Usage_Info
    {
        $cached = $this->cache->get(self::CACHE_ITEM_KEY);

        return  $cached ?: null;
    }

    private function set_in_cache(Video_Space_Usage_Info $usage_info): void
    {
        $this->cache->set(self::CACHE_ITEM_KEY, $usage_info);
    }

    public function clear_cache(): void
    {
        $this->cache->delete(self::CACHE_ITEM_KEY);
    }

    private function is_cached_version_valid(Video_Space_Usage_Info $cached): bool
    {
        try {
            $temp = $cached->get_current_storage_usage_in_bytes();
            $temp = $cached->get_max_storage_usage_in_bytes();
            $temp = $cached->get_storage_usage_percentage();
        } catch (Error $e) {
            return false;
        }

        return true;
    }

    public function will_uploading_file_of_size_exceed_max_usage(int $size_in_bytes): bool
    {
        $video_space_usage_info = $this->get_usage_info();
        return ($video_space_usage_info->get_current_storage_usage_in_bytes() + $size_in_bytes) > $video_space_usage_info->get_max_storage_usage_in_bytes();
    }
}