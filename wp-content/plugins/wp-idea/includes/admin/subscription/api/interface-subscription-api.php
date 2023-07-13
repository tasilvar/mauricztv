<?php
namespace bpmj\wpidea\admin\subscription\api;

interface Interface_Subscription_API
{
    public function get_plan_for_current_user(): string;

    public function is_go(): bool;

    public function get_license_key(): string;

    public function has_access_to_for_active_license(string $future): bool;
}