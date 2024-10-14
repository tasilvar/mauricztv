<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\packages;

interface Interface_Packages_API
{
    public function has_access_to_feature(string $feature): bool;

    public function render_no_access_to_feature_info(string $feature, ?string $custom_message = null, bool $short = false): string;

	public function get_feature_required_package(string $feature): ?string;
}