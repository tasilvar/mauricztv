<?php

namespace bpmj\wpidea\modules\videos\infrastructure\space_checker;

use bpmj\wpidea\infrastructure\io\video\Interface_Video_Space_Checker;
use bpmj\wpidea\infrastructure\io\video\Video_Space_Usage_Info;
use bpmj\wpidea\options\Interface_Options;
use bpmj\wpidea\admin\subscription\models\Subscription_Const;
use bpmj\wpidea\admin\subscription\models\Subscription;
use JsonException;
use bpmj\wpidea\modules\videos\core\repositories\Interface_Video_Repository;

class Video_Space_Checker implements Interface_Video_Space_Checker
{
    private const DEFAULT_MAX_USAGE_IN_GB = 25;

    private const MAX_STORAGE_USAGE_IN_GB_BY_LICENSE_TYPE = [
        Subscription_Const::PLAN_START => self::DEFAULT_MAX_USAGE_IN_GB,
        Subscription_Const::PLAN_PLUS => 50,
        Subscription_Const::PLAN_PRO => 100
    ];

    private const MAX_TRAFFIC_USAGE_TO_MAX_STORAGE_USAGE_MULTIPLIER = 10;

    private const USAGE_INFO_DATA_OPTION_NAME = 'bunnynet_usage_stats';

    private Interface_Options $options;
    private Subscription $subscription;
    private ?array $usage_info_data = null;
    private Interface_Video_Repository $video_repository;

    public function __construct(
        Interface_Options $options,
        Subscription $subscription,
        Interface_Video_Repository $video_repository
    )
    {
        $this->options = $options;
        $this->subscription = $subscription;
        $this->video_repository = $video_repository;
    }

    public function get_usage_info(): Video_Space_Usage_Info
    {
        $current_storage_usage = $this->get_current_storage_usage_in_bytes();
        $max_storage_usage = $this->get_max_storage_usage_in_bytes();
        $storage_usage_percentage = $this->get_usage_percentage($current_storage_usage, $max_storage_usage);

        $current_traffic_usage = $this->get_current_traffic_usage_in_bytes();
        $max_traffic_usage = $this->get_max_traffic_usage_in_bytes();
        $traffic_usage_percentage = $this->get_usage_percentage($current_traffic_usage, $max_traffic_usage);

        return new Video_Space_Usage_Info(
            $current_storage_usage,
            $max_storage_usage,
            $storage_usage_percentage,
            $current_traffic_usage,
            $max_traffic_usage,
            $traffic_usage_percentage
        );
    }

    private function get_max_storage_usage_in_bytes(): int
    {
        $max_storage_usage = self::MAX_STORAGE_USAGE_IN_GB_BY_LICENSE_TYPE[$this->subscription->get_plan()];

        return $max_storage_usage * 1000 * 1000 * 1000;
    }

    private function get_current_storage_usage_in_bytes(): int
    {
        $all_videos = $this->video_repository->find_all();
        $storage_usage = 0;

        foreach ($all_videos as $video) {
            $storage_usage += $video->get_file_size();
        }

        return $storage_usage;
    }

    private function get_current_traffic_usage_in_bytes(): int
    {
        $data = $this->get_usage_info_data();

        return $data['traffic_usage'] ?? 0;
    }

    private function get_usage_info_data(): array
    {
        $default_empty_data = [
            'traffic_usage' => 0
        ];

        if($this->usage_info_data) {
            return $this->usage_info_data;
        }

        $data = $this->options->get(self::USAGE_INFO_DATA_OPTION_NAME);

        try {
            $this->usage_info_data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->usage_info_data = $default_empty_data;
        }

        return $this->usage_info_data;
    }

    private function get_max_traffic_usage_in_bytes(): int
    {
        return $this->get_max_storage_usage_in_bytes() * self::MAX_TRAFFIC_USAGE_TO_MAX_STORAGE_USAGE_MULTIPLIER;
    }

    private function get_usage_percentage(int $current, int $max, int $precision = 2): int
    {
        return round( $current /  $max * 100, $precision );
    }
}