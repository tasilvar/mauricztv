<?php

namespace bpmj\wpidea\admin\video\usage;

use bpmj\wpidea\admin\video\Api_Credentials;
use bpmj\wpidea\admin\video\Video_Api;
use bpmj\wpidea\helpers\Bytes_Formatter;
use bpmj\wpidea\infrastructure\io\video\Interface_Video_Space_Checker;
use bpmj\wpidea\infrastructure\io\video\Video_Space_Usage_Info;

class Deprecated_Video_Space_Checker implements Interface_Video_Space_Checker
{
    private const FALLBACK_TOTAL_MAX = 1000000000;

    public function get_usage_info(): Video_Space_Usage_Info
    {
        $user_data = $this->get_user_data();

        $current = $user_data['usage']['current'] ?? 0;
        $max = $user_data['usage']['max'] ?? self::FALLBACK_TOTAL_MAX;
        $percentage = $this->get_percentage_usage_from_user_data( $user_data );

        return new Video_Space_Usage_Info(
            $current,
            $max,
            $percentage
        );
    }

    /**
     * Get user data
     *
     * @return array
     */
    private function get_user_data(): ?array
    {
        $api = new Video_Api( Api_Credentials::get_wpi_key(), Api_Credentials::get_host() );

        $user = $api->get_user_data();

        return $user ?: null;
    }

    private function get_percentage_usage_from_user_data( $user_data, $precision = 2 ): int
    {
        $usage = $user_data['usage'];

        if( empty( $usage['current'] ) ) return 0;

        if( empty( $usage['max'] ) ) $usage['max'] = self::FALLBACK_TOTAL_MAX;

        return round( $usage['current'] /  $usage['max'] * 100, $precision );
    }
}