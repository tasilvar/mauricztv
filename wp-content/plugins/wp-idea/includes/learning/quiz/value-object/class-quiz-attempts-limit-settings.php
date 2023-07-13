<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\quiz\value_object;

class Quiz_Attempts_Limit_Settings
{
	public const DEFAULT_LIMIT = 2;

	private bool $is_enabled;
	private int $limit;

	public function __construct(
		bool $is_enabled,
		int $limit
	)
	{
		$this->is_enabled = $is_enabled;
		$this->limit      = $limit;
	}

	public function is_enabled(): bool
	{
		return $this->is_enabled;
	}

	public function get_limit(): int
	{
		return $this->limit;
	}
}
