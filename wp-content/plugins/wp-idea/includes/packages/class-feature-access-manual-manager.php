<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\packages;

use bpmj\wpidea\Caps;
use bpmj\wpidea\Current_Request;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\helpers\Cache;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\user\api\Interface_User_API;

class Feature_Access_Manual_Manager implements Interface_Initiable
{
	private const QUERY_ARG_NAME_FORCE_ENABLE_FEATURE_ACCESS = 'force_enable_feature_access';
	private const QUERY_ARG_NAME_RESET_FORCE_ENABLE_FEATURE_ACCESS = 'reset_force_enabled_feature_access';
	private const TRANSIENT_NAME_BASE = 'publigo_force_enable_feature_';
	private const VALUE_ENABLED = 'forced';

	private Current_Request $current_request;
	private Interface_User_API $user_api;
	private Interface_Actions $actions;

	public function __construct(
		Current_Request $current_request,
		Interface_User_API $user_api,
		Interface_Actions $actions
	)
	{
		$this->current_request = $current_request;
		$this->user_api = $user_api;
		$this->actions = $actions;
	}

	public function init(): void
	{
		$this->actions->add('admin_init', [$this, 'do_init']);
	}

	public function is_access_to_feature_forced(string $feature): bool
	{
		return $this->is_feature_force_enabled($feature);
	}

	public function do_init(): void
	{
		if(
			!$this->current_request->get_query_arg(self::QUERY_ARG_NAME_FORCE_ENABLE_FEATURE_ACCESS)
			&& !$this->current_request->get_query_arg(self::QUERY_ARG_NAME_RESET_FORCE_ENABLE_FEATURE_ACCESS)
		) {
			return;
		}

		if(!$this->user_api->current_user_has_role(Caps::ROLE_SITE_ADMIN)) {
			return;
		}

		$this->handle_feature_access_enabling();
		$this->handle_resetting();
	}

	private function handle_feature_access_enabling(): void
	{
		$feature_to_enable_access_to = $this->current_request->get_query_arg(self::QUERY_ARG_NAME_FORCE_ENABLE_FEATURE_ACCESS);

		if(empty($feature_to_enable_access_to)) {
			return;
		}

		if($this->is_feature_force_enabled($feature_to_enable_access_to)) {
			return;
		}

		$this->set_force_enable_feature($feature_to_enable_access_to);
	}

	private function handle_resetting(): void
	{
		$feature_to_reset_access_to = $this->current_request->get_query_arg(self::QUERY_ARG_NAME_RESET_FORCE_ENABLE_FEATURE_ACCESS);

		if(empty($feature_to_reset_access_to)) {
			return;
		}

		if(!$this->is_feature_force_enabled($feature_to_reset_access_to)) {
			return;
		}

		$this->unset_force_enable_feature($feature_to_reset_access_to);
	}

	private function is_feature_force_enabled(string $feature): bool
	{
		return Cache::get( $this->create_cache_item_name( $feature ) ) === self::VALUE_ENABLED;
	}

	private function set_force_enable_feature(string $feature): void
	{
		Cache::set( $this->create_cache_item_name( $feature ), self::VALUE_ENABLED, Cache::EXPIRATION_TIME_24_HOURS);
	}

	private function unset_force_enable_feature(string $feature): void
	{
		Cache::unset($this->create_cache_item_name( $feature ));
	}

	private function create_cache_item_name( string $feature ): string
	{
		return self::TRANSIENT_NAME_BASE . $feature;
	}
}