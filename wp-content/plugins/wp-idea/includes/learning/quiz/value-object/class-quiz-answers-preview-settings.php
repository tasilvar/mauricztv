<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\quiz\value_object;

class Quiz_Answers_Preview_Settings
{
	private bool $allow_user_to_see_his_answers_for_completed_quiz;
	private bool $reveal_correct_answers;

	public function __construct(
		bool $allow_user_to_see_his_answers_for_completed_quiz,
		bool $reveal_correct_answers
	)
	{
		$this->allow_user_to_see_his_answers_for_completed_quiz = $allow_user_to_see_his_answers_for_completed_quiz;
		$this->reveal_correct_answers = $reveal_correct_answers;
	}

	public function allow_user_to_see_his_answers_for_completed_quiz(): bool
	{
		return $this->allow_user_to_see_his_answers_for_completed_quiz;
	}

	public function reveal_correct_answers(): bool
	{
		return $this->reveal_correct_answers;
	}
}
