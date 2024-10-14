<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\quiz\value_object;

class Quiz_Randomization_Settings
{
	private bool $randomize_questions_order;
	private bool $randomize_answers_order;

	public function __construct(
		bool $randomize_questions_order,
		bool $randomize_answers_order
	)
	{
		$this->randomize_questions_order = $randomize_questions_order;
		$this->randomize_answers_order = $randomize_answers_order;
	}

	public function randomize_questions_order(): bool
	{
		return $this->randomize_questions_order;
	}

	public function randomize_answers_order(): bool
	{
		return $this->randomize_answers_order;
	}
}
