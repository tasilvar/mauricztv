<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\infrastructure\io\video;

class Video_Space_Usage_Info
{
    private int $current_storage_usage_in_bytes;
    private int $max_storage_usage_in_bytes;
    private int $storage_usage_percentage;
    private ?int $current_traffic_usage_in_bytes;
    private ?int $max_traffic_usage_in_bytes;
    private ?int $traffic_usage_percentage;

    public function __construct(
        int $current_storage_usage_in_bytes,
        int $max_storage_usage_in_bytes,
        int $storage_usage_percentage,
        ?int $current_traffic_usage_in_bytes = null,
        ?int $max_traffic_usage_in_bytes = null,
        ?int $traffic_usage_percentage = null
    )
    {
        $this->current_storage_usage_in_bytes = $current_storage_usage_in_bytes;
        $this->max_storage_usage_in_bytes = $max_storage_usage_in_bytes;
        $this->storage_usage_percentage = $storage_usage_percentage;
        $this->current_traffic_usage_in_bytes = $current_traffic_usage_in_bytes;
        $this->max_traffic_usage_in_bytes = $max_traffic_usage_in_bytes;
        $this->traffic_usage_percentage = $traffic_usage_percentage;
    }

    public function get_current_storage_usage_in_bytes(): int
    {
        return $this->current_storage_usage_in_bytes;
    }

    public function get_max_storage_usage_in_bytes(): int
    {
        return $this->max_storage_usage_in_bytes;
    }

    public function get_storage_usage_percentage(): int
    {
        return $this->storage_usage_percentage;
    }

    public function get_current_traffic_usage_in_bytes(): ?int
    {
        return $this->current_traffic_usage_in_bytes;
    }

    public function get_max_traffic_usage_in_bytes(): ?int
    {
        return $this->max_traffic_usage_in_bytes;
    }

    public function get_traffic_usage_percentage(): ?int
    {
        return $this->traffic_usage_percentage;
    }
}