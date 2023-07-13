<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\quiz\value_object;

class Quiz_Time_Limit_Settings
{
	public const DEFAULT_TIME = 30;

	private bool $is_enabled;
	private int $time;

	public function __construct(
		bool $is_enabled,
		int $time
	)
	{
		$this->is_enabled = $is_enabled;
		$this->time = $time;
	}

	public function is_enabled(): bool
	{
		return $this->is_enabled;
	}

	public function get_time(): int
	{
		return $this->time;
	}
}
