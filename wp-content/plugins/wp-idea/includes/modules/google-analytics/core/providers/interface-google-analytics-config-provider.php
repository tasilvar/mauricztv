<?php

namespace bpmj\wpidea\modules\google_analytics\core\providers;

interface Interface_Google_Analytics_Config_Provider
{
    public function get_ga4_id(): ?string;

    public function is_ga4_enabled(): bool;

    public function get_ga4_debug_view(): bool;

}